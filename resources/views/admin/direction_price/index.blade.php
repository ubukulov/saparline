@extends('admin.layouts.app')

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">

                <div class="header">
                    <h2 >Список агентов</h2>
                    <a href="{{route('admin.direction.create')}}" class="btn btn-success"> <i class="material-icons">add</i></a>
                </div>



                <div class="body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Тип транспорта</th>
                                <th>Цена</th>
                                <th>Количество мест</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($direction_prices as $dp)
                                <tr>
                                    <td>{{$dp->id}}</td>
                                    <td>{{$dp->car_type->name}}</td>
                                    <td>{{$dp->price}}</td>
                                    <td>{{$dp->car_type->count_places}}</td>
                                    <td>
                                        <a href="{{route('admin.agent.edit',$dp->id)}}" class="waves-effect btn btn-success"><i class="material-icons">mode_edit</i></a>
                                        <a href="{{route('admin.agent.destroy',$dp->id)}}" onclick="return confirm('Вы уверены что хотите удалить?')" class="waves-effect btn btn-danger"><i class="material-icons">delete</i></a>
                                    </td>
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

