<?php

namespace App\Http\Controllers;

use App\SmsJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SmsHistoryController extends Controller
{
    public function index(Request $request)
    {
        
        $sort_search = null;
        $query = SmsJob::query(); // Start with a query builder instance.

        if ($request->has('search')) {
            $sort_search = $request->search;
            $query->where('phone_number', 'like', '%' . $sort_search . '%')
            ->orWhere('updated_at', 'like', '%' . $sort_search . '%')// Apply the search condition to the query.
            ->orWhere('message', 'like', '%' . $sort_search . '%'); // Apply the search condition to the query.
        }


        $todayPendingCount = SmsJob::where('status', 'pending')->whereDate('updated_at', Carbon::today())->count();
        $todaySentCount = SmsJob::where('status', 'sent')->whereDate('updated_at', Carbon::today())->count();
        $todayFailedCount = SmsJob::where('status', 'failed')->whereDate('updated_at', Carbon::today())->count();

        $histories = $query->orderBy('id','DESC')->paginate(15); // Use the query builder for pagination.
        $jobCount = DB::table('jobs')->count();
        //dd($jobCount);
        return view('backend.sms.history', compact('jobCount','histories', 'sort_search', 'todayFailedCount', 'todayPendingCount', 'todaySentCount'));
    }
}
