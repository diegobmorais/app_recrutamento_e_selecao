<?php

namespace Modules\Recruitment\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobMovies extends Model
{
  use HasFactory;
  protected $table = 'job_movies';
  protected $primaryKey = 'id';
  protected $fillable = [
    'job_id',
    'name',
    'path',
  ];
  /**
   * @param int $job_id
   * @return int
   */
  public function countMovieByJobId($job_id)
  {
    return $this->where('job_id', $job_id)->count();
  }
  /**
   * Verifica o status de exibição de vídeos para a vaga.
   *
   * @param int $id
   * @return array
   */
  public function verifyStatus($id)
  {
    $job = Job::find($id);
    if ($job) {
      return $job->qualify_leader == 1 ? ['view_movies' => 1] : ['view_movies' => 0];
    } else {
      return [];
    }
  }
  public function job()
  {
    return $this->belongsTo(Job::class, 'job_id');
  }
}
