<?php
define('CRYPT_KEY_FILE',        __DIR__.DIRECTORY_SEPARATOR.'crypter.key');
define('CRYPTER_CIPHER',        'aes-256-cbc');
define('IV_LEN',                openssl_cipher_iv_length(CRYPTER_CIPHER));
define('INITIALIZATION_VECTOR', openssl_random_pseudo_bytes(IV_LEN));
require 'Crypter.class.php';

if (is_file(CRYPT_KEY_FILE)) { // try to create a new key file each time
	try {
		@unlink(CRYPT_KEY_FILE);
	} catch (Exception $e) {
		// ignore; just use the last file
	}
}
Crypter::createKeyFile();

$input = '"If you choose not to decide, you still have made a choice."';

echo 
	"\n"
	."Input\n"
	."    ".$input."\n\n"
	."CRYPTER_CIPHER\n"
	."    ".CRYPTER_CIPHER."\n\n"
	."INITIALIZATION_VECTOR [encoded with bin2hex() for display]\n"
	."    ".bin2hex(INITIALIZATION_VECTOR)."\n\n"
	."Crypter::createKeyFile()\n"
	."    file = ".CRYPT_KEY_FILE."\n\n"
	."Crypter::encrypt() [encoded with bin2hex() for display]\n"
	."    ".bin2hex($enc = Crypter::encrypt($input))."\n\n"
	."Crypter::decrypt()\n"
	."    ".Crypter::decrypt($enc)."\n\n"
	."Crypter::encrypt64()\n"
	."    ".($enc64 = Crypter::encrypt64($input))."\n\n"
	."Crypter::decrypt64()\n"
	."    ".Crypter::decrypt64($enc64)."\n\n"
	."Crypter::URLSafeBase64Encode()\n"
	."    ".($b64 = Crypter::URLSafeBase64Encode($input))."\n\n"
	."Crypter::URLSafeBase64Decode()\n"
	."    ".Crypter::URLSafeBase64Decode($b64)."\n\n"
	."Crypter::getHash()\n"
	."    ".Crypter::getHash($input)."\n\n"
;
