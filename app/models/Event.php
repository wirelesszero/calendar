<?php
namespace App\Models;

use DateTime;
/**
*
*/
class Event extends Model
{
    public $properties;

    protected static $table = 'event';

    /**
     * Возвращает отфильтрованные записи событий
     * @param string|null $year
     * @param string|null $month
     * @return array
     */
    public function eventFilter($year = null, $month = null)
    {
        $where = '';
        if ($year) {
            $where .= " AND YEAR(start_date) = $year";
        }
        if ($month) {
            $where .= " AND MONTH(start_date) = $month";
        }
        $sql = "SELECT * FROM " . static::$table . " WHERE 1=1 $where ORDER BY finish_date";
        return $this->getByQuery($sql);
    }

    /**
     * Запрос календаря
     * @param string|null $year
     * @param string|null $month
     * @return array
     */
    public function calendar($year = null, $month = null)
    {
        if (!isset($year)) {
            $year = 2016;
        };
        $events = $this->eventFilter($year, $month);

        if (!isset($month)) {
            $type = 'year';
            $month = 1;
        } else {
            $type = 'month';
        }
        return $this->formatedEvents($events, $type, $year, $month);
    }

    /**
     * Проверяет вхождение даты
     * @param string $currentDate
     * @param string $startDate
     * @param string $finishDate
     * @return boolean
     */
    private function inRange($currentDate, $startDate, $finishDate) {
        $currentTime = strtotime($currentDate);
        $startTime = strtotime(substr($startDate,0, 10));
        $finishTime = strtotime(substr($finishDate,0, 10));
        return ($currentTime >= $startTime && $currentTime <= $finishTime);
    }

    /**
     * Расчёт календарного блока за месяц
     * @param string $dateYear
     * @param string $dateMonth
     * @return array
     */
    private function calculateCalendarBlock($dateYear, $dateMonth)
    {
        $date = $dateYear.'-'.$dateMonth.'-01';
        $monthFirstDay = date("N",strtotime($date));
        $totalDaysOfMonth = cal_days_in_month(CAL_GREGORIAN, $dateMonth, $dateYear);
        // Если первое число месяца понедельник
        if ($monthFirstDay == 1) {
            $monthLastDayCell = $totalDaysOfMonth; // ячейка с последним значащим днем
        } else {
            $monthLastDayCell = $totalDaysOfMonth + ($monthFirstDay - 1); // /--/ + добавляем пустые ячейки
        }
        switch ($monthLastDayCell) {
            case '28':
                $cellDisplay = 28;
                break;

            case $monthLastDayCell <= 35:
                $cellDisplay = 35;
                break;

            default:
                $cellDisplay = 42;
                break;
        }
        return [
            'cellDisplay' => $cellDisplay    // Количество ячеек для отображения
            , 'monthFirstDayCell' => $monthFirstDay // Номер ячейки с которой начинается заполнение
            , 'monthLastDayCell' => $monthLastDayCell // Номер ячейки на которой начинается заполнение
        ];
    }

    /**
     * Классификация события
     * @param array $event
     * @param string $currentDate
     * @return string
     */
    private function classifyEvent($event, $currentDate)
    {
        if ($this->inRange($currentDate, $event['start_date'], $event['finish_date'])) {
            if (strtotime(substr($event['finish_date'],0, 10)) < strtotime(date("Y-m-d"))) {
                return 'closedBefore';
                // unset($event[$key]); //???? дублировать даже оконченные?
            }
            else if (substr($event['start_date'],0, 10) == $currentDate) {
                return 'plannedToday';
            }
            else if (substr($event['finish_date'],0, 10) == $currentDate) {
                return 'closedToday';
            }
            else {
                return 'plannedBefore';
            }
        }
    }

    /**
     * Форматируем события под календарь
     * @param array $events
     * @param string $type
     * @param string $year
     * @param string $month
     * @param array|null $formatedEvents
     * @return array
     */
    private function formatedEvents($events, $type, $year, $month, $formatedEvents = null)
    {
        $dateYear  = $year;
        $dateMonth = sprintf("%02d", $month);
        $dateObj   = DateTime::createFromFormat('!m', $dateMonth);
        $monthName = $dateObj->format('F');

        $blockInfo = $this->calculateCalendarBlock($dateYear, $dateMonth);

        $dayCount = 1;
        $formatedEvents[$month]['monthName'] = $monthName;

        for($cb=1;$cb<=$blockInfo['cellDisplay'];$cb++) {
            if( $cb >= $blockInfo['monthFirstDayCell'] && $cb <= ($blockInfo['monthLastDayCell']) ) {
                $currentDate = $dateYear.'-'.$dateMonth.'-'.sprintf("%02d", $dayCount);
                $plannedTodayCount = 0;
                $closedTodayCount = 0;
                $closedBeforeCount = 0;
                $plannedBeforeCount = 0;

                $dayHasEvent = false;
                foreach ($events as $key => &$event) {
                    if ($eventClass = $this->classifyEvent($event, $currentDate)) {
                        $dayHasEvent = true;
                        $formatedEvents[$month]['cell'][$cb]['events'][$eventClass][] = $event;
                        $count = $eventClass . 'Count';
                        ${$count}++;
                        if ($eventClass == 'closedToday') {
                            unset($events[$key]);
                        }
                    }
                }

                if ($dayHasEvent) {
                    $formatedEvents[$month]['cell'][$cb]['type'] = 'eventCell';
                } else {
                    $formatedEvents[$month]['cell'][$cb]['type'] = 'noEventCell';
                }

                $formatedEvents[$month]['cell'][$cb]['day'] = $dayCount;
                $formatedEvents[$month]['cell'][$cb]['date'] = $currentDate;
                $formatedEvents[$month]['cell'][$cb]['closedTodayCount'] = $closedTodayCount;
                $formatedEvents[$month]['cell'][$cb]['plannedTodayCount'] = $plannedTodayCount;
                $formatedEvents[$month]['cell'][$cb]['plannedBeforeCount'] = $plannedBeforeCount;
                $formatedEvents[$month]['cell'][$cb]['closedBeforeCount'] = $closedBeforeCount;

                $dayCount++;
            } else {
                $formatedEvents[$month]['cell'][$cb]['day'] = '';
                $formatedEvents[$month]['cell'][$cb]['type'] = 'emptyCell';
            }
        }

        if ( ($type == 'year' && $month == 12) || $type == 'month' ) {
            return $formatedEvents;
        } else {
            return $this->formatedEvents($events, $type, $year, ++$month, $formatedEvents);
        }
    }
}