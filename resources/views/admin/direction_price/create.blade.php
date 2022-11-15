@extends('admin.layouts.app')

@section('content')
    <form action="{{route('admin.direction.store')}}" enctype="multipart/form-data" method="post">
        @csrf
        <div class="form-group">
            <label>Выберите направление</label>
            <select name="travel_id" class="form-control">
                @foreach($travels as $travel)
                <option value="{{ $travel->id }}">{{ $travel->from_city->name }} -> {{ $travel->to_city->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Выберите машину</label>
            <select name="car_type_id" class="form-control">
                @foreach($car_types as $item)
                    <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->count_places }})</option>
                @endforeach
            </select>
        </div>

        <input type="hidden" id="num" value="1">

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Место с </label>
                    <input type="number" min="1" required  class="form-control" name="data[0][from]">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Место до</label>
                    <input type="number" min="1" required  class="form-control" name="data[0][to]">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Цена</label>
                    <input type="number" min="1000" required  class="form-control" name="data[0][price]">
                </div>
            </div>
        </div>

        <div id="addPlaceBtn" class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <button id="addPlace" type="button" class="btn btn-success">Добавить поле</button>
                </div>
            </div>

            <div class="col-md-2">
                {{--<div  class="form-group">
                    <button id="removePlace" type="button" class="btn btn-success">Убрать поле</button>
                </div>--}}
            </div>
        </div>

        <div class="form-group">
            <button class="btn btn-primary" type="submit">Сохранить</button>
        </div>
    </form>
@endsection

@push('js')
    <script>
        $(document).ready(function(){
            $('#addPlace').click(function(){
                let num = $('#num').val();

                let html = '<div class="row">\n' +
                    '            <div class="col-md-4">\n' +
                    '                <div class="form-group">\n' +
                    '                    <label>Место с </label>\n' +
                    '                    <input type="number" min="1" required  class="form-control" name="data[' + num + '][from]">\n' +
                    '                </div>\n' +
                    '            </div>\n' +
                    '            <div class="col-md-4">\n' +
                    '                <div class="form-group">\n' +
                    '                    <label>Место до</label>\n' +
                    '                    <input type="number" min="1" required  class="form-control" name="data[' + num + '][to]">\n' +
                    '                </div>\n' +
                    '            </div>\n' +
                    '            <div class="col-md-4">\n' +
                    '                <div class="form-group">\n' +
                    '                    <label>Цена</label>\n' +
                    '                    <input type="number" min="1000" required  class="form-control" name="data[' + num + '][price]">\n' +
                    '                </div>\n' +
                    '            </div>\n' +
                    '        </div>';
                $('#addPlaceBtn').before(html);
                $('#num').val(parseInt(num) + parseInt(1));
            });
        });
    </script>
@endpush
