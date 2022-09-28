<?php

namespace App\Http\Controllers;

use Google\Service\Sheets;
use Illuminate\Http\Request;
use App\Services\GoogleSheetsServices;
use SebastianBergmann\LinesOfCode\Counter;

class GoogleSheetsController extends Controller{

    public function getData(){
        $data = (new GoogleSheetsServices ())->readSheet();
        return $data;
    }

    public function sheetOperation(Request $request)
    {
        $data = $this->getData();
        return response()->json($data);
    }

    public function todayStat(Request $request)
    {
        $data = $this->getData();
        $today_data = $data[count($data)-1];

        $today_data_to_json = array(
            'totalData' => $today_data[2], 
            'runtime' => $today_data[3], 
            "dataRuntime" => number_format((float)$today_data[2]/$today_data[3], 2, '.', ''),
            "totalError" => ($today_data[5]=="-") ? 0 : $today_data[5]
        );

        return json_encode($today_data_to_json);
    }

    public function weeklyData(Request $request){
        $data = $this->getData();
        $last_week_sunday = date('d.m.Y', strtotime('last week sunday'));
        $counter = count($data) - 1;

        $this_weekly_data = array();

        while ($data[$counter][0] != $last_week_sunday){
            $weekly_data = array(
                'data' => $data[$counter][2],
                'date' => $data[$counter][0],
                'time' => $data[$counter][3],
                'error' => ($data[$counter][5] == '-' || $data[$counter][5] == '') ? 0 : strtok($data[$counter][5], " ")
            );
            array_unshift($this_weekly_data, $weekly_data);
            $counter--;
        };
        return json_encode($this_weekly_data);
    }
}

?>