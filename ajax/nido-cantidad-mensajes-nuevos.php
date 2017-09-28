<?php

    $id = apply_filters( 'nido_get_nido_id', wp_get_current_user()->ID );

    $args = array(
        'post_type'     => 'nido-mensaje',
        'meta_query'    => array(
            'relation'  => 'AND',
            array(
                'key'   => 'wpcf-para-mensajes',
                'value' => $id,
            ),
            array(
                'key'   => 'wpcf-leido-mensajes',
                'value' => 'no',
            )
        ),
        'posts_per_page' => -1
    );
    
    $query = new WP_Query( $args );

    wp_send_json_success( $query->post_count );