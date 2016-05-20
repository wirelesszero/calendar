<?php
namespace App\Controllers;

use App\Models\Event;

class IndexController extends Controller {

    public function index()
    {
        $month = isset($_REQUEST['month']) ? $_REQUEST['month'] : null;
        $year = isset($_REQUEST['year']) ? $_REQUEST['year'] : null;
        $event = new Event();
        $events = $event->calendar($year, $month);
        $this->view('index.php', $events);
    }

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
