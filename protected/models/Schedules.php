<?php

class Schedules extends EMongoDocument
{
    public $course_code;
    public $startdate;
    public $enddate;
    public $topic;
    public $learningOutcome;
    public $hours;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @inheritDoc
     */
    function getCollectionName()
    {
        return 'schedules';
    }

    public function rules()
    {
        return [
            ['course_code, startdate, enddate', 'required']
        ];
    }

    public function indexes()
    {
        return [
            'course_code_1_startdate_1' => [
                'key' => [
                    'course_code' => EMongoCriteria::SORT_ASC,
                    'startdate' => EMongoCriteria::SORT_ASC,
                ],
                'unique' => true,
            ],
            'course_code_1_enddate_1' => [
                'key' => [
                    'course_code' => EMongoCriteria::SORT_ASC,
                    'enddate' => EMongoCriteria::SORT_ASC,
                ],
                'unique' => true,
            ],
            'startdate_1_enddate_1_course_code_1' => [
                'key' => [
                    'startdate' => EMongoCriteria::SORT_ASC,
                    'enddate' => EMongoCriteria::SORT_ASC,
                    'course_code' => EMongoCriteria::SORT_ASC,
                ],
                'unique' => true,
            ],
        ];
    }
}