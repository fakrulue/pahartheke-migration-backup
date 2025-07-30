<?php

namespace App\Http\Controllers\Backend;

use App\Affiliator;
use App\Http\Controllers\Controller;
use App\Withdraw;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function list()
    {
        $withdraws = Withdraw::with('affiliator')->orderByDesc('id')->get();
        return view('backend.affiliates.withdraw', compact('withdraws'));
    }

    public function status(Request $request)
    {
        $withdraw = Withdraw::where('id', $request->withdraw_id)->first();

        $withdraw->status = $request->status;
        $withdraw->save();

        flash('The withdraw has been approved.')->success();
        return redirect()->back();
    }
}
