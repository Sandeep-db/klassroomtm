<?php

class CalendarEvents extends EMongoDocument
{
    public $schedule_id;
    public $email;
    public $event_id;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @inheritDoc
     */
    function getCollectionName()
    {
        return 'calendarevents';
    }

    public function rules()
    {
        return [
            ['email, schedule_id, event_id', 'required']
        ];
    }

    public function indexes()
    {
        return [
            'schedule_id_1_email_1_event_id_1' => [
                'key' => [
                    'schedule_id' => EMongoCriteria::SORT_ASC,
                    'email' => EMongoCriteria::SORT_ASC,
                    'event_id' => EMongoCriteria::SORT_ASC,
                ],
                'unique' => true,
            ],
        ];
    }
}
