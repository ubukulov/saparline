@extends('admin.layouts.app')

@section('content')
    <form action="{{route('admin.direction.store')}}" enctype="multipart/form-data" method="post">
        @csrf
        <div class="form-group">
            <label>Выберите направление</label>
            <select name="travel_id" class="form-control">
                @foreach($travels as $travel)
                <option value="{{ $travel->id }}">{{ $travel->from_city->name }} -> {{ $travel->to_city->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Выберите машину</label>
            <select name="car_type_id" class="form-control">
                @foreach($car_types as $item)
                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->count_places }})</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Цена</label>
            <input type="text" required  class="form-control" name="price">
        </div>

        <div class="form-group">
            <button class="btn btn-primary" type="submit">Сохранить</button>
        </div>
    </form>
@endsection
