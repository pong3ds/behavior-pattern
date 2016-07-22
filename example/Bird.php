<?php

class Bird extends Extendable{

    public $implement = [
        Flyable::class,
        JibJibable::class
    ];

    public function __construct(){
        parent::__construct();
    }
}