<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel Typeahead JS Autocomplete Search</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" />
    <style>
        .container {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <li class="dropdown dropdown-notification nav-item  dropdown-notifications">
        <a class="nav-link nav-link-label" href="#" data-toggle="dropdown">
            <i class="fa fa-bell"> </i>
            <span
                class="badge badge-pill badge-default badge-danger badge-default badge-up badge-glow   notif-count"
                data-count="9"></span>
        </a>
        <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
            <li class="dropdown-menu-header">
                <h6 class="dropdown-header m-0 text-center">
                    <span class="grey darken-2 text-center">messages</span>
                </h6>
            </li>
            <li class="scrollable-container ps-container ps-active-y media-list w-100">
                <a href="">
                    <div class="media">
                        <div class="media-body">
                            <h6 class="media-heading text-right ">notification details </h6>
                            <p class="notification-text font-small-3 text-muted text-right"> نص الاشعار</p>
                            <small style="direction: ltr;">
                                <p class=" text-muted text-right"
                                      style="direction: ltr;"> 20-05-2020 - 06:00 pm
                                </p>
                                <br>

                            </small>
                        </div>
                    </div>
                </a>

            </li>
            <li class="dropdown-menu-footer"><a class="dropdown-item text-muted text-center"
                                                href=""> all notifications  </a>
            </li>
        </ul>
    </li>


    
    <form id="s" action="{{ route('test') }}" method="POST">
        @csrf
    <div class="container mt-5">
        <div classs="form-group">
            <input type="text" id="search" name="search" placeholder="Search" class="form-control" />
        </div>
    </div>
    <input type = "submit" name="s" value="s">
    </form>
    <div id="cpuList"></div>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js">
    </script>
   
    <script>
        $(document).on('keyup', '#search', function(event){
          event.preventDefault();
          var query = $(this).val();
          console.log(query);
          $.ajax({
            type: 'post',
            url:'{{url("search")}}',
            data:
              {
                "_token": "{{ csrf_token() }}",
                'query':query
            }
            ,
            success:function(data){
              console.log(data);
            }
          });
      });
    </script>
    <script src="https://js.pusher.com/7.1/pusher.min.js"></script>
    <script>
            Pusher.logToConsole = true;
            var pusher = new Pusher('947cdd6611e4afa92f3b', {
        cluster: 'ap2'
        });
    </script>
   <script src="{{asset('/js/pusherNotifications.js')}}"></script>

</body>
</html>