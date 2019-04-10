<?php
/**
 * A collection of general-purpose mathmatical utilities
 *
 * Only contains convertBase() so far; more to come.
 *
 * @author Ray Morgan <rayinla@gmail.com>
 */
class Math {

	const CHAR_MAP = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

	/**
	 * Converts numbers from any base (2-62) to any other base (2-62)
	 *
	 * A drop-in replacement for PHP's native <code>base_convert()</code>,
	 * which is limited to base36.
	 *
	 * @param  string $num       The number to convert. Must be < PHP_INT_MAX.
	 * @param  int    $from_base The starting base of <code>$num</code>
	 * @param  int    $to_base   The base to convert to
	 * @return string $num, converted to base <code>$to_base</code>
	 *
	 * @todo   Ensure that the intermediate conversion to base10 does not
	 *         exceed PHP_INT_MAX
	 * @todo   Add ability to handle negative numbers
	 * @todo   Investigate why conversion of the integer 9223372036854775796 fails.
	 */
	function convertBase(string $num, int $from_base, int $to_base) {
		// Verify that the chars in $num are limited to those used in $from_base
		$regex = '~^['.substr(self::CHAR_MAP,0,$from_base).']+$~';
		if (!preg_match($regex,$num)) { // if $num has out-of-range chars...
			throw new Exception("Parameter \$num ('$num') contains characters that are out of range for base $from_base");
		}
		// Convert to base 10
		$val = 0;
		$numlen = strlen($num);
		for ($i = 0; $i < $numlen; $i++) {
			$val = $val * $from_base + strpos(self::CHAR_MAP,$num[$i]);
		}
		if ($val < 0) {return 0;}
		// Convert to $to_base
		$r   = $val % $to_base;
		$to_val = self::CHAR_MAP[$r];
		$q   = floor($val / $to_base);
		while ($q) {
			$r = $q % $to_base;
			$q = floor($q / $to_base);
			$to_val = self::CHAR_MAP[$r].$to_val;
		}
		return $to_val;
	}



	/**
	 * Tests whether an integer is prime
	 *
	 * @param  int  $int The integer to test
	 * @return bool TRUE if <code>$int</code> is prime, otherwise FALSE
	 */
	function isPrime($int) {
		$int = (int)$int;
		if ($int < 3)        {return FALSE;}
		if (0 == ($int % 2)) {return FALSE;}
		if (0 == ($int % 5)) {return FALSE;}
		$sqrt = sqrt($int);
		if ($sqrt == floor($sqrt)) {return FALSE;}
		$div = 3;
		while ($div <= $sqrt) {
			if (0 == $int % $div) {return FALSE;} $div+=5;
			if (0 == $int % $div) {return FALSE;} $div+=2;
			if (0 == $int % $div) {return FALSE;} $div+=2;
			if (0 == $int % $div) {return FALSE;} $div+=2;
		}
		return TRUE;
	}
}
