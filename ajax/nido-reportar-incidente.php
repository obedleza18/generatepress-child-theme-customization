<?php

    /*  Cash de variables */
    $incidente = ( isset( $_POST['incidente'] ) ) ? $_POST['incidente'] : '';
    $familia = ( isset( $_POST['familia'] ) ) ? $_POST['familia'] : '';
    $fecha_hora = ( isset( $_POST['fecha_hora'] ) ) ? $_POST['fecha_hora'] : '';

    // $incidente = '[Incidente con Obed y Milton]: Se comieron los chocolates';
    // $familia = 'FYYYV8pQRL';
    // $fecha_hora = '18/7/2017 3:31:45 PM';

    if ( $incidente == '' || $familia == '' || $fecha_hora == '' )
        wp_send_json_error( 'Falta información' );

    /*  Se obtiene el usuario actual */
    $user = wp_get_current_user();

    /*  Se obtiene el ID único del usuario */
    $cuido_id = $user_id = get_user_meta( $user->ID, 'description', true );

    /*  Si no es un cuido, es una familia o un empleado */
    if ( $user->roles[0] !== 'cuido' ) { list( $cuido_id, $user_id ) = preg_split( '/,/', $user_id ); }

    /*  Crear la estructura para el incidente */
    $incidente_post = array(
        'post_title'    => $incidente,
        'post_status'   => 'publish',
        'post_type'     => 'nido-incidentes',
        'meta_input'    => array(
            'wpcf-nido-incidentes-cuido'        => $cuido_id,
            'wpcf-nido-incidentes-encargado'    => $user_id,
            'wpcf-nido-incidentes-familia'      => $familia,
            'wpcf-nido-incidentes-informacion'  => $incidente,
            'wpcf-nido-incidentes-fecha-y-hora' => $fecha_hora
        )
    );

    wp_insert_post( $incidente_post );

    wp_send_json_success();