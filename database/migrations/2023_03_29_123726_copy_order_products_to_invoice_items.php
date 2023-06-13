<?php

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\OrderProduct;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $orderProducts = OrderProduct::all();

        foreach ($orderProducts as $orderProduct) {
            if(Invoice::where('order_id', $orderProduct->order_id)->latest()->exists() == false) {
                continue;
            }
            if ($orderProduct->product()->exists() == false) {
                $orderProduct->delete();
            }
            $invoiceItem = new InvoiceItem();
            $invoiceItem->invoice_id = Invoice::where('order_id', $orderProduct->order_id)->latest()->get()->first()->id;
            $invoiceItem->product_id = $orderProduct->id;
            $invoiceItem->total = $orderProduct->price * $orderProduct->quantity;
            $invoiceItem->save();
            $invoiceItem->description = $orderProduct->product()->get() ? 'Unknown' : $orderProduct->product()->get()->first()->name;
            $invoiceItem->save();
        }
        foreach(Invoice::all() as $invoice) {
            if($invoice->order()->exists() == false) {
                $invoice->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach (InvoiceItem::all() as $invoiceItem) {
            $invoiceItem->delete();
        }
    }
};
