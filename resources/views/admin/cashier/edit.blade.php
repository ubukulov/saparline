@extends('admin.layouts.app')

@section('content')
    <form action="{{route('admin.cashier.update', $cashier->id)}}" enctype="multipart/form-data" method="post">
        @csrf

        <div class="form-group">
            <label>Имя</label>
            <input type="text" value="{{ $cashier->first_name }}"  class="form-control" name="first_name">
        </div>

        <div class="form-group">
            <label>Телефон</label>
            <input type="text" value="{{ $cashier->phone }}"  class="form-control" name="phone">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="text" value="{{ $cashier->email }}"  class="form-control" name="email">
        </div>

        <div class="form-group">
            <label>Компания</label>
            <input type="text" value="{{ $cashier->company_name }}"  class="form-control" name="company_name">
        </div>

        <div class="form-group">
            <label>Город</label>
            <select name="city_id" class="form-control">
                @foreach($cities as $city)
                <option @if($city->id == $cashier->city_id) selected @endif value="{{ $city->id }}">{{ $city->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Станция</label>
            <select name="station_id" class="form-control">
                @foreach($stations as $station)
                    <option @if($station->id == $cashier->station_id) selected @endif value="{{ $station->id }}">{{ $station->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Активный</label>
            <select name="active" class="form-control">
                <option @if($cashier->active == 0) selected @endif value="0">Нет</option>
                <option @if($cashier->active == 1) selected @endif value="1">Да</option>
            </select>
        </div>

        <div class="form-group">
            <button class="btn btn-primary" type="submit">Сохранить</button>
        </div>
    </form>
@endsection
