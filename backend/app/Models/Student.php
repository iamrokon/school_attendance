<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Database\Factories\StudentFactory;

class Student extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return StudentFactory::new();
    }

    protected $fillable = [
        'name',
        'student_id',
        'class',
        'section',
        'photo',
    ];

    /**
     * Get the attendances for the student.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
