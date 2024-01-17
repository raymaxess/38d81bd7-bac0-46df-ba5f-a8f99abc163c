<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['id', 'stem', 'type', 'strand', 'config'];
    public $incrementinsg = false;
    protected $keyType = 'string';

    public function __construct($questionData) {
        $this->id = $questionData['id'];
        $this->stem = $questionData['stem'];
        $this->type = $questionData['type'];
        $this->strand = $questionData['strand'];
        $this->config = $questionData['config'];
    }

    public function getId() {
        return $this->id;
    }
}