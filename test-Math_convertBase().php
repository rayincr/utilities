<?php

require 'Math.class.php';

#$vals = array_merge(range(0,65),range(PHP_INT_MAX-20,PHP_INT_MAX-1));

#$vals = range(0,2500000000);

for ($i = 0; $i < 2500000000; $i++) {
	$i62 = Math::convertBase($i,10,62);
	$ii  = Math::convertBase($i62,62,10);
	$test = ($i == $ii)?'PASS':'FAIL';

	if (0 == $i % 1000000) {
		echo "$i\t$i62\t$ii\t$test\n";
	}
	if ($test == 'FAIL') {
		echo "$i\t$i62\t$ii\t$test\n";
	}
}
