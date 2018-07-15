<?php namespace App\Helpers;

use DOMDocument;

class Helper {

	public function string_to_clean_array($input_string){
		
		$clean_string = ' '.preg_replace("/[^a-zA-Z0-9]+/", " ", $input_string).' ';
		$unwanted_strings = array(
			' the ', ' of ', ' by ', ' in ', ' and ', ' for ', ' is ', ' since ', ' that ', 
			' are ', ' a ', ' an ', ' it ', ' to ', ' but ', ' so ', ' then ', ' therefore '
		);
		foreach($unwanted_strings as $unwanted_string){
			$clean_string = str_ireplace($unwanted_string, ' ', $clean_string);
		}
		$clean_string = strtolower(trim($clean_string));
		$clean_array = array_unique(explode(' ', $clean_string));
		return $clean_array;
		
	}
	
	public function clean_html_by_tag($dom, $html, $tag){
		
		$elements = $dom->getElementsByTagName($tag);
		$html_safe = $this->get_safe_html($html, $elements, $tag);
		return $html_safe;
		
	}
	
	public function get_safe_html($html, $elements, $tag){
		
		//Replace every tag with the clean safe version
		foreach($elements as $element) {
			$old_tag_string = $this->get_old_tag_string($element);
			$new_tag_string = $this->get_new_tag_string($element, $tag);
			$html = str_ireplace($old_tag_string, $new_tag_string, $html);
		}
		return $html;
		
	}
	
	public function get_old_tag_string($element){
		
		//Get the old open tag as a string
		$dom_temp = new DOMDocument();
		$element_cloned = $element->cloneNode(TRUE);
		$dom_temp->appendChild($dom_temp->importNode($element_cloned,TRUE));
		$old_tag_with_closed = $dom_temp->saveHTML();
		$old_tag_string = substr($old_tag_with_closed, 0, strpos($old_tag_with_closed, '>'));
		return $old_tag_string;
		
	}

	public function get_new_tag_string($element, $tag){
		
		//Assemble new safe open tag as a string
		//Only allow specific attributes and values for the allowed tags
		$new_tag_string = '<' . $tag;
		
		//<a> tags
		if($tag == 'a') {
			if($element->hasAttribute('href')) {
				$new_tag_string .= (' href="' . $element->getAttribute('href') . '"');
			}
			if($element->hasAttribute('id')) {
				$new_tag_string .= (' id="' . $element->getAttribute('id') . '"');
			}
			if($element->hasAttribute('rel')) {
				if($element->getAttribute('rel') == 'footnote'){
					$new_tag_string .= (' rel="' . $element->getAttribute('rel') . '"');
				}
			}
			if($element->hasAttribute('target')) {
				if($element->getAttribute('target') == '_blank'){
					$new_tag_string .= (' target="' . $element->getAttribute('target') . '"');
				}
			}
		}
		//<p> tags
		elseif($tag == 'p') {
			if($element->hasAttribute('style')) {
				$styles = explode(';', $element->getAttribute('style'));
				$styles_safe = 1;
				foreach($styles as $style) {
					$style_trimmed = trim($style);
					$style_name = strtolower(trim(substr($style_trimmed, 0, strpos($style_trimmed, ':'))));
					if($style_name != '') {
						if(in_array($style_name, array('text-align', 'margin-left'))) {}
						else {
							$styles_safe = 0;
						}
					}
				}
				if($styles_safe == 1) {
					$new_tag_string .= (' style="' . $element->getAttribute('style') . '"');
				}
			}
		}
		//<img> tags
		elseif($tag == 'img') {
			if($element->hasAttribute('alt')) {
				$new_tag_string .= (' alt="' . $element->getAttribute('alt') . '"');
			}
			if($element->hasAttribute('src')) {
				$new_tag_string .= (' src="' . $element->getAttribute('src') . '"');
			}
			if($element->hasAttribute('style')) {
				$styles = explode(';', $element->getAttribute('style'));
				$styles_safe = 1;
				foreach($styles as $style) {
					$style_trimmed = trim($style);
					$style_name = strtolower(trim(substr($style_trimmed, 0, strpos($style_trimmed, ':'))));
					if($style_name != '') {
						if(in_array($style_name, array('border-style', 'border-width', 'float', 'height', 'margin', 'width'))) {}
						else {
							$styles_safe = 0;
						}
					}
				}
				if($styles_safe == 1) {
					$new_tag_string .= (' style="' . $element->getAttribute('style') . '"');
				}
			}
		}
		//<span> tags
		elseif($tag == 'span') {
			if($element->hasAttribute('class')) {
				if($element->getAttribute('class') == 'math-tex'){
					$new_tag_string .= (' class="' . $element->getAttribute('class') . '"');
				}
			}
		}
		//<code> tags
		elseif($tag == 'code') {
			if($element->hasAttribute('class')) {
				if(substr($element->getAttribute('class'), 0, 4) == 'lang'){
					$new_tag_string .= (' class="' . $element->getAttribute('class') . '"');
				}
			}
		}
		//<sup> tags
		elseif($tag == 'sup') {
			if($element->hasAttribute('data-footnote-id')) {
				$new_tag_string .= (' data-footnote-id="' . $element->getAttribute('data-footnote-id') . '"');
			}
		}
		//<div> tags
		elseif($tag == 'div') {
			if($element->hasAttribute('class')) {
				if($element->getAttribute('class') == 'footnotes'){
					$new_tag_string .= (' class="' . $element->getAttribute('class') . '"');
				}
			}
		}
		//<li> tags
		elseif($tag == 'li') {
			if($element->hasAttribute('data-footnote-id')) {
				$new_tag_string .= (' data-footnote-id="' . $element->getAttribute('data-footnote-id') . '"');
			}
			if($element->hasAttribute('id')) {
				$new_tag_string .= (' id="' . $element->getAttribute('id') . '"');
			}
		}
		//<table> tags
		elseif($tag == 'table') {
			if($element->hasAttribute('align')) {
				$new_tag_string .= (' align="' . $element->getAttribute('align') . '"');
			}
		}
		// <td> (table cell) tags
		elseif($tag == 'td') {
			if($element->hasAttribute('style')) {
				$styles = explode(';', $element->getAttribute('style'));
				$styles_safe = 1;
				foreach($styles as $style) {
					$style_trimmed = trim($style);
					$style_name = strtolower(trim(substr($style_trimmed, 0, strpos($style_trimmed, ':'))));
					if($style_name != '') {
						if(in_array($style_name, array('text-align'))) {}
						else {
							$styles_safe = 0;
						}
					}
				}
				if($styles_safe == 1) {
					$new_tag_string .= (' style="' . $element->getAttribute('style') . '"');
				}
			}
		}
		else {}
		
		return $new_tag_string;
		
	}

}
?>
