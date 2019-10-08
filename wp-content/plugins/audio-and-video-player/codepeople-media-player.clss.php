<?php

class CodePeopleMediaPlayer {

	// PROPERTIES
	private $current_player_playlist; // playlist of current public player

	// METHODS
	private function transform_url( $url )
	{
		return str_replace( ' ', '%20', wp_kses_decode_entities($url) );
	}

	private function get_extension( $file )
	{
		$file = strtolower($file);
		if(strpos($file, 'youtube') !== false)
		{
			$ext = 'youtube';
		}
		else
		{
			$ext = substr($file, strlen($file)-4);
			if($ext[0] == '.') $ext = substr($ext, 1);

			switch ($ext)
			{
				case 'mp4':
				case 'm4v':
				case 'm4a':
					$ext = 'mp4';
				break;
				case 'webm':
				case 'webma':
				case 'webmv':
					$ext = 'webm';
				break;
				case 'ogg':
				case 'oga':
				case 'ogv':
					$ext = 'ogg';
				break;
				default:
					$ext = $ext;
			}
		}
		return $ext;
	}

	/*
		Register plugin
		Create database structure
	*/
	function register_plugin($networkwide){
		global $wpdb;

		if (function_exists('is_multisite') && is_multisite()) {
			if ($networkwide) {
	            $old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					$this->_create_db_structure();
				}
				switch_to_blog($old_blog);
				return;
			}
		}
		$this->_create_db_structure();
    }

	/*
		A new blog has been created in a multisite WordPress
	*/
	function installing_new_blog($blog_id, $user_id, $domain, $path, $site_id, $meta ) {
		global $wpdb;

		if ( is_plugin_active_for_network() )
		{
			$current_blog = $wpdb->blogid;
			switch_to_blog( $blog_id );
			$this->_create_db_structure();
			switch_to_blog( $current_blog );
		}
	}

	/*
		Create the database structure for save player's data
	*/
	function _create_db_structure()
	{
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$db_queries = array();
		$db_queries[] = "CREATE TABLE ".$wpdb->prefix.CPMP_PLAYER." (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			player_name VARCHAR(250) NOT NULL DEFAULT '',
			config LONGTEXT NULL,
			playlist LONGTEXT NULL,
            UNIQUE KEY id (id)
        ) $charset_collate;";

        dbDelta($db_queries); // Running the queries
	}

	function _get_skin_list(&$selected_skin, $type, &$width, &$height){
		$skin_dir = CPMP_PLUGIN_DIR.'/skins';
		$skins_arr = array();
		$skins_list = '';
		$skins_list_script = 'var cpmp_skin_list = [];';
		$c = 0;
		if(file_exists($skin_dir)){
			$d = dir($skin_dir);
			while (false !== ($entry = $d->read())) {
				if($entry != '.' && $entry != '..' && is_dir($skin_dir.'/'.$entry)){
					$this_skin = $skin_dir.'/'.$entry.'/';
					if(file_exists($this_skin)){
						$skin_data = parse_ini_file($this_skin.'config.ini', true);
						if(isset($skin_data['id'])){
							if(empty($selected_skin)){
								$selected_skin = $skin_data['id'];
							}

							$skins_list .= '
									<img
										src="'.esc_url((isset($skin_data['thumbnail'])) ? CPMP_PLUGIN_URL.'/skins/'.$entry.'/'.$skin_data['thumbnail'] : CPMP_PLUGIN_URL.'/images/thumbnail.jpg').'"
										title="'.esc_attr((isset($skin_data['name'])) ?  $skin_data['name'] : $skin_data['id']).' - Available"
										onclick="cpmp.set_skin(this, \''.$skin_data['id'].'\', \''.((isset($skin_data[$type]["width"])) ? $skin_data[$type]["width"] : '').'\', \''.((isset($skin_data[$type]["height"])) ? $skin_data[$type]["height"] : '').'\');"';
							$skins_list_script .= 'cpmp_skin_list['.$c.']="'.$skin_data['id'].'";';
							$c++;

							if($selected_skin == $skin_data['id']){
								$skins_list .= ' class="skin_selected" style="border: 1px dotted #CCC;margin-left:5px;cursor:pointer;" ';
								$width  = ((isset($skin_data[$type]["width"])) ? $skin_data[$type]["width"] : '');
								$height = ((isset($skin_data[$type]["height"])) ? $skin_data[$type]["height"] : '');
							}else
								$skins_list .= ' style="margin-left:5px;cursor:pointer;" ';

							$skins_list .= '/><script>'.$skins_list_script.'</script>';
						}
					}
				}
			}
			$d->close();
		}

		return $skins_list;
	}

	/*
		Create the settings page
	*/
    function _paypal_buttons(){
        $p = CPMP_PLUGIN_DIR.'/images/paypal_buttons';
        $d = dir($p);
        $str = "";
        while (false !== ($entry = $d->read())) {
            if($entry != "." && $entry != ".." && is_file("$p/$entry"))
                $str .= "<input type='radio' disabled />&nbsp;<img src='".esc_url(CPMP_PLUGIN_URL."/images/paypal_buttons/$entry")."'/>&nbsp;&nbsp;";
        }
        $d->close();
        return $str;
    }

	function admin_page(){
		global $wpdb;
		wp_enqueue_media();
?>
		<style>.cpm-disabled,.cpm-disabled *{color: #DDDDDD !important;}.cpm-disabled img{opacity: 0.5 !important;}</style>
		<h1><?php _e('Audio And Video Player', CPMP_LANG); ?></h1>
		<p  style="border:1px solid #E6DB55;margin-bottom:10px;padding:5px;background-color: #FFFFE0;"><?php _e('For any issues with the media player, go to our <a href="https://cpmediaplayer.dwbooster.com/contact-us" target="_blank">contact page</a> and leave us a message.', CPMP_LANG); ?>
		<br />
		<?php _e('If you want test the premium version of CP Media Player go to the following links:<br/> <a href="http://demos.net-factor.com/audio-and-video/wp-login.php" target="_blank">Administration area: Click to access the administration area demo</a><br/>
		<a href="http://demos.net-factor.com/audio-and-video/" target="_blank">Public page: Click to access the Public Page</a>', CPMP_LANG); ?>
		</p>

<?php
		if(isset($_POST['cpmp_player_create_update_nonce']) && wp_verify_nonce($_POST['cpmp_player_create_update_nonce'], __FILE__)){
			// Save player's data
			// Constructs the configuration stdClass
			$conf = new stdClass;

			if(!empty($_POST['cpmp_width'])) $conf->width = sanitize_text_field($_POST['cpmp_width']);
			if(!empty($_POST['cpmp_height'])) $conf->height = sanitize_text_field($_POST['cpmp_height']);
			if(!empty($_POST['cpmp_type'])) $conf->type = sanitize_text_field($_POST['cpmp_type']);
			if(!empty($_POST['cpmp_skin'])) $conf->skin = sanitize_text_field($_POST['cpmp_skin']);
			if(isset($_POST['cpmp_autoplay'])) $conf->autoplay = 'autoplay';
			if(isset($_POST['cpmp_show_playlist'])) $conf->playlist = true;
			if(isset($_POST['cpmp_loop'])) $conf->loop = 'loop';
			$conf->preload = (isset($_POST['cpmp_preload'])) ? 'auto' : 'none';
			$playlist = json_decode(sanitize_text_field(stripslashes($_POST['cpmp_media_player_playlist'])));

			$data = array(
							'player_name' => sanitize_text_field($_POST['cpmp_player_name']),
							'config' => serialize($conf),
							'playlist' => serialize($playlist)
						);
			$format = array( '%s', '%s', '%s' );
			if(empty($_POST['cpmp_player_id'])){
				$wpdb->insert(
								$wpdb->prefix.CPMP_PLAYER,
								$data,
								$format
							);
			}
			else{
				$wpdb->update(
								$wpdb->prefix.CPMP_PLAYER,
								$data,
								array('id' => @intval($_POST['cpmp_player_id'])),
								$format,
								'%d'
							);
			}
		}

        if (
            (
                (empty($_POST['cpmp_player_edition_nonce']) ||
                !wp_verify_nonce($_POST['cpmp_player_edition_nonce'],__FILE__)) &&
                (empty($_POST['cpmp_player_creation_nonce']) ||
                !wp_verify_nonce($_POST['cpmp_player_creation_nonce'],__FILE__))
            ) ||
            (
                isset($_POST['cpmp_player_edition_nonce']) && isset($_POST['cpmp_action']) &&
                wp_verify_nonce($_POST['cpmp_player_edition_nonce'],__FILE__) &&
                $_POST['cpmp_action'] == 'remove'
            )
           ){
			if(isset($_POST['player_id'])){
                $wpdb->query(
                    $wpdb->prepare(
                        "
                         DELETE FROM ".$wpdb->prefix.CPMP_PLAYER."
                         WHERE id = %d
                        ",
                            @intval($_POST['player_id'])
                        )
                );
            }

			$sql = "SELECT id, player_name FROM ".$wpdb->prefix.CPMP_PLAYER.";";
			$players = $wpdb->get_results($sql);

			if(count($players)){
				wp_enqueue_script('cpmp-admin', plugin_dir_url(__FILE__).'js/cpmp_admin.js', array('jquery'), null, true);
?>
				<div class="wrap">

					<form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
					<?php
						// create a custom nonce for submit verification later
						echo '<input type="hidden" name="cpmp_player_edition_nonce" value="' . wp_create_nonce(__FILE__) . '" />';
					?>
						<input type="hidden" name="cpmp_action" id="cpmp_action" value="update">
						<div class="postbox">
							<h3 class="hndle" style="padding:5px;"><?php _e('Edit an existent player', CPMP_LANG); ?></h3>
							<div class="inside">
								<select id="player_id" name="player_id">
								<?php
									foreach($players as $player){
										print '<option value="'.esc_attr($player->id).'">'.stripslashes($player->player_name).'</option>';
									}
								?>
								</select>
								<input type="submit" value="<?php esc_attr_e(__('Edit media player', CPMP_LANG)); ?>" class="button-primary">
								<input type="button" value="<?php esc_attr_e(__('Remove media player', CPMP_LANG)); ?>" class="button-primary" onclick="cpmp.remove_player();">
								<h3 id="avp_display_shortcode"></h3>
							</div>
						</div>
					</form>
						<div style="padding:10px;"><?php _e('- or -', CPMP_LANG); ?></div>
<?php
			} // End player edition
?>
					<form method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
						<?php
							// create a custom nonce for submit verification later
							echo '<input type="hidden" name="cpmp_player_creation_nonce" value="' . wp_create_nonce(__FILE__) . '" />';
						?>
						<div class="postbox">
							<h3 class="hndle" style="padding:5px;"><?php _e('Create new one', CPMP_LANG); ?></h3>
							<div class="inside">
								<input type="radio" name="player_type" value="audio" checked> <?php _e('Audio', CPMP_LANG); ?> <input type="radio" name="player_type" value="video"> <?php _e('Video', CPMP_LANG); ?> <input type="submit" value="<?php esc_attr_e(__('Create new media player')); ?>" class="button-primary" />
							</div>
						</div>
					</form>
					<p style="border:1px solid #E6DB55;margin-bottom:10px;padding:5px;background-color: #FFFFE0;">
					<?php _e('The PayPal settings are available only in the commercial version of <a href="https://cpmediaplayer.dwbooster.com/download" target="_blank">Audio and Video Player</a>.', CPMP_LANG); ?>
					</p>
                    <p><?php _e('For sale a file associated to your player, complete the PayPal data.', CPMP_LANG); ?></p>
                    <p><?php _e('It is possible to create the media player with samples of audio or video files, and then associate a full version of files for sale.'); ?></p>
					<div class="postbox cpm-disabled">
						<h3 class="hndle" style="padding:5px;"><?php _e('PayPal settings', CPMP_LANG); ?></h3>
						<div class="inside">
							<table class="form-table">
								<tr valign="top">
									<th scope="row"><?php _e('Enable Paypal Payments?', CPMP_LANG); ?></th>
									<td><input type="checkbox" disabled /></td>
								</tr>

								<tr valign="top">
									<th scope="row"><?php _e('Enabling the Sandbox?', CPMP_LANG); ?></th>
									<td><input type="checkbox" disabled /></td>
								</tr>

								<tr valign="top">
									<th scope="row"><?php _e('Paypal email', CPMP_LANG); ?></th>
									<td><input type="text" size="40" disabled /></td>
								</tr>

								<tr valign="top">
									<th scope="row"><?php _e('Currency', CPMP_LANG); ?></th>
									<td>
										<select disabled><option>USD</option></select>
									</td>
								</tr>

								<tr valign="top">
									<th scope="row"><?php _e('Currency Symbol', CPMP_LANG); ?></th>
									<td><input type="text" disabled /></td>
								</tr>

								<tr valign="top">
									<th scope="row"><?php _e('Paypal language', CPMP_LANG); ?></th>
									<td><select disabled><option>United States - U.S. English</option></select>
									</td>
								</tr>

								<tr valign="top">
									<th scope="row"><?php _e('Paypal button for instant purchases', CPMP_LANG); ?></th>
									<td><?php print $this->_paypal_buttons(); ?></td>
								</tr>

								<tr valign="top">
									<th scope="row"><?php _e('Download link valid for', CPMP_LANG); ?></th>
									<td><input type="text" disabled /> <?php _e('day(s)', CPMP_LANG); ?></td>
								</tr>
							</table>
							<hr />
							<h3><?php _e('Purchase notifications', CPMP_LANG); ?></h3>
							<table class="form-table">
								<tr valign="top">
									<th scope="row"><?php _e('Notification "from" email', CPMP_LANG); ?></th>
									<td><input type="text" size="40" disabled /></td>
								</tr>

								<tr valign="top">
									<th scope="row"><?php _e('Send notification to email', CPMP_LANG); ?></th>
									<td><input type="text" size="40" disabled /></td>
								</tr>

								<tr valign="top">
									<th scope="row"><?php _e('Email subject confirmation to user', CPMP_LANG); ?></th>
									<td><input type="text" size="40" disabled /></td>
								</tr>

								<tr valign="top">
									<th scope="row"><?php _e('Email confirmation to user', CPMP_LANG); ?></th>
									<td><textarea cols="60" rows="2" disabled></textarea></td>
								</tr>

								<tr valign="top">
									<th scope="row"><?php _e('Email subject notification to admin', CPMP_LANG); ?></th>
									<td><input type="text" size="40" disabled /></td>
								</tr>

								<tr valign="top">
									<th scope="row"><?php _e('Email notification to admin', CPMP_LANG); ?></th>
									<td><textarea cols="60" rows="2" disabled></textarea></td>
								</tr>
							</table>
							<div><input type="button" value="<?php esc_attr_e(__('Save PayPal settings', CPMP_LANG)); ?>" class="button-primary" disabled /></div>
						</div>
					</div>
			</div>
			<style>#wpfooter{position:relative !important;}</style>
<?php
		}else{
			wp_enqueue_style('thickbox');
			wp_enqueue_script('cpmp-admin', plugin_dir_url(__FILE__).'js/cpmp_admin.js', array('jquery', 'thickbox'), null, true);

			$player = new stdClass;
			$config = new stdClass;
			$config->skin = '';
			$playlist = array();
			$insertion_button_text = __('Create Media Player', CPMP_LANG);
			$playlist_item_list = '';

			if(
				isset($_POST['cpmp_player_edition_nonce']) &&
				wp_verify_nonce($_POST['cpmp_player_edition_nonce'],__FILE__) &&
				isset($_POST['player_id'])
			)
			{ // Edition
				$player 	= $wpdb->get_row( $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.CPMP_PLAYER.' WHERE id=%d', @intval($_POST['player_id'])));
				if ( !empty($player) )
				{
					$player_id = $player->id;

					$config_tmp = unserialize($player->config);
					if($config_tmp) $config = $config_tmp;

					$player->playlist = str_replace(';d:', ';i:', $player->playlist);
					$playlist_tmp = unserialize($player->playlist);
					if($playlist_tmp) $playlist = $playlist_tmp;

					foreach( $playlist as $item )
					{
						$playlist_item_list .= '<div id="'.esc_attr($item->id).'" class="playlist_item" style="cursor:pointer;width:100%;margin:5px;background-color:#c7e4f3;">
						<div style="float:left;">
						<a href="javascript:void(0);" onclick="cpmp.move_item(\''.esc_js($item->id).'\', -1);" title="Up" style="text-decoration:none;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" d="M0 0h24v24H0V0z"/><path d="M4 12l1.41 1.41L11 7.83V20h2V7.83l5.58 5.59L20 12l-8-8-8 8z"/></svg></a>
						<a href="javascript:void(0);" onclick="cpmp.move_item(\''.esc_js($item->id).'\', 1);" title="Down" style="text-decoration:none;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="none" d="M0 0h24v24H0V0z"/><path fill="#010101" d="M20 12l-1.41-1.41L13 16.17V4h-2v12.17l-5.58-5.59L4 12l8 8 8-8z"/></svg></a>
						<a href="javascript:void(0);" onclick="cpmp.delete_item(\''.esc_js($item->id).'\');" title="Delete item" style="text-decoration:none;"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/><path d="M0 0h24v24H0z" fill="none"/></svg></a>
						</div>
						<div style="float:left;line-height:24px;"><span>'.$item->annotation.'</span></div>
						<div style="clear:both;"></div>
						</div>';
					}

					$file_for_sale 	= $player->file_for_sale;
					$sale_price = $player->sale_price;
					$promotional_text = $player->promotional_text;
					$insertion_button_text = __('Update Media Player', CPMP_LANG);
				}

				// Create the playlist data
				$playlist_json = json_encode($playlist);
				echo "<script>
						var cpmp_playlist_items = {$playlist_json};
					  </script>";
			}

			if ( isset($_POST['cpmp_player_creation_nonce']) && wp_verify_nonce($_POST['cpmp_player_creation_nonce'], __FILE__) )
			{
				$config->type = ($_POST['player_type'] == 'video') ? 'video' : 'audio';
			}

			$width_limit  = '';
			$height_limit = '';
			$skin_list = $this->_get_skin_list($config->skin, $config->type, $width_limit, $height_limit);
			$player_type = (isset($config->type) && $config->type == 'video') ? 'video' : 'song';
	?>
			<div class="wrap">
				<form id="cpmp_media_player_form" method="post" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">
					<input type="hidden" value="<?php esc_attr_e((isset($config->skin)) ? $config->skin : ''); ?>" name="cpmp_skin" />
					<?php
						if(isset($player_id)) echo '<input type="hidden" value="'.esc_attr($player_id).'" name="cpmp_player_id" />';
						if(isset($config->type)) echo '<input type="hidden" value="'.esc_attr($config->type).'" name="cpmp_type" />';

					?>
					<div class="updated" style="padding:5px;">
						<?php _e('For more information go to the <a href="https://cpmediaplayer.dwbooster.com" target="_blank">Audio And Video Player</a> plugin page', CPMP_LANG);
						?>
					</div>
					<div><h1><?php _e('Player shortcode', CPMP_LANG); ?>: [cpm-player id="<?php echo( ( !empty( $player_id ) ) ? $player_id : 'in progress' ); ?>"]</h1></div>
					<!-- General Settings -->
					<div class="postbox">
						<h3 class="hndle" style="padding:5px;"><?php _e('General Settings', CPMP_LANG); ?></h3>
						<div class="inside">
							<!-- Skins -->
							<h3><?php _e('Select the media player skin', CPMP_LANG); ?></h3>
							<div id="skin_container" style="overflow-x:auto;height:127px;width:100%;">
							<?php
								print $skin_list;
							?>
							</div>
							<!-- Settings -->
							<hr style="margin:20px 0;" />
							<h3><?php _e('Configure media player', CPMP_LANG); ?></h3>
							<table class="form-table">
								<tbody>
									<tr valign="top">
										<th scope="row" nowrap>
											<label for="cpmp_player_name"><?php _e('Player name', CPMP_LANG); ?> <span style="color:red">*</span> :</label>
										</th>
										<td style="width:100%">
											<input type="text" id="cpmp_player_name" name="cpmp_player_name" value="<?php echo esc_attr((isset($player->player_name)) ? stripslashes($player->player_name) : "" ); ?>" />
										</td>
									</tr>

									<tr valign="top">
										<th scope="row">
											<label for="cpmp_width"><?php _e('Width:', CPMP_LANG); ?></label>
										</th>
										<td style="width:100%">
											<input type="text" id="cpmp_width" name="cpmp_width" value="<?php echo esc_attr((isset($config->width)) ? $config->width : "" ); ?>" /><div id="cpmp_width_info" style="font-style:italic; color:#666;"><?php _e('Value should be greater than or equal to:', CPMP_LANG);print($width_limit); ?><br /><?php _e('To make the players responsive, essential in mobile devices, <b>enter the player\'s width in percentage</b>, for example: 100%', CPMP_LANG); ?></div>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="cpmp_height"><?php _e('Height:', CPMP_LANG); ?></label>
										</th>
										<td style="width:100%">
											<input type="text" id="cpmp_height" name="cpmp_height" value="<?php echo esc_attr((isset($config->height)) ? $config->height : "" ); ?>" /><div id="cpmp_height_info" style="font-style:italic; color:#666;"><?php _e('Value should be greater than or equal to:', CPMP_LANG);print($height_limit); ?></div>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="cpmp_autoplay"><?php _e('Autoplay:', CPMP_LANG); ?></label>
										</th>
										<td style="width:100%">
											<input type="checkbox" id="cpmp_autoplay" name="cpmp_autoplay" <?php echo ((isset($config->autoplay)) ? "checked" : "" ); ?> /> <span style="font-style:italic; color:#666;"><?php _e("Some devices don't allow autoplay", CPMP_LANG); ?></span>
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="cpmp_loop"><?php _e('Loop:', CPMP_LANG); ?></label>
										</th>
										<td style="width:100%">
											<input type="checkbox" id="cpmp_loop" name="cpmp_loop" <?php echo ((isset($config->loop)) ? "checked" : "" ); ?> />
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="cpmp_preload"><?php _e('Preload:', CPMP_LANG); ?></label>
										</th>
										<td style="width:100%">
											<input type="checkbox" id="cpmp_preload" name="cpmp_preload" <?php echo ((isset($config->preload) && $config->preload != 'none') ? "checked" : "" ); ?> />
										</td>
									</tr>
									<tr valign="top">
										<th scope="row">
											<label for="cpmp_preload"><?php _e('Show playlist:', CPMP_LANG); ?></label>
										</th>
										<td style="width:100%">
											<input type="checkbox" id="cpmp_show_playlist" name="cpmp_show_playlist" <?php echo ((isset($config->playlist)) ? "checked" : "" ); ?> />
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<!-- Playlist -->
					<div class="postbox">
						<h3 class="hndle" style="padding:5px;"><?php _e('Playlist', CPMP_LANG); ?></h3>
						<div class="inside">
							<table class="form-table">
								<tbody>
									<tr>
										<td>
											<div id="item_form" style="border:1px solid #ddd;padding:10px;">
												<h3><?php _e('Enter', CPMP_LANG);print(" {$player_type} ");_e('data', CPMP_LANG); ?></h3>
												<input type="hidden" name="item_id" id="item_id" value="" />
												<table>
													<tbody>
														<tr>
															<th>
																<label for="item_annotation"><?php _e('Title', CPMP_LANG); ?> <span style="color:red;">*</span> :</label>
															</th>
															<td style="width:100%;">
																<input type="text" name="item_annotation" id="item_annotation" />
															</td>
														</tr>
														<tr>
															<th nowrap>
																<label><?php _e($player_type); ?> <span style="color:red;">*</span> :</label>
															</th>
															<td>
																<div>
																	<input type="text" name="item_file[]" class="item_file" id="item_file" value="" >
																	<input type="button" title="<?php esc_attr_e(__('Select the item file', CPMP_LANG)); ?>" onclick="avp_select_file( this );" value="<?php esc_attr_e(__('Select File', CPMP_LANG)); ?>" />
																	<a href="javascript:void(0)" onclick="cpmp.add_field(this, 'item_file')" style="text-decoration:none;">[+] New file format</a>&nbsp;&nbsp;&nbsp;&nbsp;
																</div>
																<br />
																<span style="font-style:italic; color:#666;">
																<?php
																	_e('Select a', CPMP_LANG);
																	print(" {$player_type} ");
																	_e('from Media Library or enter the URL to the', CPMP_LANG);
																	print(" {$player_type} ");
																	_e('file directly on field. If the Media Library is empty, go to the <a href="media-new.php">Media Library</a> and upload the files.', CPMP_LANG);
																?>
																</span>
															</td>
														</tr>
														<tr><td colspan="2"><a href="javascript:void(0);" style="text-decoration:none;" onclick="avp_toggle_additional_attributes(this);"><?php _e('[+] additional attributes', CPMP_LANG); ?></a></td></tr>
														<tr style="display:none;" class="cpmp-additional-attr">
															<th nowrap>
																<label for="item_link"><?php _e('Associated link:', CPMP_LANG); ?></label>
															</th>
															<td>
																<input type="text" name="item_link" id="item_link" />
															</td>
														</tr>
														<?php if($player_type == 'video'){?>
														<tr style="display:none;" class="cpmp-additional-attr">
															<th>
																<label for="item_poster"><?php _e('Poster:', CPMP_LANG); ?></label>
															</th>
															<td>
																<input type="text" name="item_poster" id="item_poster" class="item_poster" />
																<input type="button" title="<?php esc_attr_e(__('Select the item poster', CPMP_LANG)); ?>" onclick="avp_select_file( this );" value="<?php esc_attr_e(__('Select Poster', CPMP_LANG)); ?>" />
																<br />
																<span style="font-style:italic; color:#666;">
																<?php
																	_e('Select a poster image from Media Library or enter the URL to the poster file directly on field. If the Media Library is empty, go to the <a href="media-new.php">Media Library</a> and upload the files.', CPMP_LANG);
																?>
																</span>
															</td>
														</tr>
														<?php } ?>
														<tr style="display:none;" class="cpmp-additional-attr">
															<th>
																<label><?php _e('Subtitles:', CPMP_LANG); ?></label>
															</th>
															<td>
																<div>
																	<input type="text" name="item_subtitle[]" class="item_subtitle" value="" />
																	<?php _e('Lang: ', CPMP_LANG); ?>
																	<input type="text" name="item_subtitle_lang[]" class="item_subtitle_lang" value="" />
																	<input type="button" value="Add new language" onclick="cpmp.add_field(this, 'item_subtitle')">
																</div>
																<br />
																<span style="font-style:italic; color:#666;">
																<?php
																	_e('Set the URL to the subtitle file directly on field. If the language field is omitted, the language is inferred from subtitle location\'s field', CPMP_LANG);
																?>
																</span>
															</td>
														</tr>
														<tr><td colspan="2"><hr /></td></tr>
														<tr>
															<td></td>
															<td>
																<input type="button" name="insert_item" value="<?php esc_attr_e(__('Add / Update item on playlist', CPMP_LANG)); ?>" class="button-primary" onclick="cpmp.add_item();">
																<input type="button" name="clear_item_form" value="<?php esc_attr_e(__('Clear', CPMP_LANG)); ?>" class="button-primary" onclick="cpmp.clear_item_form();cpmp.hide_item_form();">
															</td>
														</tr>
													</tbody>
												</table>
											</div>
										</td>
									</tr>
									<tr valign="top">
										<td>
											<div id="items_container" style="border:1px solid #ddd; width:100%; height:200px; overflow:scroll;">
											<?php
												if(isset($playlist_item_list)) echo $playlist_item_list;
											?>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<!-- For Selling -->
					<p style="border:1px solid #E6DB55;margin-bottom:10px;padding:5px;background-color: #FFFFE0;">
					<?php _e('The PayPal settings are available only in the commercial version of <a href="https://cpmediaplayer.dwbooster.com/download" target="_blank">Audio and Video Player</a>.', CPMP_LANG); ?>
					</p>
					<div class="postbox cpm-disabled">
						<h3 class="hndle" style="padding:5px;"><?php _e('For Sale', CPMP_LANG); ?></h3>
						<div class="inside">
							<table class="form-table for-sale">
								<tbody>
									<tr valign="top">
										<th>
											<label><?php _e('Select the file:', CPMP_LANG); ?> </label>
										</th>
										<td>
											<div>
												<input type="text" disabled />
												<input type="button" class="button" value="<?php esc_attr_e(__('Select file', CPMP_LANG)); ?>" disabled />
											</div>
										</td>
									</tr>
									<tr valign="top">
										<th>
											<label><?php _e('Enter price:', CPMP_LANG); ?> </label>
										</th>
										<td>
											<div>
												<input type="text" disabled /> USD
											</div>
										</td>
									</tr>
									<tr valign="top">
										<th>
											<label><?php _e('Promotional text:', CPMP_LANG); ?> </label>
										</th>
										<td>
											<div>
												<textarea cols="60" rows="2" disabled></textarea>
											</div>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div>
						<input type="button" id="create" name="create" value="<?php echo $insertion_button_text; ?>" class="button-primary" onclick="cpmp.submit_item_form();" />
						<a type="button" class="button-primary" href="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>">Cancel</a>
					</div>
					<?php
						// create a custom nonce for submit verification later
						echo '<input type="hidden" name="cpmp_player_create_update_nonce" value="' . wp_create_nonce(__FILE__) . '" />';
					?>
				</form>
            </div>
	<?php
		}
	} // End admin_page

	// Button in the post edition for media player insertion
	function insert_player_button(){
		print '
			<a href="javascript:cpmp.new_player_window(\'audio\');" title="'.esc_attr(__('New Audio Player', CPMP_LANG)).'"><img src="'.esc_url(CPMP_PLUGIN_URL.'/images/cpmp_audio.png').'" alt="'.esc_attr(__('New Audio Player', CPMP_LANG)).'" /></a>
			<a href="javascript:cpmp.new_player_window(\'video\');" title="'.esc_attr(__('New Video Player', CPMP_LANG)).'"><img src="'.esc_url(CPMP_PLUGIN_URL.'/images/cpmp_video.png').'" alt="'.esc_attr(__('New Video Player', CPMP_LANG)).'" /></a>
			<a href="javascript:cpmp.open_insertion_window();" title="'.esc_attr(__('Insert Player From Gallery', CPMP_LANG)).'"><img src="'.esc_url(CPMP_PLUGIN_URL.'/images/cpmp_gallery.png').'" alt="'.esc_attr(__('Insert Player From Gallery', CPMP_LANG)).'" /></a>
		';
	}// End insert_player_button

	// Load the player button scripts and initialize the insertion dialog
	function set_load_media_player_window(){
		global $wpdb;

		wp_enqueue_style('wp-jquery-ui-dialog');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-dialog');
		wp_enqueue_script(
			'cpmp-admin',
			CPMP_PLUGIN_URL.'/js/cpmp_admin.js',
			array('jquery', 'jquery-ui-core', 'jquery-ui-dialog'),
            null,
            true
		);

		// Load players
		$sql = "SELECT id, player_name FROM ".$wpdb->prefix.CPMP_PLAYER.";";
		$players = $wpdb->get_results($sql);

		$options = '';
		$label   = '';
		if(count($players)){
			foreach ($players as $player){
				$options .= '<option value="'.esc_attr($player->id).'">'.stripslashes($player->player_name).'</option>';
			}
			$tag = '<select id="cpmp_media_player">'.$options.'</select>';
			$label = __('Select the player to insert:', CPMP_LANG);
		}else{
			$tag = __('You must to define a media player before use it on page/post.', CPMP_LANG);
		}

		// Skins
		$skins = '<select id="cpmp_skins">';
		$skin_dir = CPMP_PLUGIN_DIR.'/skins';
		if(file_exists($skin_dir))
		{
			$d = dir($skin_dir);
			while (false !== ($entry = $d->read()))
			{
				if($entry != '.' && $entry != '..' && is_dir($skin_dir.'/'.$entry))
				{
					$this_skin = $skin_dir.'/'.$entry.'/';
					if(file_exists($this_skin))
					{
						$skin_data = parse_ini_file($this_skin.'config.ini', true);
						$skins .= '<option value="'.$skin_data['id'].'">'.esc_html($skin_data['name']).'</option>';
					}
				}
			}
			$d->close();
		}
		$skins .= '</select>';

		wp_localize_script('cpmp-admin', 'cpmp_insert_media_player', array(
			'title' => __('Insert media player on your post/page', CPMP_LANG),
			'label' => $label,
			'new_label' => __('Create or Edit a Player', CPMP_LANG),
			'tag'  	=> $tag,
			'skins' => $skins
		));
	}// End set_load_media_player_window

	/**
	 * To generate the players previews.
	 */
	public function preview()
	{
		$user = wp_get_current_user();
		$allowed_roles = array('editor', 'administrator', 'author');

		if(array_intersect($allowed_roles, $user->roles ))
		{
			if(!empty($_REQUEST['cpmp-avp-preview']))
			{
				// Sanitizing variable
				$preview = sanitize_text_field(stripcslashes($_REQUEST['cpmp-avp-preview']));

				// Remove every shortcode that is not in the plugin
				remove_all_shortcodes();
				add_shortcode('codepeople-html5-media-player', array(&$this, 'replace_shortcode'));
				add_shortcode('cpm-player', array(&$this, 'replace_shortcode'));
				add_shortcode('codepeople-html5-playlist-item', array(&$this, 'replace_shortcode_playlist_item'));
				add_shortcode('cpm-item', array(&$this, 'replace_shortcode_playlist_item'));

				if(
					has_shortcode($preview, 'codepeople-html5-media-player') ||
					has_shortcode($preview, 'cpm-player') ||
					has_shortcode($preview, 'codepeople-html5-playlist-item') ||
					has_shortcode($preview, 'cpm-item')
				)
				{
					// Deregister all scripts and styles for loading only the plugin styles.
					global  $wp_styles, $wp_scripts;
					if(!empty($wp_scripts)) $wp_scripts->reset();
					wp_enqueue_script('jquery');

					$if_empty = __('Select at least a media file or player (if appropriate)', CPMP_LANG);

					$output = do_shortcode($preview);

					if(preg_match('/^\s*$/', $output))
					{
						$output = '<div>'.$if_empty.'</div>';
					}

					if(!empty($wp_styles))  $wp_styles->do_items();
					if(!empty($wp_scripts)) $wp_scripts->do_items();

					print '<div class="cpmp-preview-container">'.$output.'</div>';
					print'<script type="text/javascript">jQuery(window).on("load", function(){ var frameEl = window.frameElement; if(frameEl) frameEl.height = jQuery(".cpmp-preview-container").outerHeight(true)+25; });</script>';
					exit;
				}
			}
		}
	} // End preview

	function replace_shortcode_playlist_item($atts, $content = '')
	{
		$atts = shortcode_atts(
			array(
				'file'   	=> '',
				'name' 		=> '',
				'poster' 	=> '',
				'link'	 	=> '',
				'subtitle' 	=> '',
				'lang'		=> ''
			),
			$atts
		);

		extract($atts);
		if(!empty($file))
		{
			if(!empty($content)) $name = $content;
			$obj = new stdClass;
			$obj->files = array($file);
			$obj->poster = $poster;
			$obj->annotation = $name;
			$obj->link = $link;

			$obj->subtitles = array();
			if(!empty($subtitle))
			{
				$subtitle_obj = new stdClass;
				$subtitle_obj->link = $subtitle;
				$subtitle_obj->language = $lang;
				$obj->subtitles[] = $subtitle_obj;
			}

			$this->current_player_playlist[] = $obj;
		}
		return '';
	}

	function replace_shortcode($atts, $content="")
	{
		global $wpdb;

		$supported_ext = array(
			'audio' => array('mp3', 'oga', 'ogg'),
			'video' => array('wmv', 'flv', 'ogg', 'ogv', 'webm', 'm4v', 'mp4', 'youtube')
		);

		extract($atts);

		$this->current_player_playlist = array();
		$content = do_shortcode($content);

		// Variables
		$player_id = 'codepeople_media_player'.time().mt_rand(1,999999);
		$mp_atts = array(); // Media Player attributes
		$pl_items = array(); // Playlist items
		$srcs = array(); // Sources of first item
		$mp_subtitles = array(); // Subtitles list of first item
		$styles = '';
		$paypal_button = '';

		if(isset($id))
		{
			$sql = $wpdb->prepare('SELECT * FROM '.$wpdb->prefix.CPMP_PLAYER.' WHERE id=%d',$id);
			$player = $wpdb->get_row($sql);

			if($player != null)
			{
				$config_obj = (isset($player->config)) ? unserialize($player->config) : new stdClass;
				// Set attributes
				if(empty($config_obj->type)) $config_obj->type = 'audio';
				if(!isset($type)) $type = trim($config_obj->type);
				if(!isset($width) && isset($config_obj->width)) $width = trim($config_obj->width);
				if(!isset($height) && isset($config_obj->height)) $height = trim($config_obj->height);
				if(!isset($skin) && isset($config_obj->skin)) $skin = trim($config_obj->skin);
				if(!isset($loop) && isset($config_obj->loop)) $loop = trim($config_obj->loop);
				if(!isset($autoplay) && isset($config_obj->autoplay)) $autoplay = trim($config_obj->autoplay);
				if(!isset($preload) && isset($config_obj->preload)) $preload = trim($config_obj->preload);
				if(!isset($playlist) && isset($config_obj->playlist)) $playlist = trim($config_obj->playlist);

				if(empty($this->current_player_playlist))
					$this->current_player_playlist = (isset($player->playlist)) ? unserialize($player->playlist) : array();
			}
		}
		if(empty($type)) $type = 'audio';
		if(!empty($this->current_player_playlist))
		{
			$first_item = true;
			foreach($this->current_player_playlist as $item)
			{
				$item_srcs = array();
				$item_subtitles = array();

				foreach($item->files as $file)
				{
					$file = $this->transform_url($file);
					$ext = $this->get_extension($file);

					$item_src_obj = new stdClass;
					$item_src_obj->src = $file;
					$item_src_obj->type = $type.'/'.$ext;
					$item_srcs[] = $item_src_obj;

					if($first_item)
					{
						if(!empty($item->poster)) $mp_atts[] = 'poster="'.esc_url($item->poster).'"';
						$srcs[] = '<source src="'.esc_url($file).'" type="'.esc_attr($item_src_obj->type).'" />';
					}
				}

				foreach( $item->subtitles as $subtitle)
				{
					$location = $this->transform_url($subtitle->link);
					$language = $subtitle->language;
					if($first_item)
					{
						$mp_subtitles[] = '<track src="'.esc_url($location).'" kind="subtitles" srclang="'.esc_attr($language).'"></track>';
					}

					$item_subtitle_obj = new stdClass;
					$item_subtitle_obj->kind = 'subtitles';
					$item_subtitle_obj->src = $location;
					$item_subtitle_obj->srclang = $language;
					$item_subtitles[] = $item_subtitle_obj;
				}

				$pl_item_obj = new stdClass;
				if(!empty($item->poster)) $pl_item_obj->poster = $this->transform_url($item->poster);
				$pl_item_obj->source = $item_srcs;
				$pl_item_obj->track = $item_subtitles;

				$pl_items[] = '<li value="'.esc_attr( json_encode( $pl_item_obj ) ).'">'.(!empty($item->link) ? '<a class="cpmp-info" href="'.esc_url($item->link).'">+</a>' : '&nbsp;&nbsp;').'&nbsp;&nbsp;'.(!empty($item->annotation) ? $item->annotation : '').'</li>';

				$first_item = false;
			}
		}
		else
		{
			return '';
		}

		if(empty($skin)) $skin = 'classic-skin';
		$skin = trim($skin);

		$base_path = dirname(__FILE__).'/';
		$base_url  = plugin_dir_url(__FILE__);

		wp_enqueue_style('wp-mediaelement');
		wp_enqueue_style('codepeople_media_player', $base_url.'css/cpmp.css');

		$css_path = $base_path.'skins/'.$skin.'/'.$skin.'.css';
		$css_url  = $base_url.'skins/'.$skin.'/'.$skin.'.css';
		$js_path  = $base_path.'skins/'.$skin.'/'.$skin.'.js';
		$js_url   = $base_url.'skins/'.$skin.'/'.$skin.'.js';

		if(file_exists($css_path))
			wp_enqueue_style('codepeople_media_player_style_'.$skin,$css_url);

		if(file_exists($js_path))
			wp_enqueue_script('codepeople_media_player_script_'.$skin, $js_url, array('jquery'), null, true);

		wp_enqueue_script('wp-mediaelement');
		wp_enqueue_script('codepeople_media_player_script', $base_url.'js/codepeople-plugins.js', array('jquery', 'wp-mediaelement'), null, true);

		if(!empty($width))
		{
			if( is_numeric( $width ) ) $width .= 'px';
			$width = esc_attr($width);
			$styles .= 'style="width:'.$width.'"';
			$mp_atts[] = 'width="'.$width.'"';
		}

		if(!empty($height)) $mp_atts[] = 'height="'.esc_attr($config_obj->height).'"';

		$mp_atts[] = 'class="codepeople-media '.(!empty($skin) ? esc_attr($skin) : '' ).'"';

		if(!empty($loop) && $loop != 'false') $mp_atts[] = 'loop="loop"';
		if(!empty($autoplay) && $autoplay != 'false') $mp_atts[] = 'autoplay="autoplay"';
		if(isset($preload)) $mp_atts[] = 'preload="'.esc_attr($preload).'"';
		else $mp_atts[] = 'preload="none"';

		return '<div id="ms_avp" '.$styles.'><'.$type.' id="'.esc_attr($player_id).'" '.implode(' ', $mp_atts).' '.$styles.'>'.implode('',$srcs).implode('', $mp_subtitles).'</'.$type.'>'.((count($pl_items) > 0) ? '<ul id="'.esc_attr($player_id).'-list" style="display: '.((!empty($playlist) && $playlist != 'false') ? 'block' : 'none').' !important;">'.implode(' ', $pl_items).'</ul>' : '').'<noscript>audio-and-video-player require JavaScript</noscript><div style="clear:both;"></div></div>';

	} // End replace_shortcode

    public static function troubleshoot($option)
	{
		if(!is_admin())
		{
			// Solves a conflict caused by the "Speed Booster Pack" plugin
			if(is_array($option) && isset($option['jquery_to_footer'])) unset($option['jquery_to_footer']);
		}
		return $option;
	}
}
