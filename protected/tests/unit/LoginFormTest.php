<?php

// include "/data/live/protected/models/LoginForm.php";

use PHPUnit\Framework\TestCase;

class LoginFormTest extends TestCase
{
    // public static function setUpBeforeClass()
    // {
    //     Yii::app()->init();
    // }
    public function testValidateUser()
    {
        $form = new LoginForm();
        $form->email = 'sandyblaze955@gmail.com';
        $form->passwd = 'sandyblaze';
        $this->assertTrue($form->validate()); // Expect validation to pass
        $user = $form->findUser();
        $this->assertEmpty($user); // Assert that $user is not empty or null
    }

    public function testInvalidEmail()
    {
        $form = new LoginForm();
        $form->email = 'invalidemail';
        $form->passwd = 'sandyblaze';

        $this->assertFalse($form->validate()); // Expect validation to fail
        $this->assertCount(1, $form->getErrors('email')); // Expect an error message for email field
    }

    public function testInvalidLogin()
    {
        $form = new LoginForm();
        $form->email = 'sandyblaze@gmail.com';
        $form->passwd = 'invalidpassword';

        $this->assertFalse($form->login('passwd')); // Expect login to fail
    }
}
