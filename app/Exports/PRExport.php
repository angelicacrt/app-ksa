<?php

namespace App\Exports;

use App\Models\OrderDetail;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;


class PRExport implements FromQuery, WithHeadings, WithEvents
{
    use Exportable;

    public function __construct($order_id)
    {
        $this->order_id = $order_id;
    }

    public function query()
    {
        $orderDetail = OrderDetail::join('order_heads', 'order_details.orders_id', '=', 'order_heads.id')->join('items', 'items.id', '=', 'order_details.item_id')->where('order_heads.order_id', $this->order_id)->where('order_details.orderItemState', '=', 'Accepted')->select('noPr', 'prDate', 'boatName', 'itemName', 'quantity', 'items.unit', 'department', 'codeMasterItem', 'note')->orderBy('itemName');

        return $orderDetail;
    }

    public function headings(): array{
        return [
            // Table
            ['Nomor PR', 'Tanggal PR', 'Nama Kapal', 'Nama Barang', 'Quantity', 'Satuan','Department/Bagian', 'Code Master Item', 'Note']
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:I1')->applyFromArray([
                    // 'font' => [
                    //     'color' => ['argb' => 'FFFFFF']
                    // ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'A01D23']
                    ]
                ]);
                $event->sheet->getDelegate()->getStyle('A:I')
                ->getAlignment()
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER); 
                $event->sheet->getDelegate()->getStyle('A:I')
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER); 
            }
        ];
    }
}
