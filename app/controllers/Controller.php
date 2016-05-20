<?php
namespace App\Controllers;

class Controller
{
    protected function view($fileName, $data=null) {
        if(is_array($data)) {
            extract($data);
        }
        require_once('../app/views/' . $fileName);
    }
}
