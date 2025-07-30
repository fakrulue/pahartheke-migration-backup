<?php

namespace App\Console\Commands;

use App\Jobs\SendBulkSmsJob;
use Illuminate\Console\Command;

class QueueSms extends Command
{
    protected $signature = 'sms:queue';
    protected $description = 'Queue SMS sending jobs';

    public function handle()
    {
        SendBulkSmsJob::dispatch("01683813854", 'This is a test message');
        $this->info('All SMS jobs have been queued.');
    }
}
