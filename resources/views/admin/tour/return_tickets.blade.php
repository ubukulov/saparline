@extends('admin.layouts.app')

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Вернуть билеты (туризм)</h2>
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
                                <th>Причина возврата</th>
                                <th>Нос номер авто</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tours as $tour)
                                <tr>
                                    <td>
                                        @if($tour->car_type_count_places == '36' )
                                            @if($tour->number > 16 and $tour->number < 33)
                                                <b style="color: red">{{(int)$tour->number - 16}} верх</b>
                                            @elseif ($tour->number < 17)
                                                <b style="color: blue">{{(int)$tour->number }} вниз</b>
                                            @else
                                                0 верх
                                            @endif
                                        @else
                                            <b >{{$tour->number }}</b>
                                        @endif

                                    </td>
                                    <td>{{$tour->from_city}}</td>
                                    <td>{{$tour->to_city}}</td>
                                    <td>{{$tour->price}}</td>
                                    <td>{{$tour->passenger_name}}</td>
                                    <td>{{$tour->passenger_phone}}</td>
                                    <td>{{$tour->departure_time}}</td>
                                    <td>{{$tour->destination_time}}</td>
                                    <td>{{$tour->driver_name}}</td>
                                    <td>{{$tour->driver_phone}}</td>
                                    <td>
                                        {{ $tour->reason_for_return }}
                                    </td>
                                    <td>{{$tour->car_state_number}}</td>


                                    <td><a class="btn btn-primary" href="{{route('admin.tour.cancelOrder',$tour->id)}}">Вернул</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{$tours->links()}}

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
