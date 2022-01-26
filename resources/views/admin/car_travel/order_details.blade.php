@extends('admin.layouts.app')

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Новые билеты (детали)</h2>
                    <a href="{{ route('admin.car_travel.orders') }}" class="btn btn-success">Назад</a>
                </div>

                <div class="body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>№ Места</th>
                                <th>Цена</th>
                                <th>Пассажир (имя)</th>
                                <th>Пассажир (Номер)</th>
                                <th>Пассажир (ИИН)</th>
                                <th>Время Билета</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($car_travel_place_orders as $item)
                                <tr>
                                    <td>
                                        @if($item->car_type_id == 2)
                                            <b>
                                                @if($item->number > 16 and $item->number < 33)
                                                    <b style="color: red">{{(int)$item->number - 16}} верх</b>
                                                @elseif ($item->number < 17)
                                                    <b style="color: blue">{{(int)$item->number }} вниз</b>
                                                @else
                                                    0 верх
                                                @endif
                                            </b>
                                        @else
                                            <b>{{ $item->number }}</b>
                                        @endif
                                    </td>
                                    <td>{{ $item->price }}</td>
                                    <td>{{$item->first_name}}</td>
                                    <td>{{$item->phone}}</td>
                                    <td>{{$item->iin}}</td>
                                    <td>{{$item->booking_time}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

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

