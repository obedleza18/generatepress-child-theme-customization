<?php
    $id = isset( $_POST['source'] ) ? $_POST['source'] : '';

    if ( $id == '' )
        wp_send_json_error();

    switch( $id[0] ) {
        case 'F': 
            $args = array(
                'post_type'     => 'nido-familia',
                'meta_query'    => array(
                    array(
                        'key'   => 'wpcf-id-familia',
                        'value' => $id,
                    )
                )
            );
            break;
        case 'E': 
            $args = array(
                'post_type'     => 'nido-empleado',
                'meta_query'    => array(
                    array(
                        'key'   => 'wpcf-id-empleado',
                        'value' => $id,
                    )
                )
            );
            break;
        case 'C': 
            $args = array(
                'post_type'     => 'nido-cuido',
                'meta_query'    => array(
                    array(
                        'key'   => 'wpcf-cuido-id',
                        'value' => $id,
                    )
                )
            );
            break;
        default:
            $args = '';
            break;
    }

    $query = new WP_Query( $args );

    $avatars = '';
    if ( ! empty( $query->posts ) && $id[0] == 'F' ) {
        $avatars = get_post_meta( $query->posts[0]->ID, 'wpcf-nido-avatares', true );
    }
    if ( ! empty( $query->posts ) && $id[0] == 'E' ) {
        $avatars = get_post_meta( $query->posts[0]->ID, 'wpcf-avatares-empleados', true );
    }
    if ( ! empty( $query->posts ) && $id[0] == 'C' ) {
        $avatars = get_post_meta( $query->posts[0]->ID, 'wpcf-avatares-cuido', true );
    }

    wp_send_json_success( $avatars );