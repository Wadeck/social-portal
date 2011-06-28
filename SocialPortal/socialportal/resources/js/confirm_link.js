
function confirmLink(obj, message){
	// Confirmation is not required in the configuration file
    // or browser is Opera (crappy js implementation)
    if (typeof(window.opera) != 'undefined') {
        return true;
    }
    var is_confirmed = confirm(message);
    return is_confirmed;
}