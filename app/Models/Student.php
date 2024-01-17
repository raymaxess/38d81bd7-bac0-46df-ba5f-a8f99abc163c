<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['id', 'firstName', 'lastName', 'yearLevel'];
    public $incrementinsg = false;
    protected $keyType = 'string';

    public function __construct($studentData) {
        $this->id = $studentData['id'];
        $this->firstName = $studentData['firstName'];
        $this->lastName = $studentData['lastName'];
        $this->yearLevel = $studentData['yearLevel'];
    }

    public function getId() {
        return $this->id;
    }
}
