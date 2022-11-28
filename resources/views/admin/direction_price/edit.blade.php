@extends('admin.layouts.app')

@section('content')
    <form action="{{route('admin.direction.update', $direction_price->id)}}" enctype="multipart/form-data" method="post">
        @csrf
        <div class="form-group">
            <label>Выберите направление</label>
            <select disabled name="travel_id" class="form-control">
                @foreach($travels as $travel)
                    <option @if($direction_price->travel_id == $travel->id) selected @endif value="{{ $travel->id }}">{{ $travel->from_city->name }} -> {{ $travel->to_city->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Выберите машину</label>
            <select disabled name="car_type_id" class="form-control">
                @foreach($car_types as $item)
                    <option @if($direction_price->car_type_id == $item->id) selected @endif value="{{ $item->id }}">{{ $item->name }} ({{ $item->count_places }})</option>
                @endforeach
            </select>
        </div>

        <input type="hidden" id="num" value="1">

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Место</label>
                    <input type="text" disabled readonly required value="{{ $direction_price->number }}"  class="form-control" name="number">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Цена</label>
                    <input type="number" min="1000" required  class="form-control" name="price" value="{{ $direction_price->price }}">
                </div>
            </div>
        </div>

        <div class="form-group">
            <button class="btn btn-primary" type="submit">Сохранить</button>
        </div>
    </form>
@endsection
