<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Error\Warning;

include "/data/live/protected/controllers/ClassController.php";

class ClassControllerTest extends TestCase
{

    public function testEmpty(): array
    {
        $stack = [];
        $this->assertEmpty($stack);

        return $stack;
    }

    /**
     * @depends testEmpty
     */
    public function testPush(array $stack): array
    {
        array_push($stack, 'foo');
        $this->assertSame('foo', $stack[count($stack) - 1]);
        $this->assertNotEmpty($stack);

        return $stack;
    }
}
