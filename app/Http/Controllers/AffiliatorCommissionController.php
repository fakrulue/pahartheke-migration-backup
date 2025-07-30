<?php

namespace App\Http\Controllers;

use App\AffiliatorOrder;
use App\WalletTransaction;
use Illuminate\Http\Request;

class AffiliatorCommissionController extends Controller
{
    public function index()
    {
        $affOrders = AffiliatorOrder::where('status', 'pending')->orderBy("id", "desc")->get();
        //dd($affOrders);
        return view('backend.affiliates.affiliator-commissions.index', compact('affOrders'));
    }



    public function update($id)
    {

        $afOrder = AffiliatorOrder::find($id);
        if (!$afOrder) {
            return redirect()->back()->with('error', 'Order not found');
        }

        if($afOrder->order->payment_status == 'unpaid') {
            return redirect()->back()->with('error', 'Order is unpaid');
        }

        if($afOrder->order->orderDetails[0]->delivery_status != 'delivered') {
            return redirect()->back()->with('error', 'Order is not delivered');
        }

        if($afOrder->status == 'paid') {
            return redirect()->back()->with('error', 'Order is already paid');
        }


        


        $affiliator = $afOrder->affiliator;
        if (!$affiliator) {
            return redirect()->back()->with('error', 'Affiliator not found');
        }

        $wallet = $affiliator->wallet;
        if (!$wallet) {
            return redirect()->back()->with('error', 'Wallet not found');
        }

        $wallet->balance += $afOrder->commission_amount;
        $wallet->save();



        $afOrder->status = 'paid';



        if ($afOrder->save()) {
            // Optional: log transaction
            WalletTransaction::create([
                'affiliator_wallet_id' => $affiliator->wallet->id,
                'amount' => $afOrder->commission_amount,
                'type' => 'credit',
                'description' => 'Commission for Order #' .$afOrder->order->code,
            ]);
        }
        return redirect()->back()->with('success', 'Status updated successfully');




        // $this->validate($request, [
        //     'status' => 'required|in:pending,approved,rejected',
        // ]);
        // $aff->status = $request->status;
        // $aff->save();
        // return redirect()->back()->with('success', 'Status updated successfully');
    }
}
