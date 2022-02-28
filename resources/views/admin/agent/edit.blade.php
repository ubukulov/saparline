@extends('admin.layouts.app')

@section('content')
    <form action="{{route('admin.agent.update', $agent->id)}}" enctype="multipart/form-data" method="post">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Имя</label>
            <input type="text" required value="{{ $agent->first_name }}"  class="form-control" name="title">
        </div>

        <div class="form-group">
            <label>Телефон</label>
            <input type="text" value="{{ $agent->phone }}" class="form-control" name="phone">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" value="{{ $agent->email }}" required  class="form-control" name="email">
        </div>

        <div class="form-group">
            <button class="btn btn-primary" type="submit">Сохранить</button>
        </div>
    </form>
@endsection
