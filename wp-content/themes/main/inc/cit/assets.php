<?php
function assets()
{

    $name = 'main';
    $vars = array(
        'admin_url' => admin_url('admin-ajax.php'),
        'theme_dir' => get_template_directory_uri() . '/',
    );

    // css
    wp_enqueue_style('style', get_stylesheet_uri());
    wp_enqueue_style('custom', get_stylesheet_directory_uri() . '/css/custom.css');

    // js
    wp_enqueue_script('mticket_widget', 'https://widget.kontramarka.ua/js/popup.js');
    wp_enqueue_script('iframeResizer', 'https://cdnjs.cloudflare.com/ajax/libs/iframe-resizer/4.1.1/iframeResizer.min.js');
    wp_register_script('deps', get_stylesheet_directory_uri() . '/js/deps.js', array(), '', true);
    wp_enqueue_script($name, get_stylesheet_directory_uri() . '/js/' . $name . '.js', array(
        'deps',
    ), '1', true);

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

