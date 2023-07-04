<?php

class TestClass extends CComponent
{
    public function test()
    {
        // echo 'Running test()<br />';

        // echo 'Now firing event<br />';
        $event = new TestEvent($this, 1, 'someTestParam'); // see TestEvent class constructor
        $this->raiseEvent('onTest', $event);
        // $this->onTest($event);
    }

    public function onTest($event)
    {
        // $this->raiseEvent('onTest', $event);
    }
}
