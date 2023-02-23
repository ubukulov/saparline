@extends('admin.layouts.app')

@section('content')
    <form action="{{route('admin.marks.update', $mark->id)}}" method="post">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>Называние</label>
            <input type="text" required value="{{ $mark->name }}"  class="form-control" name="title">
        </div>

        <div class="form-group">
            <button class="btn btn-primary" type="submit">Сохранить</button>
        </div>
    </form>
@endsection
