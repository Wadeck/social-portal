var jq = jQuery;

jq(document).ready(function() {
	jq('.list_field').each(function() {
		// add the [x] buttons right to the current fields
		// add the [+] button at the end of the list
	});
});


function removeField(obj, i){
	var jqThis = jq(obj);
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

/**
 * @param obj
 * @param field field string with placeholder %next%, where next is the id of the current link
 * @returns
 */
function addField(obj, field){
	var jqThis = jq(obj);
	
	
	var previousOne;
	
	var parent = jqThis.parentsUntil('#add_next');
	if(!parent.length){
		parent = jqThis;
	}
	parent = parent.parent();
	parent = parent[parent.length-1];
	//TODO working here
}