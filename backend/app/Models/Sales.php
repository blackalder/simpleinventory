<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    use HasFactory;

    protected $fillable = ['code_transaksi', 'tanggal_transaksi', 'customer_id', 'total_bayar', 'total_diskon'];
}
