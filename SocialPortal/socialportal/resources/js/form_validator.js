
var jq = jQuery;

function validForm(obj) {
	//FIXME !!!!! remove that
	return true;
	if(obj.isAlreadySent){
		alert(_error_messages['form_already_sent']);
		return false;
	}
	obj.disabled = true;
	try{
		// we go through all the current form and remove active to the
		// error messages
		var form = obj.form;
	
		var error_messages = jq('.error_message', form);
		error_messages.each(function() {
			jq(this).removeClass('active');
		});
	
		var numberOfError = 0;
		var fields = jq(':input', form).not(':input[type=hidden]').not(':submit');
		fields.each(function() {
			var value = getValue(jq(this).val(), 0);
			if(value == jq(this).attr('title')){
				value = '';
			}
			
			var constraints = jq(this).attr('class').split(' ');
			var error = containsError(value, constraints);
			if (error) {
				var parent = jq(this).parentsUntil('.field_container');
				if(!parent.length){
					parent = jq(this);
				}
				parent = parent.parent();
				parent = parent[parent.length-1];
				var errorLabel = jq('.error_message', parent);
				errorLabel.html(error);
				errorLabel.addClass('active');
				numberOfError++;
			}
		});
	}catch(e){
		
	}
	
	obj.disabled = false;
	
	if (numberOfError > 0) {
		return false;
	}
	
	obj.isAlreadySent = true;
	return true;
}

/**
 * i18n function used to replace the %xxx% by their real value, the text passed
 * are translated in php before
 */
function __(text, params) {
	var regexp;
	for ( var p in params) {
		regexp = new RegExp(p, 'g');
		text = text.replace(regexp, params[p]);
	}
	return text;
}

/**
 * type= 0:clean, 1:raw,...
 */
function getValue(value, type) {
	type = type || 0;
	switch (type) {
		case 0:
			value = strip_tags(stripslashes(value)).trim();
			return value;
		case 1:
			return value;
	}
}

function containsError(value, constraints) {
	erru = _error_messages;
	for ( var c in constraints) {
		var args = constraints[c].split('_');
		var base = args.shift();
		switch (base) {
			case 'mandatory':
				if (!value) {
					return __(_error_messages['mandatory']);
				}
				break;
			case 'optional':
				break;
			case 'strlen':
				switch (args[0]) {
					case 'less-than':
						if (value && !(value.length < args[1])) {
							return __(_error_messages['strlen_lessthan'], {
								'%value%' : args[1]
							});
						}
						break;
					case 'less-equal':
						if (value && !(value.length <= args[1])) {
							return __(_error_messages['strlen_lessequal'], {
								'%value%' : args[1]
							});
						}
						break;
					case 'at-least':
						if (value && !(value.length >= args[1])) {
							return __(_error_messages['strlen_atleast'], {
								'%value%' : args[1]
							});
						}
						break;
				}
				break;

			case 'value':
				var value_num = parseFloat(value);
				var args_num = parseFloat(args[1]);
				switch (args[0]) {
					case 'not-equal':
						if (value_num && (value_num == args_num)) {
							return __(_error_messages['value_notequal'], {
								'%value%' : args_num
							});
						}
						break;
					case 'less-than':
						if (value_num && !(value_num < args_num)) {
							return __(_error_messages['value_lessthan'], {
								'%value%' : args_num
							});
						}
						break;
					case 'greater-equal-than':
						if (value_num && !(value_num >= args_num)) {
							return __(_error_messages['value_greaterequalthan'], {
								'%value%' : args_num
							});
						}
						break;
					case 'greater-than':
						if (value_num && !(value_num > args_num)) {
							return __(_error_messages['value_greaterthan'], {
								'%value%' : args_num
							});
						}
						break;
				}
				break;
		}
	}
	return false;
}

// Strips HTML and PHP tags from a string
// * example 1: strip_tags('<p>Kevin</p> <b>van</b> <i>Zonneveld</i>',
// '<i><b>');
// * returns 1: 'Kevin <b>van</b> <i>Zonneveld</i>'
// * example 2: strip_tags('<p>Kevin <img src="someimage.png"
// onmouseover="someFunction()">van <i>Zonneveld</i></p>', '<p>');
// * returns 2: '<p>Kevin van Zonneveld</p>'
// * example 3: strip_tags("<a href='http://kevin.vanzonneveld.net'>Kevin van
// Zonneveld</a>", "<a>");
// * returns 3: '<a href='http://kevin.vanzonneveld.net'>Kevin van
// Zonneveld</a>'
// * example 4: strip_tags('1 < 5 5 > 1');
// * returns 4: '1 < 5 5 > 1'
// * example 5: strip_tags('1 <br/> 1');
// * returns 5: '1 1'
// * example 6: strip_tags('1 <br/> 1', '<br>');
// * returns 6: '1 1'
// * example 7: strip_tags('1 <br/> 1', '<br><br/>');
// * returns 7: '1 <br/> 1'
function strip_tags(input, allowed) {
	// making sure the allowed arg is a string containing only tags in lowercase
	// (<a><b><c>)
	allowed = (((allowed || "") + "").toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
	var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi, commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
	return input.replace(commentsAndPhpTags, '').replace(tags, function($0, $1) {
		return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
	});
}

// * example 1: stripslashes('Kevin\'s code');
// * returns 1: "Kevin's code"
// * example 2: stripslashes('Kevin\\\'s code');
// * returns 2: "Kevin\'s code"
function stripslashes(str) {
	return (str + '').replace(/\\(.?)/g, function(s, n1) {
		switch (n1) {
			case '\\':
				return '\\';
			case '0':
				return '\u0000';
			case '':
				return '';
			default:
				return n1;
		}
	});
}
