/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function showSingleVideo(sourceUrl) {
	var configobj = {
        "players": {
            "Flash":{"src":"/frontend/js/video/ovp.swf","minver":"9","controls":true, "plugins":[]},
            "Silverlight":{"src":"/frontend/js/video/ovp.xap","minver":"4.0","controls":true, "plugins":[""]},
            "HTML5":{"minver":"0","controls":true}
        },
		"controls": { 'src_img':'/frontend/img/pixel.png' },
		"strategy":{ "order":["HTML5","Silverlight","Flash"] }
	};
	ovp.init(configobj);

	var videoconf = {
		"sources":[
			{"src":sourceUrl,"type":"video/mp4"}
		],
		// For real testing, these are public and available
		'posterimg':"/frontend/img/videobackground.png",
		'autobuffer':true,
		'controls':true,
		'height':360,
		'width':640,
		'id':'vplayer'
	};

	// render the player
	return ovp.render('vplayer', videoconf);
}

$(function() {

	// Find all YouTube videos
	var $allVideos = $("iframe[src^='//player.vimeo.com'],iframe[src^='http://player.vimeo.com'], iframe[src^='//www.youtube.com'], iframe[src^='http://www.youtube.com']"),
	// The element that is fluid width
	$fluidEl = $(".span7");
	// Figure out and save aspect ratio for each video
	$allVideos.each(function() {
		$(this)
			.data('aspectRatio', this.height / this.width)
			// and remove the hard coded width/height
			.removeAttr('height')
			.removeAttr('width');
	});
	// When the window is resized
	// (You'll probably want to debounce this)
	$(window).resize(function() {
		var newWidth = $fluidEl.width();
		// Resize all videos according to their own aspect ratio
		$allVideos.each(function() {
			var $el = $(this);
			$el
				.width(newWidth)
				.height(newWidth * $el.data('aspectRatio'));
		});
	// Kick off one resize to fix all videos on page load
	}).resize();
});

