<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Affiliator;
use App\AfiliatorSocialLink;
use App\Services\SmsService;
use AWS\CRT\Log;
use Illuminate\Support\Facades\DB;
use Iyzipay\Model\Status;

class AffiliatorController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }
    public function register()
    {
        return view('frontend.affiliation.register');
    }


    public function store(Request $request)
    {




        $data =  $request->all();



        $existingAffiliator = DB::table('affiliators')->where('email',$data['email'])->first();

        

        if ($existingAffiliator) {
            return response()->json(['error' => 'Email already exists.']);
        }




        //mama tasks for create affiliator 

        $affiliator = Affiliator::create([
            'full_name'            => $data['full_name'],
            'nid'                  => $data['nid'],
            'phone'                => $data['phone'],
            'email'                => $data['email'],
            'payment_method'       => $data['payment_method'] ?? 'mobile',
            'promotion_method'     => $data['promotion_method'] ?? null,

            // Address
            'address_street1'      => $data['address_street1'] ?? 'Dhaka',
            'address_street2'      => $data['address_street2'] ?? null,
            'address_city'         => $data['address_city'] ?? 'Dhaka',
            'address_state'        => $data['address_state'] ?? 'Dhaka',
            'address_postal_code'  => $data['address_postal_code'] ?? '1216',



            // Mobile banking
            'mobile_provider'      => $data['mobile_provider'] ?? null,
            'mobile_number'        => $data['mobile_number'] ?? null,

            //banking
            'bank_name'           => $data['bank_name'] ?? null,
            'account_number'      => $data['account_number'] ?? null,
            'branch_name'           => $data['branch_name'] ?? null,
            'account_name'        => $data['account_name'] ?? null,

            // Nominee
            'nominee_name'         => $data['nominee_name'] ?? null,
            'nominee_phone'        => $data['nominee_phone'] ?? null,
            'nominee_relation'     => $data['nominee_relation'] ?? null,

        ]);







       


// dd($affiliator);
        // Send SMS using bulksms 

        $this->smsService->sendBulkSms($data['phone'], 'Your registration is successful. Thank you for joining us!');


        $linkFields = array_filter($data, function ($value, $key) {
            return str_ends_with($key, '_link') && !empty($value);
        }, ARRAY_FILTER_USE_BOTH);

        foreach ($linkFields as $key => $value) {
            AfiliatorSocialLink::create([
                'affiliator_id' => $affiliator->id,
                'platform' => str_replace('_link', '', $key),
                'url' => $value
            ]);
        }








       




        if ($affiliator) {
            return response()->json(
                [
                    'success' => 'Affiliator created successfully.',
                    'status' => 201,
                ]
            );
        } else {
            return response()->json(
                [
                    'error' => 'Affiliator not created.',
                    'status' => 500,
                ]
            );
        }
    }
}
