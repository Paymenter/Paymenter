<?php

use App\Models\Extension;
use App\Models\ExtensionSetting;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderProductConfig;
use App\Models\Product;
use App\Models\Ticket;
use App\Models\User;
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
        $orders = Order::all();

        // We first want to check if there are any loose orders without a client
        foreach ($orders as $order) {
            if (!User::find($order->client)) {
                $order->delete();
                continue;
            }
        }

        // Change to foreign key
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('client');
            $table->unsignedBigInteger('user_id')->after('id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Then we want to update the user_id column with the client id
        foreach ($orders as $order) {
            $order->user_id = $order->client;
            unset($order->client);
            $order->save();
        }


        $orderProducts = OrderProduct::all();
        foreach ($orderProducts as $orderProduct) {
            if (!$orderProduct->product()->exists())
                $orderProduct->delete();
            if (!$orderProduct->order()->exists())
                $orderProduct->delete();
        }

        Schema::table('order_products', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')->nullable()->change();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->unsignedBigInteger('order_id')->nullable()->change();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });

        $orderProductsConfig = OrderProductConfig::all();
        foreach ($orderProductsConfig as $orderProductConfig) {
            if (!$orderProductConfig->product()->exists())
                $orderProductConfig->delete();
        }

        Schema::table('order_products_config', function (Blueprint $table) {
            $table->unsignedBigInteger('order_product_id')->nullable()->change();
            $table->foreign('order_product_id')->references('id')->on('order_products')->onDelete('cascade');
        });

        $extensionSettings = ExtensionSetting::all();
        foreach ($extensionSettings as $extensionSetting) {
            if (!Extension::find($extensionSetting->extension))
                $extensionSetting->delete();
        }

        Schema::table('extension_settings', function (Blueprint $table) {
            $table->dropColumn('extension');
            $table->unsignedBigInteger('extension_id')->nullable();
            $table->foreign('extension_id')->references('id')->on('extensions')->onDelete('cascade');
        });

        foreach ($extensionSettings as $extensionSetting) {
            $extensionSetting->extension_id = $extensionSetting->extension;
            unset($extensionSetting->extension);
            $extensionSetting->save();
        }

        Schema::table('product_price', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->change();
        });

        $products = Product::all();
        foreach ($products as $product) {
            if ($product->server_id == null)
                continue;
            if (!Extension::find($product->server_id))
                $product->delete();
        }

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('server_id');
            $table->unsignedBigInteger('extension_id')->nullable();
            $table->foreign('extension_id')->references('id')->on('extensions')->onDelete('cascade');
        });

        foreach ($products as $product) {
            $product->extension_id = $product->server_id;
            unset($product->server_id);
            $product->save();
        }

        $tickets = Ticket::all();
        foreach ($tickets as $ticket) {
            if (!User::find($ticket->client))
                $ticket->delete();
        }

        Schema::table('tickets', function (Blueprint $table) {
            // Change client to user_id
            $table->dropForeign(['client']);
            $table->dropColumn('client');
            $table->unsignedBigInteger('user_id')->nullable()->after('assigned_to');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('assigned_to')->nullable()->change();
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('cascade')->change();
        });

        foreach ($tickets as $ticket) {
            $ticket->user_id = $ticket->client;
            unset($ticket->client);
            $ticket->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
