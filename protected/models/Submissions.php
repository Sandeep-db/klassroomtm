<?php

class Submissions extends EMongoDocument
{
    public $sub_topic_id;
    public $user_email;
    public $rtype;
    public $uid;
    
    public $description;
    public $attachments;
    public $last_updated_on;
    public $summary;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @inheritDoc
     */
    function getCollectionName()
    {
        return 'submissions';
    }

    public function rules()
    {
        return [
            ['sub_topic_id, user_email, rtype, description', 'required'],
            ['summary', 'safe'],
        ];
    }

    public function beforeSave()
    {
        if (!isset($this->attachments)) {
            $this->attachments = [];
        }
        if (!isset($this->uid)) {
            $this->uid = uniqid();
        }
        $this->last_updated_on = date("Y-m-d_G:i:s");
        return Parent::beforeSave();
    }

    public function indexes()
    {
        return [
            'sub_topic_id_1_user_email_1_rtype_1_uid_1' => [
                'key' => [
                    'sub_topic_id' => EMongoCriteria::SORT_ASC,
                    'user_email' => EMongoCriteria::SORT_ASC,
                    'rtype' => EMongoCriteria::SORT_ASC,
                    'uid' => EMongoCriteria::SORT_ASC,
                ],
                'unique' => true,
            ],
            'user_email_1_last_updated_on_1_rtype_1' => [
                'key' => [
                    'user_email' => EMongoCriteria::SORT_ASC,
                    'last_updated_on' => EMongoCriteria::SORT_DESC,
                    'rtype' => EMongoCriteria::SORT_ASC,
                ],
            ],
        ];
    }
}
