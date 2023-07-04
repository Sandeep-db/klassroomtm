<?php

/**
 * LoginForm class.
 * LoginForm is the data structure for keeping
 * user login form data. It is used by the 'login' action of 'SiteController'.
 */
class LoginForm extends CFormModel
{
	public $email;
	public $passwd;

	private $_identity;

	/**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return [
			['email, passwd', 'required'],
			['email', 'validateEmail'],
			['password', 'login'],
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

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return [
			'email' => 'email',
			'passwd' => 'passwd',
		];
	}

	public function findUser() 
	{
		$this->_identity = new UserIdentity($this->email, $this->passwd);
		$user = Users::model()->findByAttributes([
			'email' => $this->email,
			'passwd' => $this->passwd
		]);
		return $user;
	}

	public function login($attribute)
	{
		$user = $this->findUser();
		// if user is not found
		if (!$user) return false;

		// setting cookies
		$_idCookie = new CHttpCookie('_id', strval($user->_id));
		$_idCookie->expire = time() + (30 * 86400);
		Yii::app()->request->cookies['_id'] = $_idCookie;

		$nameCookie = new CHttpCookie('name', $user->name);
		$nameCookie->expire = time() + (30 * 86400);
		Yii::app()->request->cookies['name'] = $nameCookie;
		
		$emailCookie = new CHttpCookie('email', $user->email);
		$emailCookie->expire = time() + (30 * 86400);
		Yii::app()->request->cookies['email'] = $emailCookie;

		// setting user role and data
		Yii::app()->user->setState('role', $user->role);
		Yii::app()->user->setState('image', $user->image);

		// logging user
		Yii::app()->user->login($this->_identity);
		return true;
	}
}
