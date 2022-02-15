@extends('admin.layouts.app')

@section('content')
    <form action="{{route('admin.meet.store')}}" enctype="multipart/form-data" method="post">
        @csrf
        <div class="form-group">
            <label>Называние</label>
            <input type="text" required  class="form-control" name="title">
        </div>

        <div class="form-group">
            <label>Город</label>
            <select name="city_id" class="form-control">
                @foreach($cities as $city)
                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Широта</label>
            <input type="text" class="form-control" name="latitude">
        </div>

        <div class="form-group">
            <label>Долгота</label>
            <input type="text" class="form-control" name="longitude">
        </div>

        <div class="form-group">
            <button class="btn btn-primary" type="submit">Сохранить</button>
        </div>
    </form>
@endsection
