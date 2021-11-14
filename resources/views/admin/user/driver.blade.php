@extends('admin.layouts.app')

@section('content')



    <div class="body">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs tab-nav-right" role="tablist">
            <li role="presentation" class="active"><a href="#messages" data-toggle="tab"  aria-expanded="true">Поездки</a></li>
            <li role="presentation" ><a href="#home" data-toggle="tab">Данные</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane fade  in" id="home">
                <div class="row">
                    @if($user->passport_image)
                        <div class="col-md-4">
                            <h4>Паспорт</h4>
                            <img src="{{asset($user->passport_image)}}" width="350">
                        </div>
                    @endif

                    @if($user->identity_image)
                        <div class="col-md-4">
                            <h4>Удостоверение личности</h4>
                            <img src="{{asset($user->identity_image)}}" width="350">
                        </div>
                    @endif
                    @if($car->image)
                        <div class="col-md-4">
                            <h4>Фото транспорта</h4>
                            <img src="{{asset($car->image)}}" width="350">
                        </div>
                    @endif
                </div>
            </div>
            <div role="tabpanel" class="tab-pane  active" id="messages">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>От куда</th>
                        <th>Куда</th>
                        <th>время отправления</th>
                        <th> сколько купили через приложение</th>
                        <th>Количество</th>
                        <th>сколько водитель сам брон</th>
                        <th>Количество</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($travels as $travel)
                        <tr>
                            <td>{{$travel->from_city}}</td>
                            <td>{{$travel->to_city}}</td>
                            <td>{{\Carbon\Carbon::parse($travel->departure_time)->format('Y/m/d')}}</td>
                            <td>
                                {{ \App\Models\CarTravelPlace::where('car_travel_id',$travel->id)->whereStatus('take')->whereAdded('admin')->sum('price') }} тг
                            </td>
                            <td>
                                {{ \App\Models\CarTravelPlace::where('car_travel_id',$travel->id)->whereStatus('take')->whereAdded('admin')->count() }}
                            </td>
                            <td>
                                {{ \App\Models\CarTravelPlace::where('car_travel_id',$travel->id)->whereStatus('take')->whereAdded('driver')->sum('price') }} тг
                            </td>

                            <td>
                                {{ \App\Models\CarTravelPlace::where('car_travel_id',$travel->id)->whereStatus('take')->whereAdded('driver')->count() }}
                            </td>
                            <td>
                                <a href="{{route('admin.user.travelPlaces',$travel->id)}}" class="btn btn-flat">Пассажиры</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>


@endsection

