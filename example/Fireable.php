<?php

class Fireable
{
    private $me;
    public function __construct($me){
        $this->me = $me;
    }

    public function fire(){
        $myName = get_class($this->me);
        echo "I am a $myName , and I am firing missile\n";
    }
}