<?php

namespace App\Services;

use App\OtpConfiguration;
use Twilio\Rest\Client;

class SmsService
{
    protected $twilio;

    public function __construct()
    {
        $this->twilio = new Client(
            env('TWILIO_SID'),
            env('TWILIO_AUTH_TOKEN')
        );
    }

    public function sendSmsViaTwilio($to, $message)
    {
        return $this->twilio->messages->create($to, [
            'from' => env('VALID_TWILLO_NUMBER'),
            'body' => $message,
        ]);
    }

    public function sendBulkSms($to, $text)
    {
        if (OtpConfiguration::where('type', 'bulk_sms')->first()->value == 1) {
            $url = "http://bulksmsbd.net/api/smsapi";
            $api_key = "VBcQnYEQrG5Oa6fujqSC";
            $senderid = "Pahar Theke";
            $number = $to;
            $message = $text;

            $data = [
                "api_key" => $api_key,
                "senderid" => $senderid,
                "number" => $number,
                "message" => $message
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($ch);
            curl_close($ch);
            $p = explode("|", $response);
            $sendstatus = $p[0];
            return $sendstatus;


            // $url = "http://66.45.237.70/api.php";
            // $data= array(
            //     'username'=>env('BULK_SMS_ID'),
            //     'password'=>env('BULK_SMS_PASSWORD'),
            //     'number'=>"$to",
            //     'message'=>"$text"
            // );

            // $ch = curl_init(); // Initialize cURL
            // curl_setopt($ch, CURLOPT_URL,$url);
            // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // $smsresult = curl_exec($ch);
            // $p = explode("|",$smsresult);
            // $sendstatus = $p[0];
        }
    }
}
