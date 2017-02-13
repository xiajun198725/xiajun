/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User Software Agreement (EULA).
 * It is also available through the world-wide-web at this URL:
 * http://www.bf170.com/license
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to eula@bf170.com so we can send you a copy immediately.
 */
var TaskCard = Class.create();
var html = '';
TaskCard.prototype = {

	initialize: function(jsonCardInfo){
		this.renderCard(jsonCardInfo);
	},

	renderCard: function(jsonCardInfo){
		var callures = jsonCardInfo.tag_info;
		var calltag = jsonCardInfo.tag_kapian;
			html  =	'<div id="card-id-'+jsonCardInfo.card_id+'" class="card-container" >';
			html +=		'<div class="card-header"><h3>'+jsonCardInfo.name+'</h3></div>';
			html +=		'<div class="card-headers"><h3>紧急度:'+jsonCardInfo.tag_kapian+'</h3></div>';
			
			html +=		'<div class = "card-content" id = "card-content-'+jsonCardInfo.card_id+'">' + jsonCardInfo.description + '</div>';
			for(var idvalues in callures){
				html +=	'<div class = "user_name" id = "card-content-'+idvalues+'">'+callures[idvalues]+'</div>';
			}
			html +=	'<div class = "user_name_clear"></div>';
			html +=	'<div id="wanchengshijian'+jsonCardInfo.card_id+'" class="kapian_times_xianzhi"></div>';
			html +=	'</div>';
		jQuery("#process-id-"+jsonCardInfo.process_id).append(html);
	}

};
