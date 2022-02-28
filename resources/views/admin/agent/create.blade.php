@extends('admin.layouts.app')

@section('content')
    <form action="{{route('admin.agent.store')}}" enctype="multipart/form-data" method="post">
        @csrf
        <div class="form-group">
            <label>Имя</label>
            <input type="text" required  class="form-control" name="first_name">
        </div>

        <div class="form-group">
            <label>Телефон</label>
            <input type="text"  class="form-control" name="phone" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" required  class="form-control" name="email">
        </div>

        <div class="form-group">
            <button class="btn btn-primary" type="submit">Сохранить</button>
        </div>
    </form>
@endsection
