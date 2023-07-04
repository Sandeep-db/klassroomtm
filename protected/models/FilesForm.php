<?php

class FilesForm extends CFormModel
{
    public $name;
    public $file;

    /**
	 * Declares the validation rules.
	 * The rules state that username and password are required,
	 * and password needs to be authenticated.
	 */
	public function rules()
	{
		return [
			['name, file', 'required'],
			['name', 'validateName'],
		];
	}

    public function validateName($attribute)
    {
        $value = $this->$attribute;
        $pattern = "/[_a-zA-Z0-9]{0,40}/";
        if (!preg_match($pattern, $value)) {
            $this->addError($attribute, 'name is not valid');
        }
        return false;
    }

}