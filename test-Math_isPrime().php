<?php
require 'Math.class.php';

foreach (range(1,100) as $i) {
    echo $i.(Math::isPrime($i)?"\tPRIME":'')."\n";
}
