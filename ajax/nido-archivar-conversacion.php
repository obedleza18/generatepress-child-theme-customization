<?php
    /*
     *  Función para archivar conversación. Cuando llega un mensaje vuelve a activar la conversación
     */

    $de_quien = wp_get_current_user()->ID;
    $de_quien = apply_filters( 'nido_get_nido_id', $de_quien );
    $para_quien = ( isset( $_GET['para_quien'] ) ) ? $_GET['para_quien'] : '';

    $conversacion_args = array(
        'post_type'     => 'nido-conversaciones',
        'meta_query'    => array(
            'relation'  => 'AND',
            array(
                'key'   => 'wpcf-nido-de-quien',
                'value' => $de_quien,
            ),
            array(
                'key'   => 'wpcf-nido-para-quien',
                'value' => $para_quien
            )
        ),
        'posts_per_page' => -1
    );

    $conversacion = new WP_Query( $conversacion_args );

    /*  
     *  En caso de que ya haya posts registrados con estos datos, actualizar. 
     *  En caso contrario, crear el post
     */
    if ( ! empty( $conversacion->posts ) ) {
        update_post_meta( $conversacion->posts[0]->ID, 'wpcf-nido-archivada', 'si' );
    }
    else {
        $conversacion_post = array(
            'post_status'   => 'publish',
            'post_type'     => 'nido-conversaciones',
            'post_title'    => $de_quien . ' ' . $para_quien,
            'meta_input'    => array(
                'wpcf-nido-de-quien'    => $de_quien,
                'wpcf-nido-para-quien'  => $para_quien,
                'wpcf-nido-archivada'   => 'si'
            )
        );

        wp_insert_post( $conversacion_post );
    }

    wp_send_json_success();