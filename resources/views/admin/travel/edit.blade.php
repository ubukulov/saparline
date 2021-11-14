@extends('admin.layouts.app')

@section('content')
    <form action="{{route('admin.travel.update',$travel->id)}}" enctype="multipart/form-data" method="post">
        @csrf
        <div class="form-group">
            <label>От куда</label>
            <select name="from_city_id" required class="form-control">
                @foreach($cities as $city)
                    <option {{$travel->from_city_id == $city->id ? 'selected':''}} value="{{$city->id}}">{{$city->name}}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Куда</label>
            <select name="to_city_id" required class="form-control">
                @foreach($cities as $city)
                    <option {{$travel->to_city_id == $city->id ? 'selected':''}} value="{{$city->id}}">{{$city->name}}</option>
                @endforeach
            </select>
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
