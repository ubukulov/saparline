@extends('admin.layouts.app')

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Новые посадчики</h2>
                </div>

                <div class="body">

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Имя</th>
                                <th>Телефон номер</th>
                                <th>Компания</th>
                                <th>Регистрация</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($lodgers as $lodger)
                                <tr>
                                    <td>{{$lodger->id}}</td>
                                    <td>{{$lodger->name}}</td>
                                    <td>{{$lodger->phone}}</td>
                                    <td>{{$lodger->company->title}}</td>
                                    <td>{{$lodger->created_at}}</td>
                                    <td>
                                        <a href="{{route('admin.user.confirm.Lodger', ['id' => $lodger->id])}}" onclick="return confirm('Вы уверены?')" class="waves-effect btn btn-info"><i class="material-icons">add_circle</i></a>
                                        <a href="{{route('admin.user.reject.Lodger', ['id' => $lodger->id])}}" onclick="return confirm('Вы уверены ?')" class="waves-effect btn btn-danger"><i class="material-icons">block</i></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{$lodgers->links()}}

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

