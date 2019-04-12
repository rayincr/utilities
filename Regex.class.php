<?php
class Regex {

	const META_REGEX = '~([^0-9A-Za-z\\\s]).+\1[imsxADSUXJu]*$~';

	public static function isValid($regex) {
		if (empty($regex)) {return FALSE;}
		if (!preg_match(self::META_REGEX,$regex)) {return FALSE;}
		try {
			$result = @preg_match($regex,'');
		} catch (Exception $e) {
			return FALSE;
		}
		return ($result===FALSE)?FALSE:TRUE;
	}

}
