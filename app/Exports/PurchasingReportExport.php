<?php

namespace App\Exports;

use Illuminate\Support\Facades\Auth;
use App\Models\OrderHead;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\Exportable;

class PurchasingReportExport implements FromQuery, WithHeadings, ShouldAutoSize, WithEvents
{
    use Exportable;

    public function __construct(string $default_branch){
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

        // Find order from logistic role but with different setup with the goods in
        $users = User::whereHas('roles', function($query){
            $query->where('name', 'logistic')->orWhere('name', 'supervisorLogistic')->orWhere('name', 'supervisorLogisticMaster');
        })->where('cabang', $this->default_branch)->pluck('users.id');
        
        // Report for each role is different, adjust the report based on the auth role
        if(Auth::user()->hasRole('logistic')){

            // Find all the items that has been approved from the user | last 30 days only
            return OrderHead::whereIn('user_id', $users)->where('cabang', $this->default_branch)->join('order_details', 'order_heads.id', '=', 'order_details.orders_id')->where('status', 'like', 'Order Completed (Logistic)')->whereBetween('order_heads.created_at', [$start_date, $end_date])->select('prDate', 'noPr', 'supplier', 'noPo', 'boatName', 'descriptions')->orderBy('order_heads.updated_at', 'desc');

        }elseif(Auth::user()->hasRole('supervisor') or Auth::user()->hasRole('supervisorLogisticMaster')){

            // Find all the items that has been approved from the user | last 30 days only
            return OrderHead::whereIn('user_id', $users)->where('cabang', $this->default_branch)->join('order_details', 'order_heads.id', '=', 'order_details.orders_id')->where(function($query){
                $query->where('status', 'like', 'Order Completed (Logistic)')
                    ->orWhere('status', 'like', '%' . 'In Progress By Purchasing' . '%')
                    ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
                    ->orWhere('status', 'like', '%' . 'Revised' . '%')
                    ->orWhere('status', 'like', '%' . 'Finalized' . '%')
                    ->orWhere('status', 'like', 'Item Delivered By Supplier');
            })->whereBetween('order_heads.created_at', [$start_date, $end_date])->select('prDate', 'noPr', 'supplier', 'noPo', 'boatName', 'descriptions')->orderBy('order_heads.updated_at', 'desc');

        }elseif(Auth::user()->hasRole('purchasing') or Auth::user()->hasRole('purchasingManager')){

            // Find all the items that has been approved from the user | last 30 days only
            return OrderHead::whereIn('user_id', $users)->where('cabang', $this->default_branch)->join('order_details', 'order_heads.id', '=', 'order_details.orders_id')->where(function($query){
                $query->where('status', 'like', 'Order Completed (Logistic)')
                    ->orWhere('status', 'like', '%' . 'Revised' . '%')
                    ->orWhere('status', 'like', '%' . 'Rechecked' . '%')
                    ->orWhere('status', 'like', '%' . 'Finalized' . '%')
                    ->orWhere('status', 'like', 'Item Delivered By Supplier');
            })->whereBetween('order_heads.created_at', [$start_date, $end_date])->select('prDate', 'noPr', 'supplier', 'noPo', 'boatName', 'descriptions')->orderBy('order_heads.updated_at', 'desc');

        }
    }

    public function headings(): array{
        return [
            // Table
            ['Tanggal PR', 'Nomor PR', 'Supplier', 'Nomor PO', 'Nama Kapal', 'Keterangan']
        ];
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:F1')->applyFromArray([
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