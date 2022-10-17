<?php

namespace App\Http\Controllers;

class RuntimeController extends Controller
{
    public $DatabaseFirebase = null;

    public function __construct()
    {
        $this->DatabaseFirebase = new DatabaseFirebase();
    }

    public function getdata($date1, $date2, $type)
    {
        $dates = $this->DatabaseFirebase->getBetweenDates($date1, $date2);
        
        $dates_chunk = [];
        if ($type == "week" || $type == "month") {
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

        $accumulation_arr = [];
        for ($item_arr = 0; $item_arr < count($dates_chunk); $item_arr++) {
            $temp_arr = [];
            for ($item = 0; $item < count($dates_chunk[$item_arr]); $item++) {
                array_push($temp_arr, $this->DatabaseFirebase->data[$dates_chunk[$item_arr][$item]]['RUNTIME'] ?? 0);
            }
            if ($type == "month" || $type == "year") {
                $average = number_format((float)array_sum($temp_arr) / count($temp_arr), 2, '.', '');
                array_push($accumulation_arr,  (float)$average);
            } else {
                array_push($accumulation_arr, $temp_arr);
            }
        }
        return $accumulation_arr;
    }

    public function thisweekruntime()
    {
        $this_week_monday = date('d-m-Y', strtotime('this week monday'));
        $today = date('d-m-Y', strtotime('today -1 day'));
        return json_encode($this->getdata($this_week_monday, $today, "week"));
    }

    public function weeklyruntime($date)
    {
        $first_day = strtotime('monday this week');
        $last_day = strtotime('sunday this week');

        if ((strtotime($date) > $first_day) && (strtotime($date) < $last_day)) {
            return $this->thisweekruntime();
        } else {
            $date = strtotime($date);
            $this_week_monday = date('d-m-Y', strtotime('this week monday', $date));
            $this_week_sunday = date('d-m-Y', strtotime('this week sunday', $date));
            return $this->getdata($this_week_monday, $this_week_sunday, "week");
        }
    }

    public function thismonthruntime()
    {
        $this_month_first_day = date('d-m-Y', strtotime('first day of this month'));
        $today = date('d-m-Y', strtotime('today -1 day'));
        return json_encode($this->getdata($this_month_first_day, $today, "month"));
    }

    public function monthlyruntime($date)
    {
        $first_day = strtotime('first day of this month');
        $last_day = strtotime('last day of this month');
        if ((strtotime($date) > $first_day) && (strtotime($date) < $last_day)) {
            return $this->thismonthruntime();
        } else {
            $date = strtotime($date);
            $this_month_first_day = date('d-m-Y', strtotime('first day of this month', $date));
            $this_month_last_day = date('d-m-Y', strtotime('last day of this month', $date));
            return json_encode($this->getdata($this_month_first_day, $this_month_last_day, "month"));
        }
    }

    public function thisyearruntime()
    {
        $this_year_first_day = date('d-m-Y', strtotime('first day of january this year'));
        $today = date('d-m-Y', strtotime('today -1 day'));
        return json_encode($this->getdata($this_year_first_day, $today, "year"));
    }

    public function annualruntime($date)
    {
        $first_day = strtotime('first day of january this year');
        $last_day = strtotime('last day of december this year');
        if ((strtotime($date) > $first_day) && (strtotime($date) < $last_day)) {
            return $this->thisyearruntime();
        } else {
            $date = strtotime($date);
            $this_year_first_day = date('d-m-Y', strtotime('first day of january this year', $date));
            $this_year_last_day = date('d-m-Y', strtotime('llast day of december this year', $date));
            return json_encode($this->getdata($this_year_first_day, $this_year_last_day, "year"));
        }
    }
}
