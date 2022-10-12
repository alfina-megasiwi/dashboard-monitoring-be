<?php

namespace App\Http\Controllers;

use Kreait\Firebase\Factory;

class DatabaseFirebase extends Controller
{
    //
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


}
