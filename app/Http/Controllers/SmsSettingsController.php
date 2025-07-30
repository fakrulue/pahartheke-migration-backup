<?php

namespace App\Http\Controllers;

use App\SmsSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class SmsSettingsController extends Controller
{
    public function index()
    {
        // Example: Fetch the first record's timestamps
        $record = DB::table('sms_settings')->first(); // Replace with your query

        // Format the timestamps
        $startDate = Carbon::parse($record->from_time)->format('Y-m-d H:i:s'); // Adjust the column name
        $endDate = Carbon::parse($record->to_time)->format('Y-m-d H:i:s'); // Adjust the column name

        return view('backend.sms.settings',compact('startDate','endDate','record'));
    }


    public function store(Request $request)
    {
        $daterange =  $request->daterange;

        list($fromTime, $toTime) = explode(' - ',$daterange);

        $settings = SmsSetting::find(1);
        
        if($settings){
            $settings->update([
                'from_time' => $fromTime,
                'to_time' => $toTime
            ]);
        }
        else{
            SmsSetting::create([
                'from_time' => $fromTime,
                'to_time' => $toTime
            ]);
        }
        

        return back();
    }


    public function status(){
        
        $settings = SmsSetting::find(1);
        if($settings->status === 0){
            $settings->update([
                'status' => 1
            ]);
        }
        else{
            $settings->update([
                'status' => 0
            ]);
        }

        return back();
    }
}
