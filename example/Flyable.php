<?php

class Flyable{

    private $me;
    public function __construct($me){
        $this->me = $me;
    }

    public function fly(){
        $myName = get_class($this->me);
        echo "I am a $myName , and I am flying\n";
    }
}