var jq = jQuery;
jq(document).ready(function() {
	jq('.labelInInput:input[title]').each(function() {
		var jqThis = jq(this);
		if (jqThis.val() === '') {
			jqThis.val(jqThis.attr('title'));
		}
		jqThis.focus(function() {
			if (jqThis.val() === jqThis.attr('title')) {
				jqThis.val('');
			}
		});
		jqThis.blur(function() {
			var val = jqThis.val();
			val = strip_tags(stripslashes(val)).trim();
			if (jqThis.val() === '') {
				/** we reset the initial value when the field is empty */
				jqThis.val(jqThis.attr('title'));
			}
		});
	});
});

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