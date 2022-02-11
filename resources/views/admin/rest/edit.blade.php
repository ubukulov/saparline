@extends('admin.layouts.app')

@section('content')
    <form action="{{route('admin.rest.update', $rest->id)}}" enctype="multipart/form-data" method="post">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Называние</label>
            <input type="text" value="{{ $rest->title }}" required  class="form-control" name="title">
        </div>

        <div class="form-group">
            <label>Город</label>
            <select name="city_id" class="form-control">
                @foreach($cities as $city)
                    <option @if($rest->city_id == $city->id) selected @endif value="{{ $city->id }}">{{ $city->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Опубликовать</label>
            <select name="active" class="form-control">
                <option @if($rest->active == 0) selected @endif value="0">Нет</option>
                <option @if($rest->active == 1) selected @endif value="1">Да</option>
            </select>
        </div>

        <div class="form-group">
            <label>Описание</label>
            <textarea name="description" class="form-control" cols="30" rows="3">{{ $rest->description }}</textarea>
        </div>


        <div class="form-group">
            <button class="btn btn-primary" type="submit">Сохранить</button>
        </div>
    </form>
@endsection
