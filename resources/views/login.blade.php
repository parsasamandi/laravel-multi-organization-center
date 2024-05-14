<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        {{-- Authentication --}}
        <link rel="stylesheet" href="{{ mix('css/auth.css') }}">
        {{-- App css --}}
        <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    </head>
    <body>
        <div class="wrapper">

            {{-- Errors --}}
            <div class="mt-2">
                @if($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="alert alert-danger">
                            {{ $error }}
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Form --}}
            <form action="{{ url('login')}} " method="POST" class="login">
                @csrf
                <!-- Title -->
                <p class="title">ورود</p>
                <!-- Form -->
                <div class="form-group">
                    <input class="text-right" type="number" name="phone_number" placeholder="شماره تلفن" required>
                    <i class="fa fa-user"></i>
                </div>
                <div class="form-group">
                    <input class="text-right" type="password" name="password" placeholder="رمز عبور" required/>
                    <i class="fa fa-key"></i>
                </div>
                <!-- remember token  -->
                <label class="form-remember">
                    <input type="checkbox" name="remember_me"/>
                    <span>
                        مرا به خاطر بسپارید
                    </span>
                </label>
                <button>
                    <span class="state">ورود</span>
                </button>
            </form>

        </div>
    </body>
</html>
