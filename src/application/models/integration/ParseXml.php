<?php
/************************************************************
 *  @Author: yrd 
 *  @Create Time: 2013-07-08
 *************************************************************/
class ParseXml {
	var $_parser = null;
	var $_output_arr = array();

	public function parse($strInputXML) {
		$this->_parser = xml_parser_create();
		xml_parser_set_option($this->_parser,XML_OPTION_SKIP_WHITE,1);
		xml_parser_set_option($this->_parser,XML_OPTION_CASE_FOLDING,1);
		xml_set_object($this->_parser,$this);
		xml_set_element_handler($this->_parser, "tagOpen", "tagClosed");
		xml_set_character_data_handler($this->_parser, "tagData");

		if(!xml_parse($this->_parser,$strInputXML)) {
			$error = sprintf("XML error: %s at line %d\n",
				xml_error_string(xml_get_error_code($this->_parser)),
				xml_get_current_line_number($this->_parser)
			);
			xml_parser_free($this->_parser);
			throw new Exception($error);
		}

		xml_parser_free($this->_parser);
		return $this->_output_arr;
	}

	public function tagOpen($parser, $name, $attrs) {
		$tag = array();
		if( $attrs ) $tag['ATTRS'] = $attrs;
		array_push($this->_output_arr,$tag);
	}

	public function tagData($parser, $tagData) {
		if(trim($tagData)) {
			if( isset($this->_output_arr[count($this->_output_arr)-1]['value']) ) {
				$this->_output_arr[count($this->_output_arr)-1]['value'].= $tagData;
			} else {
				$this->_output_arr[count($this->_output_arr)-1]['value'] = $tagData;
			}
		}
	}

	public function tagClosed($parser, $name) {
		$this->_output_arr[count($this->_output_arr)-2][$name][] = $this->_output_arr[count($this->_output_arr)-1];
		array_pop($this->_output_arr);
	}

	public function getError() {
		return $errno = xml_get_error_code($this->_parser);
	}
}

?>
