<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $fillable = ['id', 'name', 'questions'];
    public $incrementinsg = false;
    protected $keyType = 'string';

    public function __construct($assessmentData) {
        $this->id = $assessmentData['id'];
        $this->name = $assessmentData['name'];
        $this->questions = $assessmentData['questions'];
    }

    public function getId() {
        return $this->id;
    }
}
