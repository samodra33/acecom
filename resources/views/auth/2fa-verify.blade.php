<?php $general_setting = DB::table('general_settings')->find(1); ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{$general_setting->site_title}}</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <link rel="manifest" href="{{url('public/manifest.json')}}">
    <link rel="icon" type="image/png" href="{{url('public/logo', $general_setting->site_logo)}}" />
    <!-- Bootstrap CSS-->
    <link rel="stylesheet" href="<?php echo asset('vendor/bootstrap/css/bootstrap.min.css') ?>" type="text/css">
    <!-- Google fonts - Roboto -->
    <link rel="preload" href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link href="https://fonts.googleapis.com/css?family=Nunito:400,500,700" rel="stylesheet"></noscript>
    <!-- login stylesheet-->
    <link rel="stylesheet" href="<?php echo asset('css/auth.css') ?>" id="theme-stylesheet" type="text/css">
  </head>
  <body>
    <div class="page login-page">
      <div class="container">
        <div class="form-outer text-center d-flex align-items-center">
          <div class="form-inner">
            <div class="logo">
                @if($general_setting->site_logo)
                <img src="{{url('public/logo', $general_setting->site_logo)}}" width="110">
                @else
                <span>{{$general_setting->site_title}}</span>
                @endif
            </div>
            @if(session()->has('delete_message'))
            <div class="alert alert-danger alert-dismissible text-center"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>{{ session()->get('delete_message') }}</div>
            @endif

            <div class="panel-body" style="text-align: center;">
                @if($errors->any())
                    <b style="color: red">{{$errors->first()}}</b>

                @endif
                <div class="mt-3">
                    <form method="POST" action="{{ route('2fa') }}" id="login-form">
                        @csrf
                        <div class="form-group mb-4">
                            <p>Please enter the  <strong>OTP</strong> generated on your Authenticator App. <br> Ensure you submit the current one because it refreshes every 30 seconds.</p>
                        </div>
                        <div class="form-group-material mb-4">
                            <input id="login-username" type="number" name="one_time_password" required class="input-material" required>
                            <label for="login-username" class="label-material">One Time Password</label>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block mb-3">Submit & Login</button>
                        @if( request()->get('firstToken') )
                            <a href="#" onclick="GoBackWithRefresh();return false;" class="btn btn-info btn-block">Move Back</a>
                        @endif
                    </form>
                </div>
            </div>
          </div>
          <div class="copyrights text-center">
            <p>{{trans('file.Developed By')}} <span class="external">{{$general_setting->developed_by}}</span></p>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
<script type="text/javascript" src="<?php echo asset('vendor/jquery/jquery.min.js') ?>"></script>
<script>
    if ('serviceWorker' in navigator ) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('public/service-worker.js').then(function(registration) {
                // Registration was successful
                console.log('ServiceWorker registration successful with scope: ', registration.scope);
            }, function(err) {
                // registration failed :(
                console.log('ServiceWorker registration failed: ', err);
            });
        });
    }
</script>
<script type="text/javascript">
    $('.admin-btn').on('click', function(){
        $("input[name='name']").focus().val('admin');
        $("input[name='password']").focus().val('admin');
    });

  $('.staff-btn').on('click', function(){
      $("input[name='name']").focus().val('staff');
      $("input[name='password']").focus().val('staff');
  });

  $('.customer-btn').on('click', function(){
      $("input[name='name']").focus().val('shakalaka');
      $("input[name='password']").focus().val('shakalaka');
  });
  // ------------------------------------------------------- //
    // Material Inputs
    // ------------------------------------------------------ //

    var materialInputs = $('input.input-material');

    // activate labels for prefilled values
    materialInputs.filter(function() { return $(this).val() !== ""; }).siblings('.label-material').addClass('active');

    // move label on focus
    materialInputs.on('focus', function () {
        $(this).siblings('.label-material').addClass('active');
    });

    // remove/keep label on blur
    materialInputs.on('blur', function () {
        $(this).siblings('.label-material').removeClass('active');

        if ($(this).val() !== '') {
            $(this).siblings('.label-material').addClass('active');
        } else {
            $(this).siblings('.label-material').removeClass('active');
        }
    });
</script>
<script type="text/javascript">
    function GoBackWithRefresh(event) {
        if ('referrer' in document) {
            window.location = document.referrer;
            /* OR */
            //location.replace(document.referrer);
        } else {
            window.history.back();
        }
    }
</script>