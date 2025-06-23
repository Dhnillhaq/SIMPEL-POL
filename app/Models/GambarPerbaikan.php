<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GambarPerbaikan extends Model
{
    use HasFactory;

    protected $table = 'gambar_perbaikan';
    protected $guarded = ['id_gambar_perbaikan'];
    protected $primaryKey = 'id_gambar_perbaikan';

    public function perbaikan()
    {
        return $this->belongsTo(Perbaikan::class, 'id_perbaikan', 'id_perbaikan');
    }
}
