@extends('admin.layouts.app')

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Посадчики</h2>
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
                                    <td>
                                        @if($lodger->company)
                                            {{ $lodger->company->title }}
                                        @else

                                        @endif
                                    </td>
                                    <td>{{$lodger->created_at}}</td>
                                    <td>

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

