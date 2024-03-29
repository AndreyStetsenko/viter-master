<?php get_header() ?>

<?php
$lang = get_locale();

if (strlen($lang) > 2) {
    $arrayLang = explode('_', $lang);
    $lang = $arrayLang[0];
}

if ($lang) {
    $lang .= '/';
}

$baseUrl = get_field('widget_base_url', 'options') ?: 'https://widget.kontramarka.ua/';

$link = $baseUrl . $lang . get_field('widget-account-address', 'options') ?: 'widget65site66/user/orders';
?>

<div id="widget-iframe" class="widget-iframe-1">
    <iframe src="<?= $link ?>" frameborder="0" width="100%" id="auto-iframe" class="widget-iframe-1"></iframe>
</div>

<?php get_footer() ?>
