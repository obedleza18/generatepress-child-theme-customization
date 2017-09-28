<?php

    /*
     *  Esta función es para checar en la base de datos cuántos mensajes nuevos hay de cada usuario
     *  con los que el usuario actual tiene conversaciones. Esta función está programada para
     *  funcionar con un método llamado en inglés Polling en el que el navegador está programado para
     *  hacer la llamada al servidor cada cierto tiempo.
     */

    /*  Obtener información del usuario actual */
    $user = wp_get_current_user();

    /*  Primero hay que determinar si no hay problemas con el usuario y qué usuario es */
    if ( !( $user instanceof WP_User ) ) { wp_send_json_error(); }

    /*  Obtener el ID único */
    $cuido_id = $id = get_user_meta( $user->ID, 'description', true );

    /*  Si no es cuido, es empleado o familia y el id único se tiene que calcular */
    if ( $user->roles[0] !== 'cuido' ) { list( $cuido_id, $id ) = preg_split( '/,/', $id); }

    /*  
     *  Se hace la búsqueda en la base de datos de los mensajes que no están leídos entre el usuario
     *  actual y todos los demás usuarios con los que tiene conversaciones
     */
    global $wpdb;
    $from = $wpdb->get_results( "
        SELECT DISTINCT m3.meta_value AS remitente
            FROM $wpdb->posts p, $wpdb->postmeta m1, $wpdb->postmeta m2, $wpdb->postmeta m3
            WHERE   p.ID = m1.post_id
                AND p.ID = m2.post_id
                AND p.ID = m3.post_id
                AND p.post_type = 'nido-mensaje'
                AND m1.meta_key = 'wpcf-para-mensajes'
                AND m1.meta_value = '$id'
                AND m2.meta_key = 'wpcf-leido-mensajes'
                AND m2.meta_value = 'no'
                AND m3.meta_key = 'wpcf-de-mensajes';
    " );

    /*  Determinar la cantidad de mensajes nuevos de cada usuario */
    $nuevos_mensajes_por_usuario = array();
    foreach ( $from as $from_id ) {

        /*  La estructura $from viene de la base de datos. Solamente tiene el atributo 'remitente' */
        $from_id = $from_id->remitente;

        /*  Se hace otra búsqueda para obtener más información de cada conversación */
        global $wpdb;
        $mensajes_para_mi = $wpdb->get_results( "
            SELECT COUNT( p.ID ) AS message_count, MAX(p.ID) AS ID, p.post_title AS family_name
            FROM $wpdb->posts p, $wpdb->postmeta m1, $wpdb->postmeta m2, $wpdb->postmeta m3
            WHERE   p.ID = m1.post_id
                AND p.ID = m2.post_id
                AND p.ID = m3.post_id
                AND p.post_type = 'nido-mensaje'
                AND m1.meta_key = 'wpcf-para-mensajes'
                AND m1.meta_value = '$id'
                AND m2.meta_key = 'wpcf-de-mensajes'
                AND m2.meta_value = '$from_id'
                AND m3.meta_key = 'wpcf-leido-mensajes'
                AND m3.meta_value = 'no';
        " );

        /*  Se crea una nueva estructura que se va a enviar al Front End */
        array_push(
            $nuevos_mensajes_por_usuario,
            array(
                'id'                => $from_id,
                'cantidad_mensajes' => (int)$mensajes_para_mi[0]->message_count,
                'ultimo_mensaje'    => get_post_meta( $mensajes_para_mi[0]->ID, 'wpcf-mensaje-mensajes', true ),
                'ultima_hora'       => get_post_meta( $mensajes_para_mi[0]->ID, 'wpcf-fecha-mensajes', true ),
                'nombre_familia'    => $mensajes_para_mi[0]->family_name,
                'avatar'            => apply_filters( 'nido_get_avatar', $from_id )
            )
        );
    }

    wp_send_json_success( $nuevos_mensajes_por_usuario );
?>