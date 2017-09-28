<?php

    $href = get_site_url() . '/panel-de-control';

    $cuido_id = apply_filters( 'nido_get_nido_id', wp_get_current_user()->ID );

?>

<!-- Dependencias para el daterangepicker -->
<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap/3/css/bootstrap.css" />
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />

<div id="nido-tod"></div>

<input type="hidden" id="nido-cuido-id" value="<?php esc_attr_e( $cuido_id ) ?>">
<input type="hidden" id="nido-start-date" value="">
<input type="hidden" id="nido-finish-date" value="">

<div class="nido-header-image">
    <a href="<?php esc_attr_e( $href ); ?>">
    <img id="nido-back-button" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-boton-atras.png" /></a>
    <img id="nido-vertical-line" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-vertical-line.png" />
    <img id="nido-mi-cuido-icon" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-reportes-header.png" />
</div>

<div class="nido-reportes-toolbar">
    <div class="nido-reportes-left-menu">
        <label class="nido-first-label">Exportar ►</label>
        <img class="nido-csv" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-reportes-csv.png"></img>
        <img class="nido-pdf" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-reportes-pdf.png"></img>
    </div>
    <div class="nido-reportes-right-menu">
        <div class="nido-seccion-individual">
            <img class="nido-sombra-individual" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-reportes-huevo-individual.png"></img>
            <img class="nido-loading-apple" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/loading_apple.gif" style="display: none">
            <label><input type="text" placeholder="Individual" id="nido-reporte-individual"> <span>▼</span></label>
        </div>
        <div class="nido-black-vertical-line"></div>
        <div class="nido-seccion-grupal nido-reportes-seccion-active">
            <img class="nido-sombre-grupal" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-reporte-huevos-grupal.png"></img>
            <label>Grupal</label>
        </div>
        <div class="nido-black-vertical-line nido-661"></div>
        <div class="nido-seccion-calendario">
            <img class="nido-sombra-calendario" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-reportes-calendario.png"></img>
            <label><input type="text" id="nido-daterangepicker"> <span class="nido-gris-reportes">▼</span></label>
        </div>
    </div>
</div>

<div class="nido-reportes-main-div">
    <div class="nido-reportes-tab1" style="display: none;">
        
    </div>
    <div class="nido-reportes-tab2">
        <div class="nido-cuido-info-wrapper">
            <div class="nido-cuido-info">
                <img class="nido-reportes-cuido-avatar" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-icono-nido.png"></img>
                <label class="nido-reportes-cuido-nombre"><?php esc_html_e( wp_get_current_user()->display_name ) ?></label>
            </div>
        </div>
        <div class="nido-reportes-numericos">
            <div class="nido-reportes-numericos-entradas nido-reportes-numericos-col-4">
                <img src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-reportes-entrada.png">
                <label class="nido-reportes-label">Entradas</label>
                <label class="nido-reportes-number" style="display: none;"></label>
                <img class="nido-loading-apple" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/loading_apple.gif">
            </div>
            <div class="nido-reportes-numericos-salidas nido-reportes-numericos-col-4">
                <img src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-reportes-salidas.png">
                <label class="nido-reportes-label">Salidas</label>
                <label class="nido-reportes-number" style="display: none;"></label>
                <img class="nido-loading-apple" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/loading_apple.gif">
            </div>
            <div class="nido-reportes-numericos-tardanzas nido-reportes-numericos-col-4">
                <img src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-reportes-tardanzas.png">
                <label class="nido-reportes-label">Tardanzas</label>
                <label class="nido-reportes-number" style="display: none;"></label>
                <img class="nido-loading-apple" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/loading_apple.gif">
            </div>
            <div class="nido-reportes-numericos-incidentes nido-reportes-numericos-col-4">
                <img src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-reportes-incidentes.png">
                <label class="nido-reportes-label">Incidentes</label>
                <label class="nido-reportes-number" style="display: none;"></label>
                <img class="nido-loading-apple" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/loading_apple.gif">
            </div>
        </div>
        <hr class="nido-reportes-separador">
        <div class="nido-reportes-diarios">
        </div>
        <div>
            <div class="nido-grafica-wrapper"><canvas id="nido-grafica-entradas"></canvas></div>
            <div class="nido-grafica-wrapper"><canvas id="nido-grafica-salidas"></canvas></div>
            <div class="nido-grafica-wrapper"><canvas id="nido-grafica-tardanzas"></canvas></div>
            <div class="nido-grafica-wrapper"><canvas id="nido-grafica-incidentes"></canvas></div>
        </div>
    </div>
</div>

<?php
    /*  Crear algunos registros para probar */
    // for ( $day = 1; $day <= 30; $day++ ) {

    //     for ( $i = 0; $i < rand( 0,9 ); $i++ ) { 
    //         $mensaje_post = array(
    //             'post_title'    => 'Salida',
    //             'post_status'   => 'publish',
    //             'post_type'     => 'nido-entrada-salida',
    //             'meta_input'    => array(
    //                 'wpcf-entrada-salida-cuido'     => 'CTGoVdbCjI',
    //                 'wpcf-familia-id'               => 'FmViKp7miL',
    //                 'wpcf-hora'                     => $day . '/6/2017 12:50:43 PM',
    //                 'wpcf-entrada-salida-telefono-principal' => '(293) 745-9237',
    //                 'wpcf-entrada-salida-familia'   => 'Galvez',
    //                 'wpcf-entrada-salida-guardian'  => 'Saul Galvez (Padre)',
    //                 'wpcf-nino-a'                   => 'Christine Galvez'
    //             )
    //         );

    //         wp_insert_post( $mensaje_post );

    //         print_r( 'Post insertado en: ' . $day . '/6/2017 12:50:43 PM' );
    //     }
    // }

    /*  Datos para prueba de incidentes */
    // for ( $day = 1; $day <= 30; $day++ ) {

    //     for ( $i = 0; $i < rand( 0,9 ); $i++ ) { 
    //         $incidente_post = array(
    //             'post_title'    => '[Incidente]: Prueba ' . $i,
    //             'post_status'   => 'publish',
    //             'post_type'     => 'nido-incidentes',
    //             'meta_input'    => array(
    //                 'wpcf-nido-incidentes-cuido'        => 'CTGoVdbCjI',
    //                 'wpcf-nido-incidentes-encargado'    => 'CTGoVdbCjI',
    //                 'wpcf-nido-incidentes-familia'      => 'FYYYV8pQRL',
    //                 'wpcf-nido-incidentes-informacion'  => 'Incidente de Prueba',
    //                 'wpcf-nido-incidentes-fecha-y-hora' => $day . '/6/2017 12:50:43 PM'
    //             )
    //         );

    //         wp_insert_post( $incidente_post );

    //         print_r( 'Post insertado en: ' . $day . '/6/2017 12:50:43 PM' );
    //     }
    // }

?>

<script type="text/javascript">
    // jQuery( '#nido-datepicker' ).datepicker();
    // jQuery( '#nido-datepicker' ).datepicker( 'hide' );
    jQuery('input#nido-daterangepicker').daterangepicker( {
        "opens": "left",
        "locale": {
            "format": "DD/MM/YYYY",
            "separator": " - ",
            "applyLabel": "Aplicar",
            "cancelLabel": "Cancelar",
            "fromLabel": "Desde",
            "toLabel": "Hasta",
            "customRangeLabel": "Personalizado",
            "weekLabel": "S",
            "daysOfWeek": [
                "Do",
                "Lu",
                "Ma",
                "Mi",
                "Ju",
                "Vi",
                "Sa"
            ],
            "monthNames": [
                "Enero",
                "Febrero",
                "Marzo",
                "Abril",
                "Mayo",
                "Junio",
                "Julio",
                "Agosto",
                "Septiembre",
                "Octubre",
                "Noviembre",
                "Diciembre"
            ],
            "firstDay": 1
        },
        ranges: {
           'Hoy': [moment(), moment()],
           'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
           'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
           'Este Mes': [moment().startOf('month'), moment().endOf('month')],
           'Mes Pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    } );
</script>