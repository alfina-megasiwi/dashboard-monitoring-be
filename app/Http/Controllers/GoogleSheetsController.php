<?php

namespace App\Http\Controllers;

use Google\Service\Sheets;
use Illuminate\Http\Request;
use App\Services\GoogleSheetsServices;

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
}