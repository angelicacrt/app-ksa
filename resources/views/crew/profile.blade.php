@if(Auth::user()->hasRole('crew'))

    @extends('../layouts.base')

    @section('title', 'Profile')

    @section('container')
   
    <!-- {{-- add your code here--}} -->
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                {{-- <x-application-logo class="w-20 h-20 fill-current text-gray-500" /> --}}
                    <div class="center-items">
                        <img src="\images\logo_ksa_1.png" style=" width: 40%;" alt="">
                    </div>
            </a>
        </x-slot>
        <div class="mt-4">
            <x-label for="name"  />
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person" viewBox="0 0 16 16">
                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
            </svg>
                <h5>
                    {{ Auth::user()->name }}
                </h5>
            </div>
        <div class="mt-4">
            {{ Auth::user()->email }}
        </div>
        <div class="mt-4">
            {{ Auth::user()->no_induk_pegawai }}
        </div>
        <div class="mt-4">
            {{ Auth::user()->cabang }}
        </div>
    </x-auth-card>
 
    @endsection
    

@else
    @include('../layouts/notAuthorized')
@endif


<style>
    /* body{
        background-image: url('/images/background-img-2.jpg');
        height: 100%;
        width: 100%;
        background-repeat: no-repeat;
        background-size: cover;
        margin: 0;
        padding: 0;
    } */
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