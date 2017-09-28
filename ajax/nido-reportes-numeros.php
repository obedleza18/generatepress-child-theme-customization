<?php

    /*  Obtener variables */
    $cuido_id = apply_filters( 'nido_get_nido_id', wp_get_current_user()->ID );
    $daterange = ( isset( $_POST['daterange'] ) ) ? $_POST['daterange'] : '';
    $kid = ( isset( $_POST['kid'] ) ) ? $_POST['kid'] : '';

    // $cuido_id = 'Ci7kCtmchA';
    // $daterange = array( '21/09/2017', '21/09/2017' );
    // $kid = 'Test Kid';

    /*  Decodificar la información del rango de fechas */
    if ( $cuido_id != '' && $daterange != '' ) {
        $daterange = json_decode( stripslashes( $daterange ) );
    }
    else {
        wp_send_json_error( 'Error: Falta información.' );
    }

    /*  Crear la expresión degular en base al rango de fechas */
    $begin = new DateTime( DateTime::createFromFormat( 'd/m/Y', $daterange[0] )->format( 'Y-m-d' ) );
    $end = new DateTime( DateTime::createFromFormat( 'd/m/Y', $daterange[1] )->format( 'Y-m-d' ) );
    $end = $end->modify( '+1 day' );

    $interval = DateInterval::createFromDateString( '1 day' );
    $period = new DatePeriod( $begin, $interval, $end );

    $regexp = '';
    $rango_fechas = array();

    $i = 0;
    foreach ( $period as $dt ) {
        if ( $i == 0 )
            $regexp .= '^' . $dt->format( "j/n/Y" );
        else
            $regexp .= '|^' . $dt->format( "j/n/Y" );
        $i++;

        array_push( $rango_fechas, $dt->format( "j/n/Y" ) );
    }

    $cpt_query = array(
        'post_type'     => array( 'nido-entrada-salida', 'nido-incidentes' ),
        'meta_query'    => array(
            'relation'  => 'OR',
            array(
                'relation'  => 'AND',
                array(
                    'key'   => 'wpcf-entrada-salida-cuido',
                    'value' => $cuido_id,
                ),
                array(
                    'key'   => 'wpcf-hora',
                    'value'  => $regexp,
                    'compare'    => 'REGEXP',
                ),
            ),
            array(
                'relation'  => 'AND',
                array(
                    'key'   => 'wpcf-nido-incidentes-cuido',
                    'value' => $cuido_id,
                ),
                array(
                    'key'   => 'wpcf-nido-incidentes-fecha-y-hora',
                    'value'  => $regexp,
                    'compare'    => 'REGEXP',
                ),
            ),
        ),
        'posts_per_page' => -1,
        'orderby'   => 'date',
        'order'     => 'ASC'
    );

    $cpt_query2 = array(
        'post_type'     => 'nido-tardanza',
        'meta_query'    => array(
            array(
                'relation'  => 'AND',
                array(
                    'key'   => 'wpcf-nido-tardanza-cuido-id',
                    'value' => $cuido_id,
                ),
                array(
                    'key'   => 'wpcf-nido-tardanza-hora-llegada',
                    'value'  => $regexp,
                    'compare'    => 'REGEXP',
                ),
            ),
        ),
        'posts_per_page' => -1,
        'orderby'   => 'date',
        'order'     => 'ASC'
    );

    $cpt_array2 = new WP_Query( $cpt_query2 );

    $cpt_array = new WP_Query( $cpt_query );

    $cpt_array_posts = array_merge( $cpt_array->posts, $cpt_array2->posts );

    $daily_data = array();
    $dates = array();

    $daily_data['T'] = array( 'e' => 0, 's' => 0, 't' => 0, 'i' => 0 );

    // var_dump( '<pre>' );
    foreach ( $cpt_array_posts as $cpt ) {
        // var_dump( get_post_meta( $cpt->ID, 'wpcf-nino-a', true ) . ' ' . get_post_meta( $cpt->ID, 'wpcf-nido-incidentes-informacion', true ) );

        /*  Continuar con la siguiente iteración si no es el niño */
        // $kids_by_check = 0;
        // $kids_by_incident = 0;
        // if ( preg_match( '/' . $kid . '/', get_post_meta( $cpt->ID, 'wpcf-nino-a', true ) ) === 0 )
        //     $kids_by_check++;
        // for ( $index = 2 ; $index <= 10; $index++ ) { 
        //     if ( preg_match( '/' . $kid . '/', get_post_meta( $cpt->ID, 'wpcf-nino-a-es-' . $index, true ) ) === 0 ) {
        //         $kids_by_check++;
        //     }
        // }
        // if ( preg_match( '/' . $kid . '/', get_post_meta( $cpt->ID, 'wpcf-nido-incidentes-informacion', true ) ) === 0 ) {
        //     $kids_by_incident++;
        // }
        
        if ( $cpt->post_title == 'Entrada' ) {
            $row = get_post_meta( $cpt->ID, 'wpcf-hora', true ) . ' ' . 'Entrada';
        }
        else if ( $cpt->post_title == 'Salida' ) {
            $row = get_post_meta( $cpt->ID, 'wpcf-hora', true ) . ' ' . 'Salida';
        }
        else if ( $cpt->post_type == 'nido-tardanza' ) {
            $row = get_post_meta( $cpt->ID, 'wpcf-nido-tardanza-hora-llegada', true ) . ' ' . 'Tardanza';
        }
        else {
            $row = get_post_meta( $cpt->ID, 'wpcf-nido-incidentes-fecha-y-hora', true ) . ' ' . 'Incidente';
        }

        if ( get_post_meta( $cpt->ID, 'wpcf-nino-a', true ) != '' ) {
            $row .= ': ' . get_post_meta( $cpt->ID, 'wpcf-nino-a', true );
        }

        for ( $index = 2 ; $index <= 10; $index++ ) {
            if ( get_post_meta( $cpt->ID, 'wpcf-nino-a-es-' . $index, true ) != '' ) {
                $row .= ', ' . get_post_meta( $cpt->ID, 'wpcf-nino-a-es-' . $index, true );
            }
        }

        if ( get_post_meta( $cpt->ID, 'wpcf-nido-incidentes-informacion', true ) != '' ) {
            $incident_info = get_post_meta( $cpt->ID, 'wpcf-nido-incidentes-informacion', true );
            $start = strpos( $incident_info, '[Incidente con ' );
            $end = strpos( $incident_info, ']: ' );
            $row .= ': ' . substr( $incident_info, $start + 15, $end - 15 );
        }

        if ( get_post_meta( $cpt->ID, 'wpcf-nido-tardanza-nombre', true ) != '' ) {
            $row .= ': ' . get_post_meta( $cpt->ID, 'wpcf-nido-tardanza-nombre', true );
        }

        $date = preg_split( '/ /', $row )[0];
        array_push( $dates, $date );

        if ( ! isset( $daily_data['D'][$date] ) ) {
            $daily_data['D'][$date] = array( 'e' => 0, 's' => 0, 't' => 0, 'i' => 0 );
        }

        if ( $kid != '' ) {
            if ( preg_match( '/' . $kid . '/', $row ) === 0 ) {
                continue;
            }
        }

        // var_dump( $row );
        // error_log( print_r( $row, true ) );

        if ( strpos( $row, 'Entrada' ) !== false ) {
            $daily_data['D'][$date]['e'] += 1;
            $daily_data['T']['e'] += 1;
        }
        else if ( strpos( $row, 'Salida' ) !== false ) {
            $daily_data['D'][$date]['s'] += 1;
            $daily_data['T']['s'] += 1;
        }
        else if ( strpos( $row, 'Tardanza' ) !== false ) {
            $daily_data['D'][$date]['t'] += 1;
            $daily_data['T']['t'] += 1;
        }
        else {
            $daily_data['D'][$date]['i'] += 1;
            $daily_data['T']['i'] += 1;
        }
    }
    // var_dump( '</pre>' );

    $daily_data_unordered = isset( $daily_data['D'] ) ? $daily_data['D'] : array();

    $dates = array_keys( $daily_data_unordered );
    $dates_datetime = array_map(
        function( $date_string ) {
            return DateTime::createFromFormat( 'd/m/Y', $date_string );
        },
        $dates
    );
    $keys = array_keys( $dates );
    array_multisort( $dates_datetime, $keys );

    $daily_data_ordered = array();
    foreach ( $keys as $index => $key ) {
        $daily_data_ordered[$dates[$key]] = $daily_data_unordered[$dates[$key]];
    }
    
    $daily_data['D'] = $daily_data_ordered;
    $daily_data['R'] = $rango_fechas;

    // var_dump( '<pre>' );
    // var_dump( $daily_data );
    // var_dump( '</pre>' );

    wp_send_json_success( $daily_data );