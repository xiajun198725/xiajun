<?php
/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User Software Agreement (EULA).
 * It is also available through the world-wide-web at this URL:
 * http://www.bf170.com/license [^]
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to eula@bf170.com so we can send you a copy immediately.
 *
 */

class Bf170_CoreService_Block_Adminhtml_System_Config_Form_Field_Json extends Mage_Adminhtml_Block_System_Config_Form_Field {
	
	/*
	 * 特殊类型的system config，将列表内容变成JSON保存
	 * 缺省支持两种类型：
	 * >> list结构，只保存值value，不保存key，没有值时缺省为[]
	 * >> map结构，保存键key与键值value，没有值时缺省为{}
	 */
	protected function _toHtml() {
		$element = $this->getData('element');
		$elementFieldConfig = $element->getFieldConfig();
		$elementJsonConfig = array(
				'structure'		=> (string)$elementFieldConfig->frontend_json_structure,
				'key_label'		=> (string)$elementFieldConfig->frontend_json_key_label,
				'value_label'	=> (string)$elementFieldConfig->frontend_json_value_label,
		);
		
		// 键值从已知常量中选取中选取
		$rendererClassName = (string)$elementFieldConfig->frontend_json_key_option_renderer;
		if(!empty($rendererClassName)){
			$optionRenderer = Mage::getModel($rendererClassName);
			foreach($optionRenderer->getAllOptions() as $optionValue => $optionLabel){
				$keyOptionHtml .= "<option value='{$optionValue}'>{$optionLabel}</option>";
			}
			$elementJsonConfig['key_option_html'] = $keyOptionHtml;
		}
		
		$elementData = $this->_prepareElementData($element, $elementJsonConfig);
		$elementJsonConfig = json_encode($elementJsonConfig);

		$htmlContent = <<< HTML_CONTENT
<input type="hidden" id="{$element->getHtmlId()}" name="{$element->getName()}" value="{$element->getEscapedValue()}" {$this->serialize($element->getHtmlAttributes())}/>
<div id="{$element->getHtmlId()}_json_config_widget" class="json_config_widget_container"></div>
<script type="text/javascript">
	var {$element->getHtmlId()} = new JsonConfigWidget();
	{$element->getHtmlId()}.init("{$element->getHtmlId()}", $elementData, $elementJsonConfig);
</script>
HTML_CONTENT;
		return $htmlContent;
	}
	
	protected function _prepareElementData($element){
		$elementFieldConfig = $element->getFieldConfig();
		$elementJsonConfig = array(
				'structure'		=> (string)$elementFieldConfig->frontend_json_structure,
				'key_label'		=> (string)$elementFieldConfig->frontend_json_key_label,
				'value_label'	=> (string)$elementFieldConfig->frontend_json_value_label,
		);
		$elementData = (string)$element->getValue();
		
		//Try to decode to validate the data, if not validate, use empty placeholders
		$jsonValidateData = json_decode(trim($element->getValue()), true);
		if(!$jsonValidateData){
			if(isset($elementJsonConfig['structure']) && $elementJsonConfig['structure'] == 'list'){
				$elementData = "[]";
			}else{
				$elementData = "{}";
			}
		}
		
		return $elementData;
	}

	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element){
		$this->addData(array(
			'element' => $element
		));
		return $this->_toHtml();
	}
	
}
