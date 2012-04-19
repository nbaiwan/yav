<?php

class CComponent {
	private $_m;
    
    public function init() {
        //
    }
    
	public function __get($name) {
		if(isset($this->$name)) {
			return $this->$name;
		} else {
			$getter='get'.$name;
			if(method_exists($this,$getter)) {
				return $this->$getter();
			} else if(is_array($this->_m)) {
				foreach($this->_m as $object) {
					if($object->getEnabled() && (property_exists($object,$name) || $object->canGetProperty($name))) {
						return $object->$name;
					}
				}
			}
		}
		
		return null;
	}
}
?>