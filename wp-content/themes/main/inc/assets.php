<?php
function assets()
{
    $name = 'main';
    $vars = [
        'admin_url' => admin_url('admin-ajax.php'),
        'theme_dir' => get_template_directory_uri() . '/',
    ];

    // css
    wp_enqueue_style('style', get_stylesheet_uri());
    wp_enqueue_style('custom', get_stylesheet_directory_uri() . '/css/custom.css');
    wp_enqueue_style( 'slick', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css', array(), '1.9.0');
    wp_enqueue_style( 'slick-theme', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css', array(), '1.9.0');
    wp_enqueue_style( 'base', get_template_directory_uri() . '/css/base.css', array(), '1.0');
    wp_enqueue_style( 'vendor', get_template_directory_uri() . '/css/vendor.css', array(), '1.0');
    wp_enqueue_style( 'fonts', get_template_directory_uri() . '/css/fonts.css', array(), '1.0');
    wp_enqueue_style( 'stylesheet', get_template_directory_uri() . '/fonts/avenirn/stylesheet.css', array(), '1.0');
    wp_enqueue_style( 'main_css', get_template_directory_uri() . '/css/main.css', array(), '1.0');

    // js
    wp_enqueue_script('jquery_3', 'https://code.jquery.com/jquery-3.4.1.min.js', [], wp_get_theme()->Version, true);
    wp_enqueue_script('mticket_widget', 'https://widget.kontramarka.ua/js/popup.js');
    wp_enqueue_script('iframeResizer', 'https://cdnjs.cloudflare.com/ajax/libs/iframe-resizer/4.1.1/iframeResizer.min.js');
    wp_enqueue_script('modernizr', get_template_directory_uri() . '/js/modernizr.js', [], wp_get_theme()->Version, true);
    wp_enqueue_script('pace', get_template_directory_uri() . '/js/pace.min.js', [], wp_get_theme()->Version, true);
    wp_enqueue_script('plugins', get_template_directory_uri() . '/js/plugins.js', [], wp_get_theme()->Version, true);
    wp_enqueue_script('tweenmax', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.0/TweenMax.min.js', [], wp_get_theme()->Version, true);
    wp_enqueue_script('timelinemax', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.0/TimelineMax.min.js', [], wp_get_theme()->Version, true);
    wp_enqueue_script('slick-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js', [], wp_get_theme()->Version, true);
    wp_enqueue_script('main_js', get_template_directory_uri() . '/js/main.js', [], wp_get_theme()->Version, true);
    wp_enqueue_script($name, get_stylesheet_directory_uri() . '/js/' . $name . '.js', [
        'deps',
    ], wp_get_theme()->Version, true);

    if (!empty($vars)) {
        foreach ($vars as $var => $data) {
            wp_localize_script('jquery', $var, $data);
        }
    }
}

add_action('wp_enqueue_scripts', 'assets');


function theme_dir($path)
{
    if (strpos($path, '/') !== 0) {
        $path = '/' . $path;
    }
    return get_template_directory_uri() . $path;
}
?>
