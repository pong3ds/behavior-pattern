<?php

include_once "RequireAll.php";

run();

function run(){
    $bird = new Bird();
    $ironMan = new IronMan();

    tryToFly($bird);
    tryToJibJib($bird);
    tryToFire($bird);

    tryToFly($ironMan);
    tryToJibJib($ironMan);
    tryToFire($ironMan);

}

function tryToFly($obj){
    if($obj->isClassExtendedWith(Flyable::class)){
        $obj->fly();
    }
}

function tryToFire($obj){
    if($obj->isClassExtendedWith(Fireable::class)){
        $obj->fire();
    }
}

function tryToJibJib($obj){
    if($obj->isClassExtendedWith(JibJibable::class)){
        $obj->jibjib();
    }
}