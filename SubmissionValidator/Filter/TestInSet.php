<?php
require_once SV_PATH . '/Filter.php';

/**
 * Tests for a value being one of a group of possibles (eg file types)
 * @param array $params['set'] - a SEPARATOR separated list of possibles
 * @param array $params['separator'] - symbol, defaults to comma
 *
 */
class SubmissionValidator_Filter_TestInSet extends SubmissionValidator_Filter
{
	/**
	 * Get the error message
	 *
	 * @param  mixed $value  - The value that is being processed
	 * @param  array $params - The params for this instance begin processed
	 * @return string
	 */
	public static function getMessage($value=null, $params=array())
	{
		$message = 'Value is not one of {set}';
		$separator = ',';
		if (isset($params['split'])) {
			$separator = $params['split'];
		}
		$params['set'] = implode($separator . ' ', explode($separator, $params['set']));
		if (isset($params['msg'])) {
			$message = $params['msg'];
		}
		return self::formatMessage($message, $value, $params);
	}

	static function getProcessDescription($params=array())
	{
		return null;
	}

	static public function execute(&$value, $params=null)
	{
		$separator = ',';
		if (isset($params['split'])) {
			$separator = $params['split'];
		}

		// Check if the value should be checked
		if (self::isEmptyScalar($value)) {
			return true;
		}

		$set = explode($separator, $params['set']);

		if (in_array($value, $set)) {
			return true;
		}else{
			return false;
		}
	}//execute
	
	
	/**
	 * Test the params if they have been passed in correctly
	 *
	 * This function will be called by addProcess to insure that the params are
	 * correct when the process is defined to insure that the error trace is correct
	 * 
	 * @see SubmissionValidator::addProcess
	 *
	 * @return bool|string  - This function should return true if the params are correct
	 *                        or false or a string with an appropriate message if they
	 *                        are incorrect
	 *
	 * @throws Exception
	 */
	static public function checkParams($params=null)
	{
		$separator = ',';
		if (isset($params['split'])) {
			$separator = $params['split'];
		}
		
		if (empty($params['set'])) {
			return __CLASS__ . '::execute - "set" not defined or not separated with ' . $separator;
		}
		
		$exts = explode($separator, $params['set']);
		$exts = array_filter($exts);
		if (count($exts)==0) {
			return __CLASS__ . '::execute - "set" contains no values';
		}
		
		return true;
	}
}
