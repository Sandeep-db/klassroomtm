<?php

class Topics extends EMongoDocument
{
    public $course_code;
    public $course_name;
    public $name;
    public $created_on;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @inheritDoc
     */
    function getCollectionName()
    {
        return 'topics';
    }

    public function rules()
    {
        return [
            ['course_code, course_name, name', 'required']
        ];
    }

    public function beforeSave()
    {
        if (!isset($this->created_on)) {
            $this->created_on = date("Y-m-d_G:i:s");
        }
        return true;
    }

    public function indexes()
    {
        return [
            'course_code_1_name_1' => [
                'key' => [
                    'course_code' => EMongoCriteria::SORT_ASC,
                    'name' => EMongoCriteria::SORT_ASC,
                ],
                'unique' => true,
            ],
        ];
    }
}
