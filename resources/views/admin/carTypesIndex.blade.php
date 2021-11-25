@extends('admin.layouts.app')

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">

                <div class="header">
                    <h2 >Направление</h2>
                    <a class="btn btn-success" data-toggle="modal" data-target="#myModal"> <i class="material-icons">add</i></a>
                </div>



                <div class="body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>Количество мест</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach(\App\Models\CarType::all() as $t)
                                <tr>
                                    <td>{{$t->id}}</td>
                                    <td>{{$t->name}}</td>
                                    <td>{{$t->count_places}}</td>
                                    <td>
                                        <a href="{{route('admin.travel.carTypeDestroy',$t->id)}}" onclick="return confirm('Вы уверены что хотите удалить?')" class="waves-effect btn btn-danger"><i class="material-icons">delete</i></a>
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

    <div id="myModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <form enctype="multipart/form-data" action="{{route('admin.travel.addCarType')}}">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Новый тип транспорта</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Название</label>
                        <input type="text" name="name" class="form-control" required  placeholder="название">
                    </div>
                    <div class="form-group">
                        <label for="exampleInputEmail1">Количество мест</label>
                        <input name="count_places" class="form-control" required type="number" min="1" max="10000" placeholder="количество мест">
                    </div>
                </div>
                <div style="padding-left: 20px">
                    <button type="submit" class="btn btn-success">Создать</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button>
                </div>
            </div>

            </form>

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

