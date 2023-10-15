<?php

namespace App\Utils;

class BitField
{
    public int $bits;

    public function __construct(int $bits)
    {
        $this->bits = $bits;
    }

    /**
     * Check if given bit-value has permission value
     *
     * @param int $flag
     * @return bool
     */
    public function hasBit(int $flag): bool
    {
        return ($this->bits & $flag) === $flag;
    }
}
