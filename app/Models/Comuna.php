<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class Comuna extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'comuna';

    public function exportData() {
        $export = new stdClass();
        $export->id = $this->CODCOMUNA;
        $export->descripcion = $this->NOMCOMUNA;
        return $export;
    }
}
