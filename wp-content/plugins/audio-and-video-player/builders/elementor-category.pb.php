<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// Register the categories
Plugin::$instance->elements_manager->add_category(
	'audio-and-video-player-cat',
	array(
		'title'=>__('Audio and Video Player', CPMP_LANG),
		'icon' => 'fa fa-plug'
	),
	2 // position
);
