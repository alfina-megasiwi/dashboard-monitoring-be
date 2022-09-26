<?php

namespace App\Http\Controllers;

use App\Models\TodayStat;
use Illuminate\Http\Request;

class TodayStatController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $todayStat = TodayStat::paginate(10);
        return response() -> json([
            $todayStat
        ]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $todayStat = TodayStat::create([
            'data' => $request -> data,
            'time' => $request -> time,
            'datatime' => $request -> datatime,
            'error' => $request -> error,
        ]);
        return response() -> json([
            $todayStat
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TodayStat  $todayStat
     * @return \Illuminate\Http\Response
     */
    public function show(TodayStat $todayStat)
    {
        return response() -> json([
            $todayStat
        ]);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TodayStat  $todayStat
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TodayStat $todayStat)
    {
        $todayStat->data = $request->data;
        $todayStat->time = $request->time;
        $todayStat->datatime = $request->datatime;
        $todayStat->error = $request->error;

        $todayStat->save();
        return response() -> json([
            $todayStat
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TodayStat  $todayStat
     * @return \Illuminate\Http\Response
     */
    public function destroy(TodayStat $todayStat)
    {
        $todayStat->delete();
        return response() -> json([
            'message' => 'today stat deleted'
        ], 204);
    }
}
