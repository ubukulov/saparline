@extends('admin.layouts.app')

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Новые Водители</h2>
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
                            @foreach($drivers as $driver)
                                <tr>
                                    <td>{{$driver->id}}</td>
                                    <td>{{$driver->name}}</td>
                                    <td>{{$driver->phone}}</td>
                                    <td>{{$driver->car_type}}</td>
                                    <td>{{$driver->state_number}}</td>
                                    <td>{{$driver->count_places}}</td>
                                    <td>{{$driver->created_at}}</td>
                                    <td>
                                        <a href="{{route('admin.user.driver',$driver->id)}}" class=" waves-effect btn btn-primary"><i class="material-icons">visibility</i></a>
                                        <a href="{{route('admin.user.confirmation.confirm',$driver->id)}}" onclick="return confirm('Вы уверены?')" class="waves-effect btn btn-info"><i class="material-icons">add_circle</i></a>
                                        <a href="{{route('admin.user.confirmation.reject',$driver->id)}}" onclick="return confirm('Вы уверены ?')" class="waves-effect btn btn-danger"><i class="material-icons">block</i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{$drivers->links()}}

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

