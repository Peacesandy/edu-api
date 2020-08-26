<?php

namespace App\Nimbus;

use App\Tenant;
use App\Course;
use App\CourseGrade;
use App\Curriculum;
use App\CurriculumType;
use App\CurriculumCourseLoadType;
use App\Subject;

use Illuminate\Support\Arr;

class Syllabus
{
  public $tenant;
  public $curriculum_type;
  public $payload = [
    'created' => [],
    'updated' => [],
  ];

  public function __construct(Tenant $tenant)
  {
    $this->tenant = $tenant;
    $this->curriculum_type = $this->getCurriculumType();
  }

  public function processCurriculum($course_load, $payload) {
    $this->payload = $payload;

    $course_grade_id = $course_load['course_grade_id'];

    if ($course_grade_id) {
      $curriculum = Curriculum::firstOrCreate([
        'course_grade_id' => $course_grade_id,
        'type_id' => $this->curriculum_type->id,
      ]);

      if(isset($course_load['core_subjects_code'])) {
        $this->processSubjects(
          $course_load['core_subjects_code'],
          $course_grade_id,
          'core',
          $curriculum
        );
      }

      if(isset($course_load['elective_subjects_code'])) {
        $this->processSubjects(
          $course_load['elective_subjects_code'],
          $course_grade_id,
          'elective',
          $curriculum
        );
      }

      if(isset($course_load['optional_subjects_code'])) {
        $this->processSubjects(
          $course_load['optional_subjects_code'],
          $course_grade_id,
          'optional',
          $curriculum
        );
      }
    }

    return $this->payload;
  }

  public function getCurriculumType($new = false){
    return $new ? 
    CurriculumType::firstOrCreate(['country' => $this->tenant->country]) : 
    CurriculumType::where(['country' => $this->tenant->country])->first();
  }

  public function processCourses($data): array {
    foreach($data as $item) {
      if (is_array($item)) {
        $item['tenant_id'] = $this->tenant->id;
        $course = Course::firstOrNew($item);

        if ($course->id) {
          $this->payload['updated'][] = $course;
        } else {
          $this->payload['created'][] = $course;
        }

        $course->save();
      }
    }

    return $this->payload;
  }

  private function processSubjects(
    $data,
    $course_grade_id,
    $type,
    Curriculum $curriculum){
    $core_subjects_codes = explode(',', $data);

    foreach ($core_subjects_codes as $code) {
      $subject = $this->getSubject($code);
      $curriculum_course_load_type = $this->getCurriculumCourseLoadType($type);

      if ($subject && $curriculum_course_load_type) {
        $this->payload['created'][] = $curriculum
          ->subjects()
          ->firstOrCreate([
            'subject_id' => $subject->id,
            'type_id' => $curriculum_course_load_type->id
          ])
          ->toArray();
      }
    }

    return $this->payload;
  }

  public function getCurriculumCourseLoadType($name){
    return CurriculumCourseLoadType::where('name', $name)
      ->first();
  }

  private function getSubject($code){
    return Subject::where('code', $code)
      ->first();
  }
}
