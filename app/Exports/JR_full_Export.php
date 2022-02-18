<?php

namespace App\Exports;

use App\Models\User;
use App\Models\JobHead;
use App\Models\JobDetails;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
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

class JR_full_Export implements FromQuery , ShouldAutoSize , WithHeadings , WithEvents
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */

    public function query()
    {
        $month_now = (int)(date('m'));

        if($month_now <= 3){
            $start_date = date('Y-01-01');
            $end_date = date('Y-03-31');
            $str_month = 'Jan - Mar';
        }elseif($month_now > 3 && $month_now <= 6){
            $start_date = date('Y-04-01');
            $end_date = date('Y-06-30');
            $str_month = 'Apr - Jun';
        }elseif($month_now > 6 && $month_now <= 9){
            $start_date = date('Y-07-01');
            $end_date = date('Y-09-30');
            $str_month = 'Jul - Sep';
        }else{
            $start_date = date('Y-10-01');
            $end_date = date('Y-12-31');
            $str_month = 'Okt - Des';
        }
      
        // $users = User::whereHas('roles', function($query){
        //     $query->where('name', 'logistic');
        // })->where('cabang', 'like', Auth::user()->cabang)->where('cabang', 'like', Auth::user()->cabang)->pluck('users.id');
        

        DB::statement(DB::raw('set @row:=0'));
        $jobs = JobHead::join('job_details', 'job_details.jasa_id', '=', 'job_heads.id')
        ->where('job_details.cabang', Auth::user()->cabang)
        ->where('job_heads.status', 'like', 'Job Request Approved By Logistics')
        ->whereBetween('job_heads.created_at', [$start_date, $end_date])
        ->selectRaw('*, @row:=@row+1 as job_heads.id')
        ->select(
        'job_heads.id',
        'Headjasa_id',
        'noJr',
        'jrDate',
        'cabang',
        'job_details.lokasi',
        'job_details.tugName',
        'job_details.bargeName',
        'job_details.note',
        'job_details.quantity');

        // dd($jobs);

        return $jobs;
    }
    
    public function headings(): array
    {
        return[
            //title
            [
               'FORM Permintaan Perbaikan'
            ],
            // table data
            [''],
            [
            'No.',
            'JR-ID :',
            'Tgl Permintaan :',
            'NOMOR-JR :',
            'Dibuat Oleh :',
            'Cabang',
            'Lokasi Permintaan :',
            'Nama TugBoat:',
            'Nama Barge:',
            'Uraian',
            'Quantity' ,
            ]
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event){
                $event->sheet->mergeCells('A1:K1');
                $event->sheet->mergeCells('A2:K2');
                $event->sheet->getStyle('A1:K1')->applyFromArray([
                    'font' => [
                        'bold' => true ,
                        // 'color' => ['argb' => 'ffffffff']
                    ]
                ]);
                $event->sheet->getStyle('A3:K3')->applyFromArray([
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
                // $event->sheet->appendRows(array(
                //     array(' '),
                //     array($this->created_at),
                //     array('Prepared by:' , ' ' , ' Disetujui :' , ' ' ,' ' ,'Diketahui :'),
                //     array(' '),
                //     array(' '),
                //     array(' '),
                //     array($this->created_by, ' ' , $this->check_by , ' ' ,' ' , 'maintance'),
                // ), $event);
                $event->sheet->getDelegate()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A3);
                // $event->sheet->getDelegate()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $event->sheet->getDelegate()->getStyle('A:K')
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
