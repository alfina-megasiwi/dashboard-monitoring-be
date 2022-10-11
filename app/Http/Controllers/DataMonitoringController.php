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
        return json_encode($this->data[$yesterday]);
    }

    // Mengambil data berdasarkan range tanggal yang diinginkan
    public function getdata($date1, $date2)
    {
        $dates = $this->getBetweenDates($date1,  $date2);
        $date_arr = array();
        $data_arr = array();
        $time_arr = array();
        $error_arr = array();

        for ($x = 0; $x < count($dates); $x++) {
            array_push($date_arr, $this->data[$dates[$x]]['DATE']);
            array_push($data_arr, (int)$this->data[$dates[$x]]['DATA']);
            array_push($time_arr, (int)$this->data[$dates[$x]]['TIME']);
            array_push($error_arr, (int)$this->data[$dates[$x]]['ERROR']);
        }

        return json_encode(array(
            'data' => $data_arr,
            'date' => $date_arr,
            'time' => $time_arr,
            'error' => $error_arr
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
        return $this->getdata($this_week_monday, $this_week_sunday);
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
