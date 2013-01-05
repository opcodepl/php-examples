<?php
require_once SV_PATH . '/Filter.php';

class SubmissionValidator_Filter_TestInt extends SubmissionValidator_Filter
{
	/**
	 * Get the error message
	 *
	 * @param  mixed $value  - The value that is being processed
	 * @param  array $params - The params for this instance beign processed
	 * @return string
	 */
	public static function getMessage($value=null, $params=array())
	{
		$message = 'The input must be an integer';
		
		if (isset($params['min'])) {
			$message .= ' and over {min}';
		} 
		
		return self::formatMessage($message, $value, $params);
	}

	static function getProcessDescription($params=array())
	{
		return 'Must be a whole number';
	}

	static public function execute(&$value, $params=null)
	{
		// Check if the value should be checked
		if (self::isEmptyScalar($value)) {
			return true;
		}

		$preg = '/^-?[0-9]+$/';
		if (!is_string($value) && !is_int($value)) {
			return false;
		}
		
		if(!preg_match($preg, $value)) {
			return false;
		}
		
		if (isset($params['min'])) {
			return ($value > $params['min']) ? true : false;
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
		if (!isset($params['min'])) {
			return '"min" must be set';
		}
		if (isset($params['min']) && !preg_match('/^\d+$/', $params['min'])) {
			return 'min must be an int';
		}
		
		return true;
	}
}
