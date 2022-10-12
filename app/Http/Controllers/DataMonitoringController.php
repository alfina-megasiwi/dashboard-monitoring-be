<?php

namespace App\Http\Controllers;

class DataMonitoringController extends Controller
{
    public $data = [];

    public function __construct()
    {
        $db = new DatabaseFirebase();
        $this->data = $db->index();
    }

    // Mengambil data untuk hari ini
    public function todaystat()
    {
        $yesterday = date('d-m-Y', strtotime('-1 days'));
        $today_data = $this->data[$yesterday] ?? [];
        return json_encode($today_data);
    }

    // Mengambil data berdasarkan range tanggal yang diinginkan
    public function getdata($date1, $date2)
    {
        $dates = $this->getBetweenDates($date1,  $date2);
        $date_arr = [];
        $data_arr = [];
        $time_arr = [];
        $error_arr = [];
        $runtime_arr = [];

        for ($item = 0; $item < count($dates); $item++) {
            array_push($date_arr, $this->data[$dates[$item]]['DATE']);
            array_push($data_arr, (int)$this->data[$dates[$item]]['DATA']);
            array_push($time_arr, (int)$this->data[$dates[$item]]['TIME']);
            array_push($error_arr, (int)$this->data[$dates[$item]]['ERROR']);
            array_push($runtime_arr, $this->data[$dates[$item]]['RUNTIME']);
        }

        return json_encode(array(
            'data' => $data_arr,
            'date' => $date_arr,
            'time' => $time_arr,
            'error' => $error_arr,
            'runtime' => $runtime_arr
        ));
    }

    // Mengambil data minggu ini
    public function thisweekdata()
    {
        $this_week_monday = date('d-m-Y', strtotime('this week monday'));
        $today = date('d-m-Y', strtotime('today -1 day'));
        return $this->getdata($this_week_monday, $today);
    }

    // Mengmabil data mingguan berdasarkan tanggal yang diberikan
    public function weeklydata($date)
    {
        $date = strtotime($date);
        $this_week_monday = date('d-m-Y', strtotime('this week monday', $date));
        $this_week_sunday = date('d-m-Y', strtotime('this week sunday', $date));
        return $this->getdatagetdata($this_week_monday, $this_week_sunday);
    }

    public function thismonthruntime()
    {
        $this_month_first_day = date('d-m-Y', strtotime('first day of this month'));
        $today = date('d-m-Y', strtotime('today -1 day'));
        $dates = $this->getBetweenDates($this_month_first_day, $today);
        $dates_chunk = array_chunk($dates, 7);

        $accumulation_arr = [];
        for ($item_arr = 0; $item_arr < count($dates_chunk); $item_arr++) {
            $temp_arr = [];
            for ($item = 0; $item < count($dates_chunk[$item_arr]); $item++) {
                array_push($temp_arr, $this->data[$dates_chunk[$item_arr][$item]]['RUNTIME']);
            }
            array_push($accumulation_arr, $temp_arr);
        }
        return json_encode($accumulation_arr);

    }

    // Fungsi yang digunakan untuk me-list seluruh tanggal pada tanggal yang diberikan
    function getBetweenDates($startDate, $endDate)
    {
        $rangArray = [];

        $startDate = strtotime($startDate);
        $endDate = strtotime($endDate);

        for (
            $currentDate = $startDate;
            $currentDate <= $endDate;
            $currentDate += (86400)
        ) {

            $date = date('d-m-Y', $currentDate);
            $rangArray[] = $date;
        }

        return $rangArray;
    }
}
