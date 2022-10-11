<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Factory;

class DataMonitoringController extends Controller
{
    // Mengambil seluruh data dari firebase
    public function index()
    {
        $firebase = (new Factory)
            ->withServiceAccount(__DIR__ . '/dashboard-monitoring-5f004-firebase-adminsdk-h6pxd-b2240004df.json')
            ->withDatabaseUri('https://dashboard-monitoring-5f004-default-rtdb.firebaseio.com');

        $database = $firebase->createDatabase();

        $data_monitoring = $database
            ->getReference('DashboardMonitoring');

        return $data_monitoring->getvalue();
    }

    // Mengambil data untuk hari ini
    public function todaystat()
    {
        $data = $this->index();
        $yesterday = date('d-m-Y', strtotime('-1 days'));
        return json_encode($data[$yesterday]);
    }

    // Mengambil data berdasarkan range tanggal yang diinginkan
    public function getdata($date1, $date2)
    {
        $data = $this->index();
        $dates = $this->getBetweenDates($date1,  $date2);
        $data_arr = array();
        for ($x = 0; $x < count($dates); $x++) {
            array_push($data_arr, $data[$dates[$x]]);
        }

        return json_encode($data_arr);
    }

    // Mengambil data minggu ini
    public function thisweekdata(){
        $this_week_monday = date('d-m-Y', strtotime('this week monday'));
        $today = date('d-m-Y', strtotime('today -1 day'));
        return $this->getdata($this_week_monday, $today);
    }

    // Mengmabil data mingguan berdasarkan tanggal yang diberikan
    public function weeklydata($date){
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
