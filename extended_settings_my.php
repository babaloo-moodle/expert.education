<?php
	class admin_setting_configtext_my extends admin_setting_configtext {
		public function __construct($name, $visiblename, $description, $defaultsetting, $paramtype=PARAM_RAW, $size=null) {
	        parent::__construct($name, $visiblename, $description, $defaultsetting);
	    }

	    public function validate($data) {
	    	parent::validate($data);
	    	$func = 'validate_' . $this->name;
	    	if(is_callable(array($this, $func))) {
	    		$res = $this->$func($data);
		    	if($res) {
		    		return $res;
		    	}
	    	}
	    	return true;
	    }

	    private function validate_legalentitypaymentaccount($data) {
	    	if($data == '') {
	    		return get_string('empty_field', 'availability_payallways');
	    	}
	    	if(strlen($data) != 16 || !is_numeric($data)) {
	    		return get_string('account_error', 'availability_payallways');
	    	}
	    	return false;
	    }

	    private function validate_bankmfonum($data) {
	    	if($data == '') {
	    		return get_string('empty_field', 'availability_payallways');
	    	}
	    	if(strlen($data) != 6) {
	    		return get_string('mfo_error', 'availability_payallways');
	    	}
	    	return false;
	    }

	    private function validate_edrpu($data) {
	    	if($data == '') {
	    		return get_string('empty_field', 'availability_payallways');
	    	}
	    	if(strlen($data) > 20 || !is_numeric($data)) {
	    		return get_string('edrpu_error', 'availability_payallways');
	    	}
	    	return false;
	    }

	    private function validate_entity_name($data) {
	    	if($data == '') {
	    		return get_string('empty_field', 'availability_payallways');
	    	}
	    	return false;
	    }

	    private function validate_iban($data) {
	    	$regex_iban = '/^[a-z]{2}\d{27}$/i';

	    	if($data == '') {
	    		return get_string('empty_field', 'availability_payallways');
	    	}
	    	if(strlen($data) > 29 || !preg_match($regex_iban, $data)) {
	    		return get_string('iban_error', 'availability_payallways');
	    	}
	    	return false;
	    }
	}
?>