@extends('admin.layouts.app')

@section('content')
    <form action="{{route('admin.car.update', $car->id)}}" enctype="multipart/form-data" method="post">
        @csrf

        <div class="form-group">
            <label>Гос.номер</label>
            <input type="text" value="{{ $car->state_number }}"  class="form-control" name="state_number">
        </div>

        <div class="form-group">
            <label>Компания</label>
            <select name="company_id" class="form-control">
                @foreach($companies as $company)
                <option value="{{ $company->id }}">{{ $company->title }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <button class="btn btn-primary" type="submit">Сохранить</button>
        </div>
    </form>
@endsection
