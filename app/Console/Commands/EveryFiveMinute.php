<?php

namespace App\Console\Commands;

use App\Models\CarTravelOrder;
use App\Packages\Firebase;
use Illuminate\Console\Command;

class EveryFiveMinute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:push';

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
        $ids = CarTravelOrder::join('car_travel', 'car_travel_orders.car_travel_id',
            'car_travel.id')
            ->join('users', 'users.id', 'car_travel_orders.passenger_id')
            ->whereRaw('car_travel.destination_time > (NOW() - INTERVAL 20  SECOND)')
            ->whereRaw('car_travel.destination_time < (NOW() + INTERVAL 20 SECOND)')
            ->select('car_travel.car_id')->first();

        Firebase::sendMultiple(CarTravelOrder::join('car_travel', 'car_travel_orders.car_travel_id',
            'car_travel.id')
            ->join('users', 'users.id', 'car_travel_orders.passenger_id')
            ->whereRaw('car_travel.destination_time > (NOW() - INTERVAL 20  SECOND)')
            ->whereRaw('car_travel.destination_time < (NOW() + INTERVAL 20 SECOND)')
            ->select('users.device_token')
            ->pluck('users.device_token')
            ->toArray(), [
            'title' => 'Saparline',
            'body' => "Пожалуйста,оцените поездку",
            'type' => 'feedback',
            'carId' => $ids
        ]);


//             $user =  CarTravelOrder::join('car_travel','car_travel_orders.car_travel_id',
//            'car_travel.id')
//            ->join('users','users.id','car_travel_orders.passenger_id')
//            ->whereRaw('car_travel.destination_time < (NOW() - INTERVAL 15 MINUTE)')
//            ->select('users.device_token')
//            ->pluck('users.device_token')
//            ->toArray();
//
//        DB::table('test')->insert([
//            ['id' => rand(1111,9999)],
//        ]);


    }
}
