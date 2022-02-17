<?php

namespace App\Console\Commands;

use App\Models\TourOrder;
use Illuminate\Console\Command;

class CheckTourPlace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tour:check-place';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        TourOrder::whereRaw('booking_time < (NOW() - INTERVAL 15 MINUTE)')
            ->where('status','in_process')
            ->update([
                'passenger_id' => NULL,
                'status' => 'free',
                'booking_time' => NULL,
                'first_name' => NULL,
                'phone' => NULL,
                'iin' => NULL,
                'price' => NULL,
            ]);
    }
}
