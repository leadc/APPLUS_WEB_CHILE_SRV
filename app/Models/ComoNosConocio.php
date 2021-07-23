<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class ComoNosConocio extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'COMO_NOS_CONOCIO';

    public function exportData() {
        $export = new stdClass();
        $export->id = $this->id;
        $export->descripcion = $this->descripcion;
        return $export;
    }
}
