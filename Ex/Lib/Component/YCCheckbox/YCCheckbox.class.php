<?php
class YCCheckbox extends HtmlCheckRadioBase
{
	public function __construct($attrs,$tagName)
	{
		parent::__construct($attrs,$tagName);
		$this->attrsDefault['text'] = '选择框';
	}
}