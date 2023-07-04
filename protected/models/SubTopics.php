<?php 

class SubTopics extends EMongoDocument
{
    public $course_code;
    public $topic_name;
    public $type;
    public $uid;

    public $name;
    public $description;
    public $attachments;
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
        return 'subtopics';
    }

    public function rules()
    {
        return [
            ['course_code, topic_name, type, uid, name, description', 'required'],
        ];
    }

    public function beforeSave()
    {
        if (!isset($this->attachments)) {
            $this->attachments = [];
        }
        if (!isset($this->created_on)) {
            $this->created_on = date("Y-m-d_G:i:s");
        }
        return true;
    }

    public function indexes()
    {
        return [
            'course_code_1_topic_name_1_type_1_uid_1' => [
                'key' => [
                    'course_code' => EMongoCriteria::SORT_ASC,
                    'topic_name' => EMongoCriteria::SORT_ASC,
                    'type' => EMongoCriteria::SORT_ASC,
                    'uid' => EMongoCriteria::SORT_ASC,
                ],
                'unique' => true,
            ],
        ];
    }
}