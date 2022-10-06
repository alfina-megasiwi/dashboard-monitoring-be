<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleSheetsServices;

class GoogleSheetsController extends Controller
{

    public $errorLog = array(
        "Nomor Rek tidak dikenal - Transaksi Valas",
        "General error - Branch 9995 not valid",
        "io.mib.arx.is.server.gateway.exceptions.EndpointClientExce",
        "io.mib.nio.common.exceptions.ReadResponseTimeoutException",
    );

    public function getData()
    {
        return (new GoogleSheetsServices())->readSheet();
    }

    public function sheetOperation()
    {
        $data = $this->getData();
        return response()->json($data);
    }

    public function isTheSame($text)
    {
        return in_array($text, $this->errorLog) ? true : false;
    }

    public function errorLog()
    {
        return response()->json($this->errorLog);
    }


    public function todayStat()
    {
        $data = $this->getData();

        $yesterday = date('d.m.Y', strtotime('-1 days'));
        $counter = count($data) - 1;

        while ($data[$counter][0] != $yesterday) {
            $counter--;
        }

        $today_data = $data[$counter];

        $total_data = $today_data[1];
        $total_error = ($today_data[4] == '-' || $today_data[4] == '') ? 0 : strtok($today_data[4], " ");


        $today_data_to_json = array(
            'totalData' => $total_data,
            'runtime' => $today_data[2],
            'dataRuntime' => number_format((float)$today_data[1] / $today_data[2], 2, '.', ''),
            'totalError' => $total_error,
            'succesRate' => $total_error == 0 ? 100 : number_format((($total_data - $total_error) / $total_data) * 100, 2, '.', '')
        );


        return json_encode($today_data_to_json);
    }

    public function weeklyData()
    {
        $data = $this->getData();
        $last_week_sunday = date('d.m.Y', strtotime('last week sunday'));
        $counter = count($data) - 1;

        $this_weekly_data = array();

        while ($data[$counter][0] != $last_week_sunday) {
            $weekly_data = array(
                'data' => $data[$counter][1],
                'date' => str_replace(".", "/", $data[$counter][0]),
                'time' => $data[$counter][2],
                'error' => ($data[$counter][4] == '-' || $data[$counter][4] == '') ? 0 : strtok($data[$counter][4], " ")
            );
            array_unshift($this_weekly_data, $weekly_data);
            $counter--;
        };
        return json_encode($this_weekly_data);
    }

    function errorDataFormat($data, $counter)
    {
        preg_match_all("/\((((?>[^()]+)|(?R))*)\)/", $data[$counter][4], $error);

        $temparr = array();
        $array_error = $error[1];

        foreach ($array_error as $value) {
            array_unshift($temparr, array(
                'name' => $value,
                'isNew' => $this->isTheSame($value) ? false : true
            ));
        }
        return array(
            'date' => strtok($data[$counter][0], "."),
            'errorName' => $temparr,
            'solvingError' => $data[$counter][5],
        );
    }

    public function weeklyError()
    {
        $data = $this->getData();
        $last_week_sunday = date('d.m.Y', strtotime('last week sunday'));
        $counter = count($data) - 1;
        $error_data = array();

        while ($data[$counter][0] != $last_week_sunday) {
            array_unshift($error_data, $this->errorDataFormat($data, $counter));
            $counter--;
        }
        return json_encode($error_data);
    }

    public function monthlyError()
    {
        $data = $this->getData();
        $last_day_of_previous_month = date('d.m.Y', strtotime('last day of previous month'));
        $counter = count($data) - 1;
        $error_data = array();

        while ($data[$counter][0] != $last_day_of_previous_month) {
            array_unshift($error_data, $this->errorDataFormat($data, $counter));
            $counter--;
        }
        return json_encode($error_data);
    }

    public function yearlyError()
    {
        $data = $this->getData();
        $year = date("Y");
        $counter = count($data) - 1;
        $error_data = array();

        while ($counter > -1 && str_contains($data[$counter][0], $year)) {
            array_unshift($error_data, $this->errorDataFormat($data, $counter));
            $counter--;
        }
        return json_encode($error_data);
    }

    public function monthlyData(Request $request)
    {
        $data = $this->getData();
        $last_date_month = date('d.m.Y', strtotime('last day of previous month'));
        $counter = count($data) - 1;

        $this_monthly_data = array();

        while ($data[$counter][0] != $last_date_month) {
            $monthly_data = array(
                'data' => $data[$counter][1],
                'date' => str_replace(".", "/", $data[$counter][0]),
                'time' => $data[$counter][2],
                'error' => ($data[$counter][4] == '-' || $data[$counter][4] == '') ? 0 : strtok($data[$counter][4], " ")
            );
            array_unshift($this_monthly_data, $monthly_data);
            $counter--;
        };

        $tmp_idx = 1;
        $array_monthly = array();
        $tmp_array = array();

        for ($i = 0; $i <= count($this_monthly_data) - 1; $i++) {
            if ($i < 7 * $tmp_idx) {
                array_unshift($tmp_array, (int)$this_monthly_data[$i]["data"]);
                if ($i == count($this_monthly_data) - 1) {
                    array_unshift($array_monthly, $tmp_array);
                }
            } else {
                $tmp_idx++;
                array_unshift($array_monthly, $tmp_array);
                $tmp_array = array();
                array_unshift($tmp_array, (int)$this_monthly_data[$i]["data"]);
                if ($i == count($this_monthly_data) - 1) {
                    array_unshift($array_monthly, $tmp_array);
                }
            }
        }

        return json_encode($array_monthly);
    }
}
