<?php

namespace App\Exports;

use App\Models\FCIexports;
use App\Models\formclaims;
use App\Models\headerformclaim;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class FCIexport implements FromCollection , ShouldAutoSize , WithHeadings , WithEvents 
// , WithCustomStartCell
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */

    // public function startCell(): string
    // {
    //     return 'A12';
    // }

    public function __construct($identify)
    {
        $this->file_id = $identify;
        $this->created_at = formclaims::where('header_id', $identify)->pluck('created_at')[0];
        $this->name = formclaims::where('header_id', $identify)->pluck('name')[0];
        $this->no_FormClaim = formclaims::where('header_id', $identify)->pluck('no_FormClaim')[0];
        $this->tgl_formclaim = formclaims::where('header_id', $identify)->pluck('tgl_formclaim')[0];
        $this->tgl_insiden = formclaims::where('header_id', $identify)->pluck('tgl_insiden')[0];
        $this->incident = formclaims::where('header_id', $identify)->pluck('incident')[0];
        $this->surveyor = formclaims::where('header_id', $identify)->pluck('surveyor')[0];
        $this->TSI_Tugboat = formclaims::where('header_id', $identify)->pluck('TSI_Tugboat')[0];
        $this->TSI_barge = formclaims::where('header_id', $identify)->pluck('TSI_barge')[0];
        $this->barge = formclaims::where('header_id', $identify)->pluck('barge')[0];
        $this->tugBoat = formclaims::where('header_id', $identify)->pluck('tugBoat')[0];
    }
    public function Collection()
    {
        DB::statement(DB::raw('set @row:=0'));
        $formclaim = formclaims::where('header_id', $this->file_id)
        ->selectRaw('*, @row:=@row+1 as id')->get();
        return $formclaim;
    }
    public function headings(): array
    {
        return[
            //title
            [
               'FORM CLAIM INSURANCE'
            ],
            //top part
            [' '],
            ['No FormClaim :', $this->no_FormClaim],
            ['tgl FormClaim :', $this->tgl_formclaim],
            ['tgl Insiden :', $this->tgl_insiden],
            ['Incident :', $this->incident],
            ['Surveyor :', $this->surveyor],
            ['TSI Tugboat :', $this->TSI_Tugboat],
            ['TSI barge :', $this->TSI_barge],
            ['Barge :', $this->barge],
            ['TugBoat :', $this->tugBoat],
            [' '],
            // table data
            [
            'No.',
            'jenis_incident',
            'item' ,
            'description',
            'deductible',
            'amount'
            ]
        ];
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
                $event->sheet->getStyle('A13:F13')->applyFromArray([
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
                    array(' ',' ',' ',' ','Total:' , ' '),
                    array('Created At :', $this->created_at),
                    array(' '),
                    array(' '),
                    array('Prepared by:' , ' ' , ' ' , ' ' ,' ' ,'Checked by :'),
                    array(' '),
                    array(' '),
                    array(' '),
                    array($this->name, ' ' , ' ' , ' ' ,' ' , 'Yusmiati'),
                ), $event);
                
                $event->sheet->getDelegate()->getStyle('A:F')
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
