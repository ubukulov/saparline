@extends('admin.layouts.app')
@section('content')
    <div id="userShowPage" class="row clearfix">
       <div class="row">
           <div class="col-lg-3 col-md-3 avatar">
               @if($user->avatar)
                   <img src="{{asset($user->avatar)}}">
               @else
                   <img src="https://novostroiki58.ru/wp-content/uploads/2019/04/no-avatar-300x300.jpg">
               @endif
           </div>
           <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12 mainInfo ">
               <table class="table">
                   <tr>
                       <th scope="row">Логин</th>
                       <td>{{$user->login}}</td>
                   </tr>
                   <tr>
                       <th scope="row">Фио</th>
                       <td>{{$user->name}}</td>
                   </tr>
                   <tr>
                       <th scope="row">Телефон номер</th>
                       <td>{{$user->phone}}</td>
                   </tr>
                   <tr>
                       <th scope="row">email</th>
                       <td>{{$user->email}}</td>
                   </tr>
                   <tr>
                       <th scope="row">язык</th>
                       <td>{{$user->lang}}</td>
                   </tr>
                   <tr>
                       <th scope="row">Дата регистрации</th>
                       <td>{{$user->created_at}}</td>
                   </tr>
                   <tr>
                       <th scope="row">О себе</th>
                       <td>{{$user->bio}}</td>
                   </tr>
               </table>
           </div>
       </div>
        <br>
        <br>
        <br>
        <br>
        <div class="row">
            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th> подписки</th>
                    <th> подписчики</th>
                    <th> Посты</th>
                    <th> РеПосты</th>
                    <th> РеКомменты</th>
                    <th> Избранные</th>
                    <th> Лайки на Коменты</th>
                    <th> Дизлайки на Коменты</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>{{$subscriptions_count}}</td>
                    <td>{{$subscribers_count}}</td>
                    <td>{{$post_count}}</td>
                    <td>{{$repost_count}}</td>
                    <td>{{$recomment_count}}</td>

                    <td>{{$favorite_count}}</td>
                    <td>{{$comment_like_count}}</td>
                    <td>{{$comment_dislike_count}}</td>
                </tr>
                </tbody>
            </table>
        </div>
        <br>
        <br>
        <br>
        <br>
        <div class="row posts">
            @foreach($posts as $post)
                @if(isset($post->media[0]['url']))
                    <div class="col-md-4">
                        <a href="{{route('admin.post.show',$post->id)}}">
                            @if($post->media[0]['mimeType'] == 'mp4')
                                <video src="{{asset($post->media[0]['url'])}}" autoplay muted ></video>
                            @else
                                <img src="{{asset($post->media[0]['url'])}}" alt="">
                            @endif
                        </a>
                    </div>
                @endif
            @endforeach
        </div>
    </div>





    <style>
        img{
            display: block;
            max-width: 100%;
            max-height: 100%;
        }
        .posts > div{
            height: 420px;
            margin-bottom: 25px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 15px;
        }
        .posts > div > div{
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            width: 100%;
            height: 100%;
            padding: 5px;
            overflow: hidden;
        }
    </style>


@endsection

