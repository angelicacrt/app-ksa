<?php

namespace App\Exports;

use Illuminate\Support\Facades\Auth;
use App\Models\OrderHead;
use App\Models\ApList;
use App\Models\ApListDetail;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class ReportAPExport implements FromQuery, WithHeadings, ShouldAutoSize, WithEvents
{
    use Exportable;

    public function __construct($default_branch)
    {
        $this->default_branch = $default_branch;
    }

    public function query()
    {
        // Basically the report is created per 3 months, so we divide it into 4 reports
        // Base on current month, then we classified what period is the report
        $month_now = (int)(date('m'));

        if($month_now <= 3){
            $start_date = date('Y-01-01');
            $end_date = date('Y-03-31');
        }elseif($month_now > 3 && $month_now <= 6){
            $start_date = date('Y-04-01');
            $end_date = date('Y-06-30');
        }elseif($month_now > 6 && $month_now <= 9){
            $start_date = date('Y-07-01');
            $end_date = date('Y-09-30');
        }else{
            $start_date = date('Y-10-01');
            $end_date = date('Y-12-31');
        }

        $apList = ApList::with('orderHead')->where('ap_lists.cabang', 'like', $this -> default_branch)->join('ap_list_details', 'ap_list_details.aplist_id', '=', 'ap_lists.id')->join('order_heads', 'order_heads.id', '=', 'ap_lists.order_id')->whereBetween('ap_lists.created_at', [$start_date, $end_date])->select('supplierName', 'noInvoice', 'noFaktur', 'noDo', 'noPo', 'noPr', 'nominalInvoice', 'additionalInformation')->orderBy('ap_lists.created_at', 'desc');

        return $apList;
    }

    public function headings(): array{
        return [
            // Heading
            ['Report AP Export'],

            [' '],

            // Table
            ['Nama Supplier', 'No. Invoice', 'No. Faktur Pajak', 'No. DO', 'No. PO', 'No. PR', 'Nominal Invoice', 'Keterangan']
        ];
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A3:H3')->applyFromArray([
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
