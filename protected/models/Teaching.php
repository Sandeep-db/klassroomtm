<?php

class Teaching extends EMongoEmbeddedDocument
{
    public $course_id;
    public $course_name;
    public $course_code;
    public $course_description;

    public function rules()
    {
        return [
            'course_name, course_name, course_code, course_description', 'required'
        ];
    }

}