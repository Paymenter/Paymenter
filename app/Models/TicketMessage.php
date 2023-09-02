<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TicketMessage extends Model
{
    use HasFactory;
    protected $table = 'ticket_messages';
    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function messageDate()
    {
        return $this->created_at->isToday() ? $this->created_at->format('H:i') : $this->created_at->format('d M Y, H:i');
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }


    public function files()
    {
        return $this->morphMany(FileUpload::class, 'fileable');
    }
}
