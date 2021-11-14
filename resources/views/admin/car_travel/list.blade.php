@extends('admin.layouts.app')

@section('content')
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>Поездки</h2>
                </div>

                <div class="header">
                <form  action="{{route('admin.car_travel.list')}}">
                        @csrf
                        <input type="search" name="search" value="{{$search}}" placeholder="текст поиска...">
                        <button>показать</button>

                    </form>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>От куда</th>
                                <th>Куда</th>
                                <th>время отправления</th>
                                <th>время прибытия</th>
                                <th>Водитель</th>
                                <th>Телефон номер</th>
                                <th>Нос номер авто</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($travels as $travel)
                                <tr>
                                    <th scope="row">{{$travel->id}}</th>
                                    <td>{{$travel->from_city}}</td>
                                    <td>{{$travel->to_city}}</td>
                                    <td>{{$travel->departure_time}}</td>
                                    <td>{{$travel->destination_time}}</td>
                                    <td>{{$travel->name}}</td>
                                    <td>{{$travel->phone}}</td>
                                    <td>{{$travel->state_number}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                {{$travels->links()}}

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

