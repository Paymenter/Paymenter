<?php

namespace Tests\Unit;

use App\Support\ClientIp;
use PHPUnit\Framework\TestCase;

class ClientIpTest extends TestCase
{
    public function test_ipv4_is_kept_as_is(): void
    {
        $this->assertSame('203.0.113.7', ClientIp::key('203.0.113.7'));
    }

    public function test_ipv6_is_reduced_to_its_64(): void
    {
        $this->assertSame('2001:db8:1:2::/64', ClientIp::key('2001:db8:1:2:aaaa:bbbb:cccc:dddd'));
        $this->assertSame('2001:db8:1:2::/64', ClientIp::key('2001:0DB8:0001:0002::1'));
        $this->assertSame(ClientIp::key('2001:db8::1'), ClientIp::key('2001:db8::ffff:1'));
        $this->assertNotSame(ClientIp::key('2001:db8::1'), ClientIp::key('2001:db8:0:1::1'));
    }

    public function test_ipv4_mapped_ipv6_counts_as_the_ipv4_address(): void
    {
        $this->assertSame('203.0.113.7', ClientIp::key('::ffff:203.0.113.7'));
        $this->assertNotSame(ClientIp::key('::ffff:203.0.113.7'), ClientIp::key('::ffff:203.0.113.8'));
    }

    public function test_unparseable_input_is_returned_unchanged(): void
    {
        $this->assertSame('', ClientIp::key(''));
        $this->assertSame('not-an-ip', ClientIp::key('not-an-ip'));
    }
}
