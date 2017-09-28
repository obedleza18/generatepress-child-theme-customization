<?php
    /*
     *  Función de AJAX para crear una conversación nueva. Esta función solamente hace una consulta
     *  para que el sistema en el Front-End sepa con quiénes se puede iniciar una conversación. Esta
     *  función solamente se ejecuta cuando el usuario da click al ícono de crear una nueva
     *  conversación.
     */

    /*  Se obtiene el usuario actual */
    $user = wp_get_current_user();

    /*  Se obtiene el ID único del usuario */
    $user_id = get_user_meta( $user->ID, 'description', true );

    /*  Si no es un cuido, es una familia o un empleado */
    if ( $user->roles[0] !== 'cuido' ) { list( $cuido_id, $user_id ) = preg_split( '/,/', $user_id ); }

    /*  Un Cuido puede hablar con Empleados o Familias. Consulta de base de datos personalizada */
    global $wpdb;
    if ( $user->roles[0] === 'cuido' ) {
        /*  Buscar a todos los usuarios con los que puede iniciar conversación */
        $usuarios_disponibles = array();
        
        $familias_disponibles_args = array(
            'post_type'     => 'nido-familia',
            'meta_query'    => array(
                array(
                    'key'   => 'wpcf-cuido-id-familia',
                    'value' => $user_id,
                )
            ),
            'posts_per_page' => -1
        );

        $familias_disponibles = new WP_Query( $familias_disponibles_args );

        $empleados_disponibles_args = array(
            'post_type'     => 'nido-empleado',
            'meta_query'    => array(
                array(
                    'key'   => 'wpcf-empleado-cuido-id',
                    'value' => $user_id,
                )
            ),
            'posts_per_page' => -1
        );

        $empleados_disponibles = new WP_Query( $empleados_disponibles_args );

        /*  Los usuarios Cuido, pueden enviar mensajes a familias */
        foreach ( $familias_disponibles->posts as $familia_post) {
            $information = array(
                'Rol'       => 'Familia',
                'ID'        => get_post_meta( $familia_post->ID, 'wpcf-id-familia', true ),
                'Nombre'    => $familia_post->post_title,
                'Avatar'    => get_post_meta( $familia_post->ID, 'wpcf-avatar-familia', true ),
            );

            array_push( $usuarios_disponibles, $information );
        }

        /*  Los usuarios Cuido, pueden enviar mensajes a empleados también */
        foreach ( $empleados_disponibles->posts as $empleado_post) {
            $information = array(
                'Rol'       => 'Empleado',
                'ID'        => get_post_meta( $empleado_post->ID, 'wpcf-id-empleado', true ),
                'Nombre'    => $empleado_post->post_title,
                'Avatar'    => get_post_meta( $empleado_post->ID, 'wpcf-avatar-empleado', true ),
            );

            array_push( $usuarios_disponibles, $information );
        }

        // wp_insert_post( $mensaje_post );


        /*  Se envián al Front-End los usuarios con los que se puede iniciar conversación */
        wp_send_json_success( $usuarios_disponibles );
    }

    if ( $user->roles[0] === 'empleado' ) {

        /*  Buscar a todos los usuarios con los que puede iniciar conversación */
        $usuarios_disponibles = array();
        
        $familias_disponibles_args = array(
            'post_type'     => 'nido-familia',
            'meta_query'    => array(
                array(
                    'key'   => 'wpcf-cuido-id-familia',
                    'value' => $cuido_id,
                )
            ),
            'posts_per_page' => -1
        );

        $familias_disponibles = new WP_Query( $familias_disponibles_args );

        $empleados_disponibles_args = array(
            'post_type'     => 'nido-empleado',
            'meta_query'    => array(
                array(
                    'key'   => 'wpcf-empleado-cuido-id',
                    'value' => $cuido_id,
                )
            ),
            'posts_per_page' => -1
        );

        $empleados_disponibles = new WP_Query( $empleados_disponibles_args );

        /*  Los usuarios Cuido, pueden enviar mensajes a familias */
        foreach ( $familias_disponibles->posts as $familia_post) {
            $information = array(
                'Rol'       => 'Familia',
                'ID'        => get_post_meta( $familia_post->ID, 'wpcf-id-familia', true ),
                'Nombre'    => $familia_post->post_title,
                'Avatar'    => get_post_meta( $familia_post->ID, 'wpcf-avatar-familia', true ),
            );

            array_push( $usuarios_disponibles, $information );
        }

        /*  Los usuarios Cuido, pueden enviar mensajes a empleados también */
        foreach ( $empleados_disponibles->posts as $empleado_post) {
            $information = array(
                'Rol'       => 'Empleado',
                'ID'        => get_post_meta( $empleado_post->ID, 'wpcf-id-empleado', true ),
                'Nombre'    => $empleado_post->post_title,
                'Avatar'    => get_post_meta( $empleado_post->ID, 'wpcf-avatar-empleado', true ),
            );

            if ( $information['ID'] !== $user_id )
                array_push( $usuarios_disponibles, $information );
        }

        $cuido_wp_id = apply_filters( 'nido_get_wp_id', $cuido_id );

        /*  Agregar el Cuido */
        $information = array(
            'Rol'       => 'Cuido',
            'ID'        => $cuido_id,
            'Nombre'    => get_userdata( $cuido_wp_id )->display_name,
            'Avatar'    => apply_filters( 'nido_get_avatar', $cuido_id ),
        );

        array_push( $usuarios_disponibles, $information );

        // wp_insert_post( $mensaje_post );


        // /*  Se envián al Front-End los usuarios con los que se puede iniciar conversación */
        wp_send_json_success( $usuarios_disponibles );
    }

    if ( $user->roles[0] === 'familia' ) {

        /*  Buscar a todos los usuarios con los que puede iniciar conversación */
        $usuarios_disponibles = array();
        
        $empleados_disponibles_args = array(
            'post_type'     => 'nido-empleado',
            'meta_query'    => array(
                array(
                    'key'   => 'wpcf-empleado-cuido-id',
                    'value' => $cuido_id,
                )
            ),
            'posts_per_page' => -1
        );

        $empleados_disponibles = new WP_Query( $empleados_disponibles_args );

        /*  Los usuarios Cuido, pueden enviar mensajes a empleados también */
        foreach ( $empleados_disponibles->posts as $empleado_post) {
            $information = array(
                'Rol'       => 'Empleado',
                'ID'        => get_post_meta( $empleado_post->ID, 'wpcf-id-empleado', true ),
                'Nombre'    => $empleado_post->post_title,
                'Avatar'    => get_post_meta( $empleado_post->ID, 'wpcf-avatar-empleado', true ),
            );

            if ( $information['ID'] !== $user_id )
                array_push( $usuarios_disponibles, $information );
        }

        $cuido_wp_id = apply_filters( 'nido_get_wp_id', $cuido_id );

        /*  Agregar el Cuido */
        $information = array(
            'Rol'       => 'Cuido',
            'ID'        => $cuido_id,
            'Nombre'    => get_userdata( $cuido_wp_id )->display_name,
            'Avatar'    => apply_filters( 'nido_get_avatar', $cuido_id ),
        );

        array_push( $usuarios_disponibles, $information );

        // wp_insert_post( $mensaje_post );


        // /*  Se envián al Front-End los usuarios con los que se puede iniciar conversación */
        wp_send_json_success( $usuarios_disponibles );
    }

    wp_send_json_error();