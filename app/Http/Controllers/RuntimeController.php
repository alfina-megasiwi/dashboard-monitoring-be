<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RuntimeController extends Controller
{
    public $DatabaseFirebase = null;

    public function __construct()
    {
        $this->DatabaseFirebase = new DatabaseFirebase();
    }

    public function thismonthruntime()
    {
        $this_month_first_day = date('d-m-Y', strtotime('first day of this month'));
        $today = date('d-m-Y', strtotime('today -1 day'));
        $dates = $this->DatabaseFirebase->getBetweenDates($this_month_first_day, $today);
        $dates_chunk = array_chunk($dates, 7);

        $accumulation_arr = [];
        for ($item_arr = 0; $item_arr < count($dates_chunk); $item_arr++) {
            $temp_arr = [];
            for ($item = 0; $item < count($dates_chunk[$item_arr]); $item++) {
                array_push($temp_arr, $this->DatabaseFirebase->data[$dates_chunk[$item_arr][$item]]['RUNTIME']);
            }
            $average = number_format((float)array_sum($temp_arr)/count($temp_arr), 2, '.', '');
            $accumulation_arr["Week" . $item_arr+1 ] = (float)$average ;

        }
        return json_encode($accumulation_arr);

    }
}
