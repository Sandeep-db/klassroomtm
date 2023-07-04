<?php

class Student extends EMongoEmbeddedDocument
{
    public $_id;
    public $name;
    public $email;

    public function rules()
    {
        return [
            '_id, name, email', 'required'
        ];
    }

}
