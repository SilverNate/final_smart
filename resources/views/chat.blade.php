<!DOCTYPE html>
<html>
    <head>
        <title>testing</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <style>
          .list-group{
              overflow-y: scroll;
              height: 200px;
          }
        </style>
    </head>
    <body><br /><br />
        <div class="container">
            <div class="row justify-content-md-center" id="app">
                <div class="col-8">
                <li class="list-group-item active" aria-current="true">Chat room</li>
                <div class="badge badge-pill badge-primary">@{{ typing }}</div>
                <ul class="list-group" v-chat-scroll>
                    <message
                    v-for="value,index in chat.message"
                    :key=value.index
                    :color=chat.color[index]
                    :user=chat.user[index]
                    :time=chat.time[index]
                    >
                    @{{ value }}
                    </message>
                </ul>
                  <input type="text" class="form-control" placeholder="type text" v-model="message" @keyup.enter='send'>

                </div>
            </div>
        </div>
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
