@extends('admin.layouts.app')

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">

                <div class="header">
                    <h2 >Список машин</h2>
                </div>



                <div class="body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Компания</th>
                                <th>Гос.номер</th>
                                <th>Тип</th>
                                <th>Подтверждено</th>
                                <th>Телевизор</th>
                                <th>Кондиционер</th>
                                <th>Багаж</th>
                                <th>Дата</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($cars as $car)
                                <tr>
                                    <td>{{$car->id}}</td>
                                    <td>{{$car->getCompanyName()}}</td>
                                    <td>{{$car->state_number}}</td>
                                    <td>{{$car->car_type->name}}</td>
                                    <td>
                                        @if($car->is_confirmed == 1) Да @else Нет @endif
                                    </td>
                                    <td>@if($car->tv == 1) Есть @else Нет @endif</td>
                                    <td>@if($car->conditioner == 1) Есть @else Нет @endif</td>
                                    <td>@if($car->baggage == 1) Есть @else Нет @endif</td>
                                    <td>{{ $car->created_at->format('d.m.Y H:i') }}</td>
                                    <td>
                                        <a href="{{route('admin.car.edit',$car->id)}}" class="waves-effect btn btn-success"><i class="material-icons">mode_edit</i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        {{ $cars->links() }}
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

