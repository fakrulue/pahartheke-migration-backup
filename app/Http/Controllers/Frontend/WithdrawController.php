<?php

namespace App\Http\Controllers\Frontend;

use App\Affiliate;
use App\Affiliator;
use App\Http\Controllers\Controller;
use App\User;
use App\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawController extends Controller
{
    public function list(Request $request)
    {
        $affiliator = Affiliator::where("user_id", Auth::id())->first();
        if(!$affiliator){
            flash(translate('Affiliator Not Found'))->error();
            return back();
        }

        $withdraws = Withdraw::where('affiliator_id', $affiliator->id)->orderBy('id', 'desc')->paginate(20);

        return view('frontend.user.customer.withdraw-history.index', compact('withdraws'));
    }


    public function store(Request $request)
    {
        $amount = intval($request->amount);

        if($amount < 500){
            flash(translate('Amount cannot be less than 500'))->error();
            return back();
        }

        $affiliator = Affiliator::where("user_id", Auth::id())->first();

        Withdraw::create([
            'affiliator_id' => $affiliator->id,
            'amount' => $amount,
        ]);

        $affilator = Affiliate::where("user_id", Auth::id())->first();

        $affilator->commission -= $amount;
        $affilator->save();

        flash(translate('Withdraw successfully done'))->success();
        return redirect()->back()->with('success', 'Withdraw Successful');
    }
}
