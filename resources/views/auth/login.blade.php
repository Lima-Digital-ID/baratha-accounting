<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="{{asset('css/sb-admin-2.min.css')}}" rel="stylesheet" />
    <link
      href="{{asset('vendor/fontawesome-free/css/all.min.css')}}"
      rel="stylesheet"
      type="text/css"
    />
    <link href="{{asset('css/custom.css')}}" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LOGIN</title>
    {{-- favicon --}}
    <link href="{{ asset('img/logobaratha.png') }}" rel="icon">
    <link href="{{ asset('img/logobaratha.png') }}" rel="apple-touch-icon">
</head>
<body class="bg-light">
    <div class="container container-login">
        <div class="row justify-content-center">
            <div class="col-md-10" id="body_form" style="overflow:auto">
                <div class="box-login">
                    <div class="left">
                        <img src="{{asset('img/logobaratha.png')}}" width="40px" alt="">
                        <div class="form mt-3">
                            <form id="FormLogin" method="post" class="" action="{{ route('login') }}">
                                @csrf

                                    <label for="">Username</label>
                                    <div class="form-underline">
                                        <input type="text" name="username" placeholder="Masukkan Username" class="@error('username') is-invalid @enderror" value="{{ old('username') }}" required autocomplete="username" autofocus>
                                        <span class="fa fa-user"></span>
                                    </div>
                                    @error('username')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <br>
                                    <label for="">Password</label>
                                    <div class="form-underline">
                                        <input type="password" name="password" class="@error('password') is-invalid @enderror" placeholder="Masukkan Password" autocomplete="current-password" required>
                                        <span class="fa fa-lock"></span>
                                    </div>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    <br>
                                    <div class="form-underline">
                                    {{-- <input style="width:auto" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label style="font-size:12px" class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label> --}}

                                    </div>
                                    <br>
                                    <button type="sumit" class="btn btn-primary px-5 font-weight-bold ls-1">Login</button>
                                </form>
                        </div>
                    </div>
                    <div class="right">
                        <div class="text">
                            <h5>Lima Accounting</h5>
                            <p class="font-weight-light">Application</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright mt-4">
        Copyright 2020 - <a href="https://limadigital.id/" target="_blank">LIMA Digital</a>
    </div>
</body>
</html>
