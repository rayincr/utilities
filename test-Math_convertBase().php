<?php
require 'Math.class.php';

$range = 2**46;
$i = 1;

while ($i < $range) {
	$i62 = Math::convertBase($i,10,62);
	$ii  = Math::convertBase($i62,62,10);
	echo str_pad($i,18,' ',STR_PAD_LEFT);
	echo str_pad($i62,18,' ',STR_PAD_LEFT);
	echo str_pad($ii,18,' ',STR_PAD_LEFT);
	echo "\n";
	if ($i != $ii) {
		echo "$i\t$i62\t$ii\t *** FAIL ***\n";
	}
	$i = ceil($i * 1.1);
}
