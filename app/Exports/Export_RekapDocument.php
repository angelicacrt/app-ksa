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
use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\FromView;
use PHPExcel_Worksheet_PageSetup;

class Export_RekapDocument implements FromView , ShouldAutoSize , WithEvents , WithHeadings
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct($cabang_download , $cabang_PR)
    {
        $this->cabang = $cabang_download;
        $this->noPR = $cabang_PR;
        $this->name = Auth::user()->name;
    }

    public function headings(): array
    {
        return[
            //title
            [
               'Nomor PR :', $this->noPR
            ],           
        ];
    }

    public function view(): View
    {
        $year = date('Y');
        if ($this->cabang == 'Babelan') {
            return view('picadmin.export_rekap-doc', [
                'document' => documents::where('upload_type','Fund_Req')
                ->where('no_PR', $this->noPR)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at' , date('m'))
                ->latest()
                ->get()->filter(function ($value) { return !empty($value); }),

                'documentberau' => documentberau::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }) ,
                'documentbanjarmasin' => documentbanjarmasin::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }) ,
                'documentsamarinda' => documentsamarinda::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
                'documentjakarta' => documentjakarta::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
            ]);
        }
        elseif ($this->cabang == 'Berau') {
            return view('picadmin.export_rekap-doc', [
                'documentberau' => documentberau::where('upload_type','Fund_Req')
                ->where('no_PR', $this->noPR)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at' , date('m'))
                ->latest()
                ->get()->filter(function ($value) { return !empty($value); }) ,

                'document' => documents::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
                'documentbanjarmasin' => documentbanjarmasin::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }) ,
                'documentsamarinda' => documentsamarinda::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
                'documentjakarta' => documentjakarta::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
            ]);
        }
        elseif ($this->cabang == 'Banjarmasin') {
            return view('picadmin.export_rekap-doc', [
                'documentbanjarmasin' => documentbanjarmasin::where('upload_type','Fund_Req')
                ->where('no_PR', $this->noPR)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at' , date('m'))
                ->latest()->get()->filter(function ($value) { return !empty($value); }) ,

                'document' => documents::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
                'documentberau' => documentberau::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }) ,
                'documentsamarinda' => documentsamarinda::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
                'documentjakarta' => documentjakarta::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
            ]);
        }
        elseif ($this->cabang == 'Samarinda' or $this->cabang == 'Kendari' or $this->cabang == 'Morosi') {
            return view('picadmin.export_rekap-doc', [
                'documentsamarinda' => documentsamarinda::where('upload_type','Fund_Req')
                ->where('no_PR', $this->noPR)
                ->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))
                ->latest()->get()->filter(function ($value) { return !empty($value); }),

                'document' => documents::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
                'documentberau' => documentberau::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }) ,
                'documentbanjarmasin' => documentbanjarmasin::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }) ,
                'documentjakarta' => documentjakarta::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
            ]);
        }
        elseif ($this->cabang == 'Jakarta') {
            return view('picadmin.export_rekap-doc', [
                'documentjakarta' => documentjakarta::where('upload_type','Fund_Req')
                ->where('no_PR', $this->noPR)
                ->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))
                ->latest()->get()->filter(function ($value) { return !empty($value); }),

                'document' => documents::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
                'documentberau' => documentberau::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }) ,
                'documentbanjarmasin' => documentbanjarmasin::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }) ,
                'documentsamarinda' => documentsamarinda::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->where('no_PR', $this->noPR)->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
            ]);
        }
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event){
                $event->sheet->getStyle('A2:I2')->applyFromArray([
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
                    array(' '),
                    array('Prepared by:' , ' ' , ' ' , ' ' ,' ' ,'Checked by :'),
                    array(' '),
                    array(' '),
                    array(' '),
                    array($this->name, ' ' , ' ' , ' ' ,' ' , 'Yusmiati'),
                ), $event);
                $event->sheet->getDelegate()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A3);
                $event->sheet->getDelegate()->getStyle('A:F')
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
