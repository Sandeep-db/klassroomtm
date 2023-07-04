<?php

class Courses extends EMongoDocument
{
    public $course_name;
    public $course_code;
    public $course_description;
    public $course_status;
    public $course_created_date;
    public $course_start_date;
    public $course_end_date;
    public $course_students;
    public $course_students_no;
    public $course_instructor;
    public $course_schedule;
    public $course_is_deleted;
    public $private;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @inheritDoc
     */
    function getCollectionName()
    {
        return 'courses';
    }

    public function rules()
    {
        return [
            ['course_name, course_code, course_description, course_start_date, course_end_date, course_instructor', 'required'],
        ];
    }

    public function embed()
    {
        return [
            'course_students' => ['Student'],
            'course_instructor' => 'Instructor',
        ];
    }

    public function attributeLabels()
    {
        return [
            'course_name' => 'Course Name',
            'course_code' => 'Course Code',
            'course_description' => 'Course Description',
            'course_fee' => 'Course Fee',
            'course_image' => 'Image',
            'course_status' => 'Course Status',
            'course_created_date' => 'Course Created Date',
            'course_start_date' => 'Start Date',
            'course_end_date' => 'End Date',
            'course_is_deleted' => 'Course Is Deleted',
        ];
    }

    public function beforeSave()
    {
        if (!isset($this->course_schedule)) {
            $this->course_schedule = [];
        }
        if (!isset($this->course_students)) {
            $this->course_students = [];
        }
        if (!isset($this->course_students_no)) {
            $this->course_students_no = 0;
        }
        if (!isset($this->course_is_deleted)) {
            $this->course_is_deleted = false;
        }
        $currentDate = strval(date('Y-m-d'));
        if (!isset($this->course_created_date)) {
            $this->course_created_date = $currentDate;
        } 
        if ($this->course_start_date > $currentDate) {
            $this->course_status = 'Not Started';
        } else if ($this->course_end_date < $currentDate) {
            $this->course_status = 'Completed';
        } else {
            $this->course_status = 'On going';
        }
        return true;
    }

    public function indexes()
    {
        return [
            'course_code_1' => [
                'key' => [
                    'course_code' => EMongoCriteria::SORT_ASC,
                ],
                'unique' => true,
            ],
            'private_1' => [
                'key' => [
                    'private' => EMongoCriteria::SORT_ASC,
                ],
            ],
        ];
    }
}
