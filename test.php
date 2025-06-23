<?php

interface testInterface {
    public function fromInterface();
}

class Animal implements testInterface {
    private static $legs = 4;
    public static $sound = "animal sound";
    
    public function fromInterface(){
        echo "From Interface";
    }

    public static function getLegs() {
        return self::$legs;
    }
    
    public static function getSound() {
        return self::$sound;
    }
}

class Mammal extends Animal {
    private static $legs = 4;
    public static $sound = "mammal sound";
    
    public static function getLegs() {
        return self::$legs;
    }
    
    public static function getSound() {
        return self::$sound;
    }
}

class Dog extends Mammal {
    private static $legs = 4;
    public static $sound = "woof";
    
    public static function getLegs() {
        return self::$legs;
    }
    
    public static function getSound() {
        return self::$sound;
    }
}

class Labrador extends Dog implements testInterface {
    private static $legs = 4;
    public static $sound = "woof woof";

    public function fromInterface(){
        echo "This is from Interface from Labrador class";
    }

    public static function getLegs() {
        return self::$legs;
    }
    
    public static function getSound() {
        return self::$sound;
    }
}

echo "Animal legs: " . Animal::getLegs() . "</br>";
echo "Animal sound: " . Animal::getSound() . "</br>";

echo "Mammal legs: " . Mammal::getLegs() . "</br>";
echo "Mammal sound: " . Mammal::getSound() . "</br>";

echo "Dog legs: " . Dog::getLegs() . "</br>";
echo "Dog sound: " . Dog::getSound() . "</br>";

echo "Golden Retriever legs: " . Labrador::getLegs() . "</br>";
echo "Golden Retriever sound: " . Labrador::getSound() . "</br>";

$animal = new Animal();
echo $animal->fromInterface() . " - Animal </br>";

$mammal = new Mammal();
echo $mammal->fromInterface() . " - Mammal </br>";

$dog = new Dog();
echo $dog->fromInterface() . " - Dog </br>";

$labrador = new Labrador();
echo $labrador->fromInterface() . " - Labrador </br>";

?>