<?php

namespace App\Http\Controllers;

use App\Affiliate;
use App\AffiliateSetting;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Affiliator;
use App\AffiliatorProduct;
use App\Services\SmsService;
use App\User;
use Illuminate\Support\Facades\Hash;

class NewAffiliateController extends Controller
{

    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function index()
    {
        $products = Product::all();
        $AfProducts = AffiliateSetting::all();
        return view('backend.affiliates.settings.index', compact('products', 'AfProducts'));
    }

    public function affiliators()
    {
        $affiliators = Affiliator::orderBy('id', 'desc')->get();
        //dd($affiliators);
        return view('backend.affiliates.affiliators', compact('affiliators'));
    }

    public function approve($id)
    {




        $affiliator = Affiliator::find($id);



        $radPass = rand(100000, 999999);


        //dd($affiliator  );

        $user = User::create([
            'name' => $affiliator->full_name,
            'email' => $affiliator->email,
            'phone' => $affiliator->phone,
            'address' => $affiliator->address_street1,
            'password' => Hash::make($radPass),
            'user_type' => 'staff',
            'email_verified_at' => date('Y-m-d H:i:s')
        ]);


        if ($user) {
            $this->smsService->sendBulkSms($user->phone, "Your account has been approved.Your email is $affiliator->email.Your password is: $radPass");
        }

        $affiliator->status = 'active';
        $affiliator->user_id = $user->id;
        $affiliator->affiliator_code = $this->generateAffiliateCode();
        $affiliator->save();



        return redirect()->back()->with('success', 'Affiliator approved successfully!');
    }

    public function generateAffiliateCode()
    {
        do {
            $code = substr(md5(uniqid()), 0, 8);
        } while (Affiliate::where('affiliate_code', $code)->exists());

        return $code;
    }

    public function reject($id)
    {
        $affiliator = Affiliator::find($id);
        $affiliator->status = 'rejected';
        $affiliator->save();
        return redirect()->back()->with('success', 'Affiliator blocked successfully!');
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $exists = AffiliateSetting::where('product_id', $data['product_id'])->first();

        if ($exists) {
            return redirect()->back()->with('error', 'Data already exists!');
        } else {
            AffiliateSetting::create($data);
        }

        return redirect()->back();
    }


    public function delete($id)
    {
        AffiliateSetting::find($id)->delete();
        return redirect()->back()->with('success', 'Data deleted successfully!');
    }


    // public function affiliators()
    // {
    //     // $Affiliators = Affiliator::all();
    //     return view('backend.affiliates.affiliators');
    // }


    public function view($id)
    {
        $affiliator = Affiliator::with('socialLinks')->find($id);
        //dd($affiliator);
        return view('backend.affiliates.view', compact('affiliator'));
    }


    public function assignProducts($id)
    {
        $affiliator = Affiliator::find($id);
        //dd($affiliator->products()->get());


        $simpleProducts = Product::where('variant_product', 0)->get();
        $varientProducts = Product::where('variant_product', '>', 0)->get();


        $assignedProducts = AffiliatorProduct::where('affiliator_id', $id)
            ->with('product') // assuming you have a relation
            ->get();

        return  view('backend.affiliates.affiliator-assign-products', compact('assignedProducts', 'affiliator', 'simpleProducts', 'varientProducts'));
    }


    public function assignProductsStore(Request $request, $id)
    {
        $data = $request->all();
        //dd($data);


        $affiliator = Affiliator::find($id);


        if ($request->type == 'variant') {
            $affiliatorId = $affiliator->id;

            $productId   = $request->var_product_id;
            $names       = $request->name;
            $commissions = $request->commission;
            $discounts   = $request->discount;

            foreach ($names as $index => $variantName) {

                $commission = $commissions[$index] ?? 0;
                $discount = $discounts[$index] ?? 0;

                // Skip empty entries (optional)
                if ($variantName === null || $variantName === '') {
                    continue;
                }

                AffiliatorProduct::create([
                    'affiliator_id' => $affiliatorId,
                    'product_id'    => $productId,
                    'variant_name'  => $variantName,
                    'has_variant'   => true,
                    'commission'    => $commission ?? 0,
                    'discount'      => $discount ?? 0,
                    'assigned_by'   => auth()->id(),
                ]);
            }
        }
        if ($request->type == 'simple') {
            $affiliatorId = $affiliator->id;

            $productIds  = $request->product_id;
            $commissions = $request->commission;
            $discounts   = $request->discount;

            foreach ($productIds as $index => $productId) {
                AffiliatorProduct::create([
                    'affiliator_id' => $affiliatorId,
                    'product_id'    => $productId,
                    'variant_name'  => null,
                    'has_variant'   => false,
                    'commission'    => $commissions[$index] ?? 0,
                    'discount'      => $discounts[$index] ?? 0,
                    'assigned_by'   => auth()->id(), // Optional
                ]);
            }
        }



        return redirect()->back()->with('success', 'Products assigned successfully!');
    }




    public function orders($id)
    {
        $affiliator = Affiliator::find($id);
        $orders = $affiliator->affiliateOrders()->get();
        //dd($orders);
        return view('backend.affiliates.orders', compact('orders', 'affiliator'));
    }
}
