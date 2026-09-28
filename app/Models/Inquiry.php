<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Quote, flight/PNR/fare/reissue/refund, visa and bus inquiries sent from the site. */
class Inquiry extends Model
{
    public const STATUSES = ['new', 'in_progress', 'resolved'];

    protected $fillable = ['type', 'name', 'email', 'phone', 'subject', 'message', 'data', 'status', 'admin_notes'];

    protected function casts(): array
    {
        return ['data' => 'array'];
    }

    public function getTypeLabelAttribute(): string
    {
        return config('travel.inquiry_types.'.$this->type, ucfirst($this->type));
    }
}
