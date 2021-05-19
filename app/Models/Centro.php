<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class Centro extends Model
{
    use HasFactory;

    protected $table = 'centros';

    public function exportData() {
        $export = new stdClass();
        $export->id = $this->Centro;
        $export->nombre = $this->Nombre;
        $export->observacion = $this->Observacion;
        return $export;
    }
}
