<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTime;

class StudentResponse extends Model
{
    protected $fillable = [
        'id', 'assessmentId', 'assigned', 'started', 'completed', 'student', 'responses', 'results'
    ];
    public $incrementinsg = false;
    protected $keyType = 'string';

    public function __construct($studentResponseData) {
        $this->id = $studentResponseData['id'];
        $this->assessmentId = $studentResponseData['assessmentId'];
        $this->assigned = $studentResponseData['assigned'];
        $this->started = $studentResponseData['started'];
        $this->completed = $studentResponseData['completed'];
        $this->student = $studentResponseData['student'];
        $this->responses = $studentResponseData['responses'];
        $this->results = $studentResponseData['results'];
    }

    public function getId() {
        return $this->id;
    }

    public function getCompletedDate() {
        $date = DateTime::createFromFormat('d/m/Y H:i:s', $this->completed);
        return $date->format('jS F Y g:i A');
    }

    public function getAssignedDate() {
        $date = DateTime::createFromFormat('d/m/Y H:i:s', $this->assigned);
        return $date->format('jS F Y');
    }
}
