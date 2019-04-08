<?php
/**
 * Obfuscates integer IDs for use in URLs
 *
 * Provides a method for obfuscating integer IDs to prevent revealing
 * information about the number of objects in the system and discourage
 * bad actors from probing pages using a sequence of numeric IDs. Expects
 * <code>ID_OBF_SALT</code> to be defined, which should be unique to each
 * site.
 *
 * This is obfuscation, not strong cryptography, and should not be used
 * for security-related functions.
 *
 * Obfuscated IDs should not be saved.
 *
 * The methods assume that two external constants are defined:
 * <ul>
 *     <li>
 *         <code>ID_OBF_SALT</code> &mdash;
 *         An arbitrary string with which to salt various hashing operations
 *     </li>
 *     <li>
 *         <code>OBF_LEN</code> &mdash;
 *         An integer, usually between 2 and 4
 *     </li>
 * </ul>
 *
 * @todo Verify tamper-resistance of new algorithm.
 */
class IdObfuscator {

	public static function encode(int $id) {
		$chars = Scrambler::scramble('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',ID_OBF_SALT);
		$init_char = $chars[$id%52];
		$hash      = self::getBase62Hash($id, OBF_LEN);
		$id62      = Math::convertBase($id, 10, 62);
		return $init_char.Scrambler::scramble($hash.$id62, ID_OBF_SALT.$init_char);
	}

	public static function decode(string $idx) {
		$init_char = $idx[0];
		$idx  = substr($idx,1);
		$idx  = Scrambler::unscramble($idx,ID_OBF_SALT.$init_char);
		$id62 = substr($idx, OBF_LEN);
		return Math::convertBase($id62,62,10);
	}

	public static function getBase62Hash($input, int $length) {
		$hex = substr(sha1($input.ID_OBF_SALT),0,8);
		$b62 = str_repeat('0',$length).Math::convertBase($hex,16,62);
		return substr($b62,-$length);
	}

}
