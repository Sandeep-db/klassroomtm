<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity
{
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		// $users = array(
		// 	'username' => 'password',
		// 	'demo' => 'demo',
		// 	'admin' => 'admin',
		// );
		// if (!isset($users[$this->username]))
		// 	$this->errorCode = self::ERROR_USERNAME_INVALID;
		// elseif ($users[$this->username] !== $this->password)
		// 	$this->errorCode = self::ERROR_PASSWORD_INVALID;
		// else
		// 	$this->errorCode = self::ERROR_NONE;
		// return !$this->errorCode;


		// find user
		$user = Users::model()->findByAttributes([
			'email' => $this->username,
			'passwd' => $this->password
		]);

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

		// setting user role
		Yii::app()->user->name = $user->role;

		return true;
	}
}
