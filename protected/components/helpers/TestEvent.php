<?php
    class TestEvent extends CEvent
    {
        public $var1;
        public $var2;
    
        public function __construct(TestClass $sender, $var1, $var2)
        {
            parent::__construct($sender);
    
            $this->var1 = $var1;
            $this->var2 = $var2;
        }
    }
?>