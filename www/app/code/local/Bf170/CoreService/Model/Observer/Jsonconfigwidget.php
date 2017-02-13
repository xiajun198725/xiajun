<?php 
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User Software Agreement (EULA).
 * It is also available through the world-wide-web at this URL:
 * http://www.bf170.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to eula@bf170.com so we can send you a copy immediately.
 * 
 */
class Bf170_CoreService_Model_Observer_Jsonconfigwidget {
	
	// ============================== 仅限后台system/config，添加JsonConfigWidget所需的相应JS和CSS ============================== //
	public function adminhtmlOnlyAddJsonConfigWidget(Varien_Event_Observer $observer){
		$actionController = Mage::app()->getFrontController()->getData('action');
		if(!($actionController instanceof Mage_Adminhtml_System_ConfigController)){
			return;
		}
        $block = $observer->getEvent()->getBlock();
        $transportObject = $observer->getEvent()->getTransport();
        if($block instanceof Mage_Page_Block_Html_Head){
        	$html = $transportObject->getHtml();
        	$html .= $this->_getJsonConfigWidgetCss();
			$html .= $this->_getJsonConfigWidgetJs();
			$transportObject->setHtml($html);
        }
        return;
	}
	
	protected function _getJsonConfigWidgetCss() {
		$jsonConfigWidgetCss = <<< JSON_CONFIG_WIDGET_CSS
<style>
.json_config_widget_container thead{
	background-color:#DFDFDF;
}
.json_config_widget_container thead th{
	min-width: 150px;
}
.json_config_widget_container th, .json_config_widget_container td{
	padding:2px;
}
.json_config_widget_container td input{
	text-align:right
}
.json_config_widget_container button{
	margin:5px;
}
.json_config_widget_message_container .success{
	font-weight: bold;
	color: #3D6611
}
</style>
JSON_CONFIG_WIDGET_CSS;
		return $jsonConfigWidgetCss;
	}
	
	protected function _getJsonConfigWidgetJs() {
		$addButtonLabel = Mage::helper('core')->__('Add');
		$editButtonLabel = Mage::helper('core')->__('Edit');
		$validateButtonLabel = Mage::helper('core')->__('Validate');
		$afterValidateMessage = Mage::helper('core')->__('Content validated. Please make sure to save config.');
		$jsonConfigWidgetJs = <<< JSON_CONFIG_WIDGET_JS
<script type="text/javascript">
function JsonConfigWidget() {

	var _const;
	var _config;
	var _elementId;
	var _elementData; //JSON for maps, ARRAY for lists
	var _renderMode;
	
	// ==================== Controllers ==================== //
	this.init = function (elementId, elementValue, elementConfig) {
		this._const = {
				contentMode:	0,
				editMode:		1,
				mapStructure:	'map',
				listStructure:	'list'
		}
		this._elementId = elementId;
		this._elementData = elementValue;
		this._config = elementConfig;
		this._renderMode = this._const.contentMode; //Default is content mode
		
		var tableHead = "";
		if(this._config.structure == this._const.mapStructure){
			tableHead = "<tr><th>" + this._config.key_label + "</th><th>" + this._config.value_label + "</th></tr>";
		}else if(this._config.structure == this._const.listStructure){
			tableHead = "<tr><th>" + this._config.value_label + "</th></tr>";
		}
		var contentTemplate = '<div class="json_config_widget_message_container"></div>'
					+ '<table border="1" style="border-collapse:collapse; text-align:right">' 
					+ '<thead class="json_config_widget_thead">' + tableHead + '</thead>'
					+ '<tbody class="json_config_widget_tbody"></tbody>'
					+ '</table>'
					+ '<button type="button" class="scalable add json_config_widget_button_add" style="display: none"><span>{$addButtonLabel}</span></button>'
					+ '<button type="button" class="scalable save json_config_widget_button_confirm"><span>{$editButtonLabel}</span></button>';
					
		$(this._elementId + "_json_config_widget").update(contentTemplate);
		$$("div#" + this._elementId + "_json_config_widget button.json_config_widget_button_add").first().observe('click', this.addInputObserver.bind(this));
		$$("div#" + this._elementId + "_json_config_widget button.json_config_widget_button_confirm").first().observe('click', this.toggleViewObserver.bind(this));
		this.renderContentView();
	}

	// ==================== Models ==================== //
	this.processEditData = function () {
		var tempDataDump = []; 
		$$("div#" + this._elementId + "_json_config_widget tbody.json_config_widget_tbody input").each(function (item){
			tempDataDump[tempDataDump.length] = item.value;
		});
		if(this._config.structure == this._const.mapStructure){
			if(tempDataDump.length%2 != 0){
				this.showMessage('error', '内容无效');
				return false;
			}
			this._elementData = {};
			for (var tempIndex=0; tempIndex<tempDataDump.length; tempIndex += 2){
				cleanedJsonKey = tempDataDump[tempIndex].trim();
				cleanedJsonValue = tempDataDump[tempIndex + 1].trim();
				if(cleanedJsonKey.length == 0 && cleanedJsonValue.length == 0){
					//Both empty rows are ignored
					continue;
				}
				if(cleanedJsonKey.length == 0 || cleanedJsonValue.length == 0){
					//Otherwise, either empty will be an error
					this.showMessage('error', '缺少输入键值或内容');
					return false;
				}
				this._elementData[cleanedJsonKey] = cleanedJsonValue;
			}
		}else if(this._config.structure == this._const.listStructure){
			this._elementData = [];
			for (var tempIndex=0; tempIndex<tempDataDump.length; tempIndex ++){
				cleanedJsonValue = tempDataDump[tempIndex].trim();
				if(cleanedJsonValue.length == 0){
					continue;
				}
				this._elementData[tempIndex] = cleanedJsonValue;
			}
		}
		
		this.showMessage('success', '{$afterValidateMessage}');
		return true;
	}
	
	// ==================== Views ==================== //
	this.showMessage = function (messageClass, messageContent) {
		$$("div#" + this._elementId + "_json_config_widget div.json_config_widget_message_container").first().update('<span class="' + messageClass + '">' + messageContent + '</span>');
	}
	
	this.renderContentView = function () {
		var tableContent = "";
		if(this._config.structure == this._const.mapStructure){
			for(jsonKey in this._elementData){
				if(this._elementData.hasOwnProperty(jsonKey)){
					jsonValue = this._elementData[jsonKey];
					tableContent += "<tr><td>" + jsonKey + "</td><td>" + jsonValue + "</td></tr>";
				}
			}
		}else if(this._config.structure == this._const.listStructure){
			for(var tempIndex = 0; tempIndex < this._elementData.length; tempIndex ++){
				tableContent += "<tr><td>" + this._elementData[tempIndex] + "</td></tr>";
			}
		}
		
		$$("div#" + this._elementId + "_json_config_widget tbody.json_config_widget_tbody").first().update(tableContent);
		this._renderMode = this._const.contentMode;
	};

	this.renderEditView = function () {
		var tableContent = "";
		if(this._config.structure == this._const.mapStructure){
			var dummyId = 0;
			for(jsonKey in this._elementData){
				if(this._elementData.hasOwnProperty(jsonKey)){
					jsonValue = this._elementData[jsonKey];
					if(this._config.key_option_html){
						tableContent += "<tr>"
								+ "<td><input class='json_key_option_input' type='hidden' id='" + this._elementId + "_json_key_" + dummyId + "' name='" + this._elementId + "_json_key_" + dummyId + "' value='" + jsonKey + "'/>"
								+ "<select class='json_key_option_select' id='" + this._elementId + "_json_key_" + dummyId + "_select' input_id='" + this._elementId + "_json_key_" + dummyId + "'>" + this._config.key_option_html + "</select></td>"
								+ "<td><input name='" + this._elementId + "_json_value_" + dummyId + "'  value='" + jsonValue + "'/></td>"
								+ "</tr>";
					}else{
						tableContent += "<tr>"
								+ "<td><input name='" + this._elementId + "_json_key_" + dummyId + "' value='" + jsonKey + "'/></td>"
								+ "<td><input name='" + this._elementId + "_json_value_" + dummyId + "'  value='" + jsonValue + "'/></td>"
								+ "</tr>";
					}
					dummyId ++;
				}
			}
		}else if(this._config.structure == this._const.listStructure){
			for(var tempIndex = 0; tempIndex < this._elementData.length; tempIndex ++){
				tableContent += "<tr>"
							+ "<td><input name='" + this._elementId + "_json_value_" + tempIndex + "' value='" + this._elementData[tempIndex] + "'/></td>"
							+ "<tr/>";
			}
		}
		$$("div#" + this._elementId + "_json_config_widget tbody.json_config_widget_tbody").first().update(tableContent);
		
		if(this._config.key_option_html){
			$$(".json_key_option_input").forEach(function(inputElement) {
				$$("#" + inputElement.readAttribute('id') + "_select")[0].setValue(inputElement.readAttribute('value'));
			});
			$$(".json_key_option_select").invoke('observe', 'change', function() {
				$$("#" + this.readAttribute('input_id'))[0].setAttribute('value', this.getValue());
			});
		}
		
		//Automatically add input fields if the data is completely empty
		if(this.isElementDataEmpty()){
			this.addInput();
		}
		
		this._renderMode = this._const.editMode;
	};
	
	this.toggleView = function () {
		if(this._renderMode == this._const.contentMode){
			this.renderEditView();
			$$("div#" + this._elementId + "_json_config_widget button.json_config_widget_button_add").first().show();
			$$("div#" + this._elementId + "_json_config_widget button.json_config_widget_button_confirm").first().update("<span>{$validateButtonLabel}</span>");
			return true;
		}else if(this._renderMode == this._const.editMode){
			//Basic validations
			if(!this.processEditData()){
				return false;
			}
			this.renderContentView();
			$(this._elementId).writeAttribute('value', JSON.stringify(this._elementData));
			$$("div#" + this._elementId + "_json_config_widget button.json_config_widget_button_add").first().hide();
			$$("div#" + this._elementId + "_json_config_widget button.json_config_widget_button_confirm").first().update("<span>{$editButtonLabel}</span>");
			return true;
		}
		//Unknown status, default back to content view
		this.renderContentView();
		$$("div#" + this._elementId + "_json_config_widget button.json_config_widget_button_confirm").first().update("<span>{$editButtonLabel}</span>");
		return true;
	}
	
	this.addInput = function () {
		var tempDataDump = [];
		$$("div#" + this._elementId + "_json_config_widget tbody.json_config_widget_tbody input").each(function (item){
			tempDataDump[tempDataDump.length] = item.value;
		});
		
		var tableContent = "";
		if(this._config.structure == this._const.mapStructure){
			if(this._config.key_option_html){
				tableContent += "<tr>"
					+ "<td><input class='json_key_option_input' type='hidden' id='" + this._elementId + "_json_key_" + (tempDataDump.length + 1) + "' name='" + this._elementId + "_json_key_" + (tempDataDump.length + 1) + "' value=''/>"
					+ "<select class='json_key_option_select' input_id='" + this._elementId + "_json_key_" + (tempDataDump.length + 1) + "'>" + this._config.key_option_html + "</select></td>"
					+ "<td><input name='" + this._elementId + "_json_value_" + (tempDataDump.length + 2) + "' value=''/></td>"
					+ "</tr>";
			}else{
				tableContent += "<tr>"
					+ "<td><input name='" + this._elementId + "_json_key_" + (tempDataDump.length + 1) + "' value=''/></td>"
					+ "<td><input name='" + this._elementId + "_json_value_" + (tempDataDump.length + 2) + "' value=''/></td>"
					+ "</tr>";
			}
		}else if(this._config.structure == this._const.listStructure){
			tableContent += "<tr><td><input name='" + this._elementId + "_json_value_" + (tempDataDump.length + 1) + "' value=''/></td></tr>";
		}

		$$("div#" + this._elementId + "_json_config_widget tbody.json_config_widget_tbody").first().insert(tableContent);
		
		if(this._config.key_option_html){
			$$(".json_key_option_select").invoke('observe', 'change', function() {
				$$("#" + this.readAttribute('input_id'))[0].setAttribute('value', this.getValue());
			});
		}
		
		return true;
	}
	
	// ==================== Oberservers ==================== //
	this.toggleViewObserver = function (event) {
		this.toggleView();
	}
	
	this.addInputObserver = function (event) {
		this.addInput();
	}
	
	// ==================== Utilities ==================== //
	this.isElementDataEmpty = function () {
	    if (this._elementData == null) return true;
	    if (this._elementData.length > 0)    return false;
	    if (this._elementData.length === 0)  return true;
	    for (var jsonKey in this._elementData) {
	        if (this._elementData.hasOwnProperty(jsonKey)) return false;
	    }
	    return true;
	}
	
}
</script>
JSON_CONFIG_WIDGET_JS;
		return $jsonConfigWidgetJs;
	}
	
}