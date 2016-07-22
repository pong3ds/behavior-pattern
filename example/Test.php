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
    if($obj->hasAbilityTo(Flyable::class)){
        $obj->fly();
    }
}

function tryToFire($obj){
    if($obj->hasAbilityTo(Fireable::class)){
        $obj->fire();
    }
}

function tryToJibJib($obj){
    if($obj->hasAbilityTo(JibJibable::class)){
        $obj->jibjib();
    }
}