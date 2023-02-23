@extends('admin.layouts.app')

@section('content')
    <form action="{{route('admin.marks.store')}}" method="post">
        @csrf
        <div class="form-group">
            <label>Называние</label>
            <input type="text" required  class="form-control" name="name">
        </div>

        <div class="form-group">
            <button class="btn btn-primary" type="submit">Сохранить</button>
        </div>
    </form>
@endsection
