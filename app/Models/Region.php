<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use stdClass;

class Region extends Model
{
    use HasFactory;

    private $plantas = [];

    protected $table = 'zona';

    public function addPlanta($planta) {
        array_push($this->plantas, $planta);
    }

    public function exportData() {
        $export = new stdClass();
        $export->id = $this->CODIGO;
        $export->nombre = $this->NOMBRE;
        $export->plantas = [];
        foreach ($this->plantas as $planta) {
            array_push($export->plantas, $planta->exportData());
        }
        return $export;
    }
}
