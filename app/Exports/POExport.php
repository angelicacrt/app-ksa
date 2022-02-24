<?php

namespace App\Exports;

use App\Models\OrderDetail;
use App\Models\OrderHead;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;

class POExport implements FromQuery, WithHeadings, ShouldAutoSize, WithEvents
{
    use Exportable, RegistersEventListeners;

    public function __construct($order_id)
    {
        $this->order_id = $order_id;
        $this->price = OrderHead::where('order_id', $order_id)->pluck('totalPrice')[0];
        $this->invoiceAddress = OrderHead::where('order_id', $order_id)->pluck('invoiceAddress')[0];
        $this->company = OrderHead::where('order_id', $order_id)->pluck('company')[0];
        $this->itemAddress = OrderHead::where('order_id', $order_id)->pluck('itemAddress')[0];
        $this->cabang = OrderHead::where('order_id', $order_id)->pluck('cabang')[0];
        $this->ppn = OrderHead::where('order_id', $order_id)->pluck('ppn')[0];
        $this->discount = OrderHead::where('order_id', $order_id)->pluck('discount')[0];
        $this->noPr = OrderHead::where('order_id', $order_id)->pluck('noPr')[0];
        $this->noPo = OrderHead::where('order_id', $order_id)->pluck('noPo')[0];
        $this->boatName = OrderHead::where('order_id', $order_id)->pluck('boatName')[0];
        $this->approvedBy = OrderHead::where('order_id', $order_id)->pluck('approvedBy')[0];
        $this->createdBy = OrderHead::where('order_id', $order_id)->join('users', 'users.id', '=', 'order_heads.user_id')->pluck('name')[0];
    }

    public function query()
    {
        $orderDetail = OrderDetail::join('order_heads', 'order_details.orders_id', '=', 'order_heads.id')->join('items', 'items.id', '=', 'order_details.item_id')->where('order_heads.order_id', $this->order_id)->where('order_details.orderItemState', '=', 'Accepted')->select('itemName', 'quantity', 'items.unit', 'supplier', 'itemPrice', 'totalItemPrice', 'note')->orderBy('itemName');

        return $orderDetail;
    }

    public function headings(): array{
        return [
            // Heading
            ['FORM PO'],

            [''],

            [$this->company],

            [''],

            // Table
            ['Nama Barang', 'Quantity', 'Satuan', 'Nama Supplier', 'Harga Per Satuan Barang', 'Total Harga Barang', 'Note']
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->mergeCells('A1:G1');
                $event->sheet->getStyle('A5:G5')->applyFromArray([
                    'font' => [
                        'bold' => true ,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'color' => ['argb' => 'FFFF8080']
                    ]
                ]);

                $event->sheet->appendRows(array(
                    array(' '),
                    array('Nomor PR', $this->noPr),
                    array('Nomor PO', $this->noPo),
                    array('Nama Kapal', $this->boatName),
                    array('Tipe PPN', $this->ppn . ' %'),
                    array('Diskon', $this->discount . ' %'),
                    array('Total Harga', 'Rp. ' . number_format($this->price, 2, ",", ".")),
                    array('Alamat Pengiriman Invoice', $this->invoiceAddress),
                    array('Alamat Pengiriman Barang', $this->itemAddress),
                    array('Cabang', $this->cabang),
                    array(' '),
                    array('Diajukan Oleh:' , ' ' , 'Disetujui Oleh:' , ' '  ,'Diketahui :'),
                    array(' '),
                    array(' '),
                    array($this->createdBy , ' ', $this -> approvedBy , ' ' ,'Endah Kusumaningtyas'),

                ), $event);

                $event->sheet->getDelegate()->getStyle('A:G')
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

                $event->sheet->getDelegate()->getStyle('A1:G1')
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $event->sheet->getDelegate()->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A3);
                $event->sheet->getDelegate()->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            }
        ];
    }
}
