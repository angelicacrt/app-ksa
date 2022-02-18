<?php

namespace App\Exports;

use App\Models\OperationalBoatData;
use App\Models\Tug;
use App\Models\Barge;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class DailyReportsExport implements FromQuery, WithHeadings, ShouldAutoSize, WithEvents, WithStrictNullComparison
{
    use Exportable;

    public function __construct($taskType, $tugName, $bargeName, $month, $year)
    {
        $this -> taskType = $taskType;
        $this -> tugName = $tugName;
        $this -> bargeName = $bargeName;
        $this -> month = $month;
        $this -> year = $year;
    }


    public function query()
    {  
        if($this -> taskType == 'Operational Transhipment'){
            $operationalData = OperationalBoatData::
            where('status', 'Finalized')
            ->where(function ($query){
                $query
                ->where('taskType', 'Operational Transhipment')
                ->orWhere('taskType', 'Return Cargo');
            })
            ->where('tugName', $this -> tugName)
            ->where('bargeName', $this -> bargeName)
            ->whereMonth('operational_boat_data.created_at', $this -> month)
            ->whereYear('operational_boat_data.created_at', $this -> year)
            ->select('tugName', 'bargeName', 'from', 'to', DB::raw('CONCAT(MONTHNAME(operational_boat_data.created_at),"/",YEAR(operational_boat_data.created_at)) as Shipment'), 'cargoAmountEnd', 'cargoAmountEndCargo', 'portOfLoading', 'portOfDischarge', 'faVessel',
                    'departureJetty', 'pengolonganNaik', 'arrivalPOL', 'startAsideL', 'asideL', 'commenceLoadL', 'completedLoadingL', 'cOffL', 'pengolonganTurun', 'mooringArea', 'DOH', 'DOB', 'departurePOD', 'arrivalPODGeneral', 'arrivalPODCargo', 'startAsideMVTranshipment', 'startAsideMVCargo', 'asideMVTranshipment', 'asideMVCargo', 'commMVTranshipment', 'commMVCargo', 'compMVTranshipment', 'compMVCargo', 'cOffMVTranshipment', 'cOffMVCargo', 'departureTimeTranshipment', 'departureTime', 'sailingToJetty', 'berthing', 'prepareLdg', 'ldgTime', 'ldgRate', 'unberthing', 'document', 'sailingToMV', 'sailingToMVCargo', 'maneuver', 'maneuverCargo', 'dischTime', 'dischTimeCargo', 'dischRate', 'dischRateCargo', 'cycleTime', 'cycleTimeCargo');
        }elseif($this -> taskType == 'Non Operational'){
            $operationalData = OperationalBoatData::
            where('status', 'Finalized')
            ->where('taskType', 'Non Operational')
            ->where('tugName', $this -> tugName)
            ->where('bargeName', $this -> bargeName)
            ->whereMonth('operational_boat_data.created_at', $this -> month)
            ->whereYear('operational_boat_data.created_at', $this -> year)
            ->select('tugName', 'bargeName', 'from', 'to', DB::raw('CONCAT(MONTHNAME(operational_boat_data.created_at),"/",YEAR(operational_boat_data.created_at)) as Shipment'), 'cargoAmountStart', 'portOfLoading', 'portOfDischarge', 'arrivalTime', 'startDocking', 'finishDocking', 'departurePOL', 'totalLostDays');
        }else{
            $operationalData = OperationalBoatData::
            where('status', 'Finalized')
            ->where('tugName', $this -> tugName)
            ->where('bargeName', $this -> bargeName)
            ->where('taskType', 'Operational Shipment')
            ->whereMonth('operational_boat_data.created_at', $this -> month)
            ->whereYear('operational_boat_data.created_at', $this -> year)
            ->select('tugName', 'bargeName', 'from', 'to', 'customer', DB::raw('CONCAT(MONTHNAME(operational_boat_data.created_at),"/",YEAR(operational_boat_data.created_at)) as Shipment'), 'portOfLoading', 'portOfDischarge', 'departureJetty', 'pengolonganNaik', 'arrivalTime', 'startAsideL', 'asideL', 'commenceLoadL', 'completedLoadingL', 'cOffL', 'pengolonganTurun', 'mooringArea', 'DOH', 'DOB', 'departurePOD', 'arrivalPODGeneral', 'startAsidePOD', 'asidePod', 'commenceLoadPOD', 'completedLoadingPOD', 'cOffPOD', 'departureTime', 'totalTime');
        }

        return $operationalData;
    }

    public function headings(): array{
        if($this -> taskType == 'Operational Transhipment'){
            return [
                // Title
                ['Report Export'],
                // Pre Data
                [' '],
                ['Tug/Barge : ', $this -> tugName . '/' . $this -> bargeName],
                [' '],
                // Table Data
                ['Tug', 'Barge', 'From', 'To', 'Shipment', 'Quantity Transhipment', 'Quantity Return Cargo', 'Port Of Loading', 'Port Of Discharge', 
                'F/A Vessel (Arrival Pangkalan)', 'Departure To Jetty', 'Pengolongan Naik', 'Arrival POL', 
                'Start Aside (L)', 'Aside (L)', 'Commence Loading (L)', 'Complete Loading (L)', 'Cast Off (L)', 'Pengolongan Turun', 'Mooring Area',
                'D.O.H', 'D.O.B', 'Departure POD', 'Arrival POD Transhipment', 'Arrival POD Return Cargo', 'Start Aside (MV) Transhipment', 'Start Aside (MV) Return Cargo', 'Aside (MV) Transhipment', 'Aside (MV) Cargo', 'Commence Loading (MV) Transhipment', 'Commence Loading (MV) Cargo', 'Complete Loading (MV) Transhipment', 'Complete Loading (MV) Cargo', 'Cast Off (MV) Transhipment', 'Cast Off (MV) Cargo', 'Departure To (Departure Pangkalan)', 'Departure To (Cargo)',
                'Sailing To Jetty', 'Berthing', 'Prepare Ldg', 'Ldg Time', 'Ldg Rate', 'Unberthing', 'Document', 'Sailing To MV Transhipment', 'Sailing To MV Cargo', 'Maneuver Transhipment', 'Maneuver Cargo', 'Disch Time Transhipment', 'Disch Time Cargo', 'Disch Rate / Day Transhipment', 'Disch Rate / Day Cargo', 'Cycle Time Transhipment', 'Cycle Time Cargo']
            ];
        }elseif($this -> taskType == 'Non Operational'){
            return [
                // Title
                ['Report Export'],

                // Pre Data
                [' '],
                ['Tug/Barge : ', $this -> tugName . '/' . $this -> bargeName],
                [' '],

                // Table Data
                ['Tug', 'Barge', 'From', 'To', 'Shipment', 'Cargo', 'Port Of Loading', 'Port Of Discharge', 'Time Arrival', 'Start Docking', 'Finish Docking', 'Departure POL', 'Total Lost Time']
            ];
        }else{
            return [
                // Title
                ['Report Export'],

                // Pre Data
                [' '],
                ['Tug/Barge : ', $this -> tugName . '/' . $this -> bargeName],
                [' '],

                // Table Data
                ['Tug', 'Barge', 'From', 'To', 'Customer', 'Shipment', 'Port Of Loading', 'Port Of Discharge',
                'Departure Jetty', 'Pengolongan Naik', 'Time Arrival', 
                'Start Aside (L)', 'Aside (L)', 'Commence Loading (L)', 'Complete Loading (L)', 'Cast Off (L)', 'Pengolongan Turun', 'Mooring Area',
                'Doc Overhand', 'Doc On Boat', 'Departure to POD', 'Arrival POD', 'Start Aside POD', 'Aside POD', 'Commence Loading POD', 'Complete Loading POD', 'Cast Off POD', 'Departure Time', 
                'totalTime']
            ];
        }
    }
    
    public function registerEvents(): array
    {
        if($this -> taskType == 'Operational Transhipment'){
            return [
                AfterSheet::class => function (AfterSheet $event) {
                    $event->sheet->getStyle('A5:BB5')->applyFromArray([
                        'font' => [
                            'color' => ['argb' => 'FFFFFF']
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['argb' => 'A01D23']
                        ]
    
                    ]);
                }
            ];
        }elseif($this -> taskType == 'Operational Shipment'){
            return [
                AfterSheet::class => function (AfterSheet $event) {
                    $event->sheet->getStyle('A5:AC5')->applyFromArray([
                        'font' => [
                            'color' => ['argb' => 'FFFFFF']
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['argb' => 'A01D23']
                        ]
    
                    ]);
                }
            ];
        }else{
            return [
                AfterSheet::class => function (AfterSheet $event) {
                    $event->sheet->getStyle('A5:M5')->applyFromArray([
                        'font' => [
                            'color' => ['argb' => 'FFFFFF']
                        ],
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'color' => ['argb' => 'A01D23']
                        ]
    
                    ]);
                }
            ];
        }

    }
}
