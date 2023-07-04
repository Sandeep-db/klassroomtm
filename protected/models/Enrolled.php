<?php

class Enrolled extends EMongoEmbeddedDocument
{
    public $course_id;
    public $course_name;
    public $course_code;
    public $course_instuctor_name;
    public $course_instuctor_email;
    public $course_description;

    public function rules()
    {
        return [
            'course_name, course_name, course_code, course_instuctor_name, course_instuctor_email, course_description', 'required'
        ];
    }

}