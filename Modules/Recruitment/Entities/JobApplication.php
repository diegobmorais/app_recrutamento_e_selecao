<?php

namespace Modules\Recruitment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job',
        'name',
        'email',
        'phone',
        'profile',
        'resume',
        'cover_letter',
        'dob',
        'gender',
        'country',
        'state',
        'city',
        'stage',
        'order',
        'skill',
        'rating',
        'is_archive',
        'custom_question',
        'workspace',
        'test_available',
        'created_by',
        'final_score',
        'final_summary',
        'behavioral_test_score',
        'behavioral_test_summary',
        'test_tokens',
    ];

    protected static function newFactory()
    {
        return \Modules\Recruitment\Database\factories\JobApplicationFactory::new();
    }

    public function jobs()
    {
        return $this->hasOne(Job::class, 'id', 'job');
    }

    public function stages()
    {
        return $this->hasOne(JobStage::class, 'id', 'stage');
    }

    public static $application_type = [
        '' => 'Select Application Type',
        'new' => 'New',
        'job_candidate' => 'Job Candidate',
    ];
    public function generateTestToken(string $testType)
    {
        $testTokens = json_decode($this->test_tokens ?? '{}', true);

        $testTokens[$testType] = [
            'token' => Str::random(32),
            'created_at' => now()->toDateTimeString()
        ];

        $this->test_tokens = json_encode($testTokens);
        $this->save();

        return $testTokens[$testType]['token'];
    }

    public function getTestToken(string $testType)
    {
        $testTokens = json_decode($this->test_tokens ?? '{}', true);

        return $testTokens[$testType]['token'] ?? null;
    }
}
