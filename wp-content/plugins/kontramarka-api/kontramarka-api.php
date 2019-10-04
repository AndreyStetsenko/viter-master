<?php
/*
 Plugin Name: Kontramarka Api
 Description: Kontramarka Api synchronize
 Version: 1.0
 Author: Liubov Sopilko
 */

if (!defined('ABSPATH')) {
    exit;
}

const KONTRAMARKA_URL = 'https://kontramarka.ua/';
const KONTRAMARKA_API_GATE = KONTRAMARKA_URL . 'ru/api/';
const KONTRAMARKA_API_KEY = '97d226549ca043a3a574d6f5203753bb';

const KONTRAMARKA_METHOD_SITES = 'sites';
const KONTRAMARKA_METHOD_CITIES = 'cities';
const KONTRAMARKA_METHOD_GENRES = 'genres';
const KONTRAMARKA_METHOD_SHOWS = 'shows';
const KONTRAMARKA_METHOD_SHOW_TYPES = 'showTypes';
const KONTRAMARKA_METHOD_ADDITIONAL_CATEGORIES = 'additionalCategories';
const KONTRAMARKA_METHOD_SLIDERS = 'sliders';

const KONTRAMARKA_PARAMS_GENRE = 'genre';
const KONTRAMARKA_PARAMS_CITY = 'city';
const KONTRAMARKA_PARAMS_SITE = 'site';
const KONTRAMARKA_PARAMS_SHOW_TYPE = 'showType';
const KONTRAMARKA_PARAMS_ADDITIONAL_CATEGORY = 'additionalCategory';

const KONTRAMARKA_SHOW_POST_TYPE = 'events';
const KONTRAMARKA_SYNC_SLIDER = false;

if (is_admin()) {
    add_action('admin_menu', 'add_kontramarka_menu');
}

function add_kontramarka_menu()
{
    add_menu_page('Kontramarka Api', 'Kontramarka API', 'administrator', __FILE__, 'kontramarka_sync_page', 'dashicons-rest-api');
    add_submenu_page(__FILE__, 'Настройки', 'Настройки', 'administrator', __FILE__ . '?action=settings', 'kontramarka_settings_page');
    add_action('admin_init', 'register_api_settings');
}

function register_api_settings()
{
    register_setting('kontramarka-api', 'kontramarka_api_gate', ['default' => KONTRAMARKA_API_GATE]);
    register_setting('kontramarka-api', 'kontramarka_api_key', ['default' => KONTRAMARKA_API_KEY]);
    register_setting('kontramarka-api', 'kontramarka_sync_slider', ['default' => KONTRAMARKA_SYNC_SLIDER]);

}

function kontramarka_sync_page()
{
    $action = empty($_POST['action']) ? (empty($_GET['action']) ? null : $_GET['action']) : $_POST['action'];
    $slider_sync = !empty($_POST['kontramarka_sync_slider']) ? true : KONTRAMARKA_SYNC_SLIDER;

    if ($action == 'sync-kontramarka'):
        echo ' <h2>Kontramarka Api</h2>';
        kontramarka_sync($slider_sync);
        echo '<br/>Синхронизация прошла успешно<br/>';
        ?>
        <form method="post">
            <?php wp_nonce_field('kontramarka-api-options'); ?>
            <input type="hidden" name="action" value="sync-kontramarka">
            <div style="padding-top: 30px">
                <input type="checkbox" id="kontramarka_sync_slider" name="kontramarka_sync_slider" value="true">
                <label for="kontramarka_sync_slider">Включить синхронизацию слайдеров</label>
            </div>

            <p class="submit">
                <input type="submit" class="button-primary" value="Начать повторную синхронизацию"/>
            </p>
        </form>
    <?php
    else: ?>
        <div class="wrap">
            <h2>Kontramarka Api</h2>
            <form method="post">
                <?php wp_nonce_field('kontramarka-api-options'); ?>
                <input type="hidden" name="action" value="sync-kontramarka">

                <div style="padding-top: 30px">
                    <input type="checkbox" id="kontramarka_sync_slider" name="kontramarka_sync_slider" value="true">
                    <label for="kontramarka_sync_slider">Включить синхронизацию слайдеров</label>
                </div>

                <p class="submit">
                    <input type="submit" class="button-primary" value="Начать синхронизацию"/>
                </p>
            </form>
        </div>
    <?php endif;
}

function kontramarka_settings_page()
{
    ?>
    <div class="wrap">
        <h2>Kontramarka Api</h2>
        <form method="post" action="options.php">
            <?php settings_fields('kontramarka-api'); ?>
            <table class="form-table">

                <tr valign="top">
                    <th scope="row">Kontramarka Api Gate</th>
                    <td><input type="text" name="kontramarka_api_gate"
                               value="<?php echo get_option('kontramarka_api_gate'); ?>"/>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Kontramarka Api Key</th>
                    <td><input type="text" name="kontramarka_api_key"
                               value="<?php echo get_option('kontramarka_api_key'); ?>"/>
                    </td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"/>
            </p>

        </form>
    </div>
    <?php
}

function kontramarka_request($url = '', $data = null, $raw = false)
{
    if (empty($url)) {
        return [];
    }

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_URL => $url,
    ]);

    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($curl, CURLOPT_POST, 1);
    }

    $resp = curl_exec($curl);
    curl_close($curl);

    if ($raw) {
        return $resp;
    }

    $resp = json_decode($resp, true);

    if (empty($resp)) {
        return [];
    }

    return $resp;
}

function downloadImage($url, $fileName)
{
    $ch = curl_init($url);
    $fp = fopen($fileName, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    fclose($fp);

    return $code;
}

function kontramarka_sync($slider_sync = false)
{
    global $wpdb;
    set_time_limit(0);
    $show_posts = [];

    foreach ($wpdb->get_results("SELECT post_id, meta_value FROM $wpdb->postmeta INNER JOIN $wpdb->posts ON ID = post_id AND post_type = '" . KONTRAMARKA_SHOW_POST_TYPE . "' WHERE meta_key='show_id' AND meta_value!=''") as $e) {
        if (!empty($show_posts[$e->post_id])) {
            wp_delete_post($e->post_id, true);
            continue;
        }

        $show_posts[$e->meta_value] = $e->post_id;
    }

    echo 'На сайте ' . count($show_posts) . ' шоу<br/>';
    flush();

    $api_gate = get_option('kontramarka_api_gate') ? get_option('kontramarka_api_gate') : KONTRAMARKA_API_GATE;
    $api_key = get_option('kontramarka_api_key') ? get_option('kontramarka_api_key') : KONTRAMARKA_API_KEY;

    $sites = kontramarka_request($api_gate . KONTRAMARKA_METHOD_SITES, ['token' => $api_key,]);
    $cites = kontramarka_request($api_gate . KONTRAMARKA_METHOD_CITIES, ['token' => $api_key]);
    $genres = kontramarka_request($api_gate . KONTRAMARKA_METHOD_GENRES, ['token' => $api_key]);
    $shows = kontramarka_request($api_gate . KONTRAMARKA_METHOD_SHOWS, ['token' => $api_key]);
    $showTypes = kontramarka_request($api_gate . KONTRAMARKA_METHOD_SHOW_TYPES, ['token' => $api_key]);
    $additionalCategories = kontramarka_request($api_gate . KONTRAMARKA_METHOD_ADDITIONAL_CATEGORIES, ['token' => $api_key]);

    if ($slider_sync) {
        $sliders = kontramarka_request($api_gate . KONTRAMARKA_METHOD_SLIDERS, ['token' => $api_key]);
    }

    if (empty($sites) || empty($cites) || empty($genres) || empty($shows) || empty($showTypes)) {
        echo 'При получении данных с API произошла ошибка ...<br/>';

        return;
    }

    echo 'Получено по API шоу: ' . count($shows) . '<br/>';
    echo 'Получено по API городов: ' . count($cites) . '<br/>';
    echo 'Получено по API площадок:' . count($sites) . '<br/>';
    echo 'Получено по API жанров:' . count($genres) . '<br/>';
    echo 'Получено по API категорий: ' . count($showTypes) . ' <br/>';
    echo 'Получено по API доп. категорий: ' . count($additionalCategories) . ' <br/>';

    if ($slider_sync) {
        echo 'Получено по API сладйдеров: ' . count($sliders) . ' <br/>';
    }

    flush();

    $citesTerm = add_city_terms_by_taxonomy($cites);
    $sitesTerm = add_site_terms_by_taxonomy($sites);
    $showTypesTerm = add_showType_terms_by_taxonomy($showTypes);
    $genresTerm = add_genre_terms_by_taxonomy($genres);
    $additionalCategoriesTerm = add_additional_category_terms_by_taxonomy($additionalCategories);
    $updatedShows = 0;
    $updatedSlider = 0;

    //обновляем шоу, добавляя им жанры/категории и т.д.
    foreach ($shows as $show) {
        if (!empty($show_posts[$show['sourceShowId']])) {
            $post_id = (int) $show_posts[$show['sourceShowId']];
            $updatedShows++;

            if (array_key_exists($show['siteId'], $sitesTerm) && array_key_exists('term_id', $sitesTerm[$show['siteId']])) {
                wp_set_object_terms($post_id, (int) $sitesTerm[$show['siteId']]['term_id'], KONTRAMARKA_PARAMS_SITE);
            }

            if (array_key_exists($show['city'], $citesTerm) && array_key_exists('term_id', $citesTerm[$show['city']])) {
                wp_set_object_terms($post_id, (int) $citesTerm[$show['city']]['term_id'], KONTRAMARKA_PARAMS_CITY);
            }


            $showTypesTermArray = [];
            foreach ($showTypesTerm as $item) {
                $term = get_term((int) $item['term_id'], KONTRAMARKA_PARAMS_SHOW_TYPE);
                $idCategory = explode('_', $term->slug);

                if (in_array((int) $idCategory[0], $show['showTypes'])) {
                    $showTypesTermArray[$item['term_id']] = (int) $item['term_id'];
                }
            }

            if (count($showTypesTermArray)) {
                wp_set_object_terms($post_id, $showTypesTermArray, KONTRAMARKA_PARAMS_SHOW_TYPE);
            }

            foreach ($show['genres'] as $type) {
                if (array_key_exists($type, $genresTerm) && array_key_exists('term_id', $genresTerm[$type])) {
                    wp_set_object_terms($post_id, (int) $genresTerm[$type]['term_id'], KONTRAMARKA_PARAMS_GENRE);
                }
            }

            foreach ($show['events'] as $key => $type) {
                foreach ($additionalCategoriesTerm as $k => $additionalCategories) {
                    if (is_array($additionalCategories['eventIds']) && array_key_exists($key, $additionalCategories['eventIds'])) {
                        wp_set_object_terms($post_id, (int) $additionalCategoriesTerm[$key]['term_id'], KONTRAMARKA_PARAMS_GENRE);
                    }
                }
            }
        }
    }

    echo '<br/>';

    if ($slider_sync) {
        $postHome = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE post_name='glavnaya' LIMIT 1");

        if ($postHome && $sliders) {
            $upload_dir = wp_upload_dir();
            $postHomeId = $postHome[0]->ID;
            $getSliders = $wpdb->get_results("SELECT post_id, meta_value FROM $wpdb->postmeta INNER JOIN $wpdb->posts ON ID = post_id WHERE post_id= '" . $postHomeId . "' AND meta_key='home-slider' AND meta_value!=''");

            foreach ($getSliders as $result) {
                for ($i = $result->meta_value; $i >= 0; $i--) {
                    delete_row('home-slider', $i, $postHomeId);
                }
            }

            foreach ($sliders as $slider) {
                $attach_id = '';
                if (!empty($slider['image'])) {
                    $filename = basename($slider['image']['uk']);

                    if (wp_mkdir_p($upload_dir['path'])) {
                        $file = $upload_dir['path'] . '/' . $filename;
                    } else {
                        $file = $upload_dir['basedir'] . '/' . $filename;
                    }

                    downloadImage(KONTRAMARKA_URL . $slider['image']['uk'], $file);

                    $wp_filetype = wp_check_filetype($filename, null);
                    $attachment = [
                        'post_mime_type' => $wp_filetype['type'],
                        'post_title' => sanitize_file_name($filename),
                        'post_content' => '',
                        'post_status' => 'inherit',
                    ];
                    require_once(ABSPATH . 'wp-admin/includes/image.php');

                    $attach_id = wp_insert_attachment($attachment, $file, $postHomeId);
                    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                }

                $sliderLink = '';

                if (!$slider['useCustomLink']) {
                    $sourceShowId = '';

                    foreach ($shows as $show) {
                        if ($show['id'] == $slider['showId']) {
                            $sourceShowId = $show['sourceShowId'];
                        }
                    }

                    if ($sourceShowId) {
                        $postShowLink = $wpdb->get_results("SELECT post_id, meta_value FROM $wpdb->postmeta INNER JOIN $wpdb->posts ON ID = post_id AND post_type = '" . KONTRAMARKA_SHOW_POST_TYPE . "' WHERE meta_key='show_id' AND meta_value='" . $sourceShowId . "' LIMIT 1");

                        if ($postShowLink && $postShowLink[0]->post_id) {
                            $sliderLink = get_permalink($postShowLink[0]->post_id);
                        }

                        if ($sliderLink) {
                            $row = [
                                'home-slider__image' => $attach_id,
                                'home-slider__title' => $slider['title']['uk'],
                                'home-slider__price' => $slider['price'],
                                'home-slider__sort' => $slider['sort'],
                                'home-slider__city' => $slider['city'],
                                'home-slider__date' => $slider['date'] ? strtotime($slider['date']) : '',
                                'home-slider__link' => $sliderLink,
                            ];

                            add_row('home-slider', $row, $postHomeId);
                            $updatedSlider++;
                        }
                    }
                }
            }
            echo 'Были обновлены данные у ' . $updatedSlider . ' слайдеров <br/>';
        }
    }

    echo 'Были обновлены данные у ' . $updatedShows . ' шоу <br/>';
    flush();
}

add_action('init', 'add_taxonomies');
function add_taxonomies()
{
    $genre = [
        'labels' => [
            'name' => 'Жанры',
            'singular_name' => 'Жанры',
            'menu_name' => 'Жанры',
            'search_items' => 'Поиск',
            'all_items' => 'Все жанры',
            'edit_item' => 'Редактировать',
            'update_item' => 'Обновить',
            'add_new_item' => 'Добавить жанр',
            'new_item_name' => 'Добавить',
        ],
        'hierarchical' => true,
        'sort' => true,
        'show_admin_column' => true,
    ];

    register_taxonomy(KONTRAMARKA_PARAMS_GENRE, KONTRAMARKA_SHOW_POST_TYPE, $genre);

    $city = [
        'labels' => [
            'name' => 'Города',
            'singular_name' => 'Города',
            'menu_name' => 'Города',
            'search_items' => 'Поиск',
            'all_items' => 'Все города',
            'edit_item' => 'Редактировать',
            'update_item' => 'Обновить',
            'add_new_item' => 'Добавить город',
            'new_item_name' => 'Добавить',
        ],
        'hierarchical' => true,
        'sort' => true,
        'show_admin_column' => true,
    ];

    register_taxonomy(KONTRAMARKA_PARAMS_CITY, KONTRAMARKA_SHOW_POST_TYPE, $city);

    $site = [
        'labels' => [
            'name' => 'Площадки',
            'singular_name' => 'Площадки',
            'menu_name' => 'Площадки',
            'search_items' => 'Поиск',
            'all_items' => 'Все площадки',
            'edit_item' => 'Редактировать',
            'update_item' => 'Обновить',
            'add_new_item' => 'Добавить площадку',
            'new_item_name' => 'Добавить',
        ],
        'hierarchical' => true,
        'sort' => true,
        'show_admin_column' => true,
    ];

    register_taxonomy(KONTRAMARKA_PARAMS_SITE, KONTRAMARKA_SHOW_POST_TYPE, $site);

    $showType = [
        'labels' => [
            'name' => 'Категории',
            'singular_name' => 'Категории',
            'menu_name' => 'Категории',
            'search_items' => 'Поиск',
            'all_items' => 'Все категории',
            'edit_item' => 'Редактировать',
            'update_item' => 'Обновить',
            'add_new_item' => 'Добавить категорию',
            'new_item_name' => 'Добавить',
        ],
        'hierarchical' => true,
        'sort' => true,
        'show_admin_column' => true,
    ];

    register_taxonomy(KONTRAMARKA_PARAMS_SHOW_TYPE, KONTRAMARKA_SHOW_POST_TYPE, $showType);

    $additionalCategories = [
        'labels' => [
            'name' => 'Дополнительные категории',
            'singular_name' => 'Дополнительные категории',
            'menu_name' => 'Дополнительные категории',
            'search_items' => 'Поиск',
            'all_items' => 'Все доп. категории',
            'edit_item' => 'Редактировать',
            'update_item' => 'Обновить',
            'add_new_item' => 'Добавить доп. категорию',
            'new_item_name' => 'Добавить',
        ],
        'hierarchical' => true,
        'sort' => true,
        'show_admin_column' => true,
    ];

    register_taxonomy(KONTRAMARKA_PARAMS_ADDITIONAL_CATEGORY, KONTRAMARKA_SHOW_POST_TYPE, $additionalCategories);
}

/**
 * @param $arrayTerm
 *
 * @return array
 */
function add_city_terms_by_taxonomy($arrayTerm)
{
    $result = [];

    foreach ($arrayTerm as $term) {
        $result[$term['id']] = term_exists($term['name']['uk'], KONTRAMARKA_PARAMS_CITY);

        if (!$result[$term['id']]) {
            $result[$term['id']] = wp_insert_term($term['name']['uk'], KONTRAMARKA_PARAMS_CITY, [
                'description' => '',
                'parent' => 0,
                'slug' => mb_strtolower($term['name']['en']),
            ]);

            updateTermNameOptions($term['name']);
        }

    }

    return $result;
}

/**
 * @param $arrayTerm
 *
 * @return array
 */
function add_site_terms_by_taxonomy($arrayTerm)
{
    $result = [];

    foreach ($arrayTerm as $term) {
        $result[$term['id']] = term_exists($term['name']['uk'], KONTRAMARKA_PARAMS_SITE);

        if (!$result[$term['id']]) {
            $result[$term['id']] = wp_insert_term($term['name']['uk'], KONTRAMARKA_PARAMS_SITE, [
                'description' => '[:ua]' . $term['address']['uk'] . '[:ru]' . $term['address']['ru'] . '[:en]' . $term['address']['en'] . '[:]',
                'parent' => 0,
                'slug' => $term['code'],
            ]);

            updateTermNameOptions($term['name']);
        }
    }

    return $result;
}

/**
 * @param $arrayTerm
 *
 * @return array
 */
function add_showType_terms_by_taxonomy($arrayTerm)
{
    $result = [];

    foreach ($arrayTerm as $term) {
        $result[$term['id']] = term_exists($term['name']['uk'], KONTRAMARKA_PARAMS_SHOW_TYPE);

        if (!$result[$term['id']]) {
            $result[$term['id']] = wp_insert_term($term['name']['uk'], KONTRAMARKA_PARAMS_SHOW_TYPE, [
                'description' => $term['id'] . ' ' . $term['name']['ru'],
                'parent' => 0,
                'slug' => $term['id'] . '_' . $term['slug'],
            ]);

            updateTermNameOptions($term['name']);
        }
    }

    return $result;
}

/**
 * @param $arrayTerm
 *
 * @return array
 */
function add_genre_terms_by_taxonomy($arrayTerm)
{
    $result = [];

    foreach ($arrayTerm as $term) {
        $result[$term['id']] = term_exists($term['name']['uk'], KONTRAMARKA_PARAMS_GENRE);

        if (!$result[$term['id']]) {
            $result[$term['id']] = wp_insert_term($term['name']['uk'], KONTRAMARKA_PARAMS_GENRE, [
                'description' => $term['id'] . ' ' . $term['name']['ru'],
                'parent' => 0,
                'slug' => $term['slug'],
            ]);

            updateTermNameOptions($term['name']);
        }
    }

    return $result;
}

/**
 * @param $arrayTerm
 *
 * @return array
 */
function add_additional_category_terms_by_taxonomy($arrayTerm)
{
    $result = [];

    foreach ($arrayTerm as $term) {
        $result[$term['id']] = term_exists($term['name']['uk'], KONTRAMARKA_PARAMS_ADDITIONAL_CATEGORY);

        if (!$result[$term['id']]) {
            $result[$term['id']] = wp_insert_term($term['name']['uk'], KONTRAMARKA_PARAMS_ADDITIONAL_CATEGORY, [
                'description' => '',
                'parent' => 0,
                'slug' => $term['id'],
                'eventIds' => $term['eventIds'],
            ]);

            updateTermNameOptions($term['name']);
        }
    }

    return $result;
}

/**
 * @param array $name
 */
function updateTermNameOptions(array $name)
{
    if (array_key_exists('uk', $name)) {
        $name['ua'] = $name['uk'];
        unset($name['uk']);
    }

    global $q_config;

    $default_name = htmlspecialchars($name[$q_config['default_language']], ENT_NOQUOTES);

    if (empty($default_name)) {
        return;
    }

    foreach ($name as $lang => $nam) {
        $q_config['term_name'][$default_name][$lang] = htmlspecialchars($nam, ENT_NOQUOTES);
    }

    update_option('qtranslate_term_name', $q_config['term_name']);
}
