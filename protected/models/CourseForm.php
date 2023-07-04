<?php

class CourseForm extends CFormModel
{
    public $course_name;
    public $course_code;
    public $course_description;
    public $course_duration;
    public $course_start_date;
    public $course_end_date;
    public $course_instructor;

    public function rules()
    {
        return [
            ['course_name, course_code, course_description, course_duration, course_start_date, course_end_date, course_instructor', 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'course_name' => 'Course Name',
            'course_code' => 'Course Code',
            'course_description' => 'Course Description',
            'course_duration' => 'Course Duration',
            'course_start_date' => 'Start Date',
            'course_end_date' => 'End Date',
            'course_instructor' => 'Instructor',
        ];
    }
}
