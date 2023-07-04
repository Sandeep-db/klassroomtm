<?php

class Users extends EMongoDocument
{

    public $name;
    public $email;
    public $role;
    public $passwd;
    public $image;
    public $sname;
    public $teaching;
    public $enrolled;

    public function scenarios()
    {
        return [
            'register' => ['name', 'email', 'role', 'passwd'],
            'login' => ['email', 'passwd'],
        ];
    }

    public function defaultScope()
    {
        return [
            'scenario' => 'register',
        ];
    }

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * @inheritDoc
     */
    public function getCollectionName()
    {
        return 'users';
    }

    public function save($runValidation = true, $attribute = null)
    {
        try {
            parent::save($runValidation, $attribute);
        } catch (EMongoException $e) {
            throw $e;
        }
    }

    public function rules()
    {
        return [
            ['name, role', 'required', 'on' => 'register'],
            ['email, passwd', 'required', 'on' => 'register, login'],

            ['email', 'validateEmail'],
            ['role', 'validateRole'],

            ['name', 'length', 'min' => '1', 'max' => '100', 'on' => 'register'],
            ['email', 'length', 'min' => '1', 'max' => '100', 'on' => 'register, login'],
        ];
    }

    public function validateEmail($attribute)
    {
        $value = $this->$attribute;
        $pattern = "/^[A-Za-z0-9\.]{3,40}@gmail\.com$/";
        if (!preg_match($pattern, $value)) {
            $this->addError($attribute, 'Email is not valid');
        }
        return false;
    }

    public function validateRole($attribute)
    {
        $value = $this->$attribute;
        $roles = ['admin', 'teacher', 'student'];
        return in_array($value, $roles);
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'ext.YiiMongoDbSuite.extra.EEmbeddedArraysBehavior',
                'arrayPropertyName' => 'teaching',
                'arrayDocClassName' => 'Teaching' 
            ],
            [
                'class' => 'ext.YiiMongoDbSuite.extra.EEmbeddedArraysBehavior',
                'arrayPropertyName' => 'enrolled',
                'arrayDocClassName' => 'Enrolled' 
            ],
        ];
    }

    // public function embed()
    // {
    //     return [
    //         'teaching' => ['Teaching'],
    //         'enrolled' => ['Enrolled'],
    //     ];
    // }

    public function deleteUser($_id)
    {
        $user = self::model()->findAllByAttributes(['_id' => $_id]);
        if ($user !== null) {
            $user->delete();
            return;
        }
        return false;
    }

    public function beforeSave()
    {
        if (!isset($this->image)) {
            $this->image = 'https://php-bucket-db.s3.us-east-2.amazonaws.com/default-profile-image';
        }
        if (!isset($this->enrolled)) {
            $this->enrolled = [];
        }
        if (!isset($this->teaching)) {
            $this->teaching = [];
        }
        $this->sname = strtolower($this->name);
        return true;
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
            'sname_1' => [
                'key' => [
                    'sname' => EMongoCriteria::SORT_ASC,
                ],
            ],
        ];
    }
}
