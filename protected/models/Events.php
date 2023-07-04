<?php

class Events extends EMongoDocument
{

    public $course_code;
    public $name;
    public $file_link;
    public $source;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @inheritDoc
     */
    function getCollectionName()
    {
        return 'events';
    }

    public function rules()
    {
        return [
            ['course_code, name, file_link, source', 'required'],
        ];
    }

    public function indexes()
    {
        return [
            'course_code_1_name_1' => [
                'key' => [
                    'course_code' => EMongoCriteria::SORT_ASC,
                    'name' => EMongoCriteria::SORT_ASC,
                ],
            ],
        ];
    }
}
