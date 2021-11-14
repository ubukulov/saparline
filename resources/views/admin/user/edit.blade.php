@extends('admin.layouts.app')

@section('content')
    <form action="{{route('admin.user.update',$user->id)}}" enctype="multipart/form-data" method="post">
        @csrf
        <div class="form-group">
            <label>Имя</label>
            <input type="text"  class="form-control" name="name" value="{{$user->name}}" >
        </div>

        <div class="form-group">
            <label>Телефон номер</label>
            <input type="text"  class="form-control" name="phone" value="{{$user->phone}}" required>
        </div>


        <div class="form-group">
            <label>Новый пароль</label>
            <input type="text"  class="form-control" name="password_new"  >
        </div>

        @if($car)
            <div class="form-group">
                <label>Гос номер авто</label>
                <input type="text"  class="form-control" name="number" value="{{$car->state_number}}" required>
            </div>
        @endif



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
