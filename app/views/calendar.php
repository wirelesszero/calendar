<?php
$i = 1;
foreach ($data as $key => $event) {

    if ($i == 1 || $i == 4 || $i == 7|| $i == 10 ) {
        echo '<div class="row">';
    }

    echo '<div class="col-md-4">
        <table class="table table-bordered">
            <thead>
                <th>M</th>
                <th>T</th>
                <th>W</th>
                <th>T</th>
                <th>F</th>
                <th>S</th>
                <th>S</th>
            </thead>
        <tbody>';

    echo "<div class='title'><h3>" . $event['monthName'] . "</h3></div>";

    $dayNumber = 0;
    $cell = '';
    foreach ($event['cell'] as $key => $day) {
        $dayNumber++;

        if ($day['type'] == 'emptyCell') {
            $cell .= "<td class='gray'>" . $day['day'] . '</td>';
        } else if ($day['type'] == 'eventCell') {
            $cellClass = ($day['date'] <= date("Y-m-d")) ? 'class="success"' : '';
            $cell .= "<td $cellClass>" . $day['day'] . "<div class='list-group'>";

            if (array_key_exists('closedBefore', $day["events"])) {
                $cell .= "<a tabindex='0' role='button' data-toggle='popover' data-placement='left' title='Closed:' data-events='" . json_encode($day["events"]["closedBefore"]) . "'><span class='badge progress-bar-warning'>".$day["closedBeforeCount"]."</span></a>";
            }

            if (array_key_exists('plannedToday', $day["events"])) {
                $cell .= "<a tabindex='0' role='button' data-toggle='popover' data-placement='left' title='Planned " . $day['date'] . "' data-events='" . json_encode($day["events"]["plannedToday"]) . "'><span class='badge progress-bar-info'>".$day["plannedTodayCount"]."</span></a>";
            }

            if (array_key_exists('plannedBefore', $day["events"])) {
                $cell .= "<a tabindex='0' role='button' data-toggle='popover' data-placement='bottom' title='Active events:' data-events='" . json_encode($day["events"]["plannedBefore"]) . "'><span class='badge progress-bar-success'>".$day["plannedBeforeCount"]."</span></a>";
            }

            if (array_key_exists('closedToday', $day["events"])) {
                $cell .= "<a tabindex='0' role='button' data-toggle='popover' data-placement='right' title='Closed " . $day['date'] . "' data-events='" . json_encode($day["events"]["closedToday"]) . "'><span class='badge progress-bar-danger'>".$day["closedTodayCount"]."</span></a>";
            }

            $cell .= "</td></div>";
        } else {
            $cellClass = ($day['date'] <= date("Y-m-d")) ? 'class="success"' : '';
            $cell .= "<td $cellClass>" . $day['day'] . '</td>';
        }

        if (($dayNumber % 7) == 0) {
            echo "<tr>";
            echo $cell;
            echo "</tr>";
            $cell = '';
        }
    }

    echo "</tbody>";
    echo "</table></div>";
    if ($i == 3 || $i == 6 || $i == 9 || $i == 12) {
        echo '</div>';
    }
    $i++;
}