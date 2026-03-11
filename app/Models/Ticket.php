<?php

namespace App\Models;

use App\Observers\TicketObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use OwenIt\Auditing\Contracts\Auditable;

#[ObservedBy([TicketObserver::class])]

class Ticket extends Model implements Auditable
{
    use \App\Models\Traits\Auditable, HasFactory;

    protected $fillable = [
        'subject',
        'status',
        'priority',
        'department',
        'user_id',
        'guest_name',
        'guest_email',
        'assigned_to',
        'service_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function messages()
    {
        return $this->hasMany(TicketMessage::class);
    }

    public function ownerEmail(): ?string
    {
        return $this->user?->email ?? $this->guest_email;
    }

    public function ownerName(): string
    {
        if ($this->user) {
            return (string) $this->user->name;
        }

        return $this->guest_name ?: ($this->guest_email ?: 'Guest');
    }
}
