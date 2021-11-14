@extends('admin.layouts.app')

@section('content')

    <table class="table table-striped">
        <thead>
        <tr>
            <th>Номер Места</th>
            <th>Сумма</th>
            <th>Пассажир</th>
            <th>Пассажир(Телефон номер)</th>
            <th>Бронивал</th>
        </tr>
        </thead>
        <tbody>
        @foreach($places as $p)
            <tr>
                <td>{{$p->number}}</td>
                <td>{{$p->price}}тг</td>
                <td>{{$p->passenger_name}}</td>
                <td>{{$p->passenger_phone}}</td>
                @if($p->added == 'admin')
                    <td class="btn-info">Админ</td>
                @else
                    <td class="btn-danger">Водитель</td>
                @endif

            </tr>
        @endforeach
        </tbody>
    </table>

@endsection

