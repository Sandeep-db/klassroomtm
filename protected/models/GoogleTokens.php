<?php

class GoogleTokens extends EMongoDocument
{

    public $email;
    public $auth_data;

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @inheritDoc
     */
    function getCollectionName()
    {
        return 'googletokens';
    }

    public function rules()
    {
        return [
            ['email, auth_data', 'required'],
        ];
    }

    public function indexes()
    {
        return [
            'email_1' => [
                'key' => [
                    'email' => EMongoCriteria::SORT_ASC,
                ],
                'unique' => true,
            ],
        ];
    }
}
