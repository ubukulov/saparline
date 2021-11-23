@extends('admin.layouts.app')

@section('content')
    <form action="{{route('admin.company.store')}}" enctype="multipart/form-data" method="post">
        @csrf
        <div class="form-group">
            <label>Называние</label>
            <input type="text" required  class="form-control" name="title">
        </div>

        <div class="form-group">
            <label>Адрес</label>
            <input type="text"  class="form-control" name="address">
        </div>

        <div class="form-group">
            <label>Телефон</label>
            <input type="text"  class="form-control" name="phone">
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" required  class="form-control" name="email">
        </div>

        <div class="form-group">
            <label>БИН</label>
            <input type="text" class="form-control" name="bin">
        </div>


        <div class="form-group">
            <button class="btn btn-primary" type="submit">Сохранить</button>
        </div>
    </form>
@endsection
