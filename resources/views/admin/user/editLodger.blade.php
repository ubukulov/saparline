@extends('admin.layouts.app')

@section('content')
    <form action="{{route('admin.user.saveLodger', $lodger->id)}}" enctype="multipart/form-data" method="post">
        @csrf
        <div class="form-group">
            <label>Имя</label>
            <input type="text" required value="{{ $lodger->name }}"  class="form-control" name="name">
        </div>

        <div class="form-group">
            <label>Телефон номер</label>
            <input type="text" value="{{ $lodger->phone }}"  class="form-control" name="phone">
        </div>

        <div class="form-group">
            <label>Компания</label>
            <select class="form-control" name="company_id">
				@foreach($companies as $company)
				<option @if($company->id == $lodger->company_id) selected @endif value="{{ $company->id }}">{{ $company->title }}</option>
				@endforeach
			</select>
        </div>

       
        <div class="form-group">
            <button class="btn btn-primary" type="submit">Сохранить</button>
        </div>
    </form>
@endsection
