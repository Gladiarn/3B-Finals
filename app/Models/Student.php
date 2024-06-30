<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'firstname', 'lastname', 'birthdate', 'sex', 'address', 'year', 'course', 'section'
    ];

    // Define relationships
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
