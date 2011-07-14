var jq = jQuery; 

function onChange(obj){
	var link = jq(obj).attr('value');
	if(0 == link){
		return;
	}
	window.location = link;
}

function hideMe(obj){
	jq(obj).hide();
}

function displayIt(targetId){
	var target = jq('#' + targetId);
	target.show();
}