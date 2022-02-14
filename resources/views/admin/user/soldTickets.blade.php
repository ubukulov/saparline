@extends('admin.layouts.app')

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2 >Проданные билеты</h2>
                </div>

                <div class="body">

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Имя</th>
                                <th>Телефон номер</th>
                                <th>ИИН</th>
                                <th>Гос номер авто</th>
                                <th>Место</th>
                                <th>Направление</th>
                                <th>Цена</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($soldTickets as $ticket)
								@php
									$car = \App\Models\Car::find($ticket->car_travel->car_id);
								@endphp
                                <tr>
                                    <td>{{$ticket->id}}</td>
                                    <td>{{$ticket->first_name}}</td>
                                    <td>{{$ticket->phone}}</td>
                                    <td>{{$ticket->iin}}</td>
                                    <td>
									{{ $car->state_number }}
									</td>
                                    <td>
										@if($car->car_type_id == 2)
                                           <b>
                                               [
                                               @foreach(\App\Models\CarTravelPlaceOrder::where('car_travel_order_id',$ticket->car_travel_order_id)->select('number')->get() as $k=>$num)
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
                                            <b>{{\App\Models\CarTravelPlace::where('car_travel_order_id',$ticket->car_travel_order_id)->select('number')->pluck('number')}}</b>
                                        @endif
									</td>
                                    <td>{{ $ticket->from_station->name }} / {{ $ticket->to_station->name }}</td>
                                    <td>{{$ticket->price}}</td>
                                   
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{$soldTickets->links()}}

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

