<?php

class JibJibable{

    private $me;
    public function __construct($me){
        $this->me = $me;
    }

    public function jibjib(){
        $myName = get_class($this->me);
        echo "I am a $myName , and I am Jib Jib\n";
    }
}