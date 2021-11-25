@extends('admin.layouts.app')

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Транспорты</h2>
                </div>

                <div class="header">
                    <form  action="{{route('admin.user.drivers')}}">
                        @csrf
{{--                        <input type="search" name="search" value="{{$search}}" placeholder="текст поиска...">--}}
                        <button>показать</button>

                    </form>
                </div>


                <div class="body">

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Имя</th>
                                <th>Телефон номер</th>
                                <th>Тип авто</th>
                                <th>Гос номер авто</th>
                                <th>Количество мест</th>
                                <th>Регистрация</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($cars as $car)
                                <tr>
                                    <td>{{$car->carId}}</td>
                                    <td>{{$car->name}}</td>
                                    <td>{{$car->phone}}</td>
                                    <td>{{$car->car_type}}</td>
                                    <td>{{$car->state_number}}</td>
                                    <td>{{$car->count_places}}</td>
                                    <td>{{$car->created_at}}</td>
                                    <td>
                                        <a href="{{route('admin.user.carDetail',$car->carId)}}" class=" waves-effect btn btn-primary"><i class="material-icons">visibility</i></a>
                                        <a href="{{route('admin.user.newCar.confirm',$car->carId)}}" onclick="return confirm('Вы уверены?')" class="waves-effect btn btn-info"><i class="material-icons">add_circle</i></a>
                                        <a href="{{route('admin.user.newCar.reject',$car->carId)}}" onclick="return confirm('Вы уверены ?')" class="waves-effect btn btn-danger"><i class="material-icons">block</i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{$cars->links()}}

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

