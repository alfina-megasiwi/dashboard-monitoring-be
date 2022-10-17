<?php

namespace App\Http\Controllers;

class ErrorController extends Controller
{
    public $DatabaseFirebase = null;

    public function __construct()
    {
        $this->DatabaseFirebase = new DatabaseFirebase();
    }

    public function getErrorLog()
    {
        return json_encode($this->DatabaseFirebase->errorLog);
    }

    public function getweek()
    {
        $this_week_first_day = date('d-m-Y', strtotime('this week monday'));
        $today = date('d-m-Y', strtotime('today -1 day'));

        $dates = $this->DatabaseFirebase->getBetweenDates($this_week_first_day, $today);
        $arr_temp = [];
        for ($idx = 0; $idx < count($dates); $idx++) {
            array_push($arr_temp, substr($dates[$idx], 0, 2));
        }
        return json_encode($arr_temp);
    }

    public function getmonth()
    {
        $this_month_first_day = date('d-m-Y', strtotime('first day of this month'));
        $today = date('d-m-Y', strtotime('today -1 day'));
        $dates = $this->DatabaseFirebase->getBetweenDates($this_month_first_day, $today);
        $dates_chunk = array_chunk($dates, 7);
        $arr_temp = [];
        for ($idx = 0; $idx < count($dates_chunk); $idx++) {
            array_push($arr_temp, "W" . $idx + 1);
        }
        return json_encode($arr_temp);
    }

    public function getyear()
    {
        $array = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $this_year_first_day = date('d-m-Y', strtotime('first day of january this year'));
        $today = date('d-m-Y', strtotime('today -1 day'));
        $dates = $this->DatabaseFirebase->getBetweenDates($this_year_first_day, $today);
        $years = array();
        $months = array();
        foreach ($dates as $d) {
            list($day, $month, $year) = explode("-", $d);
            $years[$year][] = $d;
            $months[$year . "-" . $month][] = $d;
        }
        $dates_chunk = array_values($months);
        return json_encode(array_slice($array, 0, count($dates_chunk)));
    }

    public function thisweekerror()
    {
        $check_hari = date('d-m-Y', strtotime('today'));
        $check = date('l', strtotime($check_hari));

        if ($check == "Monday") {
            $this_week_monday = date('d-m-Y', strtotime('last week monday'));
            $today = date('d-m-Y', strtotime('today -1 day'));
        } else {
            $this_week_monday = date('d-m-Y', strtotime('this week monday'));
            $today = date('d-m-Y', strtotime('today -1 day'));
        }

        $dates = $this->DatabaseFirebase->getBetweenDates($this_week_monday, $today);

        $data = $this->DatabaseFirebase->errorLog;

        $array_keys = array_keys($data);
        $arr = [];
        for ($key = 0; $key < count($array_keys); $key++) {
            $arr[$array_keys[$key]] = array_fill(0, count($dates) + 1, []);
        }
        for ($key = 0; $key < count($array_keys); $key++) {
            $date_error_occur = array_keys(($data[$array_keys[$key]])["DATE"]);
            for ($error = 0; $error < count($date_error_occur); $error++) {
                if (in_array($date_error_occur[$error], $dates)) {
                    $arr[$array_keys[$key]][array_search($date_error_occur[$error], $dates)] = "✔";
                    array_push($arr[$array_keys[$key]][count($dates)], [$date_error_occur[$error], (((($data[$array_keys[$key]])["DATE"])[$date_error_occur[$error]])["SOLVE"]), (((($data[$array_keys[$key]])["DATE"])[$date_error_occur[$error]])["SUM"])]);
                }
            }
        }
        return json_encode($arr);
    }

    public function getdata($date1, $date2, $type)
    {
        $dates = $this->DatabaseFirebase->getBetweenDates($date1, $date2);

        $dates_chunk = [];
        if ($type == "month") {
            $dates_chunk = array_chunk($dates, 7);
        } else {
            $years = array();
            $months = array();
            foreach ($dates as $d) {
                list($day, $month, $year) = explode("-", $d);
                $years[$year][] = $d;
                $months[$year . "-" . $month][] = $d;
            }
            $dates_chunk = array_values($months);
        }

        $data = $this->DatabaseFirebase->errorLog;
        $array_keys = array_keys($data);
        $arr = [];

        for ($key = 0; $key < count($array_keys); $key++) {
            $arr[$array_keys[$key]] = array_fill(0, count($dates_chunk) + 1, []);
        }

        for ($key = 0; $key < count($array_keys); $key++) {
            $date_error_occur = array_keys(($data[$array_keys[$key]])["DATE"]);
            for ($error = 0; $error < count($date_error_occur); $error++) {
                for ($chunk = 0; $chunk < count($dates_chunk); $chunk++) {
                    if (in_array($date_error_occur[$error], $dates_chunk[$chunk])) {
                        $arr[$array_keys[$key]][$chunk] = "✔";
                        array_push($arr[$array_keys[$key]][count($dates_chunk)], [$date_error_occur[$error], (((($data[$array_keys[$key]])["DATE"])[$date_error_occur[$error]])["SOLVE"]), (((($data[$array_keys[$key]])["DATE"])[$date_error_occur[$error]])["SUM"])]);
                    }
                }
            }
        }

        return $arr;
    }

    public function thismontherror()
    {
        $this_month_first_day = date('d-m-Y', strtotime('first day of this month'));
        $today = date('d-m-Y', strtotime('today -1 day'));
        return $this->getdata($this_month_first_day, $today, "month");
    }

    public function monthlyerror($date)
    {
        $first_day = strtotime('first day of this month');
        $last_day = strtotime('last day of this month');
        if ((strtotime($date) > $first_day) && (strtotime($date) < $last_day)) {
            return $this->thismontherror();
        } else {
            $date = strtotime($date);
            $this_month_first_day = date('d-m-Y', strtotime('first day of this month', $date));
            $this_month_last_day = date('d-m-Y', strtotime('last day of this month', $date));
            return json_encode($this->getdata($this_month_first_day, $this_month_last_day, "month"));
        }
    }

    public function thisyearerror()
    {
        $this_year_first_day = date('d-m-Y', strtotime('first day of january this year'));
        $today = date('d-m-Y', strtotime('today -1 day'));
        return $this->getdata($this_year_first_day, $today, "year");
    }

    public function annualerror($date)
    {
        $first_day = strtotime('first day of january this year');
        $last_day = strtotime('last day of december this year');
        if ((strtotime($date) > $first_day) && (strtotime($date) < $last_day)) {
            return $this->thisyearerror();
        } else {
            $date = strtotime($date);
            $this_year_first_day = date('d-m-Y', strtotime('first day of january this year', $date));
            $this_year_last_day = date('d-m-Y', strtotime('llast day of december this year', $date));
            return json_encode($this->getdata($this_year_first_day, $this_year_last_day, "year"));
        }
    }
}
