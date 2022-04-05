<?php

namespace App\Exports;

use App\Models\User;
use App\Models\documents;
use App\Models\documentberau;
use App\Models\documentbanjarmasin;
use App\Models\documentsamarinda;
use App\Models\documentJakarta;
use App\Models\documentrpk;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromCollection;
use PHPExcel_Worksheet_PageSetup;

class RekapDocument implements FromQuery , ShouldAutoSize , WithHeadings , WithEvents
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct($cabang_download)
    {
        $this->cabang = $cabang_download;
    }

    public function headings(): array
    {
        return[
            //title
            [
               'Rekap Document fund Request'
            ],           
            [
            'No.',
            'cabang', 
            'Nama Upload',	
            'nama kapal',	
            'approved by',	
            'periode awal',	
            'periode akhir',	
            'time upload', 
            'reason',
            'status',
            'dana',	
            'Nama Sertifikat',
            ]
        ];
    }

    public function query()
    {
        if ($this->cabang == 'Babelan') {
            # code...
        }
        elseif ($this->cabang == 'Berau') {
            # code...
        }
        elseif ($this->cabang == 'Banjarmasin') {
            # code...
        }
        elseif ($this->cabang == 'Samarinda' or $this->cabang == 'Kendari' or $this->cabang == 'Morosi') {
            # code...
        }
        elseif ($this->cabang == 'Jakarta') {
            # code...
        }
        DB::statement(DB::raw('set @row:=0'));
        $rekap = documents::where('header_id', $this->file_id)
        ->selectRaw('*, @row:=@row+1 as id')->get();
        return $formclaim;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event){
                $event->sheet->mergeCells('A1:F1');
                $event->sheet->getStyle('A1:F1')->applyFromArray([
                    'font' => [
                        'bold' => true ,
                        // 'color' => ['argb' => 'ffffffff']
                    ]
                ]);
                $event->sheet->getStyle('A6:C6')->applyFromArray([
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
                $event->sheet->getDelegate()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A3);
                $event->sheet->getDelegate()->getStyle('A:F')
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
