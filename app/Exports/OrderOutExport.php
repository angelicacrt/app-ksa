<?php

namespace App\Exports;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderDetail;
use App\Models\User;
Use \Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class OrderOutExport implements FromQuery, WithHeadings, ShouldAutoSize, WithEvents
{
    public function query()
    {
        // Find order from user/goods out
        $users = User::join('role_user', 'role_user.user_id', '=', 'users.id')->where('role_user.role_id' , '=', '2')->where('cabang', 'like', Auth::user()->cabang)->pluck('users.id');

        return OrderDetail::query()->join('order_heads', 'order_heads.id', '=', 'order_details.orders_id')->join('items', 'items.id', 'order_details.item_id')->whereIn('user_id', $users)->select('approved_at', 'itemName', 'items.serialNo', 'quantity', 'items.unit', 'noResi', 'descriptions')->where('order_heads.cabang', 'like', Auth::user()->cabang,)->where('status', 'like', 'Request Completed (Crew)')->whereMonth('order_heads.created_at', date('m'))->whereYear('order_heads.created_at', date('Y'))->orderBy('order_heads.updated_at', 'desc');
    }

    public function headings(): array{
        return [
            // Table
            ['Tanggal Keluar', 'Item Barang Keluar', 'Serial Number', 'Qty', 'Satuan', 'No. Resi', 'Note']
        ];
    }
    
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:G1')->applyFromArray([
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
