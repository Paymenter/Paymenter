<?php

namespace App\Console\Commands;

use App\Helpers\ExtensionHelper;
use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class AutoRenewInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:auto-renew';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically pay due invoices using user credits';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $autoRenewed = 0;
        $autoRenewFailed = 0;
        $now = now();

        // Show all pending invoices for debugging
        $allPending = \App\Models\Invoice::where('status', 'pending')->get();
        $this->info('Total pending invoices: ' . $allPending->count());
        foreach ($allPending as $inv) {
            $this->info("Invoice #{$inv->id}: user_id={$inv->user_id}, currency={$inv->currency_code}, due_at={$inv->due_at}, remaining={$inv->remaining}");
        }

        // Only include pending invoices that are overdue or due within the next 24 hours
        $invoices = \App\Models\Invoice::where('status', 'pending')
            ->where('due_at', '<=', $now->copy()->addDay()->toDateTimeString())
            ->get();

        $this->info('Invoices matching autorenew criteria: ' . $invoices->count());

        foreach ($invoices as $invoice) {
            $user = $invoice->user;
            $credit = $user ? $user->credits()->where('currency_code', $invoice->currency_code)->first() : null;

            // Skip invoices with "Credit deposit" in any item description (case-insensitive)
            if ($invoice->items()->whereRaw('LOWER(description) LIKE ?', ['%credit deposit%'])->exists()) {
                $this->line("Skipped invoice #{$invoice->number} (credit deposit invoice)");
                continue;
            }

            // Debug info
            if (!$credit) {
                $this->info("Invoice #{$invoice->id}: No credits in currency {$invoice->currency_code}");
            } elseif ($credit->amount < $invoice->remaining) {
                $this->info("Invoice #{$invoice->id}: Not enough credits ({$credit->amount} < {$invoice->remaining})");
            } else {
                $this->info("Invoice #{$invoice->id}: Will be paid with credits ({$credit->amount} >= {$invoice->remaining})");
            }

            // Add this check before attempting payment
            if (!$invoice->user->auto_renewal_enabled) {
                $this->line("Skipped invoice #{$invoice->number} (auto-renewal disabled for user)");
                continue;
            }

            if (!$credit) {
                // Send currency mismatch email using NotificationHelper
                \App\Helpers\NotificationHelper::sendEmailNotification(
                    'incorrect_currency',
                    [
                        'user' => $user,
                        'invoice' => $invoice,
                        'credit' => (object)[
                            'currency_code' => $invoice->currency_code,
                            'amount' => 0,
                        ],
                    ],
                    $user
                );
                $autoRenewFailed++;
                continue;
            }

            if ($credit->amount < $invoice->remaining) {
                $this->line("Invoice #{$invoice->number}: Not enough credits ({$credit->amount} < {$invoice->total})");

                // Send insufficient credits email
                \App\Helpers\NotificationHelper::sendEmailNotification(
                    'insufficient_credits',
                    [
                        'user' => $invoice->user,
                        'invoice' => $invoice,
                        'credit' => $credit,
                    ],
                    $invoice->user
                );

                $autoRenewFailed++;
                continue;
            }

            // Pay invoice with credits
            $credit->amount -= $invoice->remaining;
            $credit->save();
            ExtensionHelper::addPayment($invoice->id, null, amount: $invoice->remaining);
            $invoice->status = 'paid';
            $invoice->save();
            $autoRenewed++;
        }
        $this->info('Auto-renewed invoices: ' . $autoRenewed . ', failed: ' . $autoRenewFailed);
    }
}
