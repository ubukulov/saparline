@extends('admin.layouts.app')

@section('content')
    <form action="{{route('admin.city.update',$city->id)}}" enctype="multipart/form-data" method="post">
        @csrf
        <div class="form-group">
            <label>называние</label>
            <input type="text" required  class="form-control" name="name" value="{{$city->name}}" >
        </div>


        <div class="form-group">
            <button class="btn btn-primary" type="submit">Сохранить</button>
        </div>
    </form>
@endsection

@push('js')
    <script>
        $(function () {
            CKEDITOR.replace('ckeditor');
            CKEDITOR.config.height = 300;
        })
    </script>
@endpush
