<?php

namespace App\Rules;

use const FILTER_FLAG_IPV4;
use const FILTER_FLAG_IPV6;
use const FILTER_VALIDATE_INT;
use const FILTER_VALIDATE_IP;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Cidr implements ValidationRule
{
    /** @var bool whether or not the rule has been called with network constraints */
    private bool $has_bits;

    /**
     * @param  int|null  $ipv4minbits  The minimum number of bits allowed in an IPv4 network
     * @param  int|null  $ipv4maxbits  The maximum number of bits allowed in an IPv4 network
     * @param  int|null  $ipv6minbits  The minimum number of bits allowed in an IPv6 network
     * @param  int|null  $ipv6maxbits  The maximum number of bits allowed in an IPv6 network
     */
    public function __construct(
        public ?int $ipv4minbits = null,
        public ?int $ipv4maxbits = null,
        public ?int $ipv6minbits = null,
        public ?int $ipv6maxbits = null,
        public ?bool $allowWildCard = false,
    ) {
        $this->has_bits = ($ipv4minbits !== null || $ipv4maxbits !== null || $ipv6minbits !== null || $ipv6maxbits !== null);
    }

    /**
     * @param  string  $attribute  The attribute being validated
     * @param  mixed  $value  The current value of the attribute
     * @param  Closure  $fail  Closure to be run in case of failure
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->allowWildCard && $value === '*') {
            return;
        }
        $mask = null;
        $valid_mask = true;

        if (str_contains($value, '/')) {
            [$value, $mask] = explode('/', $value);
        } elseif ($this->has_bits) {
            // if we specify a bit constraint, assume the bits are required
            $fail($this->message());
        }

        if (str_contains($value, ':')) {
            // ipv6
            if ($mask !== null) {
                $valid_mask = filter_var(
                    $mask,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => $this->ipv6minbits ?? 0, 'max_range' => $this->ipv6maxbits ?? 128]]
                );
            }
            $valid_address = filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
        } else {
            // ipv4
            if ($mask !== null) {
                $valid_mask = filter_var(
                    $mask,
                    FILTER_VALIDATE_INT,
                    ['options' => ['min_range' => $this->ipv4minbits ?? 0, 'max_range' => $this->ipv4maxbits ?? 32]]
                );
                if ($valid_mask) {
                    $long = ip2long($value);
                    $mask = -1 << (32 - $mask);
                    $valid_mask = long2ip($long & $mask) === $value;
                }
            }
            $valid_address = filter_var($value, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        }
        if (!$valid_address || !$valid_mask) {
            $fail($this->message());
        }
    }

    public function message(): string
    {
        return !$this->has_bits
            ? __('The :attribute must be a valid IP address or CIDR subnet.')
            : sprintf(
                __('The :attribute must be a valid CIDR subnet with a mask of %d-%d (IPv4) or %d-%d (IPv6) bits.'),
                $this->ipv4minbits ?? 0,
                $this->ipv4maxbits ?? 32,
                $this->ipv6minbits ?? 0,
                $this->ipv6maxbits ?? 128
            );
    }
}
