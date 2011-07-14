var jq = jQuery;

function displayInstruction(obj, targetId, visible, cookiename) {
	var content = jq('#'+targetId);
	if(visible){
		content.slideDown(300);
	}else{
		content.slideUp(300);
	}
	jq.cookie(cookiename, visible);
	
	jq(obj).siblings().show();
	jq(obj).hide();
	
	return false;
}

function simulateClick(targetId){
	jq('#'+targetId).click();
}
