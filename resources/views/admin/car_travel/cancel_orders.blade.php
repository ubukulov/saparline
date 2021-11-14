@extends('admin.layouts.app')

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Вернуть билеты</h2>
                </div>


                <div class="body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>№ Места</th>
                                <th>От куда</th>
                                <th>Куда</th>
                                <th>Цена</th>
                                <th>Пассажир (имя)</th>
                                <th>Пассажир (Номер)</th>
                                <th>время отправления</th>
                                <th>время прибытия</th>
                                <th>Водитель (Имя)</th>
                                <th>Водитель (Номер)</th>
                                <th>Нос номер авто</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($travels as $travel)
                                <tr>
                                    <td>
                                        @if($travel->car_type_count_places == '36' )
                                            @if($travel->number > 16 and $travel->number < 33)
                                                <b style="color: red">{{(int)$travel->number - 16}} верх</b>
                                            @elseif ($travel->number < 17)
                                                <b style="color: blue">{{(int)$travel->number }} вниз</b>
                                            @else
                                                0 верх
                                            @endif
                                        @else
                                            <b >{{$travel->number }}</b>
                                        @endif

                                    </td>
                                    <td>{{$travel->from_city}}</td>
                                    <td>{{$travel->to_city}}</td>
                                    <td>{{$travel->price}}</td>
                                    <td>{{$travel->passenger_name}}</td>
                                    <td>{{$travel->passenger_phone}}</td>
                                    <td>{{$travel->departure_time}}</td>
                                    <td>{{$travel->destination_time}}</td>
                                    <td>{{$travel->driver_name}}</td>
                                    <td>{{$travel->driver_phone}}</td>
                                    <td>{{$travel->car_state_number}}</td>


                                    <td><a class="btn btn-primary" href="{{route('admin.car_travel.orderCancel',$travel->id)}}">Вернул</a></td>
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

