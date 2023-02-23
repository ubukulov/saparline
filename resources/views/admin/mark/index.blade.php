@extends('admin.layouts.app')

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">

                <div class="header">
                    <h2 >Список марки</h2>
                    <a href="{{route('admin.marks.create')}}" class="btn btn-success"> <i class="material-icons">add</i></a>
                </div>



                <div class="body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>Дата</th>
                                <th>Действие</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($marks as $mark)
                                <tr>
                                    <td>{{$mark->id}}</td>
                                    <td>{{$mark->name}}</td>
                                    <td>{{$mark->created_at->format('d.m.Y H:i:s')}}</td>
                                    <td>
                                        <a href="{{route('admin.marks.edit',$mark->id)}}" class="waves-effect btn btn-success"><i class="material-icons">mode_edit</i></a>
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

