@extends('admin.layouts.app')

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Новые билеты</h2>
                </div>


                <div class="body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>№ Места</th>
                                <th>Цена</th>
                                <th>От куда</th>
                                <th>Куда</th>
                                <th>Пассажир (имя)</th>
                                <th>Пассажир (Номер)</th>
                                <th>время отправления</th>
                                <th>Водитель (Имя)</th>
                                <th>Водитель (Номер)</th>
                                <th>Нос номер авто</th>
                                <th>Время Билета</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($travels as $travel)
                                <tr>
                                    <td>
                                        @if($travel->car_type_id == 2)
                                           <b>
                                               [
                                               @foreach(\App\Models\CarTravelPlaceOrder::where('car_travel_order_id',$travel->id)->select('number')->get() as $k=>$num)
                                                   @if($k != 0),
                                                   @endif
                                                   @if($num->number > 16 and $num->number < 33)
                                                       <b style="color: red">{{(int)$num->number - 16}} верх</b>
                                                   @elseif ($num->number < 17)
                                                       <b style="color: blue">{{(int)$num->number }} вниз</b>
                                                   @else
                                                       0 верх
                                                   @endif
                                               @endforeach
                                               ]
                                           </b>
                                        @else
                                            <b>{{\App\Models\CarTravelPlace::where('car_travel_order_id',$travel->id)->select('number')->pluck('number')}}</b>
                                        @endif

                                    </td>
                                    <td>{{\App\Models\CarTravelPlace::where('car_travel_order_id',$travel->id)->sum('price')}}</td>
                                    <td>{{$travel->from_city}}</td>
                                    <td>{{$travel->to_city}}</td>
                                    <td>{{$travel->passenger_name}}</td>
                                    <td>{{$travel->passenger_phone}}</td>
                                    <td>{{$travel->departure_time}}</td>
                                    <td>{{$travel->driver_name}}</td>
                                    <td>{{$travel->driver_phone}}</td>
                                    <td>{{$travel->car_state_number}}</td>
                                    <td>{{$travel->booking_time}}</td>


                                    <td><a class="btn btn-warning" href="{{route('admin.car_travel.order.Detail',$travel->id)}}">Посмотреть</a></td>
                                    <td><a class="btn btn-primary" href="{{route('admin.car_travel.orderTake',$travel->id)}}">Одобрить</a></td>
                                    <td><a class="btn btn-danger" href="{{route('admin.car_travel.orderReject',$travel->id)}}">Отклонить</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{$travels->links()}}

            </div>
        </div>
    </div>


    <style>
        .header {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
        }

        .header a {
            grid-column: end;
        }
        .header form input{
            border:1px solid #efefef;
            height: 30px;
            padding: 0px 5px;
        }
        .header form button{
            border:1px solid #efefef;
            height: 30px;
            padding: 0px 5px;
        }
    </style>
@endsection

