<?php
require 'IdObfuscator.class.php';
require 'Math.class.php';
require 'Scrambler.class.php';
define('ID_OBF_SALT', sha1(microtime()));
define('OBF_LEN', 4);

$range = 2**46;
$id = 1;
while ($id <= $range) {
	$idx = IdObfuscator::encode($id);
	$id2 = IdObfuscator::decode($idx);
	echo str_pad($id,  18, ' ', STR_PAD_LEFT);
	echo str_pad($idx, 18, ' ', STR_PAD_LEFT);
	echo str_pad($id2, 18, ' ', STR_PAD_LEFT);
	echo "\n";
	if ($id != $id2) {
		echo "$id\t$idx\t$id2 *** FAIL ***\n";
	}
	$id = ceil($id * 1.1);
}
