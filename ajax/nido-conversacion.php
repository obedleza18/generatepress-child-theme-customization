<?php

    /*
     *  Esta función crea la conversación completa entre el usuario recibido y el usuario actual.
     *  Crea un arreglo que contiene toda la información de los mensajes. El objeto creado es
     *  sencillo y comprimido para enviar lo menos que se pueda de información a través de la web.
     *  Esta función solamente se llama cuando el usuario da click a un tab.
     */

    /*  Se obtiene el usuario del que queremos la conversación */
    $to_id = ( isset( $_GET['to'] ) ) ? $_GET['to'] : '';

    /*  Se obtiene el usuario actual */
    $from = wp_get_current_user();

    /*  Se obtiene el ID único del usuario actual */
    $from_id = get_user_meta( $from->ID, 'description', true );

    /*  Si el usuario no es cuido, es familia o empleado. Hay que calcular su ID de la sig. forma */
    if ( $from->roles[0] !== 'cuido' ) { list( $cuido_id, $from_id ) = preg_split( '/,/', $from_id ); }

    /*  Crear los argumentos para obtener todos los mensajes de la conversación deseada */
    $args = array(
        'post_type'     => 'nido-mensaje',
        'meta_query'    => array(
            'relation'  => 'OR',
            array(
                'relation'  => 'AND',
                array(
                    'key'   => 'wpcf-de-mensajes',
                    'value' => $from_id,
                ),
                array(
                    'key'   => 'wpcf-para-mensajes',
                    'value' => $to_id,
                )
            ),
            array(
                'relation'  => 'AND',
                array(
                    'key'   => 'wpcf-de-mensajes',
                    'value' => $to_id,
                ),
                array(
                    'key'   => 'wpcf-para-mensajes',
                    'value' => $from_id,
                )
            ),
        ),
        'orderby'   => 'post_date',
        'order'     => 'ASC',
        'posts_per_page' => -1
    );

    /*  Se obtienen los mensajes (Custom Posts: Mensajes) */
    $mensajes = new WP_Query( $args );

    /*  Se va a crear un nuevo objeto que va a contener toda la información de los mensajes */
    $arreglo_mensajes = array();
    // var_dump( '<pre>' );
    if ( ! empty( $mensajes->posts ) ) {

        foreach ( $mensajes->posts as $mensaje ) {

            $mensaje_texto = get_post_meta( $mensaje->ID, 'wpcf-mensaje-mensajes', true );

            /*  Algo importante de los mensajes es quién fue el creador */
            if ( $mensaje->post_title === $from->display_name ) {
                // Mensaje mio
                $mensaje_codificado = array( 'de_quien' => 'mio', 'mensaje' => esc_html( $mensaje_texto ) );

            }
            else {
                // Mensaje otro
                $mensaje_codificado = array( 'de_quien' => 'otro', 'mensaje' => esc_html( $mensaje_texto ) );
            }

            /*  Se agrega la información al nuevo objeto de la conversación */
            array_push( $arreglo_mensajes, $mensaje_codificado );

            // var_dump( $mensaje_codificado );
        }
    }
    // var_dump( '</pre>' );

    /*
     *  Argumentos para obtener los mensajes que vienen hacia el usuario actual. El objetivo es
     *  cambiar el estatus de estos mensajes de no leídos a leídos.
     */
    $args = array(
        'post_type'     => 'nido-mensaje',
        'meta_query'    => array(
            'relation'  => 'AND',
            array(
                'key'   => 'wpcf-leido-mensajes',
                'value' => 'no',
            ),
            array(
                'key'   => 'wpcf-para-mensajes',
                'value' => $from_id,
            ),
            array(
                'key'   => 'wpcf-de-mensajes',
                'value' => $to_id,
            )
        ),
        'posts_per_page' => -1
    );

    /*  Obtener los mensajes que vienen hacia el usuario actual */
    $query = new WP_Query( $args );

    if ( ! empty( $query->posts ) ) {

        foreach ( $query->posts as $post ) {
            
            /*  Solo los mensajes que vienen hacia el usuario actual se configuran como leídos */
            update_post_meta( $post->ID, 'wpcf-leido-mensajes', 'si' );

        }

    }

    /*  Enviar al Front-End el objeto de la conversación creada en formato JSON */
    wp_send_json_success( $arreglo_mensajes );
?>