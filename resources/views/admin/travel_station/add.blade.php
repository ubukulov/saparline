@extends('admin.layouts.app')

@section('content')
    <form action="{{route('admin.travel_station.create')}}" enctype="multipart/form-data" method="post">
        @csrf
        <div class="form-group">
            <label>Остоновка</label>
            <select  required  class="form-control" name="station_id"  >
                @foreach($stations as $s)
                    <option value="{{$s->id}}">{{$s->name}}</option>
                @endforeach
            </select>
        </div>
        <input type="hidden" name="travel_id" value="{{$travel_id}}">


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
