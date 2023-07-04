<?php

use PHPUnit\Framework\TestCase;
include "/data/live/protected/controllers/HomeController.php";

class HomeControllerTest extends TestCase
{

    public function testStream()
    {
        $controller = new HomeController('home');
        $course_code = 'node-2023';
        $data = $controller->streamPage($course_code);
        $this->assertNotEmpty($data);
    }
    public function testClasswork(){
        $controller = new HomeController('home');
        $course_code = 'node-2023';
        $topic_name = 'Express';
        $type = 'assignment';

        $data = $controller->classworkPage($course_code, $topic_name, $type);
        $this->assertNotEmpty($data);
    }
    public function testPeople(){
        $controller = new HomeController('home');
        $course_code = 'node-2023';
        $data = $controller->peoplePage($course_code);
        $this->assertNotEmpty($data);
    }
    public function testSchedule(){
        $controller = new HomeController('home');
        $course_code = 'node-2023';
        $data = $controller->streamPage($course_code);
        $this->assertNotEmpty($data);
    }
  
}
