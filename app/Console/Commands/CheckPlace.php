<?php

namespace App\Console\Commands;

use App\Models\CarTravelOrder;
use App\Models\CarTravelPlace;
use App\Models\CarTravelPlaceOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckPlace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:place';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check travel places';

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
       CarTravelPlace::whereRaw('booking_time < (NOW() - INTERVAL 30 MINUTE)')
           ->where('status','in_process')
           ->update([
                'passenger_id' => NULL,
                'status' => 'free',
                'booking_time' => NULL,
                'car_travel_order_id' => NULL
       ]);

       CarTravelPlaceOrder::whereRaw('booking_time < (NOW() - INTERVAL 30 MINUTE)')
           ->where('status','in_process')
           ->update([
                'passenger_id' => NULL,
                'status' => 'free',
                'booking_time' => NULL,
                'car_travel_order_id' => NULL
       ]);
       CarTravelOrder::whereRaw('booking_time < (NOW() - INTERVAL 30 MINUTE)')
           ->where('status','in_process')
           ->update([
                'passenger_id' => NULL,
                'status' => 'free',
                'booking_time' => NULL,
       ]);

    }
}
