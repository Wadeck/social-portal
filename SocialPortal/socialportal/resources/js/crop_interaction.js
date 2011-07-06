var jq = jQuery;

jq(window).load(function() {
	var avatar = jq('#avatar-to-crop');
	var imgW = avatar.css('width');
	var imgH = avatar.css('height');
	imgW = imgW.substr(0, imgW.length-2);
	imgH = imgH.substr(0, imgH.length-2);

	jq('#avatar-to-crop').Jcrop({
		onChange : showPreview,
		onSelect : showPreview,
		onSelect : updateCoords,
		aspectRatio : 1,
		setSelect : [ 50, 50, imgW / 2, imgH / 2 ]
	});
});

function updateCoords(c) {
	jq('#x').val(c.x);
	jq('#y').val(c.y);
	jq('#w').val(c.w);
	jq('#h').val(c.h);
};

function showPreview(coords) {
	var avatar = jq('#avatar-to-crop');
	var imgW = avatar.css('width');
	var imgH = avatar.css('height');

	imgW = imgW.substr(0, imgW.length-2);
	imgH = imgH.substr(0, imgH.length-2);
	
	jq('#avatar-crop-preview').each(function() {
		var jqThis = jq(this);
		var size = getSizeFromClass(this);
		specificShowPreview(obj, coords, imgW, imgH);
	});
}

function getSizeValue(target) {
	var selectedValue = -1;
	var classes = jq(target).attr('class');
	classes = classes.split(' ');
	var size = -1;
	for ( var i = 0; i < classes.length; i++) {
		var c = classes[i];
		var index = c.indexOf('size_');
		if (-1 != index) {
			size = c.substr(index + 'size_'.length);
			return size;
		}
	}
	return size;
}

function specificShowPreview(obj, coords, imageWidth, imageHeight) {
	if (parseInt(coords.w) > 0) {
		var rx = 150 / coords.w;
		var ry = 150 / coords.h;

		jq(obj).css({
			width : Math.round(rx * imageWidth) + 'px',
			height : Math.round(ry * imageHeight) + 'px',
			marginLeft : '-' + Math.round(rx * coords.x) + 'px',
			marginTop : '-' + Math.round(ry * coords.y) + 'px'
		});
	}
}

function checkCoords() {
	if (parseInt($('#w').val()))
		return true;
	alert('Please select a crop region then press submit.');
	return false;
}