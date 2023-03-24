@extends('admin.layouts.app')
@section('content')

    <div class="body">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs tab-nav-right" role="tablist">
            <li role="presentation" class="active"><a href="#home" data-toggle="tab" aria-expanded="true">Данные</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="home">
                <div class="row">
                    @if($dataImages->passport_image)
                        <div class="col-md-4" style="margin-right: 20px">
                            <h4>Паспорт</h4>
                            <img src="{{asset($dataImages->passport_image)}}" width="350">
                        </div>
                    @endif
                    @if($dataImages->passport_image_back)
                        <div class="col-md-4" style="margin-right: 20px">
                            <h4>Паспорт (обратная сторона)</h4>
                            <img src="{{asset($dataImages->passport_image_back)}}" width="350">
                        </div>
                    @endif
                </div>
                <div class="row">

                    <div class="col-md-4" style="margin-right: 20px">
                        <h4>Удостоверение личности</h4>
                        <img src="{{asset($dataImages->identity_image??'')}}" width="350">
                    </div>

                    <div class="col-md-4" style="margin-right: 20px">
                        <h4>Удостоверение личности (обратная сторона)</h4>
                        <img src="{{asset($dataImages->identity_image_back??'')}}" width="350">
                    </div>
                </div>
                <div class="row">

                    @if(isset($dataImages->avatar))
                        <div class="col-md-4" style="margin-right: 20px">
                            <h4>Аватар</h4>
                            <img src="{{asset($dataImages->avatar)}}" width="350">
                        </div>
                    @endif
                    @if(isset($dataImages->image))
                        <div class="col-md-4" style="margin-right: 20px">
                            <h4>Фото транспорта</h4>
                            <img src="{{asset($dataImages->image)}}" width="350">
                        </div>
                    @endif
                </div>
                <div class="row">
                    @if($dataImages->image1)
                        <div class="col-md-4" style="margin-right: 20px">
                            <h4>Фото транспорта 2</h4>
                            <img src="{{asset($dataImages->image1??'')}}" style="max-width: 350px;max-height: 400px"
                                 width="350">
                        </div>
                    @endif
                    @if($dataImages->image2)
                        <div class="col-md-4">
                            <h4>Фото транспорта 3</h4>
                            <img src="{{asset($dataImages->image2??'')}}" style="max-width: 350px;max-height: 400px"
                                 width="350">
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>


@endsection
