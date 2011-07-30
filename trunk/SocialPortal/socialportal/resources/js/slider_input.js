
var jq = jQuery;
jq(document).ready(function() {
	jq('.sliderInput').each(function() {
		var jqThis = jq(this);
		var slider = jq( ".slider", jqThis );
		var field = jq( ".slider_input", jqThis);
		var label = jq( ".slider_description", jqThis );
		var identifier = field.attr('name');
		
		var content = window[identifier + '_descriptions'];
		
		var defaultValue = parseInt(retrieveFromClass(slider, 'default'));
		var minValue = parseInt(retrieveFromClass(slider, 'min'));
		var maxValue = parseInt(retrieveFromClass(slider, 'max'));
		var step = parseInt(retrieveFromClass(slider, 'step'));
		
		slider.slider({
			value: defaultValue,
			min: minValue,
			max: maxValue,
			step: step,
			slide: function( event, ui ) {
				field.val( ui.value );
				label.text(content[ui.value]);
			}
		});
		field.val( defaultValue );
		var temp = content[defaultValue];
		label.text( temp );
	});
});

function retrieveFromClass(obj, target){
	var classes = jq(obj).attr('class');
	classes = classes.split(' ');
	var value, name, c;
	for(var i in classes){
		c = classes[i];
		value = c.split('_');
		name = value.shift();
		if(name === target){
			return value;
		}
	}
	return false;
}