<?php
/**
 * Scrambles and unscrambles strings and arrays
 *
 * Receives a string or an array + a salt value and returns a scrambled
 * version of it. <code>::unscramble()</code> unscrambles the string or array.
 * (Unscrambling requires that the same salt value be used.)
 *
 * Only works with simple, integer-indexed arrays; it will not work on
 * associative arrays.
 */
class Scrambler {

	public static function scramble($input,$salt) {
		return self::reorder($input, $salt, $unscramble = FALSE);
	}

	public static function unscramble($input,$salt) {
		return self::reorder($input, $salt, $unscramble = TRUE);
	}

	private static function reorder($input, string $salt, bool $unscramble = FALSE) {
		$string_mode = FALSE;
		if (!is_array($input)) { // it's a string
			$input = str_split($input);
			$string_mode = TRUE;
		}
		$input  = array_values($input);  // just to be sure the keys are correct
		$count  = count($input);
		$seq    = self::getSequence($salt,$count);
		if ($unscramble) {$seq = array_flip($seq);}
		$return = array();
		for ($i = 0; $i < $count; $i++) {
			$return[$seq[$i]] = $input[$i];
		}
		ksort($return);
		return $string_mode ? join('',$return) : $return;
	}

	private static function getSequence($salt,$count) {
		$keys = array();
		for ($i = 0; $i < $count; $i++) {$keys[$i] = sha1($i."\t".$salt);}
		$keys = array_flip($keys);
		ksort($keys);
		return array_values($keys);
	}
}
