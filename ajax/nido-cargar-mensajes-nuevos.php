<?php

    /*
     *  Función para cargar los mensajes nuevos de un usuario con tab activo. Esta función es 
     *  llamada por AJAX por el método llamado en inglés Polling. Esto quiere decir que el navegador
     *  estará programado en jQUery para que cada cierto tiempo esté llamando esta función haya o no
     *  haya mensajes siempre y cuando haya una conversación activa.
     */

    /*  Verificar cual es la conversación activa sin es que está configurada */
    if ( ! isset( $_POST['active_id'] ) ) { wp_send_json_error( 'No hay conversaciones activas.' ); }

    /*  
     *  Como la información que viene del Front-End está codificada en un objeto JSON, hay que 
     *  decodificar
     */
    $from = json_decode( stripslashes( $_POST['active_id'] ) );

    /*  Obtener información del usuario actual */
    $user = wp_get_current_user();

    /*  Primero hay que determinar si no hay problemas con el usuario y qué usuario es */
    if ( !( $user instanceof WP_User ) ) { wp_send_json_error(); }

    /*  Se obtiene el ID único de cada usuario */
    $cuido_id = $id = get_user_meta( $user->ID, 'description', true );

    /*  Si no es cuido, es empleado o familia y el id único se tiene que calcular */
    if ( $user->roles[0] !== 'cuido' ) { list( $cuido_id, $id ) = preg_split( '/,/', $id ); }

    /*  Obtener mensajes nuevos */
    $mensajes_nuevos_usuario_args = array(
        'post_type'     => 'nido-mensaje',
        'meta_query'    => array(
            'relation'  => 'AND',
            array(
                'key'   => 'wpcf-de-mensajes',
                'value' => $from,
            ),
            array(
                'key'   => 'wpcf-para-mensajes',
                'value' => $id,
            ),
            array(
                'key'   => 'wpcf-leido-mensajes',
                'value' => 'no',
            ),
        ),
        'orderby'   => 'post_date',
        'order'     => 'ASC',
        'posts_per_page' => -1
    );

    /*  
     *  Obteniendo solamente los mensajes que no han sido leídos para agregar a la conversación del 
     *  tab activo. Algo relevante de este método es que cuando la conversación ya está guardada en
     *  el caché del navegador, solamente se agregan los mensajes nuevos, no se carga toda la
     *  conversación cada vez que llegan nuevos mensajes.
     */
    $mensajes_nuevos_usuario_results = new WP_Query( $mensajes_nuevos_usuario_args );

    $mensajes_nuevos_usuario = array();

    foreach ( $mensajes_nuevos_usuario_results->posts as $mensaje_nuevo_usuario ) {
        
        $mensaje = get_post_meta( $mensaje_nuevo_usuario->ID, 'wpcf-mensaje-mensajes', true );

        array_push( $mensajes_nuevos_usuario, $mensaje );

        update_post_meta( $mensaje_nuevo_usuario->ID, 'wpcf-leido-mensajes', 'si', 'no' );
    }

    wp_send_json_success( $mensajes_nuevos_usuario );
?>
