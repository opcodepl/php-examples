<?php
require_once SV_PATH . '/Filter.php';

class SubmissionValidator_Filter_TestStrLen extends SubmissionValidator_Filter
{
	/**
	 * Get the error message
	 *
	 * @param  mixed $value  - The value that is being processed
	 * @param  array $params - The params for this instance being processed
	 * @return string
	 */
	public static function getMessage($value=null, $params=array())
	{
		$message = 'Length of text is ';
		if (isset($params['min']) && isset($params['max']) && $params['min'] == $params['max']) {
			$message .= 'not exactly {min} characters';
		} else {
			$criteria = array();
			if (isset($params['min'])) {
				$criteria[] = 'Min. {min}';
			}
			if (isset($params['max'])) {
				$criteria[] = 'Max. {max}';
			}
			if (count($criteria)) {
				$message .= implode(' and ', $criteria) . ' characters';
			}
		}
		return self::formatMessage($message, $value, $params);
	}	
	
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
		if (!isset($params['min']) && !isset($params['max'])) {
			return 'One of "min" or "max" must be set';
		}
		if (isset($params['min']) && !preg_match('/^\d+$/', $params['min'])) {
			return 'min must be an int';
		}
		if (isset($params['max']) && !preg_match('/^\d+$/', $params['max'])) {
			return 'max must be an int';
		}
		if (isset($params['min']) && isset($params['max']) && $params['min']>$params['max']) {
			return 'max must be the same or greater than min';
		}
		return true;
	}

	static public function getProcessDescription($params=array())
	{
		$message = '';
		if (isset($params['min']) && isset($params['max']) && $params['min'] == $params['max']) {
			$message .= 'Exactly {min} characters';
		} else {
			$criteria = array();
			if (isset($params['min'])) {
				$criteria[] = 'Min. {min}';
			}
			if (isset($params['max']) && $params['max']<10000) {
				$criteria[] = 'Max. {max}';
			}
			if (count($criteria)) {
				$message .= implode(' and ', $criteria) . ' characters';
			}
		}
		return self::formatMessage($message, '', $params);
	}

	static public function execute(&$value, $params=null)
	{
		// Check if the value should be checked
		if (self::isEmptyScalar($value)) {
			return true;
		}

		$len = strlen((string)$value);
		if (isset($params['min']) && isset($params['max']) && $params['min'] == $params['max']) {
			return ($len == $params['max']) ? true : false;
		}
		$min_result = true;
		if (isset($params['min'])) {
			$min_result = ($len >= $params['min']);
		}
		$max_result = true;
		if (isset($params['max'])) {
			$max_result = ($len <= $params['max']);
		}
		return ($max_result && $min_result);
	}//execute
}
