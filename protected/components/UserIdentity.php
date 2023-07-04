<?php

use PhpParser\Node\Expr\Print_;

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
	// public function authenticate()
	// {
	// 	$users=array(
	// 		// username => password
	// 		'demo'=>'demo',
	// 		'admin'=>'admin',
	// 	);
	// 	if(!isset($users[$this->username]))
	// 		$this->errorCode=self::ERROR_USERNAME_INVALID;
	// 	elseif($users[$this->username]!==$this->password)
	// 		$this->errorCode=self::ERROR_PASSWORD_INVALID;
	// 	else
	// 		$this->errorCode=self::ERROR_NONE;
	// 	return !$this->errorCode;
	// }
	private $_id;
	public function authenticate()
	{
	
		$user = User::model()->findByAttributes(array('email' => $this->username));
        
        if ($user === null) {
            $this->errorCode = self::ERROR_USERNAME_INVALID;
        } elseif (!password_verify($this->password,$user->password)) {
            $this->errorCode = self::ERROR_PASSWORD_INVALID;
        } else {
            $this->_id = $user->email;
            $this->username = $user->username;
            $this->errorCode = self::ERROR_NONE;

            // Set a cookie to remember the user
            $duration = 3600 * 24 ; // 30 days
            $cookie = new CHttpCookie('auth', $user->email);
            $cookie->expire = time() + $duration;
            Yii::app()->request->cookies['auth'] = $cookie;
        }
        
        return $this->errorCode === self::ERROR_NONE;
	}

	public function getId()
    {
        return $this->_id;
    }
}
