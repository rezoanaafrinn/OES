<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'subject'
    ];

    public function answers()
    {
        return $this->hasMany(Answer::class,'questions_id','id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class,'id','subject_id');
    }
}
