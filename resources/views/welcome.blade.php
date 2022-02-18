<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>KSA</title>
        
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Lato&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500&display=swap" rel="stylesheet">

        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>

        
    </head>
    <body>
        <div class="">
            {{-- <div class="relative flex items-top justify-center min-h-screen sm:items-center py-4 sm:pt-0">
                @if (Route::has('login'))
                    <div class="hidden fixed top-0 right-0 px-6 py-4 sm:block">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 dark:text-gray-500 underline">Login</a>

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="ml-4 text-sm text-gray-700 dark:text-gray-500 underline">Register</a>
                            @endif
                        @endauth
                    </div>
                @endif

            </div> --}}
            <div class="d-flex justify-content-end">
                @if(Route::has('login'))
                    @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                    @if (Route::has('register'))
                        <a class="btn btn-danger" href="{{ route('register') }}">Register</a>
                    @endif
    
                    @endauth
                @endif
            </div>
            
            <div class="center-items mt-5">
                <img src="/images/logo_ksa_1.png" class="mt-5" style="width: 50%; height: 50%;">
                <h1 style="color: white;" class="mt-5">The Best Service For Our Customer</h1>
            </div>
        </div>
    </body>
</html>

<!-- Styles -->
<style>
    body{
        background-image: url('/images/background-img-2.jpg');
        height: 100%;
        width: 100%;
        background-repeat: no-repeat;
        background-size: cover;
        margin: 0;
        padding: 0;
    }
    a{
        padding: 10px;
        border-radius: 10%;
        margin-right: 20px;
        margin-top: 40px
    }
    .center-items{
        margin: auto;
        width: 50%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    @media (max-width: 768px) {
    body {
        height:117.7vh;
    }
</style>

<script>
    document.body.style.zoom = "85%";
</script>