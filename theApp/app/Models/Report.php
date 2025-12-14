<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'artwork_id',
        'reason',
        'description',
    ];

    public function user()
    {
        return $table->belongsTo(User::class);
    }

    public function artwork()
    {
        return $table->belongsTo(Artwork::class);
    }
}
