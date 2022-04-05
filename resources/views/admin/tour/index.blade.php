@extends('admin.layouts.app')

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Новые билеты (туризм)</h2>
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
                                <th>Время отправления</th>
                                <th>Водитель (Имя)</th>
                                <th>Водитель (Номер)</th>
                                <th>Нос номер авто</th>
                                <th>Время Билета</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tickets as $ticket)
                                <tr>
                                    <td>
                                        @if($ticket->car_type_id == 2)
                                            <b>
                                                [
                                                @if($ticket->number > 16 and $ticket->number < 33)
                                                    <b style="color: red">{{(int)$ticket->number - 16}} верх</b>
                                                @elseif ($ticket->number < 17)
                                                    <b style="color: blue">{{(int)$ticket->number }} вниз</b>
                                                @else
                                                    0 верх
                                                @endif
                                                ]
                                            </b>
                                        @else
                                            <b>{{ $ticket->number }}</b>
                                        @endif

                                    </td>
                                    <td>{{ $ticket->price }}</td>
                                    <td>{{ $ticket->from_city }}</td>
                                    <td>{{ $ticket->to_city }}</td>
                                    <td>{{ $ticket->first_name }}</td>
                                    <td>{{ $ticket->phone }}</td>
                                    <td>{{ $ticket->departure_time }}</td>
                                    <td>{{ $ticket->driver_name }}</td>
                                    <td>{{ $ticket->driver_phone }}</td>
                                    <td>{{ $ticket->car_state_number }}</td>
                                    <td>{{ $ticket->booking_time }}</td>


                                    <td><a class="btn btn-primary" href="{{route('admin.tour.orderTake', $ticket->id)}}">Одобрить</a></td>
                                    <td><a class="btn btn-danger" href="{{route('admin.tour.orderReject', $ticket->id)}}">Отклонить</a></td>
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
