<?php
    /*
     *	Función de AJAX para eliminar una conversación. A esta función le llega la información
     *  de la conversación que se desea eliminar. Para eliminar una conversación solamente se eliminan
     *  todos sus mensajes y es borrado de la lista de conversaciones. No se necesita hacer otra acción
     */

    /*  Obtener el usuario actual */
    $user = wp_get_current_user();

    /*  Obtener el usuario al que le vamos a eliminar la conversación */
    $destinatario = ( isset( $_GET['destinatario'] ) ) ? $_GET['destinatario'] : '';

    /*  Terminar el procesamiento si no se recibe una conversación a eliminar */
    if ( ! isset( $_GET['destinatario'] ) ) { wp_send_json_error( 'No hay destinatario' ); }

    /*  Primero hay que determinar si no hay problemas con el usuario y qué usuario es */
    if ( !( $user instanceof WP_User ) ) { wp_send_json_error(); }

    /*  Obtener el ID único del usuario */
    $cuido_id = $id = get_user_meta( $user->ID, 'description', true );

    /*  Si no es cuido, es empleado o familia y el id único se tiene que calcular */
    if ( $user->roles[0] !== 'cuido' ) { list( $cuido_id, $id ) = preg_split( '/,/', $id); }

    /*	Obtener los mensajes en donde estén involucrados estos dos usuarios */
    $obtener_mensajes_args = array(
        'post_type'     => 'nido-mensaje',
        'meta_query'    => array(
            'relation'  => 'OR',
            array(
                'relation'  => 'AND',
                array(
                    'key'   => 'wpcf-de-mensajes',
                    'value' => $id,
                ),
                array(
                    'key'   => 'wpcf-para-mensajes',
                    'value' => $destinatario,
                )
            ),
            array(
                'relation'  => 'AND',
                array(
                    'key'   => 'wpcf-de-mensajes',
                    'value' => $destinatario,
                ),
                array(
                    'key'   => 'wpcf-para-mensajes',
                    'value' => $id,
                )
            ),
        ),
        'orderby'   => 'post_date',
        'order'     => 'ASC',
        'posts_per_page' => -1
    );

    $obtener_mensajes = new WP_Query( $obtener_mensajes_args );   

    if ( empty( $obtener_mensajes->posts ) ) {
    	wp_send_json_sucess( 'No existen mensajes en esta conversación' );
    }
    
    /*  Eliminar cada mensaje involucrado en la conversación */
    foreach ( $obtener_mensajes->posts as $mensaje_eliminar_post ) {
    	$result = wp_delete_post( $mensaje_eliminar_post->ID, true );
    }

    wp_send_json_success();