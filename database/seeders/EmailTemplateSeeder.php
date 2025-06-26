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
                            
                - IP: {{ $ip }}  
                - Device: {{ $device }}
                - Time: {{ $time }}

                **If this was you**  
                You can ignore this message, there is no need to take any action.
                            
                **If this wasn't you**  
                Please reset your password [here]({{ route('password.request') }}).
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
                'key' => 'new_order_created',
                'subject' => 'New order created',
                'body' => <<<'HTML'
                # New order created

                A new order was created on your account.

                **Order details**
                <div class="table">  
                            
                |   Item   | Quantity |  Price   |  
                | :------: | :------: | :------: |
                @foreach ($items as $item)
                | {{ $item->product->name }} | {{ $item->quantity }} | {{ $item->formattedPrice }} |
                @endforeach
                </div>
                HTML,
            ],
            [
                'key' => 'new_server_created',
                'subject' => 'Server activated',
                'body' => <<<'HTML'
                # Server activated

                Your server has been activated.

                **Server details**
                - Name: {{ $service->product->name }}

                @isset($service->product->email_template)
                **Server information**  
                {!! Str::markdown(Illuminate\View\Compilers\BladeCompiler::render($service->product->email_template, get_defined_vars()['__data'])) !!}
                @endisset
                HTML,
            ],
            [
                'key' => 'server_suspended',
                'subject' => 'Server suspended',
                'body' => <<<'HTML'
                # Server suspended

                Your server has been suspended due to a payment failure.

                **Server details**
                - Name: {{ $service->product->name }}

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
                - Name: {{ $service->product->name }}

                Do you consider it a mistake?
                <div class="action">
                	<a class="button button-blue" href="{{ route('tickets.create') }}">
                		Contact us
                	</a>
                </div>
                HTML,
            ],
            [
                'key' => 'new_ticket_message',
                'subject' => 'New ticket reply',
                'body' => <<<'HTML'
                # New ticket reply

                {{ $ticketMessage->user->name }} replied to your ticket.

                **Message**
                {!! Str::markdown($ticketMessage->message, [
                    'html_input' => 'strip',
                    'allow_unsafe_links' => false,
                ]) !!}
                HTML,
            ],
            [
                'key' => 'email_verification',
                'subject' => 'Email verification',
                'body' => <<<'HTML'
                # Email verification

                Please verify your email address by clicking the link below.
                <div class="action">
                	<a class="button button-blue" href="{{ $url }}">
                		Verify email
                	</a>
                </div>
                
                This link will expire in 60 minutes.

                If you did not create an account, you can ignore this email.
                HTML,
            ],
            [
                'key' => 'password_reset',
                'subject' => 'Password reset',
                'body' => <<<'HTML'
                # Password reset

                You are receiving this email because we received a password reset request for your account.

                **Reset password**
                <div class="action">
                	<a class="button button-blue" href="{{ $url }}">
                		Reset password
                	</a>
                </div>

                This password reset link will expire in 60 minutes.

                If you did not request a password reset, no further action is required.

                HTML,
            ],
            [
                'key' => 'service_cancellation_received',
                'subject' => 'Service cancellation received',
                'body' => <<<'HTML'
                # Server Cancellation Received

                We're sorry to see you go! Your server cancellation has been successfully received.
                
                **Cancellation Details**
                - Server: {{ $service->product->name }}
                @if($cancellation->reason)
                - Reason: {{ $cancellation->reason }}
                @endif
                - Requested at: {{ $cancellation->created_at->format('F j, Y, g:i A') }}
                
                @if($cancellation->type === 'end_of_period')
                Your server will remain active until {{ $service->expires_at->format('F j, Y') }} (end of your current billing period).
                @else
                Your server has been terminated immediately.
                @endif

                HTML,
            ],
        ]);
    }
}
