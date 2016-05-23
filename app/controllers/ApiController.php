<?php
namespace App\Controllers;

use App\Models\Event;

class ApiController {

    public function events()
    {
        $month = isset($_REQUEST['month']) ? $_REQUEST['month'] : null;
        $year = isset($_REQUEST['year']) ? $_REQUEST['year'] : null;

        $event = new Event();
        $events = $event->eventFilter($year, $month);
        echo json_encode(['data' => $events]);
        exit();
    }
}