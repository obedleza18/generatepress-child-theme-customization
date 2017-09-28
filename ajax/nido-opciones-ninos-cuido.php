<?php

    $cuido_id = ( isset( $_POST['cuido'] ) ) ? $_POST['cuido'] : '';
    $kids = array();

    if ( $cuido_id != '' ) {

        $kids_args = array(
            'post_type'     => 'nido-nino',
            'meta_query'    => array(
                array(
                    'key'   => 'wpcf-id-de-cuido-ninos',
                    'value' => $cuido_id,
                ),
            ),
            'posts_per_page' => -1
        );

        $kids_posts = new WP_Query( $kids_args );

        foreach ( $kids_posts->posts as $kid_post ) {
            array_push( $kids, $kid_post->post_title );
        }

    }
    else
        wp_send_json_error( 'Error: Falta información' );

    wp_send_json_success( $kids );