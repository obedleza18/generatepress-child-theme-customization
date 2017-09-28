<?php
    $cuido = wp_get_current_user();

    $cuido_id = apply_filters( 'nido_get_nido_id', $cuido->ID );

    $year = $_POST['years'];
    $month = $_POST['meses'];

    // $year = 2017;
    // $month = 6;

    if ( ! isset( $year ) || $year == '' )
        $year = date( 'Y' );

    if ( ! isset( $month ) || $month == '' )
        $month = date( 'n' );

    $days_month = date( 't', mktime( 0, 0, 0, $month, 1, $year ) );
    $entradas = array();
    $salidas = array();
    $incidentes = array();
    // var_dump( '<pre>' );
    for ( $day = 1; $day <= $days_month; $day++ ) { 
        
        $registros_query = array(
            'post_type'     => 'nido-entrada-salida',
            'meta_query'    => array(
                'relation'  => 'AND',
                array(
                    'key'   => 'wpcf-entrada-salida-cuido',
                    'value' => $cuido_id,
                ),
                array(
                    'key'    => 'wpcf-hora',
                    'value'  => "^$day/$month/$year",
                    'compare'    => 'REGEXP'
                )
            ),
            'posts_per_page' => -1
        );

        /*  Se efectúa el Query de WordPress */
        $registros = new WP_Query( $registros_query );

        $kids_in = 0;
        $kids_out = 0;
        foreach ( $registros->posts as $registro ) {

            $kids = 0;
            if ( ! empty( get_post_meta( $registro->ID, 'wpcf-nino-a' ) ) )
                 $kids++;

            for ( $kid = 2; $kid <= 10 ; $kid++ ) { 
                if ( ! empty( get_post_meta( $registro->ID, 'wpcf-nino-a-es-' . $kid ) ) )
                    $kids++;
            }

            if ( $registro->post_title === 'Entrada' ) {
                $kids_in += $kids;
            }
            else {
                $kids_out += $kids;
            }
        }
        array_push( $entradas, $kids_in );
        array_push( $salidas, $kids_out );

        $incidentes_query = array(
            'post_type'     => 'nido-incidentes',
            'meta_query'    => array(
                'relation'  => 'AND',
                array(
                    'key'   => 'wpcf-nido-incidentes-cuido',
                    'value' => $cuido_id,
                ),
                array(
                    'key'    => 'wpcf-nido-incidentes-fecha-y-hora',
                    'value'  => "^$day/$month/$year",
                    'compare'    => 'REGEXP'
                )
            ),
            'posts_per_page' => -1
        );

        /*  Se efectúa el Query de WordPress */
        $incidentes_array = new WP_Query( $incidentes_query );

        array_push( $incidentes, $incidentes_array->post_count );

        /*  Agrugar los incidentes del día por niño */
        // global $wpdb;
        // $incidentes_familias = $wpdb->get_results( "
        //     SELECT DISTINCT m1.meta_value AS familias
        //         FROM $wpdb->posts p, $wpdb->postmeta m1
        //         WHERE   p.ID = m1.post_id
        //             AND p.post_type = 'nido-incidentes'
        //             AND m1.meta_key = 'wpcf-nido-incidentes-familia';
        // " );

        // var_dump( 'Día: ' . $day );
        // var_dump( $incidentes_familias );
    }
    // var_dump( '</pre>' );

    $registros_grafica = array( 'entradas' => $entradas, 'salidas' => $salidas, 'incidentes' => $incidentes );

    wp_send_json_success( $registros_grafica );
