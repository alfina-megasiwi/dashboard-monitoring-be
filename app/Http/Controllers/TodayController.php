<?php

namespace App\Http\Controllers;

use App\Models\Today;
use Illuminate\Http\Request;

class TodayController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $today = Today::all();
        return response()->json([
            $today
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
        $today = Today::create([
            'data' => $request->data,
            'time' => $request->time,
            'datatime' => $request->datatime,
            'error' => $request->error
        ]);

        return response()->json([
            $today
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Today  $today
     * @return \Illuminate\Http\Response
     */
    public function show(Today $today)
    {
        return response()->json([
            $today
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Today  $today
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Today $today)
    {
        $today->data = $request->data;
        $today->time = $request->time;
        $today->datatime = $request->datatime;
        $today->error = $request->error;
        $today->save();

        return response()->json([
            $today
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Today  $today
     * @return \Illuminate\Http\Response
     */
    public function destroy(Today $today)
    {
        $today->delete();
        return response()->json([
            'message' => "data deleted"
        ]);
    }
}
