<?php

namespace App\Http\Controllers;

use App\Affiliate;
use App\Affiliator;
use App\AffiliatorWallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;

class AffiliateHistoryController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $affiliator = Affiliator::where("user_id", $user->id)->first();
        
        //dd($user);
       
        return view('frontend.user.customer.affiliate-history.index',compact('affiliator'));
    }


    public function store(Request $request)
    {
        $user = Auth::user();

        //dd($user);
        $affiliator = Affiliator::where("user_id", $user->id)->first();
   
        if($affiliator)
        {
            AffiliatorWallet::create([
                'affiliator_id'=> $affiliator->id,
                 'balance' => 0
             ]);
        }
        else{
            return redirect()->back()->with('error', 'You are not an affiliator User.');
        }
     


        return redirect()->back();
    }


    public function generateAffiliateCode()
    {
        do {
            $code = substr(md5(uniqid()), 0, 8);
        } while (Affiliate::where('affiliate_code', $code)->exists());

        return $code;
    }
}
