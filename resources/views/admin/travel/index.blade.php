@extends('admin.layouts.app')

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">

                <div class="header">
                    <h2 >Направление</h2>
                    <a href="{{route('admin.travel.add')}}" class="btn btn-success"> <i class="material-icons">add</i></a>
                </div>



                <div class="body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>От куда</th>
                                <th>Куда</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($travel as $t)
                                <tr>
                                    <td>{{$t->id}}</td>
                                    <td>{{$t->from}}</td>
                                    <td>{{$t->to}}</td>
                                    <td>
                                        <a href="{{route('admin.travel_station.index',$t->id)}}" class="waves-effect btn btn-info">Промежуточный остоновки</a>
                                        <a href="{{route('admin.travel.edit',$t->id)}}" class="waves-effect btn btn-success"><i class="material-icons">mode_edit</i></a>
                                        <a href="{{route('admin.travel.destroy',$t->id)}}" onclick="return confirm('Вы уверены что хотите удалить?')" class="waves-effect btn btn-danger"><i class="material-icons">delete</i></a>
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

