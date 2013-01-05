<?php

define('SV_PATH', dirname(__FILE__));

class SubmissionValidator
{
	const FILTER_PREFIX = 'SubmissionValidator_Filter_';
	
	protected $filter_list = array();
	protected $process_list = array();
	protected $message_list = array();
	protected $labels = array();
	protected $error_msgs = array();
	protected $processed_array = array();
	protected $filter_msg_prefix = 'Error with "{label}": ';
	protected $basic_filters = array(
		'TestRequired',
		'TestInt',
		'TestInSet',
		'TestStrLen',
		'TestEmail',
	);

	/**
	 * Create an instance of SubmissionValidator
	 *
	 * @param bool $add_basic - if true the basic set of filters are added to the iP
	 */
	public function __construct($add_basic=true)
	{
		if ($add_basic) {
			$this->requireBasicFilters();
			$this->addFilters($this->basic_filters);
		}
	}

	/**
	 * Load the class files for all of the standard filter which come with SubmissionValidator
	 */
	public function requireBasicFilters()
	{
		foreach ($this->basic_filters as $filter) {
			require_once SV_PATH .'/Filter/' . $filter . '.php';
		}
	}

	/**
	 * Makes a filter type available to the SubmissionValidator.
	 *
	 * @param string $filter_name
	 * @return bool - success
	 */
	public function addFilter($filter_name)
	{
		if (substr($filter_name, 0, 4) != 'Test') {
			throw new Exception('Filter must be prefixed with Get or Test depending on its function (formatting or validation)');
			return false;
		}//IF

		$class = self::FILTER_PREFIX . $filter_name;
		$filter_obj = new $class();

		if (!is_subclass_of($filter_obj, 'SubmissionValidator_Filter')) {
			throw new Exception('Filter ' . $filter_name . ' is not subclass of filter in ' . __CLASS__ . '::' . __METHOD__);
			return false;
		}

		$this->filter_list[] = $filter_name;
		$this->$filter_name = & $filter_obj;
		return true;
	}

//addFilter

	/**
	 * Adds an array of filters
	 *
	 * @param array $filter_names
	 */
	protected function addFilters($filter_names)
	{
		foreach ($filter_names as $filter_name) {
			$this->addFilter($filter_name);
		}//FOREACH
	}

	/**
	 * Retrieves the filter list
	 *
	 * @return string filter_list
	 */
	public function getFilterList()
	{
		return $this->filter_list;
	}

	/**
	 * Internal method to break-down a process string into an array
	 *
	 * @param string $process_string
	 *
	 * @return assoc $filter_calls - filter=>array( filter_param=>filter_val )
	 */
	public function parseProcess($process_string)
	{
		$filter_calls = array();
		$calls = array();
		if (is_null($process_string) || (is_string($process_string) && empty($process_string))){
			return array();
		}
		if (preg_match_all('/([a-zA-Z\]])(\|)(Get|Test)/', $process_string, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER) > 0) {
			$off_set = 0;
			foreach ($matches as $m) {
				$pos = $m[2][1];
				$calls[] = substr($process_string, $off_set, $pos - $off_set);
				$off_set = 1 + $pos;
			}
			$calls[] = substr($process_string, $off_set);
			$calls = array_unique($calls);
		} else {
			$calls[] = $process_string;
		}

		foreach ($calls as $call) {
			if (preg_match('/^((?:Get|Test)\w+)(?:\[(.+)\])?$/', $call, $matches) == 0) {
				throw new Exception('Filter not added or bad filter call (' . $call . ') in process string "' . $process_string . '" - ' . __METHOD__);
			} elseif (!in_array($matches[1], $this->filter_list)) {
				throw new Exception('Unknown Filter (' . $matches[1] . ') "' . $process_string . '" - ' . __METHOD__ . "\n Registered Filters: " . implode(', ', $this->filter_list));
			} else {
				$params = array();


				if (isset($matches[2])) { //parameters defined for Filter call
					//Make sure that external field names (surrounded with {})
					//that use the array notation (i.e. include ][) don't get diveded.
					//To acheive that replace all occurances of ][ with $internal,
					//and then restore them once the $params array is filled in.
					//This allows for ip strings like below:
					//TestRequiredOnlyIf[value={response[23][conserns_select][value]}][equals=Yes]
					$internal = '::INTERNAL::';
					preg_match('/\{.*\}/U', $matches[2], $internal_array_names);
					if (count($internal_array_names) > 0) {
						foreach ($internal_array_names as $name) {
							$original_external_field_name = $name;
							$updated_external_field_name = str_replace('][', $internal, $original_external_field_name);
							$matches[2] = str_replace($original_external_field_name, $updated_external_field_name, $matches[2]);
						}
					}

					$params_tmp = explode('][', $matches[2]);
					foreach ($params_tmp as $param) {
						$pos = strpos($param, '=');
						$param_name = substr($param, 0, $pos); //finds FIRST equals sign
						$param_val = substr($param, $pos + 1); //finds FIRST equals sign
						$params[$param_name] = $param_val;
					}//FOREACH
					//restore the replaced sqare brackets
					if (count($internal_array_names) > 0) {
						foreach ($params as $key => $value) {
							if (strpos($value, $internal) !== false) {
								$params[$key] = str_replace($internal, '][', $value);
							}
						}
					}
				}//IF
				$params['_process'] = $matches[1];
				$filter_calls[] = $params;
			}//IF
		}//FOREACH

		return $filter_calls;
	}

//parseProcess

	/**
	 * Adds a process for a field to the SubmissionValidator.
	 *
	 * @param string $field
	 * @param string $process_string - e.g. TestRequired|TestDatetime|TestDateCompare[comparison=>=][date={start_date}]|GetMDBDatetime
	 * The string should be chained in order of execution, supplying any parameters in square brackets. Other fields in the array can be referenced using curly brace notation.
	 * @return bool - success
	 */
	public function addProcess($field, $process_string=null, $label=null)
	{
		if (isset($this->process_list[$field])) {
			throw new exception('Attempt to set process twice for field ' . $field . ' - ' . __CLASS__ . '::' . __METHOD__);
			return false;
		}
		
		if ($label) {
			$this->labels[$field] = $label;
		} elseif (!isset($this->labels[$field])) {
			$this->labels[$field] = ucwords(str_replace(array('-', '_', '[', ']'), ' ', $field));
		}

		// Pares the params
		$this->process_list[$field] = $this->parseProcess($process_string);

		// Test params
		foreach ($this->process_list[$field] as $params) {
			$filter = $params['_process'];
			unset($params['_process']);
			$params['input_field'] = $field;
			$return = $this->$filter->checkParams($params);
			if ($return === false || is_string($return)) {
				if (!is_string($return)) {
					throw new Exception("There was an issue with the params for filter: \"$filter\"");
				} else {
					throw new Exception($return);
				}
			}//IF
		}

		return true;
	}

//addProcess

	/**
	 * Removes a process for a field to the SubmissionValidator.
	 *
	 * @param  string $field
	 * @return bool - success
	 */
	public function removeProcess($field)
	{
		if (isset($this->process_list[$field])) {
			unset($this->process_list[$field]);
			return true;
		} else {
			throw new exception('Attempt to remove process which does\'t exist for field ' . $field . ' - ' . __CLASS__ . '::' . __METHOD__);
			return false;
		}
	}

//removeProcess

	/**
	 * Validate and/or format a field value using the pre-defined process for that field.
	 * Used internally for batch processing of arrays/objects. See ->process for external call.
	 * Store error msgs in object for later retrieval.
	 * NB: Value is modified in-line (passed by ref)
	 *
	 * @param string $field
	 * @param mixed &$value
	 * @return bool $success
	 */
	protected function runStoredProcess($field, &$value)
	{
		$value_tmp = $value;
		$success = true;
		if (!isset($this->process_list[$field])) { //is this needed?!
			throw new exception('No stored process defined for field ' . $field . ' in ' . __METHOD__);
			return false;
		}

		foreach ($this->process_list[$field] as $params) {
			$filter = $params['_process'];
			unset($params['_process']);
			if ($success) { //stop processing once error
				//compare values for refs to other input values
				$params = $this->_substituteParams($params);

				//
				$params['input_field'] = $field;
				if (!$this->$filter->execute($value_tmp, $params)) {
					$this->error_msgs[$field] = $this->getMessage($filter, $field, $value_tmp, $params);
					$success = false;
				}//IF
			}//IF
		}//FOREACH
		$value = $value_tmp;

		return $success;
	}

//runStoredProcess

	/**
	 * Replace values in the params with the values from other fields
	 * 
	 * @param $array $params
	 *
	 * @return $array
	 */
	private function _substituteParams($params)
	{
		if (isset($this->replace_array)) {
			$replace = $this->replace_array;
		} else {
			$replace = $this->replace_array = $this->_buildReplaceArray();
		}
		if (count($replace)>0) {
			
			//field names have to be escaped before they can be used in the preg below
			$replace_for_preg = array();
			foreach ($replace as $field => $value) {
				$replace_for_preg[preg_quote($field)] = is_array($value)?'array':$value;
			}
			$preg = '^(' . implode('|', array_keys($replace_for_preg)) . ')^';
			foreach ($params as $key => $val) {
				if (preg_match_all($preg, $val, $matches)) {
					$params[$key . '_fields'] = $matches[1];
					if (count($matches[1])==1 && isset($replace[$matches[1][0]])) {
						$params[$key] = $replace[$matches[1][0]];
					} else {
						$params[$key] = str_replace(array_keys($replace), $replace_for_preg, $val);
					}
				}
			}
		}
		return $params;
	}

	/**
	 * Create an array with field names and their values.
	 *
	 * Field names get surrounded with curly brackets and use the HTML array
	 * notation, so the following array:
	 * array(
	 * 		'field1' => 'value',
	 * 		'field2' => array(
	 * 						'subfield1' => 'value1',
	 * 						'subfield2' => 'value2',
	 * 						),
	 * )
	 *
	 * will be converted to this one:
	 * array(
	 * 		{field1} => 'value',
	 * 		{field2[subfield1]} => 'value1',
	 * 		{field2[subfield2]} => 'value2',
	 * )
	 *
	 * @return array
	 */
	private function _buildReplaceArray()
	{
		$r = array();
		if (isset($this->input_array)) {
			$this->_buildReplaceArrayRecurse($r, $this->input_array);
		}
		
		if (isset($this->process_list)) {
			foreach ($this->process_list as $field => $ignore) {
				$f = '{' . $field . '}';
				if (!array_key_exists($f, $r)) {
					$r[$f] = '';
					$r['{label:' . $field . '}'] = $this->getLabel($field);
				}
			}
		}
		if (isset($this->labels)) {
			foreach ($this->labels as $field => $ignore) {
				$f = '{' . $field . '}';
				if (!array_key_exists($f, $r)) {
					$r[$f] = '';//$this->getLabel($field);
					$r['{label:' . $field . '}'] = $this->getLabel($field);
				}
			}
		}
		return $r;
	}

	/**
	 * Build an array of replacements for substitution values, for certain array
	 * inputs we need to do extra checks to correctly handle their case. Specifically
	 * files and linear arrays.
	 *
	 * This function is only meant to be used by _buildReplaceArray()
	 *
	 * @see SubmissionValidator::_buildReplaceArray()
	 *
	 * @param array       $build   - The array to store the results in, will be build up recursively
	 * @param array       $current - The current array  to be processed
	 * @param string|null $prefix  - (optional) The field prefix, if set the key from the current array will be aeed to prefix[key]
	 */
	private function _buildReplaceArrayRecurse(&$build, $current, $prefix=null)
	{
		foreach ($current as $k => $v) {
			$field = $k;
			if (!is_null($prefix)) {
				$field = $prefix . '[' . $field . ']';
			}
			if (is_array($v)) {
				
				if (SubmissionValidator_Filter::isLinearArray($v)) {
					$build['{' . $field . '}'] = implode(',', $v);
					$build['{label:' . $field . '}'] = $this->getLabel($field);
				} else {
					$this->_buildReplaceArrayRecurse($build, $v, $field);
					$inner_glue = '=';

					$vals = array();
					foreach($v as $k=>$val) {
						$vals[] = "$k=$val";
					}

					$build['{' . $field . '}'] = implode(',', $vals);
				}
			} else {
				$build['{' . $field . '}'] = $v;
				$build['{label:' . $field . '}'] = $this->getLabel($field);
			}
		}
	}

	/**
	 * Standalone external method to validate and/or format a value using a process string.
	 * Value is only modified on success.
	 * NB: Value and message are modified in-line (passed by ref)
	 *
	 * @param string $process_string - see addProcess
	 * @param mixed &$value
	 * @param string &$msg=''
	 *
	 * @return bool $success
	 */
	public function process($process_string, &$value, &$msg=null)
	{
		$value_tmp = $value;
		$success = true;

		$filter_calls = $this->parseProcess($process_string);

		foreach ($filter_calls as $params) {
			$filter = $params['_process'];
			unset($params['_process']);
			if ($success) { //stop processing once we've hit an error
				if (!$this->$filter->execute($value_tmp, $params)) {
					$msg = $this->$filter->getMessage($value_tmp, $params);
					$success = false;
				}//IF
			}//IF
		}//FOREACH

		if ($success) {
			$value = $value_tmp;
		}
		return $success;
	}

//process

	/**
	 * Add a message for a filter across all fields, or for a specific field
	 *
	 * @param string $filter
	 * @param string $message
	 * @param string $field = null
	 * @return bool - success
	 */
	public function addFilterMessage($filter, $message, $field=null)
	{
		if ($field) {
			$this->message_list['filter_field'][$filter][$field] = $message;
		} else {
			$this->message_list['filter'][$filter] = $message;
		}
		return true;
	}

//addFilterMessage

	/**
	 * Add a message for an entire field
	 * NB: Adding a field message overides all filter messages for that field
	 *
	 * @param string $field
	 * @param string $message
	 * @return bool - success
	 */
	public function addFieldMessage($field, $message)
	{
		if ($field) {
			if (strchr('{', $message) || strchr('}', $message)) {
				throw new Exception('Substitute tags found in message for field - ' . __CLASS__ . '::' . __METHOD__);
				return false;
			}
			$this->message_list['field'][$field] = $message;
		}

		return true;
	}

//addFieldMessage

	/**
	 * Set the text attached before a default filter message.
	 * Use {field} as a place-holder for inserting the field name. e.g. 'Error with {field}: '
	 *
	 * @param string $prefix
	 */
	public function setFilterMsgPrefix($prefix)
	{
		$this->filter_msg_prefix = $prefix;
	}

	/**
	 * Get the input hints for processes
	 *
	 * @param string $process_string - The processes
	 *
	 * @return array
	 */
	public function getProcessDescriptions($process_string)
	{
		$processes = $this->parseProcess($process_string);

		$msgs = array();
		foreach ($processes as $filter => $params) {
			$filter = $params['_process'];
			unset($params['_process']);
			$params = $this->_substituteParams($params);
			$msg = $this->$filter->getProcessDescription($params);
			if (!empty($msg)) {
				$msgs[$filter] = $msg;
			}
		}
		return $msgs;
	}

	/**
	 * Retrieve the most specific message available for a given field and filter
	 *
	 * @param string $field
	 * @param string $filter
	 * @param mixed $value
	 * @param array $params
	 * @return string - the message
	 */
	public function getMessage($filter, $field, $value, $params)
	{
		if (isset($this->message_list['field'][$field])) {
			$msg = $this->message_list['field'][$field];
		} elseif (isset($this->message_list['filter_field'][$filter][$field])) {
			$msg = $this->message_list['filter_field'][$filter][$field];
		} elseif (isset($this->message_list['filter'][$filter])) {
			$msg = $this->message_list['filter'][$filter];
		} else {
			$msg = $this->filter_msg_prefix . $this->$filter->getMessage($value, $params);
		}

		if (is_array($value)) {
			$value_str = implode(', ', $value);
		} else {
			$value_str = $value;
		}
		$replace = array(
			'{label}' => $this->getLabel($field),
			'{field}' => ucwords(str_replace(array('-', '_', '[', ']'), ' ', $field)),
			'{value}' => $value_str
		);

		foreach ($params as $key => $val) {
			if (!is_array($val)) {
				$replace['{' . $key . '}'] = $val;
			}
		}

		return str_replace(array_keys($replace), array_values($replace), $msg);
	}

//getMessage

	/**
	 * Iterate through all stored processes for an array
	 * Store modified values for later retrieval.
	 *
	 * @param array $fields
	 * @param array $fields2.. - (Optional)
	 * 
	 * @return bool $success
	 */
	public function processArray($array)
	{
		$inputs = func_get_args();
		unset($inputs[0]);
		foreach ($inputs as $input) {
			if (is_array($input)) {
				$array = array_merge($array, $input);
			}
		}

		$success = true;
		$this->clearData();
		$this->input_array = $array;
		$this->replace_array = $this->_buildReplaceArray();

		foreach ($this->process_list as $field => $ignore) {
			//First process field name to allow nested html array names

			$nested_fields = str_replace(']', '', $field);
			$nested_fields = explode('[', $nested_fields);

			//check for nested value
			$temp_val = $array;
			foreach ($nested_fields as $key) {
				if (isset($temp_val[$key])) {
					$temp_val = $temp_val[$key];
				} else {
					$temp_val = null;
					break;
				}
			}
			$value = $temp_val;

			if (!$this->runStoredProcess($field, $value)) {
				$success = false;
			}
			
			self::_addNestedValue($this->processed_array, $field, $value);
			
		}//FOREACH
		
		return $success;
	}
	
	private static function _addNestedValue(&$array, $keys, &$value)
	{
		if (is_array($keys)) {		
			$key = array_shift($keys);
		} else {
			$keys = str_replace(']', '', $keys);
			$keys = explode('[', $keys);
			$key = array_shift($keys);
		}
		
		if (count($keys)==0) {
			$array[$key] = $value;
		} else {
			self::_addNestedValue($array[$key], $keys, $value);
		}
	}
	
	private function _getNestedValue(&$array, $keys)
	{
		if (is_array($keys)) {
			$key = array_shift($keys);
		} else {
			$keys = str_replace(']', '', $keys);
			$keys = explode('[', $keys);
			$key = array_shift($keys);
		}
		
		if (isset($array[$key])) {
			if (count($keys)==0) {
				return $array[$key];
			} else {
				return self::_getNestedValue($array[$key], $keys);
			}
		}
		return null;
	}

	/**
	 * Get the processes that have been defined
	 * 
	 * @return array
	 */
	public function getProcesses()
	{
		return $this->process_list;
	}

	/**
	 * Check if any processes have been defined
	 *
	 * @return bool
	 */
	public function hasProcesses()
	{
		return count($this->process_list) > 0;
	}

	/**
	 * Get the array of processed values
	 *
	 * @param bool $only_filters - (optional, default false) If true only the values which has processes
	 *                             are returned
	 * 
	 * @return array
	 */
	public function getProcessedArray($only_filtered = true)
	{
		if (count($this->error_msgs)>0) {
			if ($only_filtered) {
				$processed_unchanged = array();
				foreach ($this->process_list as $field=>$ignore) {
					$value = self::_getNestedValue($this->input_array, $field);
					self::_addNestedValue($processed_unchanged, $field, $value);
				}
				return $processed_unchanged;
			} else {
				return $this->input_array;
			}
		} else {
			if ($only_filtered) {
				return $this->processed_array;
			} else {
				return self::arrayMergeRecursiveKey($this->input_array, $this->processed_array);
			}
		}
	}

	/**
	 * Get the value of a processed field
	 *
	 * @param string $field - The field to return
	 * 
	 * @return mixed
	 */
	public function getProcessedField($field)
	{
		if (!isset($this->processed_array[$field])) {
			return false;
		}
		return $this->processed_array[$field];
	}

	/**
	 * Iterate through all stored processes for an object
	 * Modify values in-line (passed by ref)
	 *
	 * @param object &$object
	 * @return bool $success
	 */
	public function processObject(&$object)
	{
		$success = true;
		$this->clearData();

		foreach ($this->process_list as $field => $ignore) {
			if (isset($object->$field) && is_scalar($object->$field)) {
				$value = $object->$field;
			} else {
				$value = null;
			}//IF
			if (!$this->runStoredProcess($field, $value)) {
				$success = false;
			}//IF
			$object->$field = $value;
		}//FOREACH

		return $success;
	}

//processObject

	/**
	 * Get the errors that occurred whit processing values
	 * 
	 * @return array
	 */
	public function getErrors()
	{
		return $this->error_msgs;
	}

	/**
	 * Clear the errors
	 */
	public function clearErrors()
	{
		$this->error_msgs = array();
	}

//clearProcessedArray

	/**
	 * Clear the processed values
	 */
	public function clearProcessedArray()
	{
		$this->processed_array = array();
	}

//clearProcessedArray

	/**
	 * Remove the defined processes
	 */
	public function removeFilters()
	{
		foreach ($this->filter_list as $filter) {
			unset($this->$filter);
		}
		$this->filter_list = array();
	}

	/**
	 * Clean out IP for reuse with another object of the same format
	 * Clear the processedArray and all errors
	 *
	 */
	protected function clearData()
	{
		$this->clearProcessedArray();
		$this->clearErrors();
		$this->replace_array = null;
	}

	/**
	 * Clean out the IP for use with a different object format
	 * Clears the process and message lists and flushes out any data
	 * NB: Doesn't remove any added filters - use removeFilters().
	 */
	public function clearConfiguration()
	{
		$this->process_list = array();
		$this->message_list = array();
		$this->clearData();
	}

	/**
	 * Get the label for a field
	 *
	 * @param string $field - The field
	 */
	public function getLabel($field)
	{
		if (isset($this->labels[$field])) {
			return $this->labels[$field];
		} else {
			return ucwords(str_replace(array('-', '_', '[', ']'), ' ', $field));
		}
	}

	/**
	 * Set the display label for a field
	 *
	 * @param string $field - The field
	 * @param string $label - The label
	 */
	public function addLabel($field, $label)
	{
		$this->labels[$field] = $label;
	}
	
	/**
	 * Merge two arrays recursivly using keys, values in array2 will overwite array1
	 * The way array merge should have worked
	 * 
	 * @param  array $array1
	 * @param  array $array2
	 * @return array
	 */
	public static function arrayMergeRecursiveKey(array $array1, array $array2)
	{
		$keys_to_check = array_intersect_key($array1, $array2);
		$safe = array_diff_key($array1, $keys_to_check);
		$safe += array_diff_key($array2, $keys_to_check);
		foreach ($keys_to_check as $key=>$ignore) {
			if (is_array($array1[$key]) && is_array($array2[$key])) {
				$safe[$key] = self::arrayMergeRecursiveKey($array1[$key], $array2[$key]);
			}
			else {
				$safe[$key] = $array2[$key];
			}
		}
		return $safe;
	}

}
