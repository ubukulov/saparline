@extends('admin.layouts.app')

@section('content')
    <h3>Поездки</h3>
    <table class="table table-striped">
        <thead>
        <tr>
            <th>#</th>
            <th>От куда</th>
            <th>Куда</th>
            <th>время отправления</th>
            <th>время прибытия</th>
            <th>Номер места</th>
            <th>Сумма</th>
        </tr>
        </thead>
        <tbody>
        @foreach($travels as $travel)
            <tr>
                <th scope="row">{{$travel->id}}</th>
                <td>{{$travel->from_city}}</td>
                <td>{{$travel->to_city}}</td>
                <td>{{$travel->departure_time}}</td>
                <td>{{$travel->destination_time}}</td>
                <td>{{$travel->number}}</td>
                <td>{{$travel->price}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    {{$travels->links()}}

@endsection

