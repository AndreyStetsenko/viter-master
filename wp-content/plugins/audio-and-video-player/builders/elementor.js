function cpm_select_skin(target)
{
	var e = jQuery('[data-setting="'+target+'"]'), v = e.val(), s = jQuery('[id="cpm_player_skin"]').val(), m;
	if(v.length)
	{
		m = v.match(/\sskin="[^"]*"/i);
		if(m) v = v.replace(m[0], ' skin="'+s+'"');
		else
		{
			m = v.match(/cpm\-player/i);
			if(m) v = v.replace(m[0], m[0]+' skin="'+s+'"');
		}
		e.val(v).trigger('input');
	}
}

function cpm_get_media(type, target)
{
	var media = wp.media(
	{
		title: 'Select Media File',
		library: {
			type: [type]
		},
		button: {
			text: 'Select Item(s)'
		},
		multiple: true
	}).on('select',
		function()
		{
			var e = jQuery('[data-setting="'+target+'"]'),
				v = e.val(), m,
				skin = jQuery('[id="cpm_player_skin"]').val(),
				player = '',
				playlist = '',
				attachments = media.state().get('selection').map(
					function( attachment )
					{
						return attachment.toJSON();
					}
				);

			if(attachments.length)
			{
				for(var i in attachments)
				{
					var fileObj = attachments[i],
						url 	= fileObj.url,
						name 	= '';

					if(('title' in fileObj) && fileObj['title'].length) name = fileObj['title'];
					else if(('description' in fileObj) && fileObj['description'].length) name = fileObj['description'];
					else name = fileObj['filename'];
					playlist += "[cpm-item file=\""+url+"\"]"+name+"[/cpm-item]\n";
				}
				m = v.match(/(\[\s*cpm\-player[^\]]*\])/i);
				if(m)
				{
					player = m[0]+playlist+'[/cpm-player]'
				}
				else
				{
					player = '[cpm-player skin="'+skin+'" width="100%" playlist="true" type="'+type+'"]'+playlist+'[/cpm-player]';
				}
			}
			e.val(player).trigger('input');
		}
	).open();
}