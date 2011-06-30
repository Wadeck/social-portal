/**
 * 
 */
var jq = jQuery;
function radio_reset(obj){
	jqThis = jq(obj);
	jqThis.parentsUntil();
	var parent = jqThis.parentsUntil('.radio_field');
	if(!parent.length){
		parent = jqThis;
	}
	parent = parent.parent();
	parent = parent[parent.length-1];
	var radioButtons = jq('input:radio', parent);
	var radioButtonsChecked = jq('input:radio:checked', parent);
	radioButtonsChecked.attr('checked', false);
	return false;
}