<?php

    /*  Se hace el cash de todas las variables */
    $nuevo_mensaje = ( isset( $_POST['nuevo_mensaje'] ) ) ? $_POST['nuevo_mensaje'] : '';
    $de_nombre = ( isset( $_POST['de_nombre'] ) ) ? $_POST['de_nombre'] : '';
    $hora = ( isset( $_POST['hora'] ) ) ? $_POST['hora'] : '';
    $remitente = ( isset( $_POST['remitente'] ) ) ? $_POST['remitente'] : '';
    $destinatario = ( isset( $_POST['destinatario'] ) ) ? $_POST['destinatario'] : '';

    /*  Si existe la información, crear el mensaje nuevo */
    if (
        $nuevo_mensaje != '' &&
        $de_nombre != '' &&
        $hora != '' &&
        $remitente != '' &&
        $destinatario != ''
        ) {

        $data = $nuevo_mensaje;

        $mensaje_post = array(
            'post_title'    => $de_nombre,
            'post_status'   => 'publish',
            'post_type'     => 'nido-mensaje',
            'meta_input'    => array(
                'wpcf-fecha-mensajes'   => $hora,
                'wpcf-de-mensajes'      => $remitente,
                'wpcf-para-mensajes'    => $destinatario,
                'wpcf-mensaje-mensajes' => $nuevo_mensaje,
                'wpcf-leido-mensajes'   => 'no'
            )
        );

        wp_insert_post( $mensaje_post );

        /*  Checar el email del destinatario */
        $args = array(
            'meta_query' => array(
                array(
                    'key'       => 'description',
                    'value'     => $destinatario,
                    'compare'   => 'LIKE'
                )
            )
        );

        $wp_users = new WP_User_Query( $args );
        $email_destinatario = $wp_users->results[0]->data->user_email;

        /*  Enviar la notificación del mensaje */
        // wp_mail( $email_destinatario, 'Tiene un mensaje nuevo', 'Mensaje Nuevo: ' . $data );

        /*  Desarchivar la conversación */
        do_action( 'nido_sacar_conversacion', $remitente, $destinatario );
        do_action( 'nido_sacar_conversacion', $destinatario, $remitente );

        global $wpdb;
        $content = $wpdb->get_var( "SELECT post_content FROM $wpdb->posts WHERE post_type='plantilla-de-email' AND post_title='Mensaje'" );
        $content = preg_replace( '/%%FROM%%/', $de_nombre, $content );
        $content = preg_replace( '/%%HOUR%%/', $hora, $content );
        $content = preg_replace( '/%%MESSAGE%%/',stripslashes( $nuevo_mensaje ), $content );

        wp_mail(
            $email_destinatario,
            __( 'Mensaje Nuevo de ' . $de_nombre, 'ukulele' ),
            $content
        );

        /*  Enviar al Front-End un success */
        wp_send_json_success( $data );
    }
    else {
        wp_send_json_error();
    }

?>