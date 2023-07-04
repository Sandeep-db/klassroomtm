<?php

class EventQueue extends EMongoDocument
{

    public $email;
    public $type;
    public $event_id;
    public $schedule_id;
    public $event;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @inheritDoc
     */
    function getCollectionName()
    {
        return 'eventqueue';
    }

    public function rules()
    {
        return [
            ['email, type', 'required'],
        ];
    }

    public function indexes()
    {
        return [
            'email_1_type_1' => [
                'key' => [
                    'email' => EMongoCriteria::SORT_ASC,
                    'type' => EMongoCriteria::SORT_ASC,
                ],
            ],
        ];
    }
}
