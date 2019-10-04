<?php
/////////////////////CRON_MTICKET_START//////////////////////////////

add_action('init', 'sync_maestro_ticket_system_api_add_hook');
add_action('sync_maestro_ticket_system_api_worker_start', 'sync_maestro_ticket_system_api', 10, 3);

// добавляет крон функцию
function sync_maestro_ticket_system_api_add_hook()
{
    if (!wp_next_scheduled('sync_maestro_ticket_system_api_worker_start')) {

        wp_schedule_event(strtotime(date('Y-m-d 03:00:00')), 'daily', 'sync_maestro_ticket_system_api_worker_start');
        //wp_schedule_event( time(), 'sync_maestro_ticket_system_api_worker', 'sync_maestro_ticket_system_api_worker_start' );
    }
}

//добавляет функцию исполняющуюся в кроне
function sync_maestro_ticket_system_api()
{
    mticket_sync();
}

// добавляет крон интервал времени
//add_filter('cron_schedules', 'sync_maestro_ticket_system_api_add_schedule');
/*function sync_maestro_ticket_system_api_add_schedule()
{
    $schedules['sync_maestro_ticket_system_api_worker'] = [
        'interval' => 60 * 60 * 2,
        'display' => 'Sync maestro ticket system api Worker',
    ];
    return $schedules;
}*/

/////////////////////CRON_MTICKET_END//////////////////////////////


/////////////////////CRON_KONTRAMARKA_START//////////////////////////////

add_action('init', 'sync_kontramarka_api_add_hook');
add_action('sync_kontramarka_api_worker_start', 'sync_kontramarka_api', 15, 3);

// добавляет крон функцию
function sync_kontramarka_api_add_hook()
{
    if (!wp_next_scheduled('sync_kontramarka_api_worker_start')) {
        wp_schedule_event(strtotime(date('Y-m-d 03:03:00')), 'daily', 'sync_kontramarka_api_worker_start');
        //wp_schedule_event( time(), 'sync_kontramarka_api_worker', 'sync_kontramarka_api_worker_start' );
    }
}

//добавляет функцию исполняющуюся в кроне
function sync_kontramarka_api()
{
    kontramarka_sync();
}

// добавляет крон интервал времени
//add_filter('cron_schedules', 'sync_kontramarka_api_add_schedule');
/*function sync_kontramarka_api_add_schedule()
{
    $schedules['sync_kontramarka_api_worker'] = [
        'interval' => 60 * 60 * 2,
        'display' => 'Sync kontramarka api Worker',
    ];
    return $schedules;
}*/


/////////////////////CRON_KONTRAMARKA_END//////////////////////////////
