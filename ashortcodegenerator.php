<?php

// class that is implemented by all ShortcodeGenerator concrete classes
// Is our abstract Creator Class - Defines our abstract Factory Method that is used to create products
abstract class ShortcodeGenerator {
    public $shortcode;
    public function __construct(){
    }

    public function generateShortcode($type){
        $this->shortcode = $this->createShortcode($type);

    }
    abstract function createShortcode($type); // the factory method to be implemented by concrete creator classes
}
// class that is implemented by all shortcode concrete classes
// Is our abstract Procut Class - Defines a base class for the type of product were going to be making
abstract class Shortcode{

}

// creator concrete classes - They give us the type of shortcodes's we want
// NY Store creator concrete class implements the factory createShortcode method
class ATTgen extends ShortcodeGenerator{
    public function createShortcode($type){
        if($type == 'enclosed'){
            $shortcode = new ATTenclosed();
        }
        else if( $type == 'self-closed'){
            $shortcode = new ATTselfclosed();
        }else{
            $shortcode = null;
        }
        return $shortcode;
    }
}
class NonATTgen extends ShortcodeGenerator{
    public function createShortcode($type){
        if($type == 'enclosed'){
            $shortcode = new NonATTenclosed();
        }
        else if( $type == 'self-closed'){
            $shortcode = new NonATTselfclosed();
        }else{
            $shortcode = null;
        }
        return $shortcode;
    }
}
// a concrete product class
class ATTenclosed extends Shortcode{
    public function __construct(){
    }
}
// a concrete product class
class ATTselfclosed extends Shortcode{
    public function __construct(){
    }
}
// a concrete product class
class NonATTenclosed extends Shortcode{
    public function __construct(){
    }
}
// a concrete product class
class NonATTselfclosed extends Shortcode{
    public function __construct(){
    }
}