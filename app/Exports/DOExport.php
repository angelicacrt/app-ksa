<?php

namespace App\Exports;

use App\Models\OrderDo;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class DOExport implements FromQuery, WithHeadings, ShouldAutoSize, WithEvents
{
    use Exportable;

    public function __construct($order_do_id)
    {
        $this->order_do_id = $order_do_id;
    }

    public function query()
    {
        $orderDo = OrderDo::join('items', 'items.id' , '=', 'order_dos.item_requested_id')->join('users', 'users.id', '=', 'order_dos.user_id')->where('order_dos.id', $this->order_do_id)->select('users.name', 'items.itemName', 'fromCabang', 'toCabang', 'quantity', 'items.unit', 'order_dos.description');

        return $orderDo;
    }

    public function headings(): array{
        return [
            // Table
            ['Nama Requester', 'Item Barang', 'Asal Cabang', 'Cabang Tujuan', 'Quantity', 'Satuan', 'description']
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A3:G3')->applyFromArray([
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
