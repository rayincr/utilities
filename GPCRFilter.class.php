<?php
/**
 * Filters, cleans and validates <code>$_GET</code>, <code>$_POST</code>,
 * <code>$_COOKIE</code>, and <code>$_REQUEST</code> parameters
 *
 * The intent of <code>GPCRFilter</code> is to help prevent SQL-injection
 * and XSS attacks. Filtering user input is <em>one</em> thing you should
 * do, but not the <em>only</em> thing you should do.
 *
 * Calling <code>GPCRFilter::filter()</code> with the appropriate
 * arguments filters elements of the <code>$_GET</code>, <code>$_POST</code>,
 * <code>$_COOKIE</code>, and <code>$_REQUEST</code> superglobals so that
 * any values not matching the specified types are set to <code>NULL</code>.
 *
 * <h5>Example:</h5>
 * <pre class="code">
 * &lt;?php
 * GPCRFilter::filter(
 *     ['GET', 'first_name', GPCRFilter::SANITIZED],
 *     ['GET', 'last_name',  GPCRFilter::REGEX, '/^[A-Za-z]+$/'],
 *     ['GET', 'website',    GPCRFilter::URL],
 *     ['GET', 'email',      GPCRFilter::EMAIL],
 *     ['GET', 'password',   GPCRFilter::PASSWORD],
 *     ['GET', 'birthdate',  GPCRFilter::DATETIME]
 * );
 * </pre>
 * Any elements not whitelisted are deleted.
 *
 * <h5>Filter types:</h5>
 * <ul>
 *     <li>
 *         <code>GPCRFilter::RAW</code> -
 *         Leave the variable unfiltered. This usually defeats the whole
 *         purpose of filtering, so use this at your own risk.
 *     </li>
 *     <li>
 *         <code>GPCRFilter::SANITIZED</code> -
 *         Filtered through <code>filter_var($value, FILTER_SANITIZE_STRING)</code>
 *     </li>
 *     <li>
 *         <code>GPCRFilter::BOOLEAN</code> -
 *         Cast to boolean via <code>(bool)$value</code>
 *     </li>
 *     <li>
 *         <code>GPCRFilter::INTEGER</code> -
 *         Negative integer, zero, or positive integer
 *     </li>
 *     <li>
 *         <code>GPCRFilter::UNSIGNED</code> -
 *         Non-negative integer
 *     </li>
 *     <li>
 *         <code>GPCRFilter::FLOAT</code> -
 *         Negative, zero, or positive value
 *     </li>
 *     <li>
 *         <code>GPCRFilter::URL</code> -
 *         Filtered through <code>filter_var($value, FILTER_VALIDATE_URL)</code>
 *     </li>
 *     <li>
 *         <code>GPCRFilter::EMAIL</code> -
 *         Validated agains <code>EmailAddress::validate()</code>, if defined, or
 *         through <code>filter_var($value, FILTER_VALIDATE_EMAIL)</code>
 *     </li>
 *     <li>
 *         <code>GPCRFilter::PASSWORD</code> -
 *         Tested agains the regular expression defined in <code>PASSWORD_REGEX</code>
 *     </li>
 *     <li>
 *         <code>GPCRFilter::DATETIME</code> -
 *         Parsed by <code>strtotime($value)</code>, and output in the format YYYY-MM-DD HH:MM::SS
 *     </li>
 *     <li>
 *         <code>GPCRFilter::REGEX</code> -
 *         Tested against the supplied regular expression
 *     </li>
 * </ul>
 *
 * @todo Decide how to deal with arrays - all elements should pass
 */
class GPCRFilter {

	private static $temp_data; // where data is staged while being filtered
	private static $initialized = FALSE;

	const RAW       =    1;    // sanitized with filter_var($str, FILTER_SANITIZE_STRING);
	const SANITIZED =    2;    // sanitized with filter_var($str, FILTER_SANITIZE_STRING);
	const BOOLEAN   =    4;
	const INTEGER   =    8;    // positive or negative integer
	const UNSIGNED  =   16;    // unsigned integer >=0
	const FLOAT     =   32;
	const URL       =   64;
	const EMAIL     =  128;
	const PASSWORD  =  256;
	const DATETIME  =  512;
	const REGEX     = 1024;

	private static function init() {
		if (!self::$initialized) { // only initialize once
			self::$temp_data['GET']     = $_GET;
			self::$temp_data['POST']    = $_POST;
			self::$temp_data['COOKIE']  = $_COOKIE;
			self::$temp_data['REQUEST'] = $_REQUEST;
			$_GET     = array();
			$_POST    = array();
			$_COOKIE  = array();
			$_REQUEST = array();
		}
		self::$initialized = TRUE;
	}



	/**
	 * Filter parameters received in <code>$_GET</code>, <code>$_POST</code>,
	 * <code>$_COOKIE</code>, <code>$_REQUEST</code>
	 *
	 * @param array $filters Receives an array of arbitrary length, each
	 *                       element of which is an array(&lt;channel&gt;,
	 *                       &lt;param_name&gt;, &lt;filter_type&gt;
	 *                       [, &lt;regex&gt;])
	 * @return array <code>$_GET</code>, <code>$_POST</code>,
	 *               <code>$_COOKIE</code>, <code>$_REQUEST</code>, filtered
	 *               according to the parameters received.
	 */
	public static function filter() {
		self::init();
		$channels = [
			'G'       => 'GET',
			'GET'     => 'GET',
			'P'       => 'POST',
			'POST'    => 'POST',
			'C'       => 'COOKIE',
			'COOKIE'  => 'COOKIE',
			'R'       => 'REQUEST',
			'REQUEST' => 'REQUEST'
		];
		foreach (func_get_args() as $i => $filter) {
			if (
				!empty($filter[0])
				and in_array($filter[0],array_keys($channels))
				and !empty($filter[1])
				and preg_match('~^[A-Za-z_\x7f-\xff][0-9A-Za-z_\x7f-\xff]*$~',$filter[1])
				and !empty($filter[2])
				and is_int($filter[2])
			) {
				$channel = $channels[$filter[0]];
				$varname = $filter[1];
				$type    = $filter[2];

				if (!isset(self::$temp_data[$channel][$varname])) {
					self::$temp_data[$channel][$varname] = NULL;
				}
				$value = trim(self::$temp_data[$channel][$varname]);

				if ($type & self::RAW) {
					// leave the variable as-is
				}
				if ($type & self::SANITIZED) {
					$value = filter_var((string)$value, FILTER_SANITIZE_STRING);
				}
				if ($type & self::BOOLEAN) {
					$value = (bool)$value;
				}
				if ($type & self::INTEGER) {
					if (preg_match('~^-?[0-9]+$~',$value)) {
						$value = (int)$value;
					} else {
						$value = NULL;
					}
				}
				if ($type & self::UNSIGNED) {
					if (preg_match('~^[0-9]+$~',$value)) {
						$value = (int)$value;
					} else {
						$value = NULL;
					}
				}
				if ($type & self::FLOAT) {
					if (preg_match('~^-?[0-9]+(?:\.[0-9]+)?$~', $value)) {
						$value = (float)$value;
					} else {
						$value = NULL;
					}
					if (!is_float($value)) {}
				}
				if ($type & self::URL) {
					$flags = (FILTER_VALIDATE_URL);
					if (!filter_var($value, $flags)) {
						$value = NULL;
					}
				}
				if ($type & self::EMAIL) {
					$value = filter_var($value, FILTER_SANITIZE_EMAIL);
					if (method_exists('EmailAddress','validate')) {
						if (!EmailAddress::validate($value)) {
							$value = NULL;
						}
					} else {
						if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
							$value = NULL;
						}
					}
				}
				if ($type & self::PASSWORD) {
					if (!preg_match(PASSWORD_REGEX,$value)) {$value = NULL;}
				}
				if ($type & self::DATETIME) {
					if (FALSE === ($datetime = strtotime($value))) {
						$value = NULL;
					} else {
						$value = date('Y-m-d H:i:s',$datetime);
					}
				}
				if ($type & self::REGEX) {
					if (!empty($filter[3])) {
						$pattern = $filter[3];
						if (!Regex::isValid($pattern)) {
							$pattern = NULL;
						}
					}
					if (empty($pattern) or !preg_match($pattern,$value)) {$value = NULL;}
				}

				switch ($channel) {
					case 'GET':     $_GET[$varname]     = $value; break;
					case 'POST':    $_POST[$varname]    = $value; break;
					case 'COOKIE':  $_COOKIE[$varname]  = $value; break;
					case 'REQUEST': $_REQUEST[$varname] = $value; break;
					default: // do nothing
				}
			}
		}
	}



	public static function getUnfilteredInput($channel,$varname) {
		if (isset(self::$temp_data[$channel][$varname])) {
			return self::$temp_data[$channel][$varname];
		}
		return NULL;
	}


}
