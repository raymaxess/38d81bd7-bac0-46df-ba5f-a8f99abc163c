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

    // Constructor to initialize the student object with data
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

    // Getter method for the 'id' property
    public function getId() {
        return $this->id;
    }

    // create method that formats completed from  16/12/2021 10:46:00 to become 16th December 2021 10:46 AM
    public function getCompletedDate() {
        // Create a DateTime object from that string
        $date = DateTime::createFromFormat('d/m/Y H:i:s', $this->completed);

        // Format the date into the desired format
        return $date->format('jS F Y g:i A');
    }

    public function getAssignedDate() {
        // Create a DateTime object from that string
        $date = DateTime::createFromFormat('d/m/Y H:i:s', $this->assigned);

        // Format the date into the desired format
        return $date->format('jS F Y');
    }
}
