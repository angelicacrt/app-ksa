<?php

namespace App\Exports;

use App\Models\User;
use App\Models\JobHead;
use App\Models\JobDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use PHPExcel_Worksheet_PageSetup;

class JOExport implements FromQuery , ShouldAutoSize , WithHeadings , WithEvents
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($id)
    {
        $this->identify = $id;
        $this->created_by = JobHead::where('id','=', $this->identify)->pluck('created_by')[0];
        $this->check_by = JobHead::where('id','=', $this->identify)->pluck('check_by')[0];
        $this->jrDate = JobHead::where('id','=', $this->identify)->pluck('jrDate')[0];
        $this->noJr = JobHead::where('id','=', $this->identify)->pluck('noJr')[0];
        $this->JO_id = JobHead::where('id','=', $this->identify)->pluck('JO_id')[0];
        $this->Approved_by = JobHead::where('id','=', $this->identify)->pluck('Approved_by')[0];
        
        $this->company = JobHead::where('id','=', $this->identify)->pluck('company')[0];
        $this->invoiceAddress = JobHead::where('id','=', $this->identify)->pluck('invoiceAddress')[0];
        $this->ppn = JobHead::where('id','=', $this->identify)->pluck('ppn')[0];
        $this->totalPrice = JobHead::where('id','=', $this->identify)->pluck('totalPrice')[0];
        
        $this->supplier = JobDetails::where('jasa_id','=', $this->identify)->pluck('supplier')[0];
        $this->cabang = JobDetails::where('jasa_id','=', $this->identify)->pluck('cabang')[0];
        $this->barge = JobDetails::where('jasa_id','=', $this->identify)->pluck('bargeName')[0];
        $this->tugBoat = JobDetails::where('jasa_id','=', $this->identify)->pluck('tugName')[0];
        $this->lokasi = JobDetails::where('jasa_id','=', $this->identify)->pluck('lokasi')[0];
        $this->quantity = JobDetails::where('jasa_id','=', $this->identify)->pluck('quantity')[0];
        $this->note = JobDetails::where('jasa_id','=', $this->identify)->pluck('note')[0];
    }

    public function headings(): array
    {
        return[
            //title
            [
               'FORM Permintaan Perbaikan'
            ],
            [''],
            // table data
            ['Perusahaan :',$this->company],
            ['Nomor JO:',$this->JO_id , ' '  , 'Nomor JR:' , $this->noJr],
            ['Nama TugBoat:',$this->tugBoat , ' '  , 'TGL Permintaan :' , $this->jrDate],
            ['Nama Barge:',$this->barge , ' ' , 'Lokasi Permintaan :' , $this->lokasi],
            ['Invoice Address :',$this->invoiceAddress],
            [''],
            [
            'No.',
            'Uraian',
            'Quantity' ,
            'Harga Job' ,
            'total Harga' ,
            ]
        ];
    }

    public function query()
    {  
        
        $JobRequestHeads = JobHead::with('user')
        ->where('id', '=', $this->identify)
        ->where('status', '=', 'Job Request Approved By Purchasing')
        ->orwhere('status', '=', 'Job Request Completed')
        ->whereYear('created_at', date('Y'));
        
        $job_id = $JobRequestHeads->pluck('id');

        DB::statement(DB::raw('set @row:=0'));
        $jobs = JobDetails::whereIn('jasa_id', $job_id)
        ->where('job_State', '=', 'Accepted')
        ->selectRaw('*, @row:=@row+1 as id')
        ->select('id','note','quantity','HargaJob','totalHargaJob');
        // ->get();

        // dd($jobs);
        return $jobs;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event){
                $event->sheet->mergeCells('A1:E1');
                $event->sheet->getStyle('A1:E1')->applyFromArray([
                    'font' => [
                        'bold' => true ,
                        // 'color' => ['argb' => 'ffffffff']
                    ]
                ]);
                $event->sheet->getStyle('A9:E9')->applyFromArray([
                    'font' => [
                        'bold' => true ,
                        // 'color' => ['argb' => 'ffffffff']
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFFF8080']
                    ],
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FF000000'],
                    ]]
                ]);
                // Append row as very last
                $event->sheet->appendRows(array(
                    array(' '),
                    array('supplier :' , $this->supplier , ' ' , ' PPN :' , $this->ppn . '%'),
                    array(' ',' ' , ' ' , ' Grand Total :' , $this->totalPrice),
                    array(' '),
                    array('cabang :' ,$this->cabang) ,
                    array(' '),
                    array('Diajukan Oleh:' , ' Prepared by:' , ' Disetujui :' , ' '  ,'Diketahui :'),
                    array(' '),
                    array(' '),
                    array($this->Approved_by , $this->created_by, 'Endah Kusumaningtyas' , ' ' ,'maintance'),
                ), $event);
                $event->sheet->getDelegate()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A3);
                $event->sheet->getDelegate()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $event->sheet->getDelegate()->getStyle('A:E')
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
