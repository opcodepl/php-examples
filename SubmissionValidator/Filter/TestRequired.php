<?php
require_once SV_PATH . '/Filter.php';

class SubmissionValidator_Filter_TestRequired extends SubmissionValidator_Filter
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
		$message = 'The field is required';
		return self::formatMessage($message, $value, $params);
	}

	static function getProcessDescription($params=array())
	{
		return 'Required field';
	}

	static public function execute(&$value, $params=null)
	{
		if (is_array($value)) {
			$tmp_value = array_filter($value);
			return count($tmp_value)>0;
		} else {
			return !self::isEmptyScalar($value);
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
		return true;
	}
}
