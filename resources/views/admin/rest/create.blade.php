@extends('admin.layouts.app')

@section('content')
    <form action="{{route('admin.rest.store')}}" enctype="multipart/form-data" method="post">
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
            <label>Опубликовать</label>
            <select name="active" class="form-control">
                <option value="0">Нет</option>
                <option value="1">Да</option>
            </select>
        </div>

        <div class="form-group">
            <label>Описание</label>
            <textarea name="description" class="form-control" cols="30" rows="3"></textarea>
        </div>

        <div class="form-group">
            <button class="btn btn-primary" type="submit">Сохранить</button>
        </div>
    </form>
@endsection
