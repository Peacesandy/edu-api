<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Tenant;
use App\Course;
use App\SchoolTerm;
use Notifications\TermComplete;

class CompleteTerm implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  var $tenant;

  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct(Tenant $tenant)
  {
    $this->tenant = $tenant;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $course_ids = $this->tenant
      ->current_term
      ->registrations()
      ->pluck('course_id')
      ->unique()->values()->all();

    $courses = Course::find($course_ids)->update('status_id', Course::Statuses['complete']);

    $this->tenant->current_term->update('status_id', SchoolTerm::Statuses['complete']);

    //kick off job to notify all parents with results here?

    $this->tenant->notify(new TermComplete);
  }
}
