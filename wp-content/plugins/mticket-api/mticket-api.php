<?php
/*
 Plugin Name: Maestro Ticket System Api
 Description: Maestro Ticket System Api Show and Events synchronize
 Version: 1.1
 Author: Fedir Vilgota
 Editor: Liubov Sopilko
 */

if (!defined('ABSPATH')) {
    exit;
}

const MTICKET_API_GATE = 'https://api.mticket.com.ua/gate1/';
const MTICKET_API_LOGIN = 'Gena_Viter';
const MTICKET_API_PASSWORD = 'txyiwrlpzcr';
const MTICKET_AGENT_ID = 368;
const MTICKET_WIDGET_ID = 321;

const MTICKET_METHOD_LOGIN = 'login';
const MTICKET_METHOD_SHOWS = 'shows';
const MTICKET_SHOW_POST_TYPE = 'events';

if (is_admin()) { // admin actions
    add_action('admin_menu', 'add_mticket_menu');
}

function add_mticket_menu()
{
    add_menu_page('Maestro Ticket System Api', 'Maestro Ticket', 'administrator', __FILE__, 'mticket_sync_page', 'dashicons-tickets-alt');
    add_submenu_page(__FILE__, 'Настройки', 'Настройки', 'administrator', __FILE__ . '?action=settings', 'mticket_settings_page');
}

register_setting('mticket-api', 'mticket_api_gate', ['default' => MTICKET_API_GATE]);
register_setting('mticket-api', 'mticket_api_login', ['default' => MTICKET_API_LOGIN]);
register_setting('mticket-api', 'mticket_api_password', ['default' => MTICKET_API_PASSWORD]);
register_setting('mticket-api', 'mticket_agent_id', ['default' => MTICKET_AGENT_ID]);
register_setting('mticket-api', 'mticket_widget_id', ['default' => MTICKET_WIDGET_ID]);

function mticket_sync_page()
{
    $action = empty($_POST['action']) ? (empty($_GET['action']) ? null : $_GET['action']) : $_POST['action'];
    if ($action == 'sync'):
        echo ' <h2>Maestro Ticket System Api</h2>';
        mticket_sync();
        echo '<br/>Синхронизация прошла успешно<br/>';
        ?>
        <form method="post">
            <?php wp_nonce_field('mticket-api-options'); ?>
            <input type="hidden" name="action" value="sync">
            <p class="submit">
                <input type="submit" class="button-primary" value="Начать повторную синхронизацию"/>
            </p>
        </form>
    <?php
    else: ?>
        <div class="wrap">
            <h2>Maestro Ticket System Api</h2>
            <form method="post">
                <?php wp_nonce_field('mticket-api-options'); ?>
                <input type="hidden" name="action" value="sync">
                <p class="submit">
                    <input type="submit" class="button-primary" value="Начать синхронизацию"/>
                </p>
            </form>
        </div>
    <?php endif;
}

function mticket_settings_page()
{
    ?>
    <div class="wrap">
        <h2>Maestro Ticket System Api</h2>

        <form method="post" action="options.php">
            <?php settings_fields('mticket-api'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Api Gate</th>
                    <td><input type="text" name="mticket_api_gate"
                               value="<?php echo get_option('mticket_api_gate'); ?>"/>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Api Login</th>
                    <td>
                        <input type="text" name="mticket_api_login"
                               value="<?php echo get_option('mticket_api_login'); ?>"/>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Api Password</th>
                    <td><input type="text" name="mticket_api_password"
                               value="<?php echo get_option('mticket_api_password'); ?>"/></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Agent Id</th>
                    <td><input type="text" name="mticket_agent_id"
                               value="<?php echo get_option('mticket_agent_id'); ?>"/></td>
                </tr>

                <tr valign="top">
                    <th scope="row">Widget Id</th>
                    <td><input type="text" name="mticket_widget_id"
                               value="<?php echo get_option('mticket_widget_id'); ?>"/></td>
                </tr>
            </table>

            <p class="submit">
                <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"/>
            </p>

        </form>
    </div>
    <?php
}

function mticket_request($url = '', $data = null, $raw = false)
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

function mticket_sync()
{
    global $wpdb;
    set_time_limit(0);
    $show_posts = [];

    foreach ($wpdb->get_results("SELECT post_id, meta_value FROM $wpdb->postmeta INNER JOIN $wpdb->posts ON ID = post_id AND post_type = '" . MTICKET_SHOW_POST_TYPE . "' WHERE meta_key='show_id' AND meta_value!=''") as $e) {
        if (!empty($show_posts[$e->post_id])) {
            wp_delete_post($e->post_id, true);
            continue;
        }

        $show_posts[$e->meta_value] = $e->post_id;
    }

    $event_posts = [];

    foreach ($wpdb->get_results("SELECT post_id, meta_value FROM $wpdb->postmeta INNER JOIN $wpdb->posts ON ID = post_id AND post_type = '" . MTICKET_SHOW_POST_TYPE . "' WHERE meta_key='show_dates' AND meta_value!=''") as $e) {
        $sql = "SELECT post_id, meta_key, meta_value FROM $wpdb->postmeta INNER JOIN $wpdb->posts ON ID = post_id AND post_type = '" . MTICKET_SHOW_POST_TYPE . "' WHERE (";

        for ($i = 0; $i < $e->meta_value; $i++) {
            if ($i === 0) {
                $sql .= " meta_key='show_dates" . $i . "_event_id' ";
            } else {
                $sql .= " OR meta_key='show_dates_" . $i . "_event_id' ";
            }
        }

        $sql .= " ) AND meta_value!=''";

        foreach ($wpdb->get_results($sql) as $event) {
            $event_posts[$event->meta_value] = $event->meta_value;
        }
    }

    echo 'На сайте ' . count($show_posts) . ' шоу и ' . count($event_posts) . ' событий<br/>';
    flush();

    $api_gate = get_option('mticket_api_gate') ? get_option('mticket_api_gate') : MTICKET_API_GATE;

    // filling session id
    $sessionId = mticket_request($api_gate . MTICKET_METHOD_LOGIN, [
        'username' => get_option('mticket_api_login') ? get_option('mticket_api_login') : MTICKET_API_LOGIN,
        'password' => get_option('mticket_api_password') ? get_option('mticket_api_password') : MTICKET_API_PASSWORD,
    ], true);

    if (empty($sessionId)) {
        echo 'Неправильные доступы к АПИ<br/>';

        return;
    }

    // Получить список шоу
    $shows = mticket_request($api_gate . MTICKET_METHOD_SHOWS, [
        'sessionid' => $sessionId,
    ]);

    if (empty($shows)) {
        echo 'Не удалось получить список Шоу<br/>';

        return;
    }

    $events = 0;

    foreach ($shows as $show) {
        $events += count($show['events']);
    }

    echo 'Получено по API ' . count($shows) . ' шоу и ' . $events . ' событий<br/>';
    flush();

    $createdShows = 0;
    $createdEvents = 0;
    $updatedShows = 0;
    $updatedEvents = 0;
    $updatedShowsI = [];
    $updatedEventsI = [];
    $toDraftEvents = 0;
    $toDraftShows = 0;
    $upload_dir = wp_upload_dir();

    foreach ($shows as $show) {
        $updatedShowsI[$show['showId']] = $show['showId'];
        $new = false;

        if (empty($show_posts[$show['showId']])) {
            // Новое шоу создать
            $post_id = wp_insert_post([
                'post_type' => MTICKET_SHOW_POST_TYPE,
                'post_title' => '[:ua]' . $show['name'] . '[:]',
                'post_content' => '[:ua]' . $show['description'] . '[:]',
                'post_status' => 'publish',
            ]);
            $show_posts[$show['showId']] = $post_id;
            $createdShows++;
            $new = true;
            $eventsArray[$post_id] = [];

            if (!empty($show['events'])) {
                //находим минимальную дату эвента
                $min = $show['events'][0]['origin'];
                $key_min = 0;

                foreach ($show['events'] as $k => $a) {
                    if ($a['origin'] < $min) {
                        $min = $a['origin'];
                        $key_min = $k;
                    }
                }

                foreach ($show['events'] as $key => $event) {
                    $time = strtotime($event['origin']);

                    if ($key_min === $key) {
                        update_post_meta($post_id, "show_id", $show['showId']);
                        update_post_meta($post_id, "event-date", date('Ymd', $time));
                        update_post_meta($post_id, "event-time", date('H:i', $time));
                        if ($event['priceMax'] === $event['priceMin']) {
                            update_post_meta($post_id, "event-price", $event['priceMin']);
                        } else {
                            update_post_meta($post_id, "event-price", $event['priceMin'] . '-' . $event['priceMax']);
                        }
                        update_post_meta($post_id, "event-place", $event['displaySiteName']);
                        update_post_meta($post_id, "event-address", $event['displaySiteAddr'] . ', ' . $event['displaySiteCityName']);
                    }

                    //Создаем евент в нашем шоу
                    if ($event['freePlacesCount'] > 0) {
                        $sold = 0;
                    } else {
                        $sold = 1;
                    }

                    if ($event['priceMax'] === $event['priceMin']) {
                        $price = $event['priceMin'];
                    } else {
                        $price = $event['priceMin'] . '-' . $event['priceMax'];
                    }

                    $row = [
                        'event_id' => $event['eventId'],
                        'show_dates__date' => date('Ymd', $time),
                        'show_dates__time' => date('H:i', $time),
                        'show_dates__price' => $price,
                        'show_dates__sold' => $sold,
                        'show_dates__buy' => 'javascript:mTicketWidget.open(' . MTICKET_WIDGET_ID . ', ' . $event['siteId'] . ', ' . $event['eventId'] . ')',
                    ];

                    add_row('show_dates', $row, $post_id);
                    $createdEvents++;
                }
            }

        } else {
            $post_id = $show_posts[$show['showId']];
            $updatedShows++;
            $get_results_n = $wpdb->get_results("SELECT post_id, meta_value FROM $wpdb->postmeta INNER JOIN $wpdb->posts ON ID = post_id AND post_type = '" . MTICKET_SHOW_POST_TYPE . "' WHERE post_id= '" . $post_id . "' AND meta_key='show_dates' AND meta_value!=''");

            foreach ($get_results_n as $result) {
                for ($i = $result->meta_value; $i >= 0; $i--) {
                    if (delete_row('show_dates', $i, $post_id)) {
                        $toDraftEvents++;
                    }
                }
            }

            if (empty($show['events'])) {
                // Если нет событий у шоу, переместим его в черновики
                $wpdb->query("
                    UPDATE {$wpdb->posts}
                    SET post_status='draft'
                    WHERE `ID`={$post_id}
                ");
                $toDraftShows++;

            } else {
                // Если есть события у шоу, опубликуем его
                $wpdb->query("
                    UPDATE {$wpdb->posts}
                    SET post_status='publish'
                    WHERE `ID`={$post_id}
                ");

                //находим минимальную дату эвента
                $min = $show['events'][0]['origin'];
                $key_min = 0;

                foreach ($show['events'] as $k => $a) {
                    if ($a['origin'] < $min) {
                        $min = $a['origin'];
                        $key_min = $k;
                    }
                }

                foreach ($show['events'] as $key => $event) {
                    $time = strtotime($event['origin']);

                    if ($key_min === $key) {
                        update_post_meta($post_id, "show_id", $show['showId']);
                        update_post_meta($post_id, "event-date", date('Ymd', $time));
                        update_post_meta($post_id, "event-time", date('H:i', $time));

                        if ($event['priceMax'] === $event['priceMin']) {
                            update_post_meta($post_id, "event-price", $event['priceMin']);
                        } else {
                            update_post_meta($post_id, "event-price", $event['priceMin'] . '-' . $event['priceMax']);
                        }

                        update_post_meta($post_id, "event-place", $event['displaySiteName']);
                        update_post_meta($post_id, "event-address", $event['displaySiteAddr'] . ', ' . $event['displaySiteCityName']);
                    }

                    if ($event['freePlacesCount'] > 0) {
                        $sold = 0;
                    } else {
                        $sold = 1;
                    }

                    if ($event['priceMax'] === $event['priceMin']) {
                        $price = $event['priceMin'];
                    } else {
                        $price = $event['priceMin'] . '-' . $event['priceMax'];
                    }

                    $row = [
                        'event_id' => $event['eventId'],
                        'show_dates__date' => date('Ymd', $time),
                        'show_dates__time' => date('H:i', $time),
                        'show_dates__price' => $price,
                        'show_dates__sold' => $sold,
                        'show_dates__buy' => 'javascript:mTicketWidget.open(' . MTICKET_WIDGET_ID . ', ' . $event['siteId'] . ', ' . $event['eventId'] . ')',
                    ];

                    add_row('show_dates', $row, $post_id);
                    $createdEvents++;
                }
            }
        }

        $attach_id = get_post_meta($post_id, "_thumbnail_id", true);

        if (!empty($show['poster']) && empty($attach_id)) {
            $image_data = mticket_request($show['poster'], null, true);
            $filename = basename($show['poster']);

            if (wp_mkdir_p($upload_dir['path'])) {
                $file = $upload_dir['path'] . '/' . $filename;
            } else {
                $file = $upload_dir['basedir'] . '/' . $filename;
            }

            file_put_contents($file, $image_data);
            $wp_filetype = wp_check_filetype($filename, null);
            $attachment = [
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name($filename),
                'post_content' => '',
                'post_status' => 'inherit',
            ];
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            $attach_id = wp_insert_attachment($attachment, $file, $post_id);
            $attach_data = wp_generate_attachment_metadata($attach_id, $file);
            wp_update_attachment_metadata($attach_id, $attach_data);
            set_post_thumbnail($post_id, $attach_id);
        }

        update_post_meta($post_id, "show_id", $show['showId']);

        if ($new) {
            update_post_meta($post_id, "show-short-title", '<!--:ua-->' . $show['name'] . '<!--:-->');
            update_post_meta($post_id, "duration", '<!--:ua-->' . $show['duration'] . ' Хвилин<!--:ru-->' . $show['duration'] . ' Минут<!--:en-->' . $show['duration'] . ' Minutes<!--:-->');
        }
    }

    $updatedShowsI = array_diff_key($show_posts, $updatedShowsI);

    if (!empty($updatedShowsI)) {
        //удаляем евенты у старых щоу
        foreach ($updatedShowsI as $item) {
            $results = $wpdb->get_results("SELECT post_id, meta_value FROM $wpdb->postmeta INNER JOIN $wpdb->posts ON ID = post_id AND post_type = '" . MTICKET_SHOW_POST_TYPE . "' WHERE post_id= '" . $item . "' AND meta_key='show_dates' AND meta_value!=''");

            foreach ($results as $result) {
                for ($i = $result->meta_value; $i >= 0; $i--) {
                    if (delete_row('show_dates', $i, $item)) {
                        $toDraftEvents++;
                    }

                }
            }
        }

        // Шоу которые не пришли по Апи перемещаем в черновики
        $wpdb->query("
             UPDATE {$wpdb->posts}
             SET post_status='draft'
             WHERE `ID` IN (" . implode(',', $updatedShowsI) . ")
         ");
    }

    $updatedEventsI = array_diff_key($event_posts, $updatedEventsI);

    if (!empty($updatedEventsI)) {
        // События которые не пришли по Апи удаляем
        foreach ($updatedEventsI as $post_id => $item) {
            $get_results = $wpdb->get_results("SELECT post_id, meta_key, meta_value FROM $wpdb->postmeta INNER JOIN $wpdb->posts ON ID = post_id AND post_type = '" . MTICKET_SHOW_POST_TYPE . "' WHERE post_id= '" . $post_id . "' AND meta_key='show_dates' AND meta_value!=''");

            if ($get_results) {
                foreach ($get_results as $result) {
                    for ($i = $result->meta_value; $i >= 0; $i--) {
                        if (delete_row('show_dates', $i, $post_id)) {
                            $toDraftEvents++;
                        }

                    }
                }
            }
        }
    }

    //если остались шоу без show_id - переводим их в черновик
    foreach ($wpdb->get_results("SELECT post_id, meta_value FROM $wpdb->postmeta INNER JOIN $wpdb->posts ON ID = post_id AND post_type = '" . MTICKET_SHOW_POST_TYPE . "' WHERE meta_key='show_id' AND meta_value =''") as $e) {
        // Если нет событий у шоу, переместим его в черновики
        $wpdb->query("
            UPDATE {$wpdb->posts}
            SET post_status='draft'
            WHERE `ID`={$e->post_id}
        ");
        $toDraftShows++;
    }

    echo 'Создано ' . $createdShows . ' шоу и ' . $createdEvents . ' событий<br/>';
    echo 'Обновлено ' . $updatedShows . ' шоу и ' . $updatedEvents . ' событий<br/>';
    echo 'В черновиках ' . $toDraftShows . ' шоу<br/>';
    echo 'Удалено ' . $toDraftEvents . ' и обновлено ' . count($updatedEventsI) . ' событий<br/>';
    flush();
}

/*
одно шоу со своими событиями
array(11) {
["siteId"]=> int(19)
["showId"]=> int(5524)
["name"]=> string(76) "Ліга Жіночого Боксу. «Українські левиці»."
["duration"]=> int(0)
["poster"]=> string(85) "https://mticket.com.ua/m-ticket/sfad/upload/show_5H4zvt153dEVXGoKn3aafzn5Xpi_2cRB.jpg"
["fee"]=> int(0)
["cityId"]=> int(7)
["categoryId"]=> int(5)
["events"]=> array(1) {
    [0]=> array(18) {
        ["showId"]=> int(5524)
        ["siteId"]=> int(19)
        ["eventId"]=> int(26027)
        ["hallId"]=> int(22751695)
        ["origin"]=> string(14) "20190801213000"
        ["closeTime"]=> string(14) "20190801230000"
        ["providerId"]=> int(1075)
        ["premiere"]=> bool(false)
        ["eventType"]=> int(2)
        ["extData"]=> array(0) { }
        ["priceMin"]=> int(300)
        ["priceMax"]=> int(700)
        ["freePlacesCount"]=> int(897)
        ["displaySiteId"]=> int(11740)
        ["displaySiteName"]=> string(8) "Red Line"
        ["displaySiteAddr"]=> string(43) "м. Одеса, пляж Аркадія, 25"
        ["displaySiteCityName"]=> string(12) "Одесса"
        ["displayHallName"]=> string(32) "Red Line Одесса (бокс)"
    }
}
["orgName"]=> string(22) "Контрамарка"
["description"]=> string(8930) "
«Левиці» продовжують переможну ходу. На черзі яскраве боксерське шоу в Одесі
«Українські левиці» стрімко здобувають популярність по всій Україні завдяки своїм яскравим переможними поєдинкам та неймовірним шоу програмам в рамках боксерських вечорів. Наступне спортивне шоу наша команда проведе в Одесі в місцевому нічному клубі «Red Light Club».
Команда «Українські левиці» анонсувала дату та наступне місто проведення третього вечора боксу у дебютному для себе змагальному році. 1 серпня, о 21:30, в розпал курортного сезону наші неймовірні дівчата демонструватимуть майстерність у сонячному місті Одеса у новому та безумовно одному з найкращих місцевих нічних клубів - «Red Light Club».
За підтримки генерального партнера проекту  «Фаворит спорт», Федерації боксу України, Одеської обласної Федерації боксу, «Ліга Жіночого Боксу» підготували для вболівальників потужне боксерське шоу та традиційно яскраві та безкомпромісні поєдинки.
Варто зазначити, що хедлайнерами вечора боксу стане одна з найбільш прогресивних сучасних музичних гуртів України, яскрава та запальна група «DILEМMA», яка виконає для учасників вечора боксу свої найкращі хіти та подарує більше яскравих емоцій.
Традиційно участь у боксерському шоу в Одесі прийматимуть найкращі, найсильніші та найбільш титуловані спортсменки Національної збірної України з боксу.
Щодо суперників наших дівчат, то організатори боксерського шоу тримають інтригу, проте з впевненістю можна сказати, що це буде одна з найбільш потужних жіночих боксерських збірних Європи.
Таким чином, якщо ти бажаєш провести перший день серпня яскраво та незабутньо, то вечір боксу за участю «Українських левиць» - саме те, що необхідно для цього. Яскраві та безкомпромісні поєдинки, потужна шоу-програма та безліч цікавих сюрпризів для глядачів.
1 серпня, 21:30, нічний клуб «Red Light Club», черговий у поточному сезоні вечір боксу однієї з найбільш популярних команд нашої держави – «Українських левиці».
«Ліга Жіночого Боксу» - унікальний спортивний проект, якому на сьогоднішній день не має аналогів в усьому світі. Поєднання спортивного духу з яскравими боксерськими шоу та найвідомішими представниками шоу-бізнесу роблять цей проект дійсно унікальним.
Продаж квитків здійснюється на сайті КОНТРАМАРКА та  ESPORT.IN.UA.
Пише історію українського спорту разом!
Прийди та підтримай «Українських левиць!
" }
*/
