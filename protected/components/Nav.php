<?php

class Nav extends Cwidget
{
    public $links;
    public function init()
    {
    }
    public function run()
    {
        // echo "<a href=$this->link1>Link1</a>";
        foreach ($this->links as $tag => $link) {
            echo "<a href=$link>$tag</a>";
        }
    }
}
