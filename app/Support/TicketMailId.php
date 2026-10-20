<?php

namespace App\Support;

/**
 * Message-IDs for ticket mail carry an HMAC of the ticket message id, so a
 * piped reply can prove it answers mail that was actually sent to the customer.
 * Rotating the app key invalidates every id already sent.
 */
final class TicketMailId
{
    private const TOKEN_LENGTH = 24;

    /**
     * The local part of the Message-ID for a ticket message: `{id}.{token}`.
     */
    public static function localPart(int $ticketMessageId): string
    {
        return $ticketMessageId . '.' . self::token($ticketMessageId);
    }

    /**
     * The ticket message id an In-Reply-To value refers to, or null if it
     * carries no valid token.
     */
    public static function parse(string $inReplyTo): ?int
    {
        if (!preg_match('/^<?(\d+)\.([a-f0-9]{' . self::TOKEN_LENGTH . '})@/', trim($inReplyTo), $matches)) {
            return null;
        }

        $ticketMessageId = (int) $matches[1];

        return hash_equals(self::token($ticketMessageId), $matches[2]) ? $ticketMessageId : null;
    }

    private static function token(int $ticketMessageId): string
    {
        return substr(hash_hmac('sha256', 'ticket-mail:' . $ticketMessageId, (string) config('app.key')), 0, self::TOKEN_LENGTH);
    }
}
