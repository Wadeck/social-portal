var jq = jQuery;

jq(window).load(function() {
	var avatar = jq('#avatar-to-crop');
	var imgW = avatar.css('width');
	var imgH = avatar.css('height');
	imgW = imgW.substr(0, imgW.length-2);
	imgH = imgH.substr(0, imgH.length-2);

	// they are both defined in global view, passed by php directly
	if(!window.avatarImageMaxWidth){
		avatarImageMaxWidth = 600;
	}
	if(!window.avatarImageMaxHeight){
		avatarImageMaxHeight = 600;
	}
	if(!window.avatarCropMaxWidth){
		avatarCropMaxWidth = avatarImageMaxWidth ;
	}
	if(!window.avatarCropMaxHeight){
		avatarCropMaxHeight = avatarImageMaxHeight ;
	}
	if(!window.avatarCropMinWidth){
		avatarCropMinWidth = 0 ;
	}
	if(!window.avatarCropMinHeight){
		avatarCropMinHeight = 0 ;
	}
	
	jq('#avatar-to-crop').Jcrop({
		onChange : showPreview,
		onSelect : showPreview,
		onSelect : updateCoords,
		aspectRatio : 1,
		boxWidth: avatarImageMaxWidth,
		boxHeight: avatarImageMaxHeight,
		minSize: [avatarCropMinWidth, avatarCropMinHeight],
		maxSize: [avatarCropMaxWidth, avatarCropMaxHeight],
		setSelect : [ imgW *0.2, imgH *0.2, imgW *0.4, imgH *0.4 ]
	});
	
	jq('.resizable').each(function(){
		var size = getSizeValue(this);
		jq(this).css({
			width : size + 'px',
			height : size + 'px'
		});
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
	var previews = jq('.avatar-crop-preview');
	jq('.avatar-crop-preview').each(function() {
		var jqThis = jq(this);
		var size = getSizeValue(jqThis.parent()[0]);
		specificShowPreview(this, coords, imgW, imgH, size);
	});
}

function specificShowPreview(obj, coords, imageWidth, imageHeight, size) {
	if(!size){
		size = 150;
	}
	if (parseInt(coords.w) > 0) {
		// rx,ry represents the ratio of width/height compared to the total image
//		var rx = 150 / coords.w;
//		var ry = 150 / coords.h;
		var rx = size / coords.w;
		var ry = size / coords.h;

		jq(obj).css({
			width : Math.round(rx * imageWidth) + 'px',
			height : Math.round(ry * imageHeight) + 'px',
//			width : Math.round(rx * imageWidth) + 'px',
//			height : Math.round(ry * imageHeight) + 'px',
			marginLeft : '-' + Math.round(rx * coords.x) + 'px',
			marginTop : '-' + Math.round(ry * coords.y) + 'px'
		});
	}
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

function checkCoords() {
	if (parseInt($('#w').val()))
		return true;
	alert('Please select a crop region then press submit.');
	return false;
}