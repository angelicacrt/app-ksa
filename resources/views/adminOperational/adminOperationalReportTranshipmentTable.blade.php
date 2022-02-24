@if(empty($operationalData))
    <h1 class="text-center">No Data Found</h1>
@else
    <div class="table-wrapper-scroll-y my-custom-scrollbar">
        @if(count($operationalData) > 0)
            <div class="d-flex justify-content-center">
                <form action="/admin-operational/daily-reports/download" method="POST">
                    @csrf

                    <input type="hidden" name="tugName" value="{{ $tugName }}">
                    <input type="hidden" name="bargeName" value="{{ $bargeName }}">
                    <input type="hidden" name="taskType" value="{{ $taskType }}">
                    <input type="hidden" name="month" value="{{ $month }}">
                    <input type="hidden" name="year" value="{{ $year }}">

                    <button class="btn btn-danger btn-lg mt-2" type="submit">Download</button>
                </form>
            </div>
        @endif
        <table class="table">
            <thead class="thead bg-danger">
            <tr>
                <th scope="col">No</th>
                <th scope="col">Tug</th>
                <th scope="col">Barge</th>
                <th scope="col">Periode</th>
                <th scope="col">From-To</th>
                <th scope="col">Jenis Kegiatan</th>
                @if($taskType != 'Non Operational')
                    <th scope="col">Jumlah Kargo Akhir</th>
                @endif
                @if($taskType == 'Operational Shipment')
                    <th scope="col">Customer</th>
                    <th scope="col">Total Days</th>
                @else
                    <th scope="col">Estimasi</th>
                @endif
            </tr>
            </thead>
            <tbody>
                @forelse($operationalData as $key => $od)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $od -> tugName }}</td>
                        <td>{{ $od -> bargeName }}</td>
                        <td>{{ $od -> created_at -> format('M/Y') }}</td>
                        <td>{{ $od -> from . ' - ' . $od -> to }}</td>
                        <td>{{ $od -> taskType }}</td>
                        @if($od -> taskType != 'Non Operational')
                            @if($od -> taskType == 'Return Cargo')
                                <td>{{ $od -> cargoAmountEndCargo }} Ton</td>
                            @else
                                <td>{{ $od -> cargoAmountEnd }} Ton</td>
                            @endif
                        @endif
                        @if($taskType == 'Operational Shipment')
                            <td>{{ $od -> customer }}</td>
                            <td>{{ $od -> totalTime }}</td>
                        @else
                            <td>{{ $od -> estimatedTime }}</td>
                        @endif
                    </tr>
                @empty
                    <h5>No Data Found</h5>
                @endforelse
            </tbody>
        </table>
    </div>
@endif

<style>
    .my-custom-scrollbar {
        position: relative;
        height: 800px;
        overflow: auto;
    }
    .table-wrapper-scroll-y {
        display: block;
    }
    thead th { 
        position: sticky; 
        top: 0; 
        z-index: 1; 
    }
    th{
        color: white;
    }
    td, th{
        word-wrap: break-word;
        min-width: 150px;
        max-width: 150px;
        text-align: center;
    }
</style>