<table class="table table-hover">
    <thead class="thead-dark">
        <tr>
            <th scope="col">Name</th>
            <th scope="col">GT</th>
            <th scope="col">NT</th>
            <th scope="col">Flag</th>
            <th scope="col">Action</th>
        </tr>
    </thead>
    <tbody>
        @forelse($barges as $key => $b)
            <tr>
                <td>{{ $b -> bargeName }}</td>
                <td>{{ $b -> gt }}</td>
                <td>{{ $b -> nt }}</td>
                <td>{{ $b -> flag }}</td>
                <td>
                    <button class="btn btn-sm btn-danger" type="button" data-toggle="modal" id="finalize" data-target="#deleteBarge-{{ $b -> id }}">Delete</button>
                </td>
            </tr>
        @empty
            <tr>
                <h1>No Data Found.</h1>
            </tr>
        @endforelse
    </tbody>
</table>

@foreach($barges as $b)
    <!-- Modal #1 -->
    <div class="modal fade" id="deleteBarge-{{ $b -> id }}" tabindex="-1" role="dialog" aria-labelledby="deleteBarge"
    aria-hidden="true" data-backdrop="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger">
                    <h5 class="modal-title" id="deleteBargeTitle" style="color: white">Delete Barge - {{ $b -> bargeName }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex flex-column justify-content-center align-items-center">
                        <span class="text-danger" data-feather="alert-circle" style="height: 15%; width: 15%;"></span>
                        <h5 class="font-weight-bold mt-3">Are You Sure Want To Remove This Barge ?</h5>
                    </div>
                </div>
                <div class="modal-footer">
                    <form method="POST" action="/admin-operational/delete-barge">
                        @csrf
                        @method('delete')
                        <input type="hidden" name="bargeId" value="{{ $b -> id }}">

                        <button type="button" class="btn btn-secondary mr-3" data-dismiss="modal">No</button>
                        <button type="submit" class="btn btn-primary" href="">Yes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endforeach