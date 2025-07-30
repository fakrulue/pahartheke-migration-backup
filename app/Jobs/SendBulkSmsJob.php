<?php

namespace App\Jobs;

use App\Http\Middleware\DelayAfter50Jobs;
use App\Services\SmsService;
use App\SmsJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\OtpConfiguration;
class SendBulkSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // Set timeout to 600 seconds (10 minutes)
    public $tries = 3; // Retry the job 3 times

    public $phone;
    public $message;

    public function __construct($phone, $message)
    {
        $this->phone = $phone;
        $this->message = $message;
    }

    public function middleware()
    {
        return [new DelayAfter50Jobs];
    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SmsService $smsService)
    {

        $smsJob = SmsJob::create([
            'phone_number' => $this->phone,
            'message' => $this->message,
            'status' => 'pending',
        ]);

        try {

            if (OtpConfiguration::where('type', 'bulk_sms')->first()->value == 1) {
                $res = $smsService->sendBulkSms($this->phone, $this->message);

                
                $resArr = json_decode($res,true);
                Log::info($resArr);

                if(is_array($resArr)){
                    if($resArr['response_code'] === 202){
                        $smsJob->update([
                            'status' => 'sent',
                            'response' => $resArr['success_message'],
                        ]);
                    }
                    else{
                        $smsJob->update([
                            'status' => 'failed',
                            'response' => $resArr['error_message'],
                        ]);
                    }
                }
                
            }
            else{
                Log::info("Sms Ses Hoye Gese");
            }
        } catch (\Exception $e) {
            // Update the status to 'failed' and store the error message
            
            $smsJob->update([
                'status' => 'failed',
                'response' => $e->getMessage(),
            ]);
            Log::info($e);
        }
    }
}
