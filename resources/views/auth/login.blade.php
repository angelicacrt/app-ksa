<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Login</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    </head>
        <body>
            <x-guest-layout>
                <x-auth-card>
                    <x-slot name="logo">
                        <a href="/">
                            {{-- <x-application-logo class="w-20 h-20 fill-current text-gray-500" /> --}}
                            <div class="center-items">
                                <img src="\images\logo.png" style=" width: 60%; margin-block: -10%;" alt="">
                            </div>
                        </a>
                    </x-slot>
                    
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />
                    
                    <!-- Validation Errors -->
                    <x-auth-validation-errors class="mb-4" :errors="$errors" />
                    
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <!-- Email Address -->
                        <div>
                            <x-label for="email" :value="__('Email')" />
                            
                            <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                        </div>
                        
                        <!-- Password -->
                        <div class="mt-4">
                            <x-label for="password" :value="__('Password')" />
                            
                            <x-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
                        </div>
                        
                        <!-- Remember Me -->
                        {{-- <div class="block mt-4">
                            <label for="remember_me" class="inline-flex items-center">
                                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" name="remember">
                                <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                            </label>
                        </div> --}}
                        <br>
                        <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('register') }}">
                            {{ __('Register Now') }}
                        </a>
                        
                        <div class="flex items-center justify-end mt-4">
                            @if (Route::has('password.request'))
                            <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                                {{ __('Forgot your password?') }}
                            </a>
                            @endif
                            
                            <x-button class="ml-3">
                                {{ __('Log in') }}
                            </x-button>
                        </div>
                    </form>
                </x-auth-card>
            </x-guest-layout>
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