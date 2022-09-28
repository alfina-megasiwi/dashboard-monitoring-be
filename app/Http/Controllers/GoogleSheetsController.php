<?php

namespace App\Http\Controllers;

use Google\Service\Sheets;
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

    public function sheetOperation(Request $request)
    {
        $data = $this->getData();
        return response()->json($data);
    }

    public function todayStat(Request $request)
    {
        $data = $this->getData();
        $today_data = $data[count($data) - 1];

        $today_data_to_json = array(
            'totalData' => $today_data[1],
            'runtime' => $today_data[2],
            'dataRuntime' => number_format((float)$today_data[1] / $today_data[2], 2, '.', ''),
            'totalError' => ($today_data[4] == '-' || $today_data[4] == '') ? 0 : strtok($today_data[4], " ")
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

            foreach ($array_error as $value){
                array_unshift($temparr, array(
                    'name' => $value,
                    'isNew' => $this->isTheSame($value)? false : true
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
        return in_array($text, $this->errorLog)? true : false;
    }

    public function errorLog(Request $request)
    {
        return response()->json($this->errorLog);
    }
}
