var jq = jQuery;

jq(document).ready(function() {
	jq('.dependent_dropdown_field.master_only select').each(function() {
		var jqThis = jq(this);
		jqThis.change();
	});
	jq('.dependent_dropdown_field.master select').each(function() {
		var jqThis = jq(this);
		jqThis.change();
	});
});

function onSelectChange(obj, targets, data){
	var parent = jq(obj).parentsUntil("form");
	if(!parent.length){
		parent = jq(obj);
	}
	parent = parent[parent.length-1];
	parent = jq(parent).parent();
	
	var ts = new Array();
	var id;
	var temp;
	for ( var i in targets) {
		id = '#'+targets[i];
		temp = jq(id, parent[0]);
		if(temp){
			ts.push( temp[0] );
		}
	}
	
	var selectedValue;
	var index = jq(obj).attr('value');
	for ( var i in ts ) {
		selectedValue = getSelectedValue(ts[i]);
		changeOptions(ts[i], index, selectedValue, data);
	}
}

function getSelectedValue(target){
	var selectedValue = -1;
	var classes = jq(target).attr('class');
	classes = classes.split(' ');
	var selectedValue = -1;
	for(var i= 0; i < classes.length; i++){
		var c = classes[i];
		var index = c.indexOf('dependent_selected_');
		if(-1 != index){
			selectedValue = c.substr(index + 'dependent_selected_'.length);
			return selectedValue;
		}
	}
	return selectedValue;
}

/**
 * Insert into the option of the select object (obj) the data[index][value]=> description
 *	The options will have as value "value" and as content "description"
 * @param obj Select element
 * @param index where to search in the data
 * @param data data[index][value] => description
 */
function changeOptions(obj, index, selectedValue, data){
	obj.length = 0;
	var x, i=0;
	var content = data[index];
	for(x in content){
		obj.options[i++] = new Option(content[x], x, selectedValue == x);
	}
}
