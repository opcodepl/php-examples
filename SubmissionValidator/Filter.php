<?php

abstract class SubmissionValidator_Filter
{
	/**
	 * The test to be run 
	 * 
	 * Example:
	 * TestInSet[set=Mr,Mrs,Ms,Miss,Dr]
	 *
	 * @param mixed &$value - The value to be tested against or processed
	 * @param array $params - (Optional) arguments that can be passed to a 
	 *                        Filter String in between the square brackets
	 * 
	 * @return bool
	 */
	abstract static public function execute(&$value, $params=null);	//return bool

	
	/**
	 * Test the params if they have been passed in correctly
	 *
	 * This function is intended to be overloaded, it has not been declared abstract as it
	 * would break backward compatible
	 *
	 * This function should be able to cope with params that are substitution fields
	 *
	 * This function will be called by addProcess to ensure that the params are
	 * correct when the process is defined to ensure that the error trace is correct
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

	/**
	 * Get the error message
	 *
	 * @param  mixed $value  - The value that is being processed
	 * @param  array $params - (optional) The params for this instance beign processed
	 *
	 * @return string
	 */
	abstract public static function getMessage($value=null, $params=array());
	
	/**
	 * Guidance to show to the user for the feild
	 *
	 * Typically this would be displayed by the field
	 *
	 * @param array $params - (optional) Ths params for the field
	 *
	 * @return string
	 */
	abstract public static function getProcessDescription($params=array());
	
	/**
	 * Check if the value is a valid scaler
	 * And the value is empty
	 *
	 * @param mixed $value - Value to check
	 *
	 * @return bool
	 */
	protected static function isEmptyScalar($value)
	{
		// Check if the value is a scaler
		if (!is_scalar($value) && !is_null($value)) {
			throw new Exception('Scaler expected - "' . gettype($value) . '" given');
		}
		return (((is_string($value) && $value=='') || is_null($value)));
	}
	
	/**
	 * Standard message processing called from inside every getMessage method
	 *
	 * Replaces macros in the string
	 *
	 * @param sting $msg    - The message to be formatted
	 * @param mixed $value  - (optional) The value
	 * @param array $params - (optional) The params
	 *
	 * @return string
	 */
	static protected function formatMessage($msg, $value=null, $params=array())
	{	
		// Build an array of replacements
		$replace=array();
		if (is_array($value)) {
			$val = '';
			foreach ($value as $k=>$v) {
				$val .= "$k=$v|";
			}
			rtrim($val,'|');
			$replace['{value}'] = $val;
		} elseif ($value!==null) {
			$replace['{value}'] = $value;
		}
		
		if (is_array($params) && count($params)>0) {
			foreach ($params as $key=>$val) {
				if (!is_array($val)) {
					$replace['{' . $key . '}'] = $val;
				}
			}//FOREACH
		}//IF
		
		return str_replace(array_keys($replace), array_values($replace), $msg);
	}

	/**
	 * Check if value is a linear or assoc array
	 *
	 * @param  array $value
	 * @return bool
	 */
	static public function isLinearArray(array $value)
	{
		if (!is_array($value)) {
			throw new Exception("Value only allowed to be an array");
		}

		// Check for any differences between the original array and the forced liner version of the array
		$differences = array_diff_assoc($value, array_values($value));

		return count($differences)==0;
	}
}