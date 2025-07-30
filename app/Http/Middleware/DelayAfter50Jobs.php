<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Cache;

class DelayAfter50Jobs
{
    public function handle($job, $next)
    {
        // $counter = Cache::increment('job_counter', 1);

        // // Proceed with job processing
        // $next($job);

        // // Delay after 50 jobs
        // if ($counter % 30 === 0) {
        //     sleep(30);
        // }


        $next($job);
         // Wait for 1 second after processing this job
         sleep(2);
    }
}
