var jq = jQuery;
jq(document).ready(function() {
	jq('div.flash_message').click(function() {
		jq(this).hide();

		return true;
	});
});
