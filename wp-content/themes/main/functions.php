<?php // ==== FUNCTIONS ==== //

// styles, scripts
// require_once(trailingslashit(get_stylesheet_directory()) . 'inc/assets.php');

function kmtemp_styles_scripts() {
  // Connect Styles
  wp_enqueue_style( 'slick', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css', array(), '1.9.0');
  wp_enqueue_style( 'slick-theme', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.min.css', array(), '1.9.0');
  wp_enqueue_style( 'base', get_template_directory_uri() . '/css/base.css', array(), '1.0');
  wp_enqueue_style( 'vendor', get_template_directory_uri() . '/css/vendor.css', array(), '1.0');
  wp_enqueue_style( 'vendor', get_template_directory_uri() . '/css/fonts.css', array(), '1.0');
  wp_enqueue_style( 'main_css', get_template_directory_uri() . '/css/main.css', array(), '1.0');

  // Register Scripts
  wp_register_script('jquery_3', 'https://code.jquery.com/jquery-3.4.1.min.js', array(), '3.4.1', true);
  wp_register_script('modernizr', get_template_directory_uri() . '/js/modernizr.js', array(), '1.0', true);
  wp_register_script('pace', get_template_directory_uri() . '/js/pace.min.js', array(), '1.0', true);
  wp_register_script('plugins', get_template_directory_uri() . '/js/plugins.js', array(), '1.0', true);
  wp_register_script('tweenmax', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.0/TweenMax.min.js', array(), '1.19.0', true);
  wp_register_script('timelinemax', 'https://cdnjs.cloudflare.com/ajax/libs/gsap/1.19.0/TimelineMax.min.js', array(), '1.19.0', true);
  wp_register_script('slick-carousel', 'https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js', array(), '1.9.0', true);
  wp_register_script('main_js', get_template_directory_uri() . '/js/main.js', array(), '1.0', true);

  // Connect Scripts
  wp_enqueue_script('jquery_3');
  wp_enqueue_script('modernizr');
  wp_enqueue_script('pace');
  wp_enqueue_script('plugins');
  wp_enqueue_script('tweenmax');
  wp_enqueue_script('timelinemax');
  wp_enqueue_script('slick-carousel');
  wp_enqueue_script('main_js');
}

add_action( 'wp_enqueue_scripts', 'kmtemp_styles_scripts');

// custom post type
require_once(trailingslashit(get_stylesheet_directory()) . 'inc/cpt.php');

// CRON COMMANDS API Maestro & API Kontramarka
require_once(trailingslashit(get_stylesheet_directory()) . 'inc/api_cron.php');

date_default_timezone_set('Europe/Kiev');

// Only the bare minimum to get the theme up and running
function main_setup()
{

    // Domain: "main"
    load_theme_textdomain('main', get_template_directory() . '/languages');

    add_theme_support('title-tag');

    // Enable support for Post Thumbnails on posts and pages.
    // add_theme_support('post-thumbnails');
    // add_image_size('slider-1', 1920, 700, true);
    // add_image_size('254x360', 254, 360, true);

    register_nav_menus(array(
        'primary' => 'Main Menu',
        'mobile' => 'Mobile Menu',
        'footer_one' => 'Footer one',
        'footer_two' => 'Footer two',
        'footer_three' => 'Footer three',
    ));

    function mainMenu()
    {
        $args = array(
            'menu' => 'Main menu',
            'menu_class' => 'd-flex main-nav',
            'theme_location' => 'primary'
        );
        wp_nav_menu($args);
    }

    // function mobileMenu()
    // {
    //     $options = array(
    //         'theme_location' => 'mobile',
    //         'container' => false,
    //         'echo' => false,
    //         'fallback_cb' => 'fall_back_menu'
    //     );
    //
    //     $menu = wp_nav_menu($options);
    //
    //     echo preg_replace(array(
    //         '#^<ul[^>]*>#',
    //         '#</ul>$#'
    //     ), '', $menu);
    // }

    function footerOne()
    {
        $args = array(
            'theme_location' => 'footer_one',
            'menu_class' => 'footer-menu',
            'container' => false,
        );
        wp_nav_menu($args);
    }

    function footerTwo()
    {
        $args = array(
            'theme_location' => 'footer_two',
            'menu_class' => 'footer-menu',
            'container' => false,
        );
        wp_nav_menu($args);
    }

    function footerThree()
    {
        $args = array(
            'theme_location' => 'footer_three',
            'menu_class' => 'footer-menu',
            'container' => false,
        );
        wp_nav_menu($args);
    }

    // HTML5 support; mainly here to get rid of some nasty default styling that WordPress used to inject
    // add_theme_support('html5', array('search-form', 'gallery'));
}

// add_action('after_setup_theme', 'main_setup', 11);
//
// function icon($name, $class = '', $return = false)
// {
//     $string = '<svg class="svgicon ' . $class . '"><use xlink:href="' . get_template_directory_uri() . '/img/sprite.svg#' . $name . '"></use></svg>';
//
//     if ($return) {
//         return $string;
//     } else {
//         echo $string;
//     }
// }


// ACF Option page settings
if (function_exists('acf_add_options_page')) {
    acf_add_options_page(array(
        'page_title' => 'Настройки сайта',
        'menu_title' => 'Опции',
        'menu_slug' => 'theme-general-settings'
    ));
}

// rename posts category
function edit_admin_menus()
{
    global $menu;
    $menu[5][0] = 'Новости';
}

add_action('admin_menu', 'edit_admin_menus');


function get_no_image()
{
    return get_template_directory_uri() . '/img/noimage.jpg';
}

// add post formats
add_theme_support('post-formats', array('video', 'image'));

// WP Dashboard custom css
add_action('admin_head', 'custom_css');

// function custom_css()
// {
//     echo '<style>
//   .qtranxs-lang-switch-wrap{position:fixed;top:0;left:60%;z-index:99999;margin:0;padding:0}.qtranxs-lang-switch-wrap li{box-sizing:border-box;height:32px;padding:0 10px;line-height:30px;cursor:pointer;color:#fff;text-transform:uppercase;display:inline-block;border:0;margin:0;background:#000;font-size:12px}.qtranxs-lang-switch-wrap li span{margin-left:4px;vertical-align:middle}.qtranxs-lang-switch-wrap li img{vertical-align:middle}.qtranxs-lang-switch-wrap li.active{color:#fff;background:green}.menu-icon-comments{display:none}
//   </style>';
// }

// custom excerpt length
function custom_excerpt_length($length)
{
    return 40;
}

add_filter('excerpt_length', 'custom_excerpt_length', 999);

function new_excerpt_more($more)
{
    return '...';
}

add_filter('excerpt_more', 'new_excerpt_more');

function post_excerpt($max_words = 40)
{
    $phrase_array = explode(' ', get_the_excerpt());
    if (count($phrase_array) > $max_words && $max_words > 0) {
        $result = implode(' ', array_slice($phrase_array, 0, $max_words)) . '...';
    }
    return $result;
}

/*--------------------------------------------------------------
# SPLIT STRING
--------------------------------------------------------------*/
function break_string($string)
{
    $length2 = strlen($string) * 0.4;
    $tmp = explode(' ', $string);
    $index = 0;
    $result = Array('', '');
    foreach ($tmp as $word) {
        if (!$index && strlen($result[0]) > $length2) {
            $index++;
        }
        $result[$index] .= $word . ' ';
    }
    if ($result[1] != '') {
        return $result[0] . '<br/>' . $result[1];
    }
    return $result[0];
}

$site_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

// exclude pages from search
add_filter('register_post_type_args', function ($args, $post_type) {
    if (!is_admin() && $post_type == 'page') {
        $args['exclude_from_search'] = true;
    }
    return $args;
}, 10, 2);

// disable srcset
add_filter('wp_calculate_image_srcset', '__return_false');

// reduce revisions to 5
function my_revisions_to_keep()
{
    return 5;
}

add_filter('wp_revisions_to_keep', 'my_revisions_to_keep');

function time_left($date)
{
    return date_diff(new DateTime(date('Y-m-d H:i:s')), new DateTime($date));
}

function console_log($data)
{
    echo '<script>';
    echo 'console.log(' . json_encode($data) . ')';
    echo '</script>';
}


//show filter
add_action('wp_ajax_filter', 'filter_ajax');
add_action('wp_ajax_nopriv_filter', 'filter_ajax');
function filter_ajax()
{
    global $wpdb;
    $limit = 10;

    $date = $_GET['date'] ?: false;
    $showType = $_GET['showType'] ?: false;
    $site = $_GET['site'] ?: false;
    $city = $_GET['city'] ?: false;
    $page = $_GET['page'] ? (int)$_GET['page'] - 1 : 0;

    $dateArray = ['start' => date('Ymd'), 'end' => date('Ymd', strtotime('+ 5 years'))];
    $where = '';

    if ($date) {
        if ($date === 'today') {
            $dateArray = ['start' => date('Ymd'), 'end' => date('Ymd')];
        } elseif ($date === 'weekend') {
            if (date('w') == 6) { // Если сегодня суббота
                $dateArray = ['start' => date('Ymd'), 'end' => date('Ymd', strtotime('+1 days'))];
            } elseif (date('w') == 0) { // Если сегодня воскресенье
                $dateArray = ['start' => date('Ymd', strtotime('-1 days')), 'end' => date('Ymd')];
            } else {
                $dateArray = [
                    'start' => date('Ymd', strtotime('next Saturday')),
                    'end' => date('Ymd', strtotime('next Sunday')),
                ];
            }
        } else {
            $dateArray = ['start' => date('Ymd', strtotime($date)), 'end' => date('Ymd', strtotime($date))];
        }
    }

    $where .= " mt.meta_key = 'event-date' AND mt.meta_value >= '{$dateArray['start']}' AND mt.meta_value <= '{$dateArray['end']}' ";

    if ($city) {
        $where .= " AND city.term_id ='{$city}' ";
    } else {
        $cityDefault = get_term_by('slug', 'kiev', 'city');
        $where .= " AND city.term_id ='{$cityDefault->term_id}' ";
    }

    if ($showType) {
        $where .= " AND term.object_id IN (SELECT object_id FROM `wp_term_relationships` WHERE `term_taxonomy_id` = ('{$showType}') ) ";
    }

    if ($site) {
        $where .= " AND term.object_id IN (SELECT object_id FROM `wp_term_relationships` WHERE `term_taxonomy_id` = ('{$site}') ) ";
    }

    $sql = "
        SELECT
            wp_posts.*,
            mt.meta_value as 'event-date'
        FROM wp_posts
        INNER JOIN wp_postmeta AS mt ON wp_posts.ID = mt.post_id
        INNER JOIN wp_term_relationships AS term ON wp_posts.ID = term.object_id
        INNER JOIN wp_term_taxonomy AS tax ON term.term_taxonomy_id = tax.term_taxonomy_id
        INNER JOIN wp_term_taxonomy AS city ON term.term_taxonomy_id = city.term_taxonomy_id
        WHERE
        " . $where . "
        AND wp_posts.post_type = 'events'
        AND
            (wp_posts.post_status = 'publish'
            OR
            wp_posts.post_status = 'future')
        GROUP BY wp_posts.ID
        ORDER BY mt.meta_value ASC "
        . " LIMIT " . $limit . " OFFSET " . $limit * $page . ";";

    $showItems = (array)$wpdb->get_results($sql);

    if (count($showItems) > 0) {
        foreach ($showItems as $showItem) {
            include "inc/event-loop-ajax.php";
        }
    }

    wp_reset_postdata();
    wp_die();
}

// widget url
function getEventUrl($fieldString)
{
//    $string = get_field('show_dates', $showId)[0]['show_dates__buy'];
    preg_match_all('!\d+!', $fieldString, $matches);
    $matches = $matches[0];

    return '/buy-ticket/?widgetId=' . $matches[0] . '&siteId=' . $matches[1] . '&eventId=' . $matches[2];
}

function get_custom_date($dateField)
{
    $date = date_i18n('d F', strtotime($dateField));
    $date = explode(' ', $date);
    return '<b class="afisha-item__big">' . $date[0] . '</b>' . ' ' . mb_strtolower($date[1]);
}

function get_future_dates_by_show_id($id)
{
    // достать события
    $dates = get_field('show_dates', $id);
    // отфильтровать только будущие события
    return array_values(array_filter($dates, function ($date) {
        return strtotime($date['show_dates__date'] . ' ' . $date['show_dates__time']) > strtotime('now');
    }));
}

function get_nearest_date_by_show_id($id)
{
    $future_dates = get_future_dates_by_show_id($id);
    return $future_dates[0]['show_dates__date'] . ' ' . $future_dates[0]['show_dates__time'];
}