<?php
    /*
     *  En esta sección de código se cargan todos los datos que serán utilizados para el Layout de
     *  la asistencia del día de hoy.
     */

    $atts['field'] = 'id';

    /*  Ya que se ha inicializado el atributo 'field' se pide el ID del cuido actual */
    $cuido_id_actual = nido_current( $atts );

    $args = array(
        'post_type'     => 'nido-entrada-salida',
        'meta_query'    => array(
            array(
                'key'   => 'wpcf-entrada-salida-cuido',
                'value' => $cuido_id_actual,
            )
        ),
        'orderby'           => 'title',
        'order'             => 'ASC',
        'posts_per_page'    => -1
    );

    /*  Se efectúa el Query para obtener los registros de acuerdo al ID del Cuido. */
    $query = new WP_Query( $args );

    /*
     *  Se inicializa el huso horario de Puerto Rico y se obtiene el día, mes y años actuales. Esto
     *  servirá para obtener la referencia de la fecha y solamente mostrar los registros de hoy.
     */
    $timezone = -4;
    $nido_fecha_php = gmdate( "j-m-Y", time() + 3600 * ( $timezone+date( "I" ) ) );
    list( $nido_day_php, $nido_month_php, $nido_year_php ) = preg_split( '/-/', $nido_fecha_php );

    /*
     *  Se inicializan arreglos auxiliares de entrada y de salida para hacer más sencillo mostrar
     *  la información.
     */
    $registros = $query->posts;
    $entradas = array();
    $salidas = array();

    /*  Solamente se muestran registros del día de hoy hora de Puerto Rico */
    foreach ( $registros as $registro ) {
        $nido_fecha_hora = get_post_meta( $registro->ID, 'wpcf-hora', true );
        list( $fecha, $hora ) = preg_split( '/ /', $nido_fecha_hora );
        list( $nido_day, $nido_month, $nido_year ) = preg_split( '~/~', $fecha );

        if ( ( intval( $nido_day_php ) == intval( $nido_day ) ) && 
             ( intval( $nido_month_php ) == intval( $nido_month ) ) && 
             ( intval( $nido_year_php ) == intval( $nido_year ) ) ) {
    
            if ( $registro->post_title == 'Entrada' ) {
                array_push( $entradas, array( 'nido-item' => $registro, 'nido-hora' => $hora ) );
            }
            else {
                array_push( $salidas, array( 'nido-item' => $registro, 'nido-hora' => $hora ) );
            }
        }
    }

    /*
     *  Además de mostrar todos los registros de entradas y salidas de hoy se va a crear otra fun-
     *  cionalidad. Se va a crear un registro en donde se tengan solamente una lista de niños y el
     *  registro de sus entradas y salidas en la misma tarjeta.
     */

    /*  Crear una lista de niños involucrados en los registros del dia de hoy */
    $ninos_hoy = array();

    foreach ( $entradas as $entrada ) {
        for ( $i = 1; $i <= 10 ; $i++ ) { 
            $meta_key = ( $i == 1 ) ? 'wpcf-nino-a' : 'wpcf-nino-a-es-' . $i;

            $name = get_post_meta( $entrada['nido-item']->ID, $meta_key, true );

            if ( $name != '' && ! in_array( $name, $ninos_hoy ) ) {
                array_push( $ninos_hoy, $name );
            }
        }
    }

    foreach ( $salidas as $salida ) {
        for ( $i = 1; $i <= 10 ; $i++ ) { 
            $meta_key = ( $i == 1 ) ? 'wpcf-nino-a' : 'wpcf-nino-a-es-' . $i;

            $name = get_post_meta( $salida['nido-item']->ID, $meta_key, true );

            if ( $name != '' && ! in_array( $name, $ninos_hoy ) ) {
                array_push( $ninos_hoy, $name );
            }
        }
    }

    global $wpdb;
    $posts_hoy = array();

    foreach ( $ninos_hoy as $nino ) {
        /*  Generar Query */
        //$special_query = "SELECT ID FROM $wpdb->posts WHERE post_title = '" . $nino . "' AND post_type = 'nido-nino'";
        $special_query = "SELECT ID FROM $wpdb->posts, $wpdb->postmeta WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id AND $wpdb->posts.post_title = '" . $nino . "' AND $wpdb->posts.post_type = 'nido-nino' AND $wpdb->postmeta.meta_key = 'wpcf-id-de-cuido-ninos' AND $wpdb->postmeta.meta_value = '" . $cuido_id_actual . "'";

        /*  Generar Query */
        $postid = $wpdb->get_var( $special_query );

        /*  Asignar el Post para guardarlo */
        //$post = get_post( $postid );

        array_push( $posts_hoy, array( 'post_title' => $nino, 'post' => $postid ) );
    }
?>

<div class="nido-header-image">
    <img class="nido-asistencia-hoy-header" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-asistencia-hoy-imagen.png" />
</div>

<div id="nido-tod"></div>

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $( function() {
    $( "#tabs" ).tabs();
    } );
</script>

<div id="tabs" class="nido-asistencia-tabs">
    <ul>
        <li><a href="#tabs-1">Asistencia</a></li>
        <li><a href="#tabs-2">Registros</a></li>
    </ul>
    <div id="tabs-1">
        <div class="nido-asistencia-2">

            <?php if ( empty( $posts_hoy ) ): ?>

                <h3>No hay registros el día de hoy</h3>
                
            <?php endif ?>
            <?php foreach ( $posts_hoy as $post ) : ?>

                <?php
                    $estatus = get_post_meta( $post['post'], 'wpcf-estatus-de-entrada-salida', true );
                    $hora_fecha_entrada = get_post_meta( $post['post'], 'wpcf-hora-de-entrada-ninos', true );
                    $hora_fecha_salida = get_post_meta( $post['post'], 'wpcf-hora-de-salida-ninos', true );
                    $hora_entrada = '';
                    $hora_salida = '';

                    list( $fecha_entrada, $hora_entrada ) = preg_split( '/ /', $hora_fecha_entrada );

                    if ( $hora_fecha_salida != '' ) {
                        list( $fecha_salida, $hora_salida ) = preg_split( '/ /', $hora_fecha_salida );
                    }

                    if ( $estatus === 'En Cuido' )
                        $hora_salida = '--------';
                ?>

                <div class="nido-asistencia-row">
                    <div class="nido-elements">
                        <img class="nido-asistencia-avatar" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-huevo-1.png" />
                        <label class="nido-nombre"><?php esc_html_e( $post['post_title'] ) ?></label>
                        <hr>
                        <div class="nido-mismo-renglon">
                            <img class="nido-flecha-entrada" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-flecha-entrada.png" />
                            <label class="nido-hora-entrada"><?php esc_html_e( $hora_entrada ) ?></label>
                            <img class="nido-flecha-salida" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-flecha-salida.png" />
                            <label class="nido-hora-salida"><?php esc_html_e( $hora_salida ) ?></label>
                        </div>
                    </div>
                    <img class="nido-sombra-bajo" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-sombra-bajo.png">
                </div>
                
            <?php endforeach ?>
        </div>
    </div>
    <div id="tabs-2">
        <div class="nido-asistencia-hoy nido-reportes-entrada">
            <div class="nido-grupos-familiares-count">
                <img src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-huevo-entrada-asistencia-hoy.png" />
                <label id="nido-asistencia-hoy-entradas" class="nido-asistencia-hoy-count"></label>
            </div>
        </div>

        <div class="nido-family-list nido-reports">

            <?php $cont = 0; foreach ( $entradas as $entrada ): $meta_key = ''; ?>
                <?php for ( $i = 1; $i <= 10; $i++ ) : $meta_key = ( $i == 1 ) ? 'wpcf-nino-a' : 'wpcf-nino-a-es-' . $i ; ?>
                    <?php if ( get_post_meta( $entrada['nido-item']->ID, $meta_key, true ) != '' ) : ?>

                        <div class="nido-family-element nido-report-element nido-entrada">
                            <img class="nido-message-image" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-huevo-entrada-asistencia-hoy-mensaje.png" />
                            <img class="nido-asistencia-avatar" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-huevo-1.png" />
                            <label class="nido-asistencia-nombre"><?php esc_html_e( get_post_meta( $entrada['nido-item']->ID, $meta_key, true ) ) ?></label>
                            <label class="nido-asistencia-hora"><?php esc_html_e( $entrada['nido-hora'] ) ?></label>
                        </div>

                    <?php $cont++; endif ?>
                <?php endfor ?>
            <?php endforeach ?>
            <?php if ( $cont == 0 ): ?>
                            
                <div class="nido-family-element nido-report-element">
                    <label class="nido-asistencia-nombre">No hay registros de Entrada hoy</label>
                </div>

            <?php endif ?>

        </div>
        <img class="nido-sombra-bajo" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-sombra-bajo.png">

        <div class="nido-asistencia-hoy nido-reportes-salida">
            <div class="nido-grupos-familiares-count">
                <img src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-asistencia-hoy-salida.png" class="nido-imagen-salida" />
                <label id="nido-asistencia-hoy-salidas" class="nido-asistencia-hoy-count-salida"></label>
            </div>
        </div>

        <div class="nido-family-list nido-reports">

            <?php $cont = 0; foreach ( $salidas as $salida ): $meta_key = ''; ?>
                <?php for ( $i = 1; $i <= 10; $i++ ) : $meta_key = ( $i == 1 ) ? 'wpcf-nino-a' : 'wpcf-nino-a-es-' . $i ; ?>
                    <?php if ( get_post_meta( $salida['nido-item']->ID, $meta_key, true ) != '' ) : ?>

                        <div class="nido-family-element nido-report-element nido-salida">
                            <img class="nido-message-image" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-huevo-entrada-asistencia-hoy-mensaje.png" />
                            <img class="nido-asistencia-avatar" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-huevo-1.png" />
                            <label class="nido-asistencia-nombre"><?php esc_html_e( get_post_meta( $salida['nido-item']->ID, $meta_key, true ) ) ?></label>
                            <label class="nido-asistencia-hora"><?php esc_html_e( $salida['nido-hora'] ) ?></label>
                        </div>

                    <?php $cont++; endif ?>
                <?php endfor ?>
            <?php endforeach ?>
            <?php if ( $cont == 0 ): ?>
                            
                <div class="nido-family-element nido-report-element">
                    <label class="nido-asistencia-nombre">No hay registros de Salida hoy</label>
                </div>

            <?php endif ?>

        </div>
        <img class="nido-sombra-bajo" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-sombra-bajo.png">
    </div>
</div>