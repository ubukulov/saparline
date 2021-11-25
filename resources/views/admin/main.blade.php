@extends('admin.layouts.app')

@section('content')
    <div class="row clearfix">
        <div class="row clearfix">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <a href="">
                    <div class="info-box bg-light-green hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">person</i>
                        </div>
                        <div class="content">
                            <div class="text">Водители</div>
                            <div class="number count-to" data-from="0"
                                 data-to="{{\App\Models\User::where('role','driver')->count()}}" data-speed="1000"
                                 data-fresh-interval="20"></div>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <a href="">
                    <div class="info-box bg-light-green hover-expand-effect">
                        <div class="icon">
                            <i class="material-icons">person</i>
                        </div>
                        <div class="content">
                            <div class="text">Пассажиры</div>
                            <div class="number count-to" data-from="0"
                                 data-to="{{\App\Models\User::where('role','passenger')->count()}}" data-speed="1000"
                                 data-fresh-interval="20"></div>
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <form action="/admin/sendMultiple" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="exampleInputEmail1">Разослать уведомление</label>
                        <input  class="form-control"
                                name="text"
                                id="exampleInputEmail1"
                               aria-describedby="emailHelp" placeholder="начинайте набирать текст ...">
                    </div>
                    <button type="submit" class="btn btn-primary">Отправить</button>
                </form>
            </div>


        </div>
    </div>
@endsection
@push('js')
    <script>
        $(function () {
            //Widgets count
            $('.count-to').countTo();
        });

    </script>
@endpush

