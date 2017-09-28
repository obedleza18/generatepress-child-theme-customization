<?php
    /*
     *  Función para cargar inicialmente las conversaciones de un Cuido. Solamente se van a cargar
     *  conversaciones que tengan mensajes nuevos y se van a ordenar teniendo en cuenta el siguiente
     *  parámetro: La primera conversación es la que tiene un mensaje más reciente.
     *
     *  Un dato del funcionamiento de este layout es que solamente se va a utilizar para un Cuido.
     */

    /*  Primer paso: Obtener usuario actual */
    $familia = wp_get_current_user();
    list( $cuido_id, $familia_id ) = preg_split( '/,/', get_user_meta( $familia->ID, 'description', true ) );

    /*  Se obtienen las conversaciones activas y ordenadas por el último mensaje */
    global $wpdb;
    $conversaciones_activas_ordenadas = $wpdb->get_results("
        SELECT MAX($wpdb->posts.ID) AS ID, $wpdb->posts.post_title, MAX($wpdb->posts.post_date) AS date_post
        FROM $wpdb->posts 
            INNER JOIN $wpdb->postmeta ON ($wpdb->posts.ID = $wpdb->postmeta.post_id) 
        WHERE 1=1 
            AND (($wpdb->postmeta.meta_key = 'wpcf-para-mensajes' AND $wpdb->postmeta.meta_value = '$familia_id')) 
            AND $wpdb->posts.post_type = 'nido-mensaje' 
            AND ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'private') 
        GROUP BY $wpdb->posts.post_title 
        ORDER BY MAX($wpdb->posts.post_date) DESC
    ");

?>

<div id="nido-tod"></div>

<div class="nido-title" style="margin-bottom: 40px; float: none; margin-left: 30px">
    <img 
        class="nido-title" 
        src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-huevo-mensajes.png"
    />
    <label class="nido-lable-title">Mensajes</label>
</div>

<div class="nido-mensajes-encabezado">
    <i class="fa fa-arrow-left" aria-hidden="true" style="display: none"></i>
    <label>Mensajes</label>
    <img 
        id="nido-crear-nueva-conversacion"
        src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-escribir-mensaje.png">
    <img style="display: none" class="nido-loading" src="<?php echo get_site_url() ?>/wp-content/plugins/formidable/images/ajax_loader.gif">
    <label class="nido-para-fields" style="display: none">Para:</label>
    <div class="ui-widget nido-para-fields" style="display: none">
        <input id="search">
    </div>
</div>

<div class="nido-buscar-mensajes">
    <div class="nido-busqueda">
        <input type="text" id="nido-buscar-familia" placeholder="Buscar Mensaje De">
        <img src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-lupa.png">
    </div>
    <div class="nido-encabezado-conversacion">
        <div class="nido-encabezado-conversacion-wrapper">
            <img src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-huevo-1.png">
            <label class="nido-titulo-conversacion">Conversación</label>
        </div>
    </div>
</div>

<div id="nido-mensajes" class="nido-mensajes">
    <ul>

    <?php 
        if ( ! empty( $conversaciones_activas_ordenadas ) ) :
            foreach ( $conversaciones_activas_ordenadas as $conversacion ) : 
                $familia_id = get_post_meta( $conversacion->ID, 'wpcf-de-mensajes', true );
                $preview_mensaje = get_post_meta( $conversacion->ID, 'wpcf-mensaje-mensajes', true );
                if( $preview_mensaje != strip_tags( $preview_mensaje ) ) {
                    $preview_mensaje = 'Documento';
                }

                if ( strlen( $preview_mensaje ) > 35 ) {
                    $preview_mensaje = substr( $preview_mensaje, 0, 35 ) . ' ...';
                }

                /*  Si la conversación está archivada, no mostrar tab */
                if ( apply_filters( 'nido_conversacion_archivada', $cuido_id, $familia_id ) ) {
                    continue;
                }

                /*  Contar mensajes */
                $mensajes_nuevos = $wpdb->get_var( "
                    SELECT COUNT( ID )
                    FROM $wpdb->posts p, $wpdb->postmeta m1, $wpdb->postmeta m2, $wpdb->postmeta m3
                    WHERE   p.ID = m1.post_id
                        AND p.ID = m2.post_id
                        AND p.ID = m3.post_id
                        AND p.post_type = 'nido-mensaje'
                        AND m1.meta_key = 'wpcf-para-mensajes'
                        AND m1.meta_value = '$familia_id'
                        AND m2.meta_key = 'wpcf-de-mensajes'
                        AND m2.meta_value = '$familia_id'
                        AND m3.meta_key = 'wpcf-leido-mensajes'
                        AND m3.meta_value = 'no';
                " );

                /*  Verificar si el mensaje es de hoy */
                $fecha_hora_ampm = get_post_meta( $conversacion->ID, 'wpcf-fecha-mensajes', true );
                list( $fecha, $hora, $ampm ) = preg_split( '/ /', $fecha_hora_ampm );
                $timezone = -4;
                $fecha_hoy = gmdate( "j-m-Y", time() + 3600 * ( $timezone+date( "I" ) ) );
                $fecha_hoy = str_replace( '-', '/', $fecha_hoy );
                list( $dia, $mes, $anio ) = preg_split( '~/~', $fecha );
                list( $dia_hoy, $mes_hoy, $anio_hoy ) = preg_split( '~/~', $fecha_hoy );
                
                $dia = (int)$dia;
                $mes = (int)$mes;
                $anio = (int)$anio;
                $dia_hoy = (int)$dia_hoy;
                $mes_hoy = (int)$mes_hoy;
                $anio_hoy = (int)$anio_hoy;
                $meses_str = array( 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre' );

                if ( $dia == $dia_hoy && $mes == $mes_hoy && $anio == $anio_hoy ) {
                    list( $horas, $minutos, $segundos ) = preg_split( '/:/', $hora );
                    $hora_display = "$horas:$minutos $ampm";
                }
                else {
                    // $hora_display = $dia . ' ' . $meses_str[ $mes - 1 ];
                    $hora_display = $dia . '/' . $mes;
                }

    ?>

            <li>
                <a href="<?php echo admin_url('admin-ajax.php') . '?action=nido_cargar_conversacion&to=' . $familia_id ?>">
                    <div class="nido-mensaje" id="<?php echo esc_html_e( $familia_id ) ?>">

                        <input type="hidden" class="nido-id-de-familia-mensajes" value="<?php echo esc_html_e( $familia_id ) ?>">

                        <div style="visibility: <?php echo ( $mensajes_nuevos ) ? 'visible' : 'hidden'; ?>">
                            <img class="nido-bubble" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-bubble.png" />
                            <label class="nido-mensajes-no-leidos"><?php echo $mensajes_nuevos ?></label>
                        </div>

                        <img class="avatar" src="<?php esc_html_e( apply_filters( 'nido_get_avatar', $familia_id ) ) ?>"/>
                        <div class="nido-after-avatar">
                            <label class="nombre-chat">
                                <?php esc_html_e( $conversacion->post_title ) ?>
                            </label>
                            <hr>
                            <label class="nido-preview"><?php esc_html_e( $preview_mensaje ) ?></label>
                        </div>
                        <label class="nido-hora-mensaje"><?php echo $hora_display ?></label>
                    </div>
                </a>
            </li>

        <?php endforeach ?>
    <?php endif ?>
    
    </ul>
</div>
<div class="nido-enviar-mensaje-wrapper">
    <div class="nido-enviar-mensaje-spacer"></div>
    <div class="nido-enviar-mensaje" style="display: none">
        <div class="nido-message-content">
            <form id="nido_forma_mensaje" action="#" method="post" autocomplete="off">
                <input type="text" id="nido_mensaje" name="nido_mensaje" placeholder="Escribe mensaje">
                <input type="hidden" id="de_nombre" value="<?php echo do_shortcode( '[nido-current field=nombre]', false ) ?>">
                <input class="nido-place-hour" type="hidden" id="hora">
                <input type="hidden" id="remitente" value="<?php echo do_shortcode( '[nido-current field=id by_role=true]', false ) ?>">
                <input type="hidden" id="destinatario">
                <input type="hidden" id="nido-attachment">
            </form>
        </div>
        <div class="nido-message-uploads"><img class="nido-adjuntar" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-documentos-mensajes.png"><img style="display: none" class="nido-loading" src="<?php echo get_site_url() ?>/wp-content/plugins/formidable/images/ajax_loader.gif"></div>
    </div>
</div>
<div class="nido-sombra-wrapper">
    <img class="nido-sombra-bajo" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-sombra-bajo.png">
</div>

<div class="nido-subir-archivos">
    <input type="file" name="files[]" class="nido-archivos" multiple />
    <input type="submit" value="subir" class="nido-boton-subir" />
</div>