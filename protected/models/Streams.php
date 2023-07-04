<?php

class Streams extends EMongoDocument
{

    public $course_code;
    public $sub_or_topic_id;
    public $type;
    public $topic_name;
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
        return 'streams';
    }

    public function rules()
    {
        return [
            ['course_code, sub_or_topic_id', 'required']
        ];
    }

    public function beforeSave()
    {
        if (!$this->created_on) {
            $this->created_on = date("Y-m-d_G:i:s");
        }
        return true;
    }

    public function indexes()
    {
        return [
            'course_code_1_sub_or_topic_id_1' => [
                'key' => [
                    'course_code' => EMongoCriteria::SORT_ASC,
                    'sub_or_topic_id' => EMongoCriteria::SORT_ASC,
                ],
                'unique' => true,
            ],
        ];
    }
}
