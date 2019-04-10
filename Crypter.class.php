<?php
/**
 * Simple encryption, decryption, and hashing
 *
 * Provides a simple interface to PHP's <code>openssl_encrypt()</code>.
 * Expects the following external constants to be defined:
 * <ul>
 *     <li>
 *         <code>CRYPT_KEY_FILE</code> - the name of a file containing an
 *         encryption key. If the file does not exist, it may be created by
 *         calling <code>Crypter::createKeyFile()</code>
 *     </li>
 *     <li>
 *         <code>CRYPTER_CIPHER</code> - The name of the cipher to be used
 *         for encryption and decryption
 *     </li>
 *     <li>
 *         <code>INITIALIZATION_VECTOR</code> - The initialization vector
 *         to be used for encryption and decryption
 *     </li>
 * </ul>
 *
 */
class Crypter {

	private static $key = NULL;



	/**
	 * Creates the key file, if it does not already exist
	 *
	 * @return bool TRUE if the key file already exists or was successfully
	 *              written; FALSE if the key file did not exist and could
	 *              not be created.
	 */
	public static function createKeyFile() {
		if (!defined('CRYPT_KEY_FILE')) {
			throw new Exception('CRYPT_KEY_FILE is not defined.');
		}
		if (is_file(CRYPT_KEY_FILE) and is_readable(CRYPT_KEY_FILE)) {
			return TRUE;
		}
		$dir = dirname(CRYPT_KEY_FILE);
		if (!is_dir($dir)) {
			throw new Exception("Directory '$dir' not found.");
		}
		if (!is_writable($dir)) {
			throw new Exception("Unable to write ".CRYPT_KEY_FILE." to '$dir'; check permissions on that directory.");
		}
		if (FALSE === ($fp = fopen(CRYPT_KEY_FILE, 'w'))) {
			throw new Exception('Unable to open key file for writing.');
		}
		$key = bin2hex(openssl_random_pseudo_bytes(32));
		if (empty($key)) {
			throw new Exception('Unable to generate key.');
		}
		if (FALSE === fwrite($fp, $key)) {
			throw new Exception('Unable to write key to file.');
		}
		chmod(CRYPT_KEY_FILE,0444);
		fclose($fp);
		if (is_file(CRYPT_KEY_FILE) and is_readable(CRYPT_KEY_FILE)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}



	/**
	 * Returns the contents of <code>CRYPT_KEY_FILE</code>
	 *
	 * @return string The contents of <code>CRYPT_KEY_FILE</code>
	 */
	private static function getKey() {
		if (empty(self::$key)) {
			self::$key = hex2bin(file_get_contents(CRYPT_KEY_FILE)); // created during installation
		}
		return self::$key;
	}



	/**
	 * Receives data and returns it in binary encrypted form
	 *
	 * @param  string $data Any arbitrary scalar data; arrays and objects must be serialized before encryption
	 * @return string An encrypted string in binary form
	 */
	public static function encrypt($data) {
		$key = self::getKey();
		return openssl_encrypt(
			$data,
			CRYPTER_CIPHER,
			$key,
			OPENSSL_RAW_DATA,
			INITIALIZATION_VECTOR
		);
	}



	/**
	 * Encrypts data to a base-64-encoded string
	 *
	 * Example:
	 * <pre class="code">
	 * $string    = 'Hello, world!';
	 * $encrypted = Crypter::encrypt64($string);
	 * echo $encrypted;
	 * </pre>
	 *
 	 * Outputs something that looks like:
	 * <pre class="bugout">
	 * WFBudjNma1BNbHJpN2daSFlyY2dWdz09Ojoy5idgkwbjDaeNPmNJ5X7g
	 * </pre>
	 *
	 * @param string $data An arbitrary string
	 * @return string An encrypted, base-64 encodeding of $data
	 */
	public static function encrypt64($data) {
		$encrypted = self::encrypt($data);
		return self::URLSafeBase64Encode($encrypted);
	}



	/**
	 * Decrypts a binary encrypted string encrypted by <code>Crypter::encrypt()</code>
	 *
	 * @param  string $encrypted_binary_data A string of binary data produced by <code>Crypter::encrypt()</code>
	 * @return string The decrypted data
	 */
	public static function decrypt($encrypted_binary_data) {
		$key = self::getKey();
		return openssl_decrypt(
			$encrypted_binary_data,
			CRYPTER_CIPHER,
			$key,
			OPENSSL_RAW_DATA,
			INITIALIZATION_VECTOR
		);
	}



	/**
	 * Decrypts a string encrypted with <code>::encrypt64</code>
	 *
	 * Example:
	 * <pre class="code">
	 * $string    = 'Hello, world!';
	 * $encrypted = Crypter::encrypt64($string);
	 * $decrypted = Crypter::decrypt64($encrypted);
	 * echo $decrypted;
	 * </pre>
	 *
	 * Outputs:
	 * <pre class="bugout">
	 * Hello, world!
	 * </pre>
	 *
	 * @param string $base_64_data A string encrypted with <code>Crypter::encrypt()</code>
	 * @return string The decrypted string
	 */
	public static function decrypt64($base_64_data) { // expects data encoded by Crypter::encrypt64()
		$encrypted_data = self::URLSafeBase64Decode($base_64_data);
		return self::decrypt($encrypted_data);
	}



	/**
	 * Produce a hex encoding of a sha256 hash
	 *
	 * Example:
	 * <pre class="code">
	 * $data = 'Hello world!';
	 * $hash = Crypter::getHash($data);
	 * echo $hash;
	 * </pre>
	 *
	 * Outputs something like:
	 * <pre class="bugout">
	 * 9cb958de0ed225484e4689cb9479ff7bf59d5b82ec53b1e73e0bfe3fa4cfd66f
	 * </pre>
	 *
	 * @param string $data Any arbitrary string
	 * @return string A hex encoding of a sha256 hash of $data
	 */
	public static function getHash($data) {
		return hash ('sha256',$data."\n".self::getKey());
	}



	/**
	 * Encodes data into a URL-safe base-64 string
	 *
	 * Identical to PHP's native <code>base64_encode()</code> except:
	 * <ul>
	 *     <li>'-' (dash) and '_' (underscore) are used instead of '+' and '/'</li>
	 *     <li>There are no terminating '=' characters</li>
	 * </ul>
	 *
	 * @param  string $data Any scalar data. Arrays and objects must be serialized before encoding.
	 * @return string A base-64 encoded string
	 * @link   https://tools.ietf.org/html/rfc4648#section-5
	 * @link   https://en.wikipedia.org/wiki/Base64#Implementations_and_history
	 */
	public static function URLSafeBase64Encode($data) {
		$base_64 = base64_encode($data);
		$url_safe = str_replace(array('+','/','='),array('-','_',''),$base_64);
		return $url_safe;
	}



	/**
	 * Decodes a string encoded by <code>Crypter::::URLSafeBase64Encode()</code>
	 *
	 * @param  string $url_safe_string A string produced by <code>Crypter::::URLSafeBase64Encode()</code>
	 * @return string The original, unencoded data
	 */
	public static function URLSafeBase64Decode($url_safe_string) {
		$base_64 = str_replace(array('-','_'),array('+','/'),$url_safe_string);
		return base64_decode($base_64);
	}

}
