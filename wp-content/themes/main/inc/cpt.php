<?php

function cpt_events()
{

    $labels = array(
        'name' => __('Події', 'main'),
        'singular_name' => 'События',
        'menu_name' => 'События',
        'name_admin_bar' => 'События',
        'all_items' => 'События',
        'add_new' => 'Добавить',
        'new_item' => 'Добавить',
        'edit_item' => 'Редактировать',
        'update_item' => 'Обновить',
        'search_items' => 'Поиск',
        'not_found' => 'Не найдено'
    );
    $args = array(
        'labels' => $labels,
        'has_archive' => true,
        'public' => true,
        'menu_icon' => 'dashicons-megaphone',
        'menu_position' => 2,
        'hierarchical' => true,
        'supports' => array(
            'title',
            'editor',
            'thumbnail',
            'excerpt',
            'revisions',
        )
    );
    register_post_type('events', $args);

}

add_action('init', 'cpt_events', 0);
