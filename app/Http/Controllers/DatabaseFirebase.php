<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Factory;

class DatabaseFirebase extends Controller
{
    public $data = [];
    public $errorLog = [];

    public function __construct()
    {
        $firebase = (new Factory)
            ->withServiceAccount(__DIR__ . '/dashboard-monitoring-5f004-firebase-adminsdk-h6pxd-b2240004df.json')
            ->withDatabaseUri('https://dashboard-monitoring-5f004-default-rtdb.firebaseio.com');

        $database = $firebase->createDatabase();

        $data_monitoring = $database
            ->getReference('DashboardMonitoring');
        $error_log = $database
            ->getReference('ErrorLog');

        $this->data = $data_monitoring->getvalue();
        $this->errorLog = $error_log->getvalue();

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
