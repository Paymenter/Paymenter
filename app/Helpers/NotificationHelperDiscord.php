<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Invoice;

class NotificationHelperDiscord
{
    /**
     * Send a Discord DM to the user if enabled and discord_id is valid.
     */
    public static function sendDiscordDM(User $user, $message): void
    {
        $discordSetting = \App\Classes\Settings::getSetting('discord_dm_enabled');
        if (empty($discordSetting->value) || $discordSetting->value === '0' || $discordSetting->value === 0 || $discordSetting->value === false) {
            Log::info('Discord DM not sent: setting disabled', ['user_id' => $user->id]);
            return;
        }
        if (!empty($user->discord_id) && preg_match('/^\d{17,20}$/', $user->discord_id)) {
            Log::info('Attempting Discord DM by discord_id', ['user_id' => $user->id, 'discord_id' => $user->discord_id]);
            $result = \App\Helpers\DiscordHelper::sendDM($user->discord_id, $message);
            Log::info('Discord DM result', ['result' => $result]);
        } else {
            Log::warning('Discord DM not sent: discord_id missing or invalid', ['user_id' => $user->id, 'discord_id' => $user->discord_id ?? null]);
        }
    }

    /**
     * Send a Discord DM when an invoice is paid.
     */
    public static function invoicePaidNotification(User $user, Invoice $invoice): void
    {
        $invoiceUrl = url("/invoices/{$invoice->id}");
        $company = config('app.name', 'Our Company');
        $embed = [
            'embeds' => [[
                'title' => '✅ Invoice Paid',
                'color' => 0x43B581,
                'fields' => [
                    [ 'name' => 'Invoice #', 'value' => "#{$invoice->number}", 'inline' => true ],
                    [ 'name' => 'Amount', 'value' => (string)$invoice->formattedTotal, 'inline' => true ],
                ],
                'description' => "Thank you for your payment!\n[View Invoice]({$invoiceUrl})",
                'footer' => [ 'text' => $company ],
            ]]
        ];
        $discordSetting = \App\Classes\Settings::getSetting('discord_dm_enabled');
        if (!empty($discordSetting->value) && $discordSetting->value !== '0' && $discordSetting->value !== 0 && $discordSetting->value !== false) {
            if (!empty($user->discord_id) && preg_match('/^\d{17,20}$/', $user->discord_id)) {
                \App\Helpers\DiscordHelper::sendDM($user->discord_id, $embed);
            }
        }
    }

    /**
     * (Unused) Placeholder for service cancellation Discord notification.
     */
    public static function serviceCancellationReceivedNotification(User $user, \App\Models\ServiceCancellation $cancellation, array $data = []): void
    {
        $data['cancellation'] = $cancellation;
        $data['service'] = $cancellation->service;
        // ...existing code...
    }

    /**
     * Send a Discord DM when a new invoice is created.
     */
    public static function sendInvoiceCreatedDiscordDM(User $user, Invoice $invoice): void
    {
        $company = Config::get('app.name', 'Our Company');
        $invoiceUrl = URL::to("/invoices/{$invoice->id}");
        $dueDate = ($invoice->due_at ?? null) ? (is_object($invoice->due_at) ? $invoice->due_at->format('F j, Y') : Carbon::parse($invoice->due_at)->format('F j, Y')) : 'N/A';
        $embed = [
            'embeds' => [[
                'title' => '🧾 New Invoice Created',
                'color' => 0x7289DA,
                'description' => "A new invoice has been generated for your account.\n[View & Pay Invoice]({$invoiceUrl})",
                'fields' => [
                    [ 'name' => 'Invoice #', 'value' => "#{$invoice->number}", 'inline' => true ],
                    [ 'name' => 'Amount Due', 'value' => (string)$invoice->formattedTotal, 'inline' => true ],
                    [ 'name' => 'Due Date', 'value' => ($dueDate !== 'N/A' ? "`{$dueDate}`" : '*N/A*'), 'inline' => false ],
                ],
                'footer' => [ 'text' => $company ],
            ]]
        ];
        self::sendDiscordDM($user, $embed);
    }

    /**
     * Send a Discord DM when an invoice is paid.
     */
    public static function sendInvoicePaidDiscordDM(User $user, Invoice $invoice): void
    {
        $company = Config::get('app.name', 'Our Company');
        $invoiceUrl = URL::to("/invoices/{$invoice->id}");
        $embed = [
            'embeds' => [[
                'title' => '✅ Invoice Paid',
                'color' => 0x43B581,
                'fields' => [
                    [ 'name' => 'Invoice #', 'value' => "#{$invoice->number}", 'inline' => true ],
                    [ 'name' => 'Amount', 'value' => (string)$invoice->formattedTotal, 'inline' => true ],
                ],
                'description' => "Thank you for your payment!\n[View Invoice]({$invoiceUrl})",
                'footer' => [ 'text' => $company ],
            ]]
        ];
        self::sendDiscordDM($user, $embed);
    }
}
