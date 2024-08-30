<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('email_templates')->insertOrIgnore([
            [
                'key' => 'new_login_detected',
                'subject' => 'New login detected',
                'body' => <<<'HTML'
                # New login detected  
                            
                A new login was detected on your account.
                            
                Location: {{ $location }}  
                IP: {{ $ip }}  
                            
                            
                **If this was you**  
                You can ignore this message, there is no need to take any action.
                            
                **If this wasn't you**  
                Please reset your password [here]({{ route('register') }}).
                HTML,
            ],
            [
                'key' => 'new_invoice_created',
                'subject' => 'New invoice created',
                'body' => <<<'HTML'
                # New invoice created  
                            
                A new invoice was created on your account.
                            
                Total amount: **{{ $total }}**
                            
                            
                <div class="table">  
                            
                |   Item   | Quantity |  Price   |  
                | :------: | :------: | :------: |
                @foreach ($items as $item)
                | {{ $item->description }} | {{ $item->quantity }} | {{ $item->price }} |
                @endforeach
                </div>
                            
                <div class="action">
                	<a class="button button-blue" href="{{ route('invoices.show', $invoice) }}">
                		Go to invoice
                	</a>
                </div>
                            
                @if($has_subscription)
                You have a active subscription, the invoice will be automatically paid.
                @endif
                HTML,
            ],
            [
                'key' => 'new_server_created',
                'subject' => 'Server activated',
                'body' => <<<'HTML'
                # Server activated

                Your server has been activated.

                **Server details**
                - Name: {{ $orderProduct->product->name }}

                **Server information**
                {!! Str::markdown(Illuminate\View\Compilers\BladeCompiler::render($body, $data)) !!}
                HTML,
            ],
            [
                'key' => 'server_suspended',
                'subject' => 'Server suspended',
                'body' => <<<'HTML'
                # Server suspended

                Your server has been suspended due to a payment failure.

                **Server details**
                - Name: {{ $orderProduct->product->name }}

                Please pay the invoice to reactivate the server.
                HTML,
            ],
            [
                'key' => 'server_terminated',
                'subject' => 'Server terminated',
                'body' => <<<'HTML'
                # Server terminated

                Your server has been terminated.

                **Server details**
                - Name: {{ $orderProduct->product->name }}

                Do you consider it a mistake?
                <div class="action">
                	<a class="button button-blue" href="{{ route('tickets.create') }}">
                		Contact us
                	</a>
                </div>
                HTML,
            ],
        ]);
    }
}
