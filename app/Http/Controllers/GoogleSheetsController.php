<?php

namespace App\Http\Controllers;

use Google\Service\Sheets;
use Illuminate\Http\Request;
use App\Services\GoogleSheetsServices;
use SebastianBergmann\LinesOfCode\Counter;

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

    public function sheetOperation(Request $request)
    {
        $data = $this->getData();
        return response()->json($data);
    }

    public function todayStat(Request $request)
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

    public function weeklyError(Request $request)
    {

        $data = $this->getData();
        $last_week_sunday = date('d.m.Y', strtotime('last week sunday'));
        $counter = count($data) - 1;

        $this_week_error = array();

        while ($data[$counter][0] != $last_week_sunday) {
            preg_match_all("/\((((?>[^()]+)|(?R))*)\)/", $data[$counter][4], $error);


            $temparr = array();
            $array_error = $error[1];

            foreach ($array_error as $value) {
                array_unshift($temparr, array(
                    'name' => $value,
                    'isNew' => $this->isTheSame($value) ? false : true
                ));
            }
            array_unshift($this_week_error, array(
                'date' => strtok($data[$counter][0], "."),
                'errorName' => $temparr,
                'solvingError' => $data[$counter][5],
            ));

            $counter--;
        }

        return json_encode($this_week_error);
    }

    public function isTheSame($text)
    {
        return in_array($text, $this->errorLog) ? true : false;
    }

    public function errorLog(Request $request)
    {
        return response()->json($this->errorLog);
    }
    public function weeklyData(Request $request)
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
    public function monthlyData(Request $request){
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

        $x = 1;
        $tmp_array = array();
        $average_monthly = array();
        for ($index = 0; $index <= count($this_monthly_data)-1; $index++) {
            if ($index <= 6 * $x){
                array_unshift($average_monthly, $this_monthly_data[$index]);  
            } elseif($index == count($this_monthly_data)){
                echo "hello there!";
            } 
            else{
                $x++;
                array_unshift($tmp_array, $average_monthly);
                $average_monthly = array();
                array_unshift($average_monthly, $this_monthly_data[$index]);
            }
        }
        return json_encode($tmp_array);

    }
}
