<?php
require 'Scrambler.class.php';
define('SALT', 'something random');

$array = array(0,1,2,3,4,5,6,7,8,9);

$scrambled = Scrambler::scramble($array,SALT);
echo "\n";
print_r($scrambled);

$unscrambled = Scrambler::unscramble($scrambled,SALT);
echo "\n";
print_r($unscrambled);
