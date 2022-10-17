<?php

namespace App\Http\Controllers;

use PhpParser\Node\Expr\Cast\Array_;

class DataMonitoringController extends Controller
{
    public $DatabaseFirebase = null;

    public function __construct()
    {
        $this->DatabaseFirebase = new DatabaseFirebase();
    }

    // Mengambil data untuk hari ini
    public function todaystat()
    {
        $yesterday = date('d-m-Y', strtotime('-1 days'));
        $today_data = $this->DatabaseFirebase->data[$yesterday] ?? [];
        return json_encode($today_data);
    }

    // Mengambil data berdasarkan range tanggal yang diinginkan
    public function getdata($date1, $date2)
    {
        $dates = $this->DatabaseFirebase->getBetweenDates($date1,  $date2);
        $date_arr = [];
        $data_arr = [];
        $time_arr = [];
        $error_arr = [];


        for ($item = 0; $item < count($dates); $item++) {
            array_push($date_arr, $this->DatabaseFirebase->data[$dates[$item]]['DATE'] ?? 0);
            array_push($data_arr, $this->DatabaseFirebase->data[$dates[$item]]['DATA'] ?? 0);
            array_push($time_arr, $this->DatabaseFirebase->data[$dates[$item]]['TIME'] ?? 0);
            array_push($error_arr, $this->DatabaseFirebase->data[$dates[$item]]['ERROR'] ?? 0);
        }

        return json_encode(array(
            'data' => $data_arr,
            'date' => $date_arr,
            'time' => $time_arr,
            'error' => $error_arr,
        ));
    }

    // Mengambil data minggu ini
    public function thisweekdata()
    {
        $this_week_monday = date('d-m-Y', strtotime('this week monday'));
        $today = date('d-m-Y', strtotime('today -1 day'));
        return $this->getdata($this_week_monday, $today);
    }

    // Mengambil data mingguan berdasarkan tanggal yang diberikan
    public function weeklydata($date)
    {
        $first_day = strtotime('monday this week');
        $last_day = strtotime('sunday this week');
        if ((strtotime($date) > $first_day) && (strtotime($date) < $last_day)) {
            return $this->thisweekdata();
        } else {
            $date = strtotime($date);
            $this_week_monday = date('d-m-Y', strtotime('this week monday', $date));
            $this_week_sunday = date('d-m-Y', strtotime('this week sunday', $date));
            return $this->getdata($this_week_monday, $this_week_sunday);
        }
    }

    public function getdata2($date1, $date2, $type){
        $dates =  $this->DatabaseFirebase->getBetweenDates($date1, $date2);
        $dates_chunk = [];
        if ($type == "week" || $type == "month"){
            $dates_chunk = array_chunk($dates, 7);
        } else {
            $year = array();
            $months =  array();
            foreach ($dates as $d){
                list($day, $month, $year) = explode("-", $d);
                $years[$year][] = $d;
                $months[$year . "-" . $month][] = $d;
            }
            $dates_chunk = array_values($months);
        }

        $accumulation_arr_data = [];
        $accumulation_arr_time = [];
        $accumulation_arr_error = [];
        $accumulation_arr = [];
        for ($item_arr = 0; $item_arr < count($dates_chunk); $item_arr++) {
            $temp_arr_data = [];
            $temp_arr_time = [];
            $temp_arr_error = [];
            for ($item = 0; $item < count($dates_chunk[$item_arr]); $item++) {
                array_push($temp_arr_data, $this->DatabaseFirebase->data[$dates_chunk[$item_arr][$item]]['DATA'] ?? 0);
                array_push($temp_arr_time, $this->DatabaseFirebase->data[$dates_chunk[$item_arr][$item]]['TIME'] ?? 0);
                array_push($temp_arr_error, $this->DatabaseFirebase->data[$dates_chunk[$item_arr][$item]]['ERROR'] ?? 0);
            }
            if ($type == "month" || $type == "year") {
                $average_data = number_format((float)array_sum($temp_arr_data) / count($temp_arr_data), 2, '.', '');
                $average_time = number_format((float)array_sum($temp_arr_time) / count($temp_arr_time), 2, '.', '');
                $average_error = number_format((float)array_sum($temp_arr_error) / count($temp_arr_error), 2, '.', '');
                array_push($accumulation_arr_data,  (float)$average_data);
                array_push($accumulation_arr_time,  (float)$average_time);
                array_push($accumulation_arr_error,  (float)$average_error);
            } else {
                array_push($accumulation_arr_data, $temp_arr_data);
                array_push($accumulation_arr_time, $temp_arr_time);
                array_push($accumulation_arr_error, $temp_arr_error);
            }
        }
        array_push($accumulation_arr, $accumulation_arr_data);
        array_push($accumulation_arr, $accumulation_arr_time);
        array_push($accumulation_arr, $accumulation_arr_error);
        return  $accumulation_arr;
    }

    public function thismonthdata(){
        $this_month_first_day = date('d-m-Y', strtotime('first day of this month'));
        $today = date('d-m-Y', strtotime('today -1 day')); 
        
        return json_encode($this->getdata2($this_month_first_day, $today, "month"));
    }

    public function monthlydata($date)
    {
        $first_day = strtotime('first day of this month');
        $last_day = strtotime('last day of this month');
        if ((strtotime($date) > $first_day) && (strtotime($date) < $last_day)) {
            return $this->thismonthdata();
        } else {
            $date = strtotime($date);
            $this_month_first_day = date('d-m-Y', strtotime('first day of this month', $date));
            $this_month_last_day = date('d-m-Y', strtotime('last day of this month', $date));
            return json_encode($this->getdata2($this_month_first_day, $this_month_last_day, "month"));
        }
    }
}
