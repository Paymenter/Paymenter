<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateUser extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function invoices()
    {
        return $this->hasManyThrough(Invoice::class, User::class, 'id', 'user_id', 'user_id', 'id');
    }

    public function earnings()
    {
        $earnings = 0;
        foreach ($this->invoices as $invoice) {
            if (!$invoice->isPaid())
                continue;
            $earnings += $invoice->total() * $this->affiliate->commission / 100;
        }
        // Round to 2 decimal places
        $earnings = round($earnings, 2);

        return $earnings;
    }
}
