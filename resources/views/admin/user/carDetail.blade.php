@extends('admin.layouts.app')

@section('content')


    <div class="body">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs tab-nav-right" role="tablist">
            <li role="presentation" class="active"><a href="#messages" data-toggle="tab"  aria-expanded="true">Данные</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane  active" id="messages">
                <div class="row">
{{--                    @if($user->passport_image)--}}
{{--                        <div class="col-md-4">--}}
{{--                            <h4>Паспорт</h4>--}}
{{--                            <img src="{{asset($user->passport_image)}}" width="350">--}}
{{--                        </div>--}}
{{--                    @endif--}}
                    <div class="col-md-4" style="margin-right: 20px">
                        <h4>Аватар</h4>
                        <img src="{{asset($car->avatar??'')}}" style="max-width: 350px;max-height: 400px" width="350">
                    </div>
                    <div class="col-md-4" style="margin-right: 20px">
                        <h4>Фото паспорта</h4>
                        <img src="{{asset($car->passport_image??'')}}" style="max-width: 350px;max-height: 400px" width="350">
                    </div>
                    <div class="col-md-4" style="margin-right: 20px">
                        <h4>Фото паспорта(обратная сторона)</h4>
                        <img src="{{asset($car->passport_image_back??'')}}" style="max-width: 350px;max-height: 400px" width="350">
                    </div>
                    <div class="col-md-4" style="margin-right: 20px">
                        <h4>Фото удостоверения личности</h4>
                        <img src="{{asset($car->identify_image??'')}}" style="max-width: 350px;max-height: 400px" width="350">
                    </div>
                    <div class="col-md-4" style="margin-right: 20px">
                        <h4>Фото удостоверения личности(обратная сторона)</h4>
                        <img src="{{asset($car->identify_image_back??'')}}" style="max-width: 350px;max-height: 400px" width="350">
                    </div>
                    @if($car->image)
                        <div class="col-md-4">
                            <h4>Фото транспорта 1</h4>
                            <img src="{{asset($car->image??'')}}" style="max-width: 350px;max-height: 400px" width="350">
                        </div>
                    @endif
                    @if($car->image1)
                        <div class="col-md-4" style="margin-right: 20px">
                            <h4>Фото транспорта 2</h4>
                            <img src="{{asset($car->image1??'')}}" style="max-width: 350px;max-height: 400px" width="350">
                        </div>
                    @endif
                    @if($car->image2)
                        <div class="col-md-4">
                            <h4>Фото транспорта 3</h4>
                            <img src="{{asset($car->image2??'')}}" style="max-width: 350px;max-height: 400px" width="350">
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>


@endsection

