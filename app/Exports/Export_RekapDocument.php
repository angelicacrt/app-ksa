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
use Illuminate\Contracts\View\View;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;
use PHPExcel_Worksheet_PageSetup;

class Export_RekapDocument implements FromView , ShouldAutoSize , WithEvents
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */

    public function __construct($cabang_download)
    {
        $this->cabang = $cabang_download;
        $this->name = Auth::user()->name;
    }

    // public function headings(): array
    // {
    //     return[
    //         //title
    //         [
    //            'Rekap Document fund Request'
    //         ],           
    //         [
    //         'No.',
    //         'cabang', 
    //         'Nama Upload',	
    //         'nama kapal',	
    //         'status',
    //         'approved by',	
    //         'periode awal',	
    //         'periode akhir',	
    //         'reason',
    //         'dana',	
    //         'Nama Sertifikat',
    //         ]
    //     ];
    // }

    public function view(): View
    {
        $year = date('Y');
        if ($this->cabang == 'Babelan') {
            return view('picadmin.export_rekap-doc', [
                'document' => documents::where('upload_type','Fund_Req')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at' , date('m'))
                ->latest()
                ->get()->filter(function ($value) { return !empty($value); }),

                'documentberau' => documentberau::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }) ,
                'documentbanjarmasin' => documentbanjarmasin::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }) ,
                'documentsamarinda' => documentsamarinda::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
                'documentjakarta' => documentjakarta::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
            ]);
        }
        elseif ($this->cabang == 'Berau') {
            return view('picadmin.export_rekap-doc', [
                'documentberau' => documentberau::where('upload_type','Fund_Req')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at' , date('m'))
                ->latest()
                ->get()->filter(function ($value) { return !empty($value); }) ,

                'document' => documents::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
                'documentbanjarmasin' => documentbanjarmasin::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }) ,
                'documentsamarinda' => documentsamarinda::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
                'documentjakarta' => documentjakarta::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
            ]);
        }
        elseif ($this->cabang == 'Banjarmasin') {
            return view('picadmin.export_rekap-doc', [
                'documentbanjarmasin' => documentbanjarmasin::where('upload_type','Fund_Req')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at' , date('m'))
                ->latest()->get()->filter(function ($value) { return !empty($value); }) ,

                'document' => documents::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
                'documentberau' => documentberau::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }) ,
                'documentsamarinda' => documentsamarinda::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
                'documentjakarta' => documentjakarta::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
            ]);
        }
        elseif ($this->cabang == 'Samarinda' or $this->cabang == 'Kendari' or $this->cabang == 'Morosi') {
            return view('picadmin.export_rekap-doc', [
                'documentsamarinda' => documentsamarinda::where('upload_type','Fund_Req')
                ->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))
                ->latest()->get()->filter(function ($value) { return !empty($value); }),

                'document' => documents::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
                'documentberau' => documentberau::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }) ,
                'documentbanjarmasin' => documentbanjarmasin::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }) ,
                'documentjakarta' => documentjakarta::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
            ]);
        }
        elseif ($this->cabang == 'Jakarta') {
            return view('picadmin.export_rekap-doc', [
                'documentjakarta' => documentjakarta::where('upload_type','Fund_Req')
                ->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))
                ->latest()->get()->filter(function ($value) { return !empty($value); }),

                'document' => documents::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
                'documentberau' => documentberau::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }) ,
                'documentbanjarmasin' => documentbanjarmasin::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }) ,
                'documentsamarinda' => documentsamarinda::where('cabang',$this->cabang)->where('upload_type','Fund_Req')->whereYear('created_at', $year)->whereMonth('created_at' , date('m'))->latest()->get()->filter(function ($value) { return !empty($value); }),
            ]);
        }
    }

    // public function Collection()
    // {
    //     $month = date('m');
    //     $year = date('Y');
    //     if ($this->cabang == 'Babelan') {

        //         $BABELAN = array('sertifikat_keselamatan',
        //         'sertifikat_garis_muat','penerbitan_sekali_jalan','sertifikat_safe_manning',
        //         'endorse_surat_laut','perpanjangan_sertifikat_sscec','perpanjangan_sertifikat_p3k' ,
        //         'biaya_laporan_dok','pnpb_sertifikat_keselamatan','pnpb_sertifikat_garis_muat',
        //         'pnpb_surat_laut','sertifikat_snpp','sertifikat_anti_teritip',    
        //         'pnbp_snpp&snat','biaya_survey' ,'pnpb_sscec', 'bki_lambung', 'bki_mesin', 'bki_Garis_muat',
        //         'Sertifikat_Konstruksi_Kapal_Barang' , 'Sertifikat_Radio_Kapal_Barang' , 'PNBP_Safe_Maning' , 'Lain_Lain1' , 'Lain_Lain2');
        //         for ( $a = 1 ; $a <= 24 ; $a++){
        //             $stats ="status".$a;
        //             $viewdoc = $BABELAN[$a-1];
        //         } 
        //         DB::statement(DB::raw('set @row:=0'));
        //         $rekap = documents::whereMonth('created_at', $month)
        //         ->whereYear('created_at', $year)
        //         ->where('upload_type','Fund_Req')
        //         ->selectRaw('*, @row:=@row+1 as id')->get();
        //         return $rekap;
        //     }
    //     elseif ($this->cabang == 'Berau') {
        //         $viewer = documentberau::whereDate('periode_akhir', '<', $datetime)
        //         ->whereNotNull ($filename)
        //         ->where('upload_type','Fund_Req')
        //         ->where($filename, 'Like', '%' . $result . '%')
        //         ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
        //         ->pluck($filename)[0];
        //         // dd($viewer);
        //         return $rekap;
        //     }
    //     elseif ($this->cabang == 'Banjarmasin') {
        //         $viewer = documentbanjarmasin::whereDate('periode_akhir', '<', $datetime)
        //                 ->whereNotNull ($filename)
        //                 ->where('upload_type','Fund_Req')
        //                 ->where('cabang' , $request->cabang)
        //                 ->where($filename, 'Like', '%' . $result . '%')
        //                 ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
        //                 ->pluck($filename)[0];
        //         return $rekap;
        //     }
    //     elseif ($this->cabang == 'Samarinda' or $this->cabang == 'Kendari' or $this->cabang == 'Morosi') {
        //         $viewer = documentsamarinda::whereDate('periode_akhir', '<', $datetime)
        //                 ->whereNotNull ($filename)
        //                 ->where('upload_type','Fund_Req')
        //                 ->where('cabang' , $request->cabang)
        //                 ->where($filename, 'Like', '%' . $result . '%')
        //                 ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
        //                 ->pluck($filename)[0];
        //         return $rekap;
        //     }
    //     elseif ($this->cabang == 'Jakarta') {
        //         $viewer = documentJakarta::whereDate('periode_akhir', '<', $datetime)
        //         ->whereNotNull ($filename)
        //         ->where('upload_type','Fund_Req')
        //         ->where($filename, 'Like', '%' . $result . '%')
        //         ->where('nama_kapal', 'Like', '%' . $kapal_id . '%')
        //         ->pluck($filename)[0];
        //         return $rekap;
        //     }
    // }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event){
                $event->sheet->getStyle('A1:K1')->applyFromArray([
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
