<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ErrorController extends Controller
{
    public $data = [];

    public function __construct(){
        $db = new DatabaseFirebase();
        $this->data = $db->index();
    }

}
