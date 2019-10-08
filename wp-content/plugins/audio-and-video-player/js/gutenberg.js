jQuery(function(){
	( function( blocks, element ) {
		var el 					= element.createElement,
			InspectorControls  	= wp.editor.InspectorControls,
			MediaUpload			= wp.editor.MediaUpload;

		/* Plugin Category */
		blocks.getCategories().push({slug: 'cpmp', title: 'Audio and Video Player'});

		/* ICONS */
		const iconCPMP_gallery = el('img', { width: 20, height: 20, src:  "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAV/QAAFf0BzXBRYQAAABZ0RVh0Q3JlYXRpb24gVGltZQAxMS8xMi8xOPSptAIAAAAcdEVYdFNvZnR3YXJlAEFkb2JlIEZpcmV3b3JrcyBDUzbovLKMAAAAg0lEQVQ4jeWUwQ2AIAxFW8IsbKfOItvpMNYTipGWVjEe6JGfPH6aBwjDQtBwXEvYJ0BfOqQYzCAcVx6YQu3kBTwXWJsVgW+a3YAUA5BSIET+8hNIAG7SNdxmfjWiNk92WvXQClWJbYH+8/RqOuV5FWh1UwRamqU5PESU/bqAUMj6+2B3U1goCDJYkC8AAAAASUVORK5CYII=" } );

		const iconCPMP_audio = el('img', { width: 20, height: 20, src:  "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAV/QAAFf0BzXBRYQAAABZ0RVh0Q3JlYXRpb24gVGltZQAxMS8xMi8xOPSptAIAAAAcdEVYdFNvZnR3YXJlAEFkb2JlIEZpcmV3b3JrcyBDUzbovLKMAAAAvklEQVQ4jWNkyL35n4GKgImahg0eA/9PUqOugYx5t3AaStDAfAdBht8TVDFchstQnAZaK3EyPGtRYpgQJMrAwsSI1RBshmI1cH+uDMPhfFkGST4WDDl83mVgYGBA0YFPIS4As4Ax7xZuF1IChpiBjHm3GBjzbjEcuP2NaAOQww/DQBhwnPyEwbr/McOH7/8IGoDXhcjg2P3vDILldxjad79j+PPvP1bDsBnOSG7xhculZBcOuLxNtgtxgcGfDgEQzU4dGpHbMAAAAABJRU5ErkJggg==" } );

		const iconCPMP_video = el('img', { width: 20, height: 20, src:  "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAAV/QAAFf0BzXBRYQAAABZ0RVh0Q3JlYXRpb24gVGltZQAxMS8xMi8xOPSptAIAAAAcdEVYdFNvZnR3YXJlAEFkb2JlIEZpcmV3b3JrcyBDUzbovLKMAAAAYklEQVQ4jeWUsRHAMAgDRS6zZDs8TLbDyyiVK7BD4SI51NCIR1AgUCM26tgJ+wcQUCPUSNLVlaIeqNEllNbTWSKvA/K+0sDIWzDhGU2V1lPg4V0Ch+Ft9dnQ79+wYEKp92AfB1dxhqcdi5sAAAAASUVORK5CYII=" } );

		/* Shortcode generator */
		function shortcodeGenerator(playerId)
		{
			var shortcode  = new wp.shortcode(
								{
									'tag'	:'cpm-player',
									'attrs' : { 'id' : playerId },
									'type' 	: 'self-closing'
								}
							);
			return shortcode.string();
		};

		function extractAttsFromShortcode(shortcode)
		{
			var attrs = false,
				sc = wp.shortcode.next('codepeople-html5-media-player', shortcode);
			if(!sc) sc = wp.shortcode.next('cpm-player', shortcode);
			if(sc) attrs = sc.shortcode.attrs;
			return attrs;
		};

		function createNewPlayer(props, type)
		{
			var children	  = [],
				focus 	  	  = props.isSelected,
				base_opt_name = 'cpmp-skin-list-option-',
				shortcode     = props.attributes.shortcode || '',
				attrs		  = extractAttsFromShortcode(shortcode),
				skin		  = 'classic-skin',
				skins_options = [];

			/* Extract the current skin selected */
			if(attrs) skin = attrs.named[ 'id' ] || skin;

			/* Populate the skins list if it has not been populated previously */
			skins_options.push(el('option',{key: base_opt_name+0, value: ''}, 'Select a skin'));
			if(
				typeof cpmp_insert_media_player != 'undefined' &&
				typeof cpmp_insert_media_player['skins'] != 'undefined'
			)
			{
				jQuery('<span>'+cpmp_insert_media_player.skins+'</span>')
				.find('option')
				.each(
					function()
					{
						var e = jQuery(this),
							v = e.val(),
							t = e.text(),
							o = {key:base_opt_name+v, value:v};

						skins_options.push(el('option', o, t));
					}
				);
			}

			if(props.attributes.shortcode.length == 0)
			{
				children.push(
					el(
						MediaUpload,
						{
							id 			: 'cpmp-mediaupload',
							key			: 'cpmp-mediaupload',
							title		: 'Select the '+type+' files',
							allowedTypes: type,
							multiple	: true,
							onSelect	: function(data)
							{
								var player = "",
									playlist = "\n";

								if(data.length)
								{
									for(var i in data)
									{
										var fileObj = data[i],
											url 	= fileObj.url,
											name 	= '';

										if(('title' in fileObj) && fileObj['title'].length) name = fileObj['title'];
										else if(('description' in fileObj) && fileObj['description'].length) name = fileObj['description'];
										else name = fileObj['filename'];
										playlist += "[cpm-item file=\""+url+"\"]"+name+"[/cpm-item]\n";
									}
								}
								player = '[cpm-player skin="classic-skin" width="100%" playlist="true" type="'+type+'"]'+playlist+'[/cpm-player]';
								props.setAttributes({shortcode:player});
							},
							render  	: function(obj)
							{
								return el(
									'button',
									{
										onClick: obj.open
									},
									'Open Media Library'
								);
							}
						}
					)
				);
			}
			else
			{
				children.push(
					el(
						'textarea',
						{
							key		: 'cpmp-shortcode',
							style	: {width: '100%'},
							value	: props.attributes.shortcode,
							onChange : function(evt)
							{
								props.setAttributes({ shortcode : evt.target.value });
							}
						}
					)
				);

				children.push(
					el(
						'div', {className: 'cpmp-iframe-container', key:'cpmp_iframe_container'},
						el('div', {className: 'cpmp-iframe-overlay', key:'cpmp_iframe_overlay'}),
						el('iframe',
							{
								key: 'cpmp_iframe',
								src: cpmp_gutenberg_editor_config.url+encodeURIComponent(props.attributes.shortcode),
								height: 0,
								width: 500,
								scrolling: 'no'
							}
						)
					)
				);
			}

			if(!!focus)
			{
				children.push(
					el(
						InspectorControls,
						{key: 'cpmp-inspector'},
						[
							el('p', {key : 'cpmp-label'}, 'Select Skin'),
							el('select',
								{
									key: 'cpmp-skins-list',
									onChange: function(evt)
									{
										var shortcode = props.attributes.shortcode;
											next = wp.shortcode.next('cpm-player', shortcode);

										if(next)
										{
											var obj =  next.shortcode;
											obj.attrs.named['skin'] = evt.target.value;
											props.setAttributes({shortcode: obj.string()});
										}
										else
										{
											var obj  = wp.shortcode(
												{
													'tag':'cpm-player',
													'attrs' : {
														'skin' : evt.target.value,
														'width' : '100%',
														'type' : type
													},
													'type' : 'self-closing'
												}
											);
											props.setAttributes({shortcode: obj.string()});
										}
									},
									value : skin
								},
								skins_options
							),
							el(
								'div',
								{
									key : 'cpmp-link-container'
								},
								el(
									'a',
									{
										key : 'cpmp-create-player',
										href: 'options-general.php?page=codepeople-media-player.php'
									},
									'Go to the players gallery'
								)
							)
						]
					)
				);
			}
			return children;
		};

		/* Create new Audio Player */
		blocks.registerBlockType( 'cpmp/new-audio-player', {
			title: 'New Audio Player',
			icon: iconCPMP_audio,
			category: 'cpmp',
			supports: {
				customClassName	: false,
				className		: false,
				html			: false
			},
			attributes: {
				shortcode : {
					type 	: 'string',
					default : ''
				}
			},

			edit: function( props ) {
				return createNewPlayer(props, 'audio');
			},

			save: function( props ) {
				return el(element.RawHTML, null, props.attributes.shortcode);
			}
		});

		/* Create new Video Player */
		blocks.registerBlockType( 'cpmp/new-video-player', {
			title: 'New Video Player',
			icon: iconCPMP_video,
			category: 'cpmp',
			supports: {
				customClassName	: false,
				className		: false,
				html			: false
			},
			attributes: {
				shortcode : {
					type 	: 'string',
					default : ''
				}
			},

			edit: function( props ) {
				return createNewPlayer(props, 'video');
			},

			save: function( props ) {
				return el(element.RawHTML, null, props.attributes.shortcode);
			}
		});

		/* Insert Player From Players Gallery */
		blocks.registerBlockType( 'cpmp/from-gallery', {
			title: 'Insert Player From Gallery',
			icon: iconCPMP_gallery,
			category: 'cpmp',
			supports: {
				customClassName	: false,
				className		: false,
				html			: false
			},
			attributes: {
				id : {
					type : 'string',
					default : ''
				}
			},

			edit: function( props ) {
				var children 	  = [],
					focus 	  	  = props.isSelected,
					base_opt_name = 'cpmp-list-option-',
					ids_options	  = [],
					id   	  	  = props.attributes.id || '';

				/* Populate the options list if it has not been populated previously */
				ids_options.push(el('option',{key: base_opt_name+0, value: ''}, 'Select a player'));
				if(
					typeof cpmp_insert_media_player != 'undefined' &&
					typeof cpmp_insert_media_player['tag'] != 'undefined'
				)
				{
					jQuery('<span>'+cpmp_insert_media_player.tag+'</span>')
					.find('option')
					.each(
						function()
						{
							var e = jQuery(this),
								v = e.val(),
								t = e.text(),
								o = {key:base_opt_name+v, value:v};

							if(typeof id == 'undefined') id = v;
							ids_options.push(el('option', o, t));
						}
					);
				}

				children.push(
					el(
						'textarea',
						{
							key		: 'cpmp-shortcode',
							type	: 'text',
							style	: { width: '100%'},
							value	: shortcodeGenerator(id),
							onChange : function(evt)
							{
								var id = '',
									sc = wp.shortcode.next('codepeople-html5-media-player', evt.target.value);
								if(!sc) sc = wp.shortcode.next('cpm-player', evt.target.value);
								if(sc) id = sc.shortcode.attrs.named[ 'id' ] || '';
								props.setAttributes({ id : id });
							}
						}
					)
				);

				children.push(
					el(
						'div', {className: 'cpmp-iframe-container', key: 'cpmp_iframe_container'},
						el('div', {className: 'cpmp-iframe-overlay', key: 'cpmp_iframe_overlay'}),
						el('iframe',
							{
								key: 'cpmp_iframe',
								src: cpmp_gutenberg_editor_config.url+encodeURIComponent(shortcodeGenerator(props.attributes.id || '')),
								height: 0,
								width: 500,
								scrolling: 'no'
							}
						)
					)
				);

				if(!!focus)
				{
					children.push(
						el(
							InspectorControls,
							{key: 'cpmp-inspector'},
							[
								el('p', {key : 'cpmp-label'}, 'Select the Player'),
								el('select',
									{
										key: 'cpmp-list',
										onChange: function(evt){
											props.setAttributes({id: evt.target.value});
										},
										value : id
									},
									ids_options
								),
								el(
									'div',
									{
										key : 'cpmp-link-container'
									},
									el(
										'a',
										{
											key : 'cpmp-create-player',
											href: 'options-general.php?page=codepeople-media-player.php'
										},
										'Create or edit players'
									)
								)
							]
						)
					);
				}
				return children;
			},

			save: function( props ) {
				return shortcodeGenerator(props.attributes.id || '');
			}
		});

	} )(
		window.wp.blocks,
		window.wp.element
	);
});