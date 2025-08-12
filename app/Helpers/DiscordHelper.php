<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Classes\Settings;

class DiscordHelper
{
    /**
     * Send a direct message to a Discord user by user ID.
     *
     * @param string $discordUser Discord user ID (numeric)
     * @param string|array $message Message content (plain text or embed array)
     * @return bool True on success, false on failure
     */
    public static function sendDM($discordUser, $message)
    {
        $token = Settings::getSetting('discord_bot_token')->value ?? null;
        Log::debug('DiscordHelper: DEBUG - About to send DM', [
            'token_first_8' => $token ? substr($token, 0, 8) : null,
            'token_length' => $token ? strlen($token) : 0,
            'raw_token' => $token,
            'discordUser' => $discordUser,
            'discordUser_type' => gettype($discordUser),
            'message' => $message,
            'called_from' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3)
        ]);
        if (!$token || !$discordUser) {
            Log::warning('DiscordHelper: Missing bot token or user.', [
                'token_present' => (bool)$token,
                'discordUser' => $discordUser
            ]);
            return false;
        }

        // Only user IDs are supported for DMs
        $userId = preg_match('/^\d{17,20}$/', $discordUser) ? $discordUser : null;
        if (!$userId) {
            Log::warning('DiscordHelper: Only user ID supported for DM.', [
                'discordUser' => $discordUser
            ]);
            return false;
        }

        // Create DM channel with the user
        Log::debug('DiscordHelper: Creating DM channel', [
            'endpoint' => 'https://discord.com/api/v10/users/@me/channels',
            'recipient_id' => $userId
        ]);
        $response = Http::withHeaders([
            'Authorization' => 'Bot ' . $token,
            'Content-Type' => 'application/json'
        ])->post('https://discord.com/api/v10/users/@me/channels', [
            'recipient_id' => $userId
        ]);
        Log::debug('DiscordHelper: DM channel creation response', [
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json()
        ]);
        if (!$response->ok() || !isset($response['id'])) {
            Log::error('DiscordHelper: Failed to create DM channel', [
                'status' => $response->status(),
                'body' => $response->body(),
                'json' => $response->json(),
                'token_first_8' => substr($token, 0, 8)
            ]);
            return false;
        }
        $channelId = $response['id'];

        // Send message
        Log::debug('DiscordHelper: Sending message to DM channel', [
            'channelId' => $channelId,
            'payload' => $message
        ]);
        $payload = is_array($message) ? $message : ['content' => $message];
        $msgResponse = Http::withHeaders([
            'Authorization' => 'Bot ' . $token,
            'Content-Type' => 'application/json'
        ])->post("https://discord.com/api/v10/channels/{$channelId}/messages", $payload);
        Log::debug('DiscordHelper: Message send response', [
            'status' => $msgResponse->status(),
            'body' => $msgResponse->body(),
            'json' => $msgResponse->json()
        ]);
        if (!$msgResponse->ok()) {
            Log::error('DiscordHelper: Failed to send DM', [
                'status' => $msgResponse->status(),
                'body' => $msgResponse->body(),
                'json' => $msgResponse->json()
            ]);
            return false;
        }
        return true;
    }
}
