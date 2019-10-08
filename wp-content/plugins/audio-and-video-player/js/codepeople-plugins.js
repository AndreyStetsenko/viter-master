// Namespace
var mejs = mejs || {},
	codepeople_avp_generator = function()
	{
		if('undefined' != typeof codepeople_avp_generator_flag) return;
		codepeople_avp_generator_flag = true;

		var $ = jQuery;
		if(parseInt($.fn.jquery.replace(/\./g, '')) < 183)
		{
			$.ajax(
				{
				  url: 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js',
				  dataType: "script",
				  success: function(data)
				  {
					codepeople_avp(jQuery.noConflict());
				  }
				}
			);
		}
		else codepeople_avp($);
	};

function codepeople_avp($){
	var jQuery = $;

	/***  PLAYLIST CONTROLS  ***/
	mejs.Playlist = function(player){
		var me 		 = this,
			c 		 = player.container,
			n  		 = player.$node,
			id 		 = (n.closest('mediaelementwrapper').length) ? n.closest('mediaelementwrapper').attr('id') : n.attr('id'),
			clss 	 = n.attr('class').split(/\s+/),
			playlist = $('[id="'+id+'-list"]');

        // The playlist loop was activated
        me.loop = (n.attr('loop')) ? true : false;
		n.removeAttr('loop');

		// Player size
        me.playerWidth  = c.width();
        me.playerHeight = c.height();

        // Set the player object associated to the playlist
        me.player = player;

        // Set the player id
        me.playerId = id;

        // Playback the next playlist item
		me.player.media.addEventListener('ended', function (e) {
			me.player.pause();
            if(me.playlist && 2<=me.playlist.find('li').length)
                me.playNext();
            else if(me.loop){
                me.player.play();
            }
        }, false);

		// There is a playlist associated to the player
		if(playlist.length){
			// Set the playlist node
			me.playlist = playlist;

			// Set the playlist class
			me.playlist.addClass('emjs-playlist');

			// Set the skin class to playlist
			for(var i = 0, h = clss.length; i < h; i++){
				if(/\-skin/i.test(clss[i])){
					me.skin = clss[i];
					me.playlist.addClass(me.skin);
					break;
				}
			}

			// Set the playlist width
			me.playlist.width(c.width());

			// Associate click events to the playlist items
			$('li', me.playlist).click(function(){me.selectItem($(this));});

			// Associate the playist to the music player
			me.player.playlist = me;
		}
	};

	mejs.Playlist.prototype = {
		playlist 		: null,
		player 			: null,
		playerId 		: '',
		playerWidth 	: null,
		playerHeight 	: null,
		attributes 		:{
			show : true
		},
		skin 			: null,

		parseItem : function(item){
			var e;
			try{
				return $.parseJSON(item);
			}catch(e){
				return item;
			}

		},

		parseSrc : function(src){
			function adjustedSrc(v){
				var d = new Date();
				v += ((v.indexOf('?') == -1) ? '?' : '&cpmp=')+d.getTime();
				return v;
			};

			var source = {};

			if(typeof src != 'string'){ // The src is an object
				if(src.type) source['type'] = src.type;
				if(src.src)  source['src']  = adjustedSrc(src.src);
			}else { // The src is a string with media location
				source['src'] = adjustedSrc(src);
			}
			return source;
		},

		parseTrack : function(trck){
			var track = '<track';

			if(typeof src != 'string'){ // The trck is an object
				track += ' srclang = "' + ((trck.srclang) ? trck.srclang : 'en') + '"';
				track += ' kind = "'    + ((trck.kind)    ? trck.kind    : 'subtitles') + '"';
				if(trck.src) track += ' src="' + trck.src + '"';
			}else{ // The trck is a string with caption location
				track += ' kind="subtitles" srclang="en" src="' + trck + '"';
			}

			track += ' />';
			return track;
		},

		playItem : function(item){
			var me = this, player = me.player, node = player.node, poster = '', srcTags, trackTags = '';
			if(typeof item != 'string')
			{
				srcTags = [];
				if(item.poster) player.setPoster(item.poster);
				if(item.source)
				{
					if($.isArray(item.source))
						// many source formats
						$.each(item.source, function(i, src){ srcTags.push(me.parseSrc(src)); });
					else
						// only one source
						srcTags.push(me.parseSrc(item.source));
				}
				// Assign tracks
				if(item.track)
				{
					if($.isArray(item.track))
						// many captions
						$.each(item.track, function(i, track){ trackTags += me.parseTrack(track); });
					else
						trackTags += me.parseTrack(item.track);
				}
			}
			else srcTags = item;
			if(srcTags.length)
			{
				$(node).find('track').remove();
				if(trackTags != '')
				{
					$(node).append(trackTags);
					player.rebuildtracks();
				}
				player.setSrc( srcTags );
				player.load();
				player.play();
			}

		},

		/**
		 * playNext allow to play the next and previous items from playlist
		 * if next argument is false the previous item is selected
		 */
		playNext : function(next){
			var me = this,
				current_item = me.playlist.find('li.current:first'), // get the .current song
				item;
			if(typeof next == 'undefined') next = true;

			if (current_item.length == 0){
				current_item = me.playlist.find('li:first'); // get :first if we don't have .current class
			}

			if(current_item.length){ // If playlist is not empty
				if( ($(current_item).is(':last-child') && next) ||  ($(current_item).is(':first-child') && !next)) { // if it is last - stop playing or jump to the first item
					$(current_item).removeClass('current');
					if(me.loop){
						if(next){
							item = $('li:first', me.playlist).addClass('current')[0].getAttribute('value');
						}else{
							item = $('li:last', me.playlist).addClass('current')[0].getAttribute('value');
						}
						me.playItem(me.parseItem(item));
					}
				}else{ // take the next item to playback
					var next = (next) ? $(current_item).next() : $(current_item).prev(),
						item = next[0].getAttribute('value');

					next.addClass('current').siblings().removeClass('current');
					me.playItem(me.parseItem(item));
				}
			}
		},

		selectItem : function(item){
			var me = this;
            $(".mejs-pause").trigger('click');
            item.addClass('current').siblings().removeClass('current');

            if(item.siblings().length){
                var the_item = item[0].getAttribute('value');
                me.playItem(me.parseItem(the_item));
            }else{
                item.parents('#ms_avp').find('.mejs-play').click();
            }
		}
	};

	/***  NEXT BUTTON CONTROL  ***/
	MediaElementPlayer.prototype.buildnext = function(player, controls, layers, media) {
		if(jQuery('[id="'+media.id+'-list"] li').length < 2) return;
        var
            // create the loop button
            next =
            $('<div class="mejs-button mejs-next-button">' +
                '<button title="Next"></button>' +
            '</div>')
            // append it to the toolbar
            .appendTo(controls)
            // add a click toggle event
            .click(function() {
				if(player.playlist)
					player.playlist.playNext();
            });
    };

	/***  PREVIOUS BUTTON CONTROL  ***/
	MediaElementPlayer.prototype.buildprevious = function(player, controls, layers, media) {
		if(jQuery('[id="'+media.id+'-list"] li').length < 2) return;
        var
            // create the loop button
            next =
            $('<div class="mejs-button mejs-previous-button">' +
                '<button title="Previous"></button>' +
            '</div>')
            // append it to the toolbar
            .appendTo(controls)
            // add a click toggle event
            .click(function() {
				if(player.playlist)
					player.playlist.playNext(false);
            });
    };

	/***  EQ CONTROL  ***/
	MediaElementPlayer.prototype.buildeq = function(player, controls, layers, media) {
        var
            // create the eq bars
            eq =
            $('<div class="eq" style="display:none">'+
				'<span class="bar"></span>'+
				'<span class="bar"></span>'+
				'<span class="bar"></span>'+
				'<span class="bar"></span>'+
				'<span class="bar"></span>'+
			  '</div>')
            // append it to the toolbar
            .appendTo(controls);


		// Animate bars
		function fluctuate(bar, h) {
			var v = player.media.volume || 0,
				hgt = (Math.random()) * h * v,
				t = (hgt+1) * 30;

			if(media.paused || media.ended) {
				eq.hide();
			}else
				if(media.currentTime){
					eq.show();
				}

			bar.animate({
				height: hgt
			}, t, function() {
				fluctuate($(this), h);
			});
		}

		controls.find('.bar').each(function(i, bar){
			var b = $(bar),
				w = b.width(),
				h = b.height();

			b.css('left', (w*i+2*i)+'px');
			fluctuate(b, h);
		});
	};

	$('.codepeople-media').each(function(){
		var me 		 = $(this),
			settings = {
				features: ['previous','playpause','next','fullscreen','tracks','eq','current','progress','duration','volume'],
				videoVolume: 'horizontal',
				iPadUseNativeControls: false,
				iPhoneUseNativeControls: false,
				success: function(media, node,  player) {
					var cp = $(node).parents('.codepeople-media');
					if(media.pluginType && media.pluginType == 'silverlight'){
						cp.addClass('silverlight');
					}

					// Set titles
					cp.find( '.mejs-time-handle' ).attr( 'title', 'Seek' );
					cp.find( '.mejs-horizontal-volume-current,.mejs-vertical-volume-current' ).attr( 'title', 'Volume' );

					// Get skin
					var cls = cp.attr('class');
					cls = cls.replace(/^\s+/, '').replace(/\s+$/, '');
					cls = cls.split(/\s+/);

					for(var i = 0, h = cls.length; i < h; i++){
						if(/\-skin$/.test(cls[i])){
							if( typeof cp_skin_js != 'undefined' && cp_skin_js[cls[i]]){
								cp_skin_js[cls[i]]($);
							}
							break;
						}
					}

					media.addEventListener( 'play', function(){
						var p = $( node ).parents( '#ms_avp' );
						if( p.length && p.find( '.current' ).length == 0 ){
							p.find( '.emjs-playlist li:first' ).addClass( 'current' );
						}

					} );

					new mejs.Playlist(player);
				}
			};

		settings[ 'defaultVideoHeight' ] = settings[ 'audioHeight' ] = settings[ 'videoHeight' ] = me.height();
		settings[ 'defaultVideoWidth' ] = settings[ 'audioWidth' ] = settings[ 'videoWidth' ] = me.parent('#ms_avp').width();
		me.mediaelementplayer( settings );
	});
}

jQuery(codepeople_avp_generator);
jQuery(window).on('load',codepeople_avp_generator);