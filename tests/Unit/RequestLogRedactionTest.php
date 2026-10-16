<?php

namespace Tests\Unit;

use App\Listeners\RequestListener;
use Tests\TestCase;

class RequestLogRedactionTest extends TestCase
{
    private function invoke(string $method, ...$args)
    {
        return (fn () => $this->{$method}(...$args))->call(new RequestListener);
    }

    public function test_credentials_in_the_query_string_are_hidden(): void
    {
        $url = $this->invoke('url', 'https://httpapi.com/api/domains/register.json?auth-userid=123&api-key=SECRET&domain-name=example.com&ns=ns1&ns=ns2');

        $this->assertStringNotContainsString('SECRET', $url);
        $this->assertStringContainsString('api-key=********', $url);
        $this->assertStringContainsString('domain-name=example.com&ns=ns1&ns=ns2', $url);
    }

    public function test_urls_without_query_string_are_unchanged(): void
    {
        $this->assertSame('https://api.example.com/v1/things', $this->invoke('url', 'https://api.example.com/v1/things'));
    }

    public function test_api_credentials_in_the_payload_are_hidden(): void
    {
        $payload = $this->invoke('payload', ['username' => 'client1', 'passwd' => 'P4ss', 'passwd2' => 'P4ss', 'api_key' => 'K']);

        $this->assertSame('client1', $payload['username']);
        $this->assertSame('********', $payload['passwd']);
        $this->assertSame('********', $payload['passwd2']);
        $this->assertSame('********', $payload['api_key']);
    }
}
