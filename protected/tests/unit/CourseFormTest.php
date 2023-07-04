<?php


include "/data/live/protected/models/CourseForm.php";
use PHPUnit\Framework\TestCase;

class CourseFormTest extends TestCase
{
    public function testValidation()
    {
        $model = new CourseForm();

        // Set invalid values for the attributes
        $model->course_name = '';
        $model->course_code = '';
        $model->course_description = '';
        $model->course_duration = '';
        $model->course_start_date = '';
        $model->course_end_date = '';
        $model->course_instructor = '';

        // Validate the model
        $this->assertFalse($model->validate());

        // Check if there are any errors
        $this->assertNotEmpty($model->errors);

        // Set valid values for the attributes
        $model->course_name = 'Test Course';
        $model->course_code = 'C001';
        $model->course_description = 'Test course description';
        $model->course_duration = '2 weeks';
        $model->course_start_date = '2022-01-01';
        $model->course_end_date = '2022-01-14';
        $model->course_instructor = 'John Doe';

        // Validate the model
        $this->assertTrue($model->validate());

        // Check if there are no errors
        $this->assertEmpty($model->errors);
    }
}
