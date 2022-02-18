@extends('../layouts.base')

@section('title', 'Logistic Dashboard')

@section('container')
<div class="row">
    @include('logistic.sidebar')

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <form method="POST" action="/logistic/upload" files="true" enctype="multipart/form-data" class="mt-5">
            @csrf
            <input name="fileInput" type="file">
            <button type="submit">submit</button>
        </form>
    </main>
</div>

@endsection
