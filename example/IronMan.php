<?php

class IronMan extends Extendable{

    public $implement = [
        Flyable::class,
        Fireable::class
    ];

    public function __construct(){
        parent::__construct();
    }
}