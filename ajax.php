<?php
    
    /*
     *  Este archivo contiene las llamadas a las funciones de AJAX
     */

    add_action( 'wp_ajax_nido_cargar_conversacion', 'nido_cargar_conversacion' );
    add_action( 'wp_ajax_nido_enviar_mensaje', 'nido_enviar_mensaje' );
    add_action( 'wp_ajax_nido_actualizar_mensajes_nuevos', 'nido_actualizar_mensajes_nuevos' );
    add_action( 'wp_ajax_nido_cargar_mensajes_nuevos', 'nido_cargar_mensajes_nuevos' );
    add_action( 'wp_ajax_nido_crear_conversacion', 'nido_crear_conversacion' );
    add_action( 'wp_ajax_nido_eliminar_conversacion', 'nido_eliminar_conversacion' );
    add_action( 'wp_ajax_nido_archivar_conversacion', 'nido_archivar_conversacion' );
    add_action( 'wp_ajax_nido_actualizar_graficas', 'nido_actualizar_graficas' );
    add_action( 'wp_ajax_nido_subir_archivos', 'nido_subir_archivos' );
    add_action( 'wp_ajax_nido_reportar_incidente', 'nido_reportar_incidente' );
    add_action( 'wp_ajax_nido_opciones_ninos', 'nido_opciones_ninos' );
    add_action( 'wp_ajax_nido_reportes_numeros', 'nido_reportes_numeros' );
    add_action( 'wp_ajax_nido_opciones_ninos_cuido', 'nido_opciones_ninos_cuido' );
    add_action( 'wp_ajax_nido_opciones_avatares', 'nido_opciones_avatares' );
    add_action( 'wp_ajax_nido_cantidad_mensajes_nuevos', 'nido_cantidad_mensajes_nuevos' );
    add_action( 'wp_ajax_nido_solicitud_crear_cuenta', 'nido_solicitud_crear_cuenta' );


    function nido_cargar_conversacion()         { nido_cargar_y_morir( 'nido-conversacion.php' ); }
    function nido_enviar_mensaje()              { nido_cargar_y_morir( 'nido-enviar-mensaje.php' ); }
    function nido_actualizar_mensajes_nuevos()  { nido_cargar_y_morir( 'nido-actualizar-mensajes-nuevos.php' ); }
    function nido_cargar_mensajes_nuevos()      { nido_cargar_y_morir( 'nido-cargar-mensajes-nuevos.php' ); }
    function nido_crear_conversacion()          { nido_cargar_y_morir( 'nido-crear-conversacion.php' ); }
    function nido_eliminar_conversacion()       { nido_cargar_y_morir( 'nido-eliminar-conversacion.php' ); }
    function nido_archivar_conversacion()       { nido_cargar_y_morir( 'nido-archivar-conversacion.php' ); }
    function nido_actualizar_graficas()         { nido_cargar_y_morir( 'nido-actualizar-graficas.php' ); }
    function nido_subir_archivos()              { nido_cargar_y_morir( 'nido-subir-archivos.php' ); }
    function nido_reportar_incidente()          { nido_cargar_y_morir( 'nido-reportar-incidente.php' ); }
    function nido_opciones_ninos()              { nido_cargar_y_morir( 'nido-opciones-ninos.php' ); }
    function nido_reportes_numeros()            { nido_cargar_y_morir( 'nido-reportes-numeros.php' ); }
    function nido_opciones_ninos_cuido()        { nido_cargar_y_morir( 'nido-opciones-ninos-cuido.php' ); }
    function nido_opciones_avatares()           { nido_cargar_y_morir( 'nido-opciones-avatares.php' ); }
    function nido_cantidad_mensajes_nuevos()    { nido_cargar_y_morir( 'nido-cantidad-mensajes-nuevos.php' ); }
    function nido_solicitud_crear_cuenta()      { nido_cargar_y_morir( 'nido-solicitud-crear-cuenta.php' ); }

    function nido_cargar_y_morir( $archivo ) {
        $filepath = get_stylesheet_directory() . '/ajax/' . $archivo;

        if ( file_exists( $filepath ) ) {
            include( $filepath );
        }
        else {
            wp_send_json_error( 'Archivo no existe' );
        }

        wp_die();
    }