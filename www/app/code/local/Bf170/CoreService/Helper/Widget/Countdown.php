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
 */

class Bf170_CoreService_Helper_Widget_Countdown extends Mage_Core_Helper_Data {
	
	public function getWidgetSupportHtml($widgetElementId, $countdownEndDatetime){
		$widgetSupportHtml = <<< WIDGET_HTML
<div id="{$widgetElementId}-template" style="display: none;">
	<div class="countdown-time {{label}}">
		<span class="countdown-item countdown-curr countdown-top">{{curr}}</span>
		<span class="countdown-item countdown-next countdown-top">{{next}}</span>
		<span class="countdown-item countdown-next countdown-bottom">{{next}}</span>
		<span class="countdown-item countdown-curr countdown-bottom">{{curr}}</span>
		<span class="countdown-label">{{label_name}}</span>
	</div>
</div>
<script type="text/javascript">
	jQuery(window).on('load', function() {
		var labels = ['countdown-days', 'countdown-hours', 'countdown-minutes', 'countdown-seconds'];
		var labelNames = ['日', '时', '分', '秒'];
		var endDate = '{$countdownEndDatetime}';
		var template = jQuery('#{$widgetElementId}-template').html();
		var currDate = '00:00:00:00';
		var nextDate = '00:00:00:00';
		var parser = /([0-9]{2})/gi;
		var countdownFrame = jQuery('#{$widgetElementId}');
		
		// Parse countdown string to an object
		function strfobj(str) {
			var parsed = str.match(parser),
			obj = {};
			for (i = 0; i < labels.length; i++) {
				obj[labels[i]] = parsed[i]
			}
			return obj;
		}
		
		// Return the time components that diffs
		function countdownDiff(obj1, obj2) {
			var countdownDiff = [];
			for (i = 0; i < labels.length; i++) {
				if (obj1[labels[i]] !== obj2[labels[i]]) {
					countdownDiff.push(labels[i]);
				}
			}
			return countdownDiff;
		}
		
		// Build the layout
		var initData = strfobj(currDate);
		for (i = 0; i < labels.length; i++) {
			content = template.replace(/{{label}}/g, labels[i]).replace(/{{curr}}/g, initData[labels[i]]).replace(/{{next}}/g, initData[labels[i]]).replace(/{{label_name}}/g, labelNames[i]);
			countdownFrame.append(content);
		}
		
		// Starts the countdown
		countdownFrame.countdown(endDate, function(event) {
			var newDate = event.strftime('%D:%H:%M:%S');
			var data;
			if (newDate !== nextDate) {
				currDate = nextDate;
				nextDate = newDate;
				// Setup the data
				data = {
					'curr': strfobj(currDate),
					'next': strfobj(nextDate)
				};
				// Apply the new values to each node that changed
				diffResults = countdownDiff(data.curr, data.next);
				for (i = 0; i < diffResults.length; i++) {
					var selector = '.%s'.replace(/%s/, diffResults[i]);
					var countdownFrameNode = countdownFrame.find(selector);
					// Update the node
					countdownFrameNode.removeClass('countdown-flip');
					countdownFrameNode.find('.countdown-curr').text(data.curr[diffResults[i]]);
					countdownFrameNode.find('.countdown-next').text(data.next[diffResults[i]]);
					countdownFrameNode.delay(50).queue(function(next){
						jQuery(this).addClass('countdown-flip');
						next();
					});
				}
			}
			event.preventDefault();
		});
	});
</script>
WIDGET_HTML;
		return $widgetSupportHtml;
	}
	
}