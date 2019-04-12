<?php

require 'GPCRFilter.class.php';
require 'Regex.class.php';
define('PASSWORD_REGEX', '~^[0-9a-z]{8,30}$~i');
define('TEST_REGEX',     '~^hello~i');

function dump($var) {
	ob_start();
	var_dump($var);
	$dump = ob_get_contents();
	ob_clean();
	return trim($dump);
}

$_GET['raw']             = '<script>document.write("Hello, world!")</script>';
$_GET['sanitized']       = '<script>document.write("Hello, world!")</script>';
$_GET['boolean_true_1']  = '123';
$_GET['boolean_true_2']  = 'Velociraptors!';
$_GET['boolean_false_1'] = '0';
$_GET['boolean_false_2'] = '';
$_GET['integer_pass_1']  = '42';
$_GET['integer_pass_2']  = '-42';
$_GET['integer_fail_1']  = '42.1';
$_GET['integer_fail_2']  = '4A';
$_GET['unsigned_pass_1'] = '0';
$_GET['unsigned_pass_2'] = '1';
$_GET['unsigned_fail_1'] = '-1';
$_GET['unsigned_fail_2'] = '1.1';
$_GET['float_pass_1']    = '-1000';
$_GET['float_pass_2']    = '2.2';
$_GET['float_fail']      = '2.A';
$_GET['url_pass']        = 'https://www.example.com/path/?this=that';
$_GET['url_fail']        = 'www.example.com';
$_GET['email_pass']      = 'example@example.com';
$_GET['email_fail']      = 'example@example-com';
$_GET['password_pass_1'] = 'mypassword';
$_GET['password_pass_2'] = 'password4321';
$_GET['password_fail_1'] = 'password 4321';
$_GET['password_fail_2'] = 'passme';
$_GET['datetime_pass_1'] = '9:12pm May 24, 1974';
$_GET['datetime_pass_2'] = '24May74';
$_GET['datetime_fail']   = '42';
$_GET['regex_pass']      = 'Hello, world!';
$_GET['regex_fail']      = 'Goodbye, world.';
$_GET['unlisted_1']      = 'Hold Your Fire';
$_GET['unlisted_2']      = 'Snakes and Arrows';
$_GET['unlisted_3']      = 'Clockwork Angels';

GPCRFilter::filter(
	['GET', 'raw',             GPCRFilter::RAW],
#	['GET', 'unlisted_1',      GPCRFilter::RAW],
#	['GET', 'unlisted_2',      GPCRFilter::RAW],
#	['GET', 'unlisted_3',      GPCRFilter::RAW],
	['GET', 'sanitized',       GPCRFilter::SANITIZED],
	['GET', 'boolean_true_1',  GPCRFilter::BOOLEAN],
	['GET', 'boolean_true_2',  GPCRFilter::BOOLEAN],
	['GET', 'boolean_false_1', GPCRFilter::BOOLEAN],
	['GET', 'boolean_false_2', GPCRFilter::BOOLEAN],
	['GET', 'integer_pass_1',  GPCRFilter::INTEGER],
	['GET', 'integer_pass_2',  GPCRFilter::INTEGER],
	['GET', 'integer_fail_1',  GPCRFilter::INTEGER],
	['GET', 'integer_fail_2',  GPCRFilter::INTEGER],
	['GET', 'unsigned_pass_1', GPCRFilter::UNSIGNED],
	['GET', 'unsigned_pass_2', GPCRFilter::UNSIGNED],
	['GET', 'unsigned_fail_1', GPCRFilter::UNSIGNED],
	['GET', 'unsigned_fail_2', GPCRFilter::UNSIGNED],
	['GET', 'float_pass_1',    GPCRFilter::FLOAT],
	['GET', 'float_pass_2',    GPCRFilter::FLOAT],
	['GET', 'float_fail',      GPCRFilter::FLOAT],
	['GET', 'url_pass',        GPCRFilter::URL],
	['GET', 'url_fail',        GPCRFilter::URL],
	['GET', 'email_pass',      GPCRFilter::EMAIL],
	['GET', 'email_fail',      GPCRFilter::EMAIL],
	['GET', 'password_pass_1', GPCRFilter::PASSWORD],
	['GET', 'password_pass_2', GPCRFilter::PASSWORD],
	['GET', 'password_fail_1', GPCRFilter::PASSWORD],
	['GET', 'password_fail_2', GPCRFilter::PASSWORD],
	['GET', 'datetime_pass_1', GPCRFilter::DATETIME],
	['GET', 'datetime_pass_2', GPCRFilter::DATETIME],
	['GET', 'datetime_fail',   GPCRFilter::DATETIME],
	['GET', 'regex_pass',      GPCRFilter::REGEX, TEST_REGEX],
	['GET', 'regex_fail',      GPCRFilter::REGEX, TEST_REGEX]
);

echo "\n\n==== GPCRFilter::RAW ==========================================================\n\n";
echo "\$_GET['raw']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','raw')."\"\n";
echo "    FILTERED:    ".dump($_GET['raw'])."\n\n";
echo "\$_GET['unlisted_1']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','unlisted_1')."\"\n";
echo "    FILTERED:    ".@dump($_GET['unlisted_1'])."\n\n";
echo "\$_GET['unlisted_2']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','unlisted_2')."\"\n";
echo "    FILTERED:    ".@dump($_GET['unlisted_2'])."\n\n";
echo "\$_GET['unlisted_3']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','unlisted_3')."\"\n";
echo "    FILTERED:    ".@dump($_GET['unlisted_3'])."\n\n";

echo "\n\n==== GPCRFilter::SANITIZED ====================================================\n\n";
echo "\$_GET['sanitized']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','sanitized')."\"\n";
echo "    FILTERED:    ".dump($_GET['sanitized'])."\n\n";

echo "\n\n==== GPCRFilter::BOOLEAN ======================================================\n\n";
echo "\$_GET['boolean_true_1']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','boolean_true_1')."\"\n";
echo "    FILTERED:    ".dump($_GET['boolean_true_1'])."\n\n";
echo "\$_GET['boolean_true_2']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','boolean_true_2')."\"\n";
echo "    FILTERED:    ".dump($_GET['boolean_true_2'])."\n\n";
echo "\$_GET['boolean_false_1']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','boolean_false_1')."\"\n";
echo "    FILTERED:    ".dump($_GET['boolean_false_1'])."\n\n";
echo "\$_GET['boolean_false_2']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','boolean_false_2')."\"\n";
echo "    FILTERED:    ".dump($_GET['boolean_false_2'])."\n\n";

echo "\n\n==== GPCRFilter::INTEGER ======================================================\n\n";
echo "\$_GET['integer_pass_1']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','integer_pass_1')."\"\n";
echo "    FILTERED:    ".dump($_GET['integer_pass_1'])."\n\n";
echo "\$_GET['integer_pass_2']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','integer_pass_2')."\"\n";
echo "    FILTERED:    ".dump($_GET['integer_pass_2'])."\n\n";
echo "\$_GET['integer_fail_1']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','integer_fail_1')."\"\n";
echo "    FILTERED:    ".dump($_GET['integer_fail_1'])."\n\n";
echo "\$_GET['integer_fail_2']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','integer_fail_2')."\"\n";
echo "    FILTERED:    ".dump($_GET['integer_fail_2'])."\n\n";

echo "\n\n==== GPCRFilter::UNSIGNED =====================================================\n\n";
echo "\$_GET['integer_pass_1']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','unsigned_pass_1')."\"\n";
echo "    FILTERED:    ".dump($_GET['unsigned_pass_1'])."\n\n";
echo "\$_GET['unsigned_pass_2']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','unsigned_pass_2')."\"\n";
echo "    FILTERED:    ".dump($_GET['unsigned_pass_2'])."\n\n";
echo "\$_GET['unsigned_fail_1']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','unsigned_fail_1')."\"\n";
echo "    FILTERED:    ".dump($_GET['unsigned_fail_1'])."\n\n";
echo "\$_GET['unsigned_fail_2']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','unsigned_fail_2')."\"\n";
echo "    FILTERED:    ".dump($_GET['unsigned_fail_2'])."\n\n";

echo "\n\n==== GPCRFilter::FLOAT ========================================================\n\n";
echo "\$_GET['float_pass_1']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','float_pass_1')."\"\n";
echo "    FILTERED:    ".dump($_GET['float_pass_1'])."\n\n";
echo "\$_GET['float_pass_2']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','float_pass_2')."\"\n";
echo "    FILTERED:    ".dump($_GET['float_pass_2'])."\n\n";
echo "\$_GET['float_fail']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','float_fail')."\"\n";
echo "    FILTERED:    ".dump($_GET['float_fail'])."\n\n";

echo "\n\n==== GPCRFilter::URL ==========================================================\n\n";
echo "\$_GET['url_pass']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','url_pass')."\"\n";
echo "    FILTERED:    ".dump($_GET['url_pass'])."\n\n";
echo "\$_GET['url_fail']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','url_fail')."\"\n";
echo "    FILTERED:    ".dump($_GET['url_fail'])."\n\n";

echo "\n\n==== GPCRFilter::EMAIL ========================================================\n\n";
echo "\$_GET['email_pass']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','email_pass')."\"\n";
echo "    FILTERED:    ".dump($_GET['email_pass'])."\n\n";
echo "\$_GET['email_fail']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','email_fail')."\"\n";
echo "    FILTERED:    ".dump($_GET['email_fail'])."\n\n";

echo "\n\n==== GPCRFilter::PASSWORD (tested against ".PASSWORD_REGEX.") ===============\n\n";
echo "\$_GET['password_pass_1']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','password_pass_1')."\"\n";
echo "    FILTERED:    ".dump($_GET['password_pass_1'])."\n\n";
echo "\$_GET['password_pass_2']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','password_pass_2')."\"\n";
echo "    FILTERED:    ".dump($_GET['password_pass_2'])."\n\n";
echo "\$_GET['password_fail_1']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','password_fail_1')."\"\n";
echo "    FILTERED:    ".dump($_GET['password_fail_1'])."\n\n";
echo "\$_GET['password_fail_2']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','password_fail_2')."\"\n";
echo "    FILTERED:    ".dump($_GET['password_fail_2'])."\n\n";

echo "\n\n==== GPCRFilter::DATETIME =====================================================\n\n";
echo "\$_GET['datetime_pass_1']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','datetime_pass_1')."\"\n";
echo "    FILTERED:    ".dump($_GET['datetime_pass_1'])."\n\n";
echo "\$_GET['datetime_pass_2']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','datetime_pass_2')."\"\n";
echo "    FILTERED:    ".dump($_GET['datetime_pass_2'])."\n\n";
echo "\$_GET['datetime_fail']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','datetime_fail')."\"\n";
echo "    FILTERED:    ".dump($_GET['datetime_fail'])."\n\n";

echo "\n\n==== GPCRFilter::REGEX (tested against ".TEST_REGEX.") ============================\n\n";
echo "\$_GET['regex_pass']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','regex_pass')."\"\n";
echo "    FILTERED:    ".dump($_GET['regex_pass'])."\n\n";
echo "\$_GET['regex_fail']\n";
echo "    INPUT:       \"".GPCRFilter::getUnfilteredInput('GET','regex_fail')."\"\n";
echo "    FILTERED:    ".dump($_GET['regex_fail'])."\n\n";
