<?php

namespace Modules\Recruitment\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobInterviewCandidate extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'job_interview_candidates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'job_application_id',
        'sender',
        'content',
        'test_type',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'job_application_id' => 'integer',
        'sender' => 'string',
        'content' => 'string',
        'test_type' => 'string',
    ];

    /**
     * Get the job application associated with this interview candidate.
     */
    public function jobApplication()
    {
        return $this->belongsTo(JobApplication::class);
    }
}
