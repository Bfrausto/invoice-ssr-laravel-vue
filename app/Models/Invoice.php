<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;
    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
    ];
    protected $fillable = [
        'company_id',
        'client_id',
        'invoice_number',
        'date',
        'due_date',
        'total_amount',
        'status',
        'issue_date',
        'tax_id',
        'folio',
        'subtotal',
        'total_taxes',
        'total',
        'notes',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
