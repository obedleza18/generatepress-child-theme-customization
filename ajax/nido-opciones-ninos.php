<?php

    /*  Cash de variables */
    $familia_id = ( isset( $_POST['familia'] ) ) ? $_POST['familia'] : '';
    // $familia_id = 'FYYYV8pQRL'; // For Debug

    if ( $familia_id == '' )
        wp_send_json_error( 'No se configuró la familia' );

    $familia_args = array(
        'post_type'     => 'nido-familia',
        'meta_query'    => array(
            array(
                'key'   => 'wpcf-id-familia',
                'value' => $familia_id,
            )
        )
    );

    $familia_post = new WP_Query( $familia_args );

    $kids = array();
    if ( ! empty( $familia_post->posts ) ) {
        $kids_family = array();
        $familia_post_id = $familia_post->posts[0]->ID;

        /*  Obtener el primer niño de la familia */
        $kid = get_post_meta( $familia_post_id, 'wpcf-nombre-del-nino-a', true );
        $last_name = get_post_meta( $familia_post_id, 'wpcf-apellidos-del-nino-a', true );
        if ( $kid != '' )
            array_push( $kids, $kid . ' ' . $last_name );

        /*  Obtener a los demás niños */
        for ( $kid_id = 2; $kid_id <= 10; $kid_id++ ) { 
            $kid = get_post_meta( $familia_post_id, 'wpcf-nombre-del-nino-a-' . $kid_id, true );
            $last_name = get_post_meta( $familia_post_id, 'wpcf-apellidos-del-nino-a-' . $kid_id, true );
            if ( $kid != '' )
                array_push( $kids, $kid . ' ' . $last_name );
        }

        wp_send_json_success( $kids );
    }

    wp_send_json_error( $familia_post );