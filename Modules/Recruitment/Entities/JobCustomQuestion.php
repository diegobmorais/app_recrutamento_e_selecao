<?php

namespace Modules\Recruitment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobCustomQuestion extends Model
{
    use HasFactory;

    protected $table = 'job_custom_questions';

    protected $fillable = [
        'job_id',
        'question',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Recruitment\Database\factories\JobCustomQuestionFactory::new();
    }
    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
