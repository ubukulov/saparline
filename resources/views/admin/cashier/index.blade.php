@extends('admin.layouts.app')

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">

                <div class="header">
                    <h2 >Список кассиров</h2>
                </div>



                <div class="body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Имя</th>
                                <th>Телефон</th>
                                <th>Email</th>
                                <th>Город</th>
                                <th>Станция</th>
                                <th>Компания</th>
                                <th>Активный</th>
                                <th>Дата</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($cashiers as $cashier)
                                <tr>
                                    <td>{{$cashier->id}}</td>
                                    <td>{{$cashier->first_name}}</td>
                                    <td>{{$cashier->phone}}</td>
                                    <td>{{$cashier->email}}</td>
                                    <td>{{$cashier->city->name}}</td>
                                    <td>{{$cashier->station->name}}</td>
                                    <td>{{$cashier->company_name}}</td>
                                    <td>
                                        @if($cashier->active == 1) Да @else Нет @endif
                                    </td>

                                    <td>{{ $cashier->created_at->format('d.m.Y / H:i') }}</td>
                                    <td>
                                        <a href="{{route('admin.cashier.edit',$cashier->id)}}" class="waves-effect btn btn-success"><i class="material-icons">mode_edit</i></a>
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

