<?php

namespace App\Http\Controllers;

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
        $check_hari = date('d-m-Y', strtotime('today'));
        $check = date('l', strtotime($check_hari));

        if($check == "Monday"){
            $this_week_monday = date('d-m-Y', strtotime('last week monday'));
            $today = date('d-m-Y', strtotime('today -1 day'));
        }else{
            $this_week_monday = date('d-m-Y', strtotime('this week monday'));
            $today = date('d-m-Y', strtotime('today -1 day'));
        }
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
}
