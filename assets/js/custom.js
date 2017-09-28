/**
 *  Archivo funciones jQuery y Javascript personalizadas para Child Theme
 *
 *  Este archivo es leído por WordPress como funciones personalizadas para el tema. Este archivo
 *  contiene todas las dependencias de archivos adicionales y el código necesario para modificar
 *  la funcionalidad de WordPress. En este archivo se encuentran las funciones que se ejecutan del
 *	lado del cliente para el comportamiento del website.
 *
 *  @link               http://nidowp.miopr.com/
 *  @since              1.0.0
 *  @package            Nido
 *
 *  @wordpress-child-theme-js-functions
 *
 *  Nombre de Proyecto: Nido
 *  URL del Proyecto:   http://nidowp.miopr.com/
 *  Descripción:        Funciones Personalizadas JS del Tema
 *  Versión:            1.0.0
 *  Autor:              Máximo Obed Leza Correa (TUNEL)
 *  URL del Autor:      http://portafoliomax.com
 *  License:            GPL-2.0+
 *  License URI:        http://www.gnu.org/licenses/gpl-2.0.txt
 */

( function( $ ) {
    //'use strict';

    /*
     *  Las funciones que se van a realizar como parte del proyecto se efectúan cuando el documento
     *  ha sido completamente cargado. En cada página se va a llamar la función ready. Sin embargo
     *  solamente se van a ejecutar las funciones correspondientes cuando se encuentren los
     *  elementos en el documento.
     */

    $( document ).ready( function() {

        function nido_appear( selector ) {
            selector.css( 'display', 'none' );
            selector.css( 'visibility', 'visible' );
            selector.fadeIn();
        }

        nido_appear( $( '.nido-imagen-dashboard' ) );

        /*  Inicializar el estado de los botones de entrada y salida */
        $( '#nido-boton-entrada' ).css( 'opacity', '0.5' );
        $( '#nido-boton-salida' ).css( 'opacity', '0.5' );
        $( '#nido-boton-entrada' ).off( 'click', nido_entrada_click );
        $( '#nido-boton-salida' ).off( 'click', nido_salida_click );

        /*
         *  Función que oculta los campos marcados como "No Asignado" en las formas. Esta función
         *  oculta los campos correspondientes a los guardianes.
         */
        $( '.nido-mismo-renglon-2' ).each(function(){
            if ( ~ $( this ).text().indexOf( 'No Asignado' ) )
                $( this ).hide();
            else
                $( this ).show();
        } );

        /*
         *  Función que oculta los campos marcados como "No Asignado" en las formas. Esta función
         *  oculta los campos correspondientes a los niños.
         */
        $( '.nido-mismo-renglon-ninos' ).each(function(){
            if ( ~ $( this ).text().indexOf( 'No Asignado' ) )
                $( this ).hide();
            else
                $( this ).show();
        } );

        /*
         *  Función para mostrar la hora en las páginas de administración. Hay que tomar en cuenta
         *  que esta función se efectúa cada segundo y solamente va a funcionar si trabaja en
         *  conjunto con el shortcode de wordpress llamado [nido-tod]. Como alternativa, se puede
         *  utilizar directamente el código html <div id="nido-tod"></div> y esta función automáti
         *  camente se encargará de reemplazar el html interno del div y agregar la hora actual.
         */
        var nido_tod = function () {
    		var nido_date = new Date();
    		var dd = nido_date.getDate();
    		var MM = nido_date.getMonth() + 1;
    		var yyyy = nido_date.getFullYear();
    		var hh = nido_date.getHours();
    		var mm = nido_date.getMinutes();
    		var ss = nido_date.getSeconds();
            var ampm = ( hh >= 12) ? 'PM' : 'AM';
            hh = hh % 12;
            hh = ( hh ) ? hh : 12;
            mm = ( mm < 10 ) ? '0' + mm : mm;
            ss = ( ss < 10 ) ? '0' + ss : ss;

            var datetime = dd + '/' + MM + '/' + yyyy + ' ' + hh + ':' + mm + ':' + ss + ' ' + ampm;

    		$( '#nido-tod' ).html( datetime );

            $( '.nido-place-hour input' ).val( datetime );

            $( '.nido-place-hour' ).val( datetime );
    	}

        /*  Cuando el documento está listo se genera la primer iteración de la función de tiempo */
        nido_tod();

        /*  Se configura el intervalo para la función de tiempo cada segundo -> 1000 milisegundos */
        setInterval( nido_tod, 1000 );

        /*  Función para configurar los placeholders de algunas formas de Formidable */
        setTimeout( function(){
            $( '#frm_field_655_container span' ).html( 'Buscar familia...' );
            $( '#frm_field_655_container select option:selected' ).html( 'Buscar familia...' );
        }, 1000 );

        /*
         *  Funciones para programar la funcionalidad de los botones que seleccionan a los niños
         *  para entrar o Salir del Cuido (Eventos de click). En la siguiente función se hace uso
         *  de una expresión regular para determinar todos los elementos con clase que inicie en
         *  nido-seleccion- la cual indica la imagen de un huevo con el check mark para elegir
         *  niños para salir o entrar de un cuido.
         */
        var id = 0;
        var status = [];
        $( '[class^=nido-seleccion-]' ).each(

            function () {
                var id_str = 'field_h9qlt-' + id;
                var id_label = 'nido-nino-' + id +'-estatus';
                var str_label = document.getElementById( id_label ).textContent;

                /*  Style of labels */
                if ( ~ str_label.indexOf( 'En Cuido' ) ) {
                    document.getElementById( id_label ).style.color = '#03d0b1';
                }
                else if ( ~ str_label.indexOf( 'No en Cuido' ) ) {
                    document.getElementById( id_label ).style.color = '#ff6471';
                }

                /*  En las siguientes líneas de código se configuran los eventos de click */
                $( this ).on( 'click', function () {

                    var seleccion = $( this ).attr( 'src' );
                    
                    if ( seleccion == nido_custom_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/nido-huevo-vacio-1.png' ) {
                        $( this ).attr( 'src', nido_custom_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/nido-huevo-check-1.png' );
                        document.getElementById( id_str ).checked = true;

                        /*  Función que actualiza la lista de estatus a evaluar (agregar) */
                        if ( ~ str_label.indexOf( 'En Cuido' ) ) {
                            status.push('En Cuido');
                        }
                        else if ( ~ str_label.indexOf( 'No en Cuido' ) ) {
                            status.push('No en Cuido');
                        }

                    }
                    else {
                        $( this ).attr( 'src', nido_custom_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/nido-huevo-vacio-1.png' );
                        document.getElementById( id_str ).checked = false;

                        /*  Función que actualiza la lista de estatus a evaluar (remover) */
                        if ( ~ str_label.indexOf( 'En Cuido' ) ) {
                            var index = status.indexOf( 'En Cuido' );
                            if (index > -1) {
                                status.splice( index, 1 );
                            }
                        }
                        else if ( ~ str_label.indexOf( 'No en Cuido' ) ) {
                            var index = status.indexOf( 'No en Cuido' );
                            if (index > -1) {
                                status.splice( index, 1 );
                            }   
                        }
                    }

                    /*
                     *  Evaluar el arreglo de estatus. La forma a evaluar será la siguiente: si
                     *  todos los elementos son iguales, entonces se habilitará la opción que
                     *  indique la única acción que se puede realizar y se deshabilitará la otra.
                     *
                     *  Por ejemplo: Si todos los niños están fuera del cuido, entonces la opción
                     *  que se habilitará será la de Entrada de niños. En caso de que los elementos
                     *  del arreglo de estatus sean diferentes al menos por uno, se deshabilitarán
                     *  los dos botones.
                     */
                    var flag = false;
                    var allowed_action = -1;

                    /*  Iniciarlizar el estado de los botones */
                    $( '#nido-boton-entrada' ).css( 'opacity', '0.5' );
                    $( '#nido-boton-salida' ).css( 'opacity', '0.5' );
                    $( '#nido-boton-entrada' ).off( 'click', nido_entrada_click );
                    $( '#nido-boton-salida' ).off( 'click', nido_salida_click );

                    if ( status.length > 0 ) {
                        var temp = status[0];

                        for (var i = status.length - 1; i >= 0; i--) {
                            if ( status[i] == temp ) {
                                flag = true;
                                allowed_action = ( status[i] == 'En Cuido') ? 1 : 0;
                                continue;
                            }
                            else {
                                flag = false;
                                allowed_action = -1;
                                break;
                            }
                        }
                    }

                    if ( flag != false && allowed_action > -1) {
                        switch( allowed_action ) {
                            case 0: {
                                $( '#nido-boton-entrada' ).on( 'click', nido_entrada_click );
                                $( '#nido-boton-salida' ).off( 'click', nido_salida_click );
                                $( '#nido-boton-entrada' ).css( 'opacity', '1.0' );
                                $( '#nido-boton-salida' ).css( 'opacity', '0.5' );
                                break;
                            }
                            case 1: {
                                $( '#nido-boton-entrada' ).off( 'click', nido_entrada_click );
                                $( '#nido-boton-salida' ).on( 'click', nido_salida_click );
                                $( '#nido-boton-entrada' ).css( 'opacity', '0.5' );
                                $( '#nido-boton-salida' ).css( 'opacity', '1.0' );
                                break;
                            }
                            default: {
                                $( '#nido-boton-entrada' ).off( 'click', nido_entrada_click );
                                $( '#nido-boton-salida' ).off( 'click', nido_salida_click );
                                $( '#nido-boton-entrada' ).css( 'opacity', '0.5' );
                                $( '#nido-boton-salida' ).css( 'opacity', '0.5' );
                                break;
                            }
                        }
                    }
                    
                } );

                id++;
            }
        );

        /*
         *  Esta función tiene una funcionalidad parecida a la función previa. Solamente que esta
         *  se encarga de configurar los eventos de click y de cambios de imágenes para los
         *  guardianes.
         */
        id = 0;
        $( '[id^=nido-seleccion-c-]' ).each(
            function () {
                var id_str = 'field_biz4a-' + id;

                $( this ).on( 'click',
                    function () {
                        clearAll();
                        var seleccion = $( this ).attr( 'src' );
                        $( this ).attr( 'src', nido_custom_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/nido-box-check.png' );
                        document.getElementById( id_str ).checked = true;
                    }
                );

                id++;
            }
        );

        /*
         *  Las funciones a continuacón están programadas para poder coordinar el funcionamiento
         *  deseado con el funcionamiento de Formidable. Al crear esta forma de Formidable, el
         *  usuario tiene que seleccionar si desea hacer una entrada o una salida y después dar
         *  click en el botón de Submit. Para hacer esto más automático, una vez que el usuario
         *  da click en el huevo de entrada o de salida, detrás de escenas lo que en realidad pasa
         *  es que se selecciona la entrada o salida respectivamente y después automáticamente se
         *  da click en el botón de Sumit.
         */

        function nido_entrada_click() {
            $( '#field_fk7lr-0' ).prop( 'checked', true );
            $( '.frm_submit input' ).click();
        }

        function nido_salida_click() {
            $( '#field_fk7lr-1' ).prop( 'checked', true );
            $( '.frm_submit input' ).click();
        }

        /*
         *  Función para mostrar en la Forma de información del Cuido dinámicamente los días que
         *  trabaja el Cuido.
         */

        $( '#nido-horario-hidden-info a' ).each(
            function () {
                var day = $( this ).text();
                var style = 'background-color: #777777; color: white;';

                switch ( day[0] ) {
                    case 'L':
                        $( '#nido-lunes' ).attr( 'style', style );
                        break;
                    case 'M':
                        if ( day[1] == 'a' ) {
                            $( '#nido-martes' ).attr( 'style', style );
                        }
                        else {
                            $( '#nido-miercoles' ).attr( 'style', style );
                        }
                        break;
                    case 'J':
                        $( '#nido-jueves' ).attr( 'style', style );
                        break;
                    case 'V':
                        $( '#nido-viernes' ).attr( 'style', style );
                        break;
                    case 'S':
                        $( '#nido-sabado' ).attr( 'style', style );
                        break;
                    case 'D':
                        $( '#nido-domingo' ).attr( 'style', style );
                        break;
                    default: break;
                }
            }
        );


        /*
         *  La siguientes 2 funciones son para mostrar los campos que están ocultos de la dirección
         *  de los Cuidos. Dependiendo de qué dirección es la que este activa, es la que se va a
         *  mostrar en la Vista.
         */
        $( '.nido-dir-mapa' ).each(
            function () {
                var str = $( this ).text();

                str = str.substring( str.indexOf( ':' ) + 2 );

                if ( str != '' )
                    $( this ).show();
            }
        );

        $( '.nido-dir' ).each(
            function () {
                var str = $( this ).text();

                str = str.substring( str.indexOf( ':' ) + 2 );

                if ( str != '' )
                    $( this ).show();
            }
        );

        /*
         *  Función para configurar el evento de click en la imagen de la lupa cuando el usuario
         *  quiere buscar Grupos Familiares
         */

        $( '#frm_field_676_container #nido-lupa' ).on( 'click',
            function () {
                $( this ).attr( 'class', 'frm_ajax_loading' );
                $( this ).attr( 'src', nido_custom_object.site_url + '/wp-content/plugins/formidable/images/ajax_loader.gif' );
                $( '.page-id-1161 input#field_jsx19' ).css( 'width', '369px' );
                $( '.page-id-1161 img#nido-lupa' ).css( {'height': '11px', 'padding': '22px 15px'} );
                $( '#frm_form_27_container .frm_submit input' ).click();
            }
        );

        /*
         *  Función para configurar el comportamiento del Wrapper que contiene al huevo de Añadir
         *  un Grupo Familiar.
         */

        $.fn.followTo = function ( pos ) {
            var $this = this,
                $window = $( window );

            $window.scroll( function ( e ) {
                if ( $window.scrollTop() < pos ) {
                    $this.css( {
                        'position': 'relative',
                        'bottom': '0',
                        'left': '0',
                        'margin-left': '0'
                    } );
                } else {
                    $this.css( {
                        'position': 'fixed',
                        'bottom': '20px',
                        'left': '50%',
                        'margin-left': '-100px'
                    } );
                }
            } );
        };

        $( '.nido-wrapper' ).followTo( 70 );

        /*
         *  Función para programar los eventos de click de los elementos en forma de lapiz (el que
         *  está dentro de un huevo). Esta función va a ligar a cada imagen del lapiz a el
         *  respectivo link de edición.
         */

        $( '[class^=nido-edit-]' ).on( 'click',
            function () {
                var id = $( this ).attr( 'class' );

                window.location.replace( $( '#' + id + ' a' ).attr( 'href' ) );
            }
        );

        $( '[class^=frm_delete_]' ).on( 'click',
            function () {
                var id = $( this ).attr( 'class' );

                $( '#' + id ).click();
            }
        );

        /*
         *  Esta función es para configurar el evento de click para Añadir Guardianes
         */

        $( '[name^=nido-add-guardian-]' ).on( 'click',
            function () {
                var id = $( this ).attr( 'name' );
                $( '#' + id + ' input' ).first().click();
            }
        );

        /*
         *  Estas funciones son para configurar los eventos de click para agregar teléfonos
         */

        nido_eventos_telefonos( '686', '684' );
        nido_eventos_telefonos( '688', '693' );

        function nido_eventos_telefonos( container1, container2 ) {

            $( '#frm_field_'+container1+'_container .nido-agregar-telefono' ).on( 'click',
                function () {
                    $( '#frm_field_'+container1+'_container .frm_radio label' ).first().click();
                    $( '#frm_field_'+container1+'_container' ).hide();
                }
            );

            $( '#frm_field_'+container2+'_container img' ).on( 'click',
                function () {
                    $( '#frm_field_'+container1+'_container .frm_radio label' ).last().click();
                    $( '#frm_field_'+container1+'_container' ).show();
                }
            );
        }

        /*  Funciones para el primer niño */
        nido_set_click_events( '704', '519', '520', '0' );
        nido_set_click_events( '530', '427', '428', '519' );
        nido_set_click_events( '438', '440', '441', '427' );
        nido_set_click_events( '451', '453', '454', '440' );
        nido_set_click_events( '464', '467', '468', '453' );
        nido_set_click_events( '478', '480', '481', '467' );
        nido_set_click_events( '491', '493', '494', '480' );
        nido_set_click_events( '504', '506', '507', '493' );
        nido_set_click_events( '517', '606', '607', '506' );
        nido_set_click_events( '617', '532', '594', '606' );
        nido_set_click_events( '800', '70', '72', '0' );
        nido_set_click_events( '318', '299', '300', '70' );
        nido_set_click_events( '420', '325', '326', '299' );
        nido_set_click_events( '421', '344', '345', '325' );
        nido_set_click_events( '422', '363', '364', '344' );
        nido_set_click_events( '423', '382', '383', '363' );
        nido_set_click_events( '424', '401', '402', '382' );

        /*
         *  Esta función configura los eventos de click para los niños de las Familias. El mismo
         *  código será utilizado para los eventos de click de los Guardianes. Estos eventos de
         *  click muestran algunos elementos mientras que ocultan otros.
         */
        function nido_set_click_events( container1, container2, container3, container4 ) {

            $( '#frm_field_' + container2 + '_container h3.frm_trigger' ).on( 'change',
                function () {
                    alert( 'Hello' );
                }
            );

            if ( $( '#frm_field_' + container2 + '_container h3.frm_trigger' ).hasClass( 'active' ) ) {
                $( '#frm_field_' + container2 + '_container .nido-collapse' ).css( { 'background-position-x' : "left" } );
            }
            else {
                $( '#frm_field_' + container2 + '_container .nido-collapse' ).css( { 'background-position-x' : "-26px" } );
            }

            /*  Contenedor con el botón para agregar niño */
            $( '#frm_field_' + container1 + '_container .nido-add-kid' ).on( 'click',
                function() {
                    $( '#frm_field_' + container1 + '_container .frm_radio label' ).first().click();
                    $( '#frm_field_' + container1 + '_container' ).slideUp();

                    //$( '#frm_field_' + container4 + '_container .nido-cross' ).fadeOut();
                }
            );

            /*  Contenedor con la tarjeta del niño */
            // $( '#frm_field_' + container2 + '_container .nido-cross' ).on( 'click',
            //     function() {
            //         $( '#frm_field_' + container1 + '_container .frm_radio label' ).last().click();
            //         $( '#frm_field_' + container1 + '_container' ).show();

            //         //$( '#frm_field_' + container4 + '_container .nido-cross' ).fadeIn();
            //     }
            // );

            /*  Contenedor con la tarjeta del niño */
            $( '#frm_field_' + container2 + '_container .nido-collapse' ).on( 'click',
                function() {
                    $( '#frm_field_' + container2 + '_container h3.frm_trigger' ).click();
                    if ( $( '#frm_field_' + container2 + '_container h3.frm_trigger' ).hasClass( 'active' ) ) {
                        $( '#frm_field_' + container2 + '_container .nido-collapse' ).css( { 'background-position-x' : "left" } );
                    }
                    else {
                        $( '#frm_field_' + container2 + '_container .nido-collapse' ).css( { 'background-position-x' : "-26px" } );
                    }
                }
            );

            /*  Contenedor del nombre que va a aparecer en la tarjeta del niño */
            $( '#frm_field_' + container3 + '_container input' ).on( 'change',
                function () {
                    $( '#frm_field_' + container2 + '_container .nido-name' ).text( $( this ).val() );
                }
            );

            $( '#frm_field_' + container2 + '_container .nido-name' ).text(
                $( '#frm_field_' + container3 + '_container input' ).val()
            );
        }

        /*  Función para evento de click para botón de guardar */
        $( '.nido-submit-button' ).on( 'click',
            function () {
                $( '.frm_submit input' ).click();
            }
        );

        /*  Función para contar la cantidad de familias en los resultados */
        var count = 0;
        $( '.nido-family-list .nido-family-element' ).each(
            function () {
                count++;
                $( '#nido-grupos-familiares-count' ).text( count );
            }
        );

        /*  Funciones para contar la cantidad de entradas y salidas */
        var count = 0;
        $( '.nido-entrada' ).each(
            function () {
                count++;
                $( '#nido-asistencia-hoy-entradas' ).text( count );
            }
        );

        /*  Funciones para contar la cantidad de entradas y salidas */
        var count = 0;
        $( '.nido-salida' ).each(
            function () {
                count++;
                $( '#nido-asistencia-hoy-salidas' ).text( count );
            }
        );

        /*  Configurar evento de click para que los usuarios puedan modificar su información */
        $( '.page-id-1159 #nido-pencil' ).on( 'click',
            function () {
                window.location = $( '.page-id-1159 #nido-edit-link a' ).attr( 'href' );
            }
        );

        /*  Configurar clicks de los horarios */
        $( '[class^=nido-dia]' ).on( 'click',
            function() {
                var element = $( '#field_211-' + $( this ).attr( 'name' ) );
                var checkbox = $( '#nido-dia-' + $( this ).attr( 'name' ) );
                checkbox.css( 'background-color', 'white' );
                checkbox.css( 'color', '#666' );

                element.click();

                if ( element.attr( 'checked' ) != null ) {
                    if ( element.attr( 'checked' ).length > 0 ) {
                        checkbox.css( 'background-color', '#777' );
                        checkbox.css( 'color', 'white' );
                    }
                }
            }
        );

        /*  Configurar eventos de click para editar o eliminar Empleados */
        // $( '[name^=nido-pencil-]' ).on( 'click',
        //     function() {
        //         alert( '.' + $( this ).attr( 'name' ) + ' a' );
        //         $( '.' + $( this ).attr( 'name' ) + ' a' ).click();
        //     }
        // );

        // $( '.nido-cross' ).on( 'click',
        //     function() {
        //         $( '#frm_delete_' + $( this ).attr( 'name' ) ).click();
        //     }
        // );

        // $( '.nido-kid-row .nido-cross' ).each( function() {
        //     console.log( $( this ) );
        // } );

        /* Automaticamente hacer el scroll hacia abajo */
        var height = 0;
        $( '.nido-mensaje-conversacion' ).each( function( i, value ) {
            height += parseInt( $( this ).height() );
        } );

        height += '';

        $( '.nido-conversacion' ).animate( {scrollTop: height} );

        /*
         *  Funciones para la búsqueda de mensajes
         */

        $( 'input#nido-buscar-familia' ).on( 'keyup', nido_encuentra_mensaje );
        $( '.nido-busqueda img' ).on( 'click', nido_encuentra_mensaje );


        /*
         *  Funciones Auxiliares
         *  
         *  La siguiente función es para poner en blanco todas las casillas de los guardianes
         *  cuando el usuario decida elegir a otro guardián.
         */
        function clearAll() {
            for (var i = 0; i <= 6; i++) {
                var id_str = 'nido-seleccion-c-' + i;

                document.getElementById( id_str ).src = 
                    nido_custom_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/nido-box.png';
            }
        }

        function eventFire(el, etype){
            if (el.fireEvent) {
                el.fireEvent('on' + etype);
            }
            else {
                var evObj = document.createEvent('Events');
                evObj.initEvent(etype, true, false);
                el.dispatchEvent(evObj);
            }
        }

        $( '#nido-test' ).on( 'click',
            function () {
                $( '.nido-accordion' ).slideToggle();
            }
        );

        function nido_encuentra_mensaje( event ) {

            var index = 0;

            $( '[role=tab]' ).each( function() {
                var nombre = $( this ).find( 'label.nombre-chat' ).html().trim().toLowerCase();
                var search = $( 'input#nido-buscar-familia' ).val().toLowerCase();
                var nombre_sanitizado;

                /*  Quitar los acentos del nombre para comparación */
                nombre_sanitizado = nombre.
                    replace( '\xE1', 'a' ).
                    replace( '\xE9', 'e' ).
                    replace( '\xED', 'i' ).
                    replace( '\xF3', 'o' ).
                    replace( '\xFA', 'u' ).
                    replace( '\xC1', 'A' ).
                    replace( '\xC9', 'E' ).
                    replace( '\xCD', 'I' ).
                    replace( '\xD3', 'O' ).
                    replace( '\xDA', 'U' );

                if ( ~ nombre_sanitizado.indexOf( search ) ) {
                    $( this ).show();
                    // $( '#nido-mensajes' ).tabs( { active: index } );
                }
                else {
                    $( this ).hide();
                }

                index++;

            } );

        }


        /*
         *  Funciones para configurar los Mensajes
         */

        $( '#nido-mensajes li' ).on( 'click',
            function() {
                var id = $( this ).find( 'input.nido-id-de-familia-mensajes' ).val();
                var tab = $( this ).find( 'input.nido-tab-mensajes' ).val();
                $( '.nido-enviar-mensaje .nido-message-content input#nido_mensaje' ).val( '' );
                $( '.nido-enviar-mensaje' ).show();
                $( 'input#destinatario' ).val( id );

                /*
                 *  Funcionalidad para móvil
                 */

                if ( $( window ).width() <= 768 ) {
                    $( '.nido-buscar-mensajes .nido-busqueda' ).hide();
                    $( '.nido-buscar-mensajes .nido-encabezado-conversacion' ).show();
                    $( '.nido-mensajes-encabezado .fa.fa-arrow-left' ).show();
                }
            }
        );

        $( '.nido-enviar-mensaje .nido-message-content input' ).bind( 'keyup',
            function( event ) {
                if ( event.keyCode == 13 && $( this ).val() != '' ) {
                    /*  Muestra loading */
                    $( '.nido-message-uploads img' ).hide();
                    $( '.nido-message-uploads .nido-loading' ).show();

                    // $( '#nido_forma_mensaje' ).submit();
                    // $( this ).unbind();
                }
            }
        );

        function scrollDown() {
            var height = 0;
            $( ".nido-contenido-mensaje" ).each( function( i, value ) {
                height += parseInt( $( this ).height() );
            } );

            height += "";

            $( ".nido-contenido-wrapper" ).animate( {scrollTop: height} );

            $( '#nido_mensaje' ).val( '' );
        }

        /*
         *  Configuración de los mensajes para móvil
         */

        $( '.nido-mensajes-encabezado .fa.fa-arrow-left' ).on( 'click', function() {
            $( 'div#nido-mensajes' ).tabs( 'option', 'active', false );
            $( '.nido-enviar-mensaje' ).hide();
            $( '.nido-buscar-mensajes .nido-busqueda' ).show();
            $( '.nido-buscar-mensajes .nido-encabezado-conversacion' ).hide();
            $( this ).hide();
        } );

        $( window ).scroll( function() {

            var scr = $( this ).scrollTop();

            $( '.nido-mensajes-encabezado' ).css( 'position', 'relative' );
            $( '.nido-buscar-mensajes' ).css( {
                    'position' : 'inherit',
                    'margin' : 'auto'
            } );
            $( '#nido-mensajes' ).css( 'padding-top', '0' );
            $( '.nido-busqueda' ).css( 'margin', 'auto' );
            $( '.nido-encabezado-conversacion' ).css( 'margin', 'auto' );
            if ( scr > 115 && $( this ).width() <= 768 ) {
                $( '.nido-mensajes-encabezado' ).css( {
                    'position' : 'fixed',
                    'top' : '0',
                    'z-index' : '10',
                    'left' : '50%',
                    'margin-left' : '-150px'
                } );
                $( '.nido-buscar-mensajes' ).css( {
                    'position' : 'fixed',
                    'top' : '40px',
                    'z-index' : '10',
                    'left' : '50%',
                    'margin-left' : '-150px'
                } );
                $( '.nido-busqueda' ).css( 'margin', '0' );
                $( '.nido-encabezado-conversacion' ).css( 'margin', '0' );
                $( '#nido-mensajes' ).css( 'padding-top', '80px' );
            }

            // console.log( $( this ).scrollTop() );
            // console.log( $( this ).height() );
        } );


        /*
         *  Generate PDF and CSV
         */

        $( '.nido-pdf' ).on( 'click', function() {
            var doc = new jsPDF({
              orientation: 'landscape',
              unit: 'in',
              format: [4, 2]
            })

            doc.text($( '.nido-reportes-cuido-nombre' ).text(), 1, 1)
            doc.save('two-by-four.pdf')
        } );

        $( '.nido-csv' ).on( 'click', function() {
            var data = 'Row 1 Column 1,Row 1 Column 2\nRow 2 Column 1,Row 2 Column 2';
            //var data = [['Test', 'Data'],['Test2', 'Data2']];
            var filename = 'test.csv';
            var blob = new Blob( [data], {type: 'text/csv'} );
            if(window.navigator.msSaveOrOpenBlob) {
                window.navigator.msSaveBlob( blob, filename );
            }
            else{
                var elem = window.document.createElement('a');
                elem.href = window.URL.createObjectURL( blob );
                elem.download = filename;        
                document.body.appendChild( elem );
                elem.click();        
                document.body.removeChild( elem );
            }
        } );

        $( '.nido-seccion-individual' ).on( 'click', function() {
            $( '.nido-seccion-grupal' ).removeClass( 'nido-reportes-seccion-active' );
            $( this ).find( 'label' ).css( 'color', '#808080' );
            $( this ).addClass( 'nido-reportes-seccion-active' );
        } );

        $( '.nido-seccion-grupal' ).on( 'click', function() {
            $( '.nido-seccion-individual' ).removeClass( 'nido-reportes-seccion-active' );
            $( '.nido-seccion-individual' ).find( 'label' ).css( 'color', '#c0c0c0' );
            $( this ).addClass( 'nido-reportes-seccion-active' );
        } );

        $( '.nido-edad input' ).on( 'change', function() {
            var edad_string = $( this ).val();
            var edad = parseInt( edad_string );

            // console.log( $( this ).attr('id') );

            if ( edad > 0 && edad_string.indexOf( 'mes' ) === -1 ) {
                $( this ).val( edad + ' a\u00f1o' );

                if ( edad != 1 ) {
                    $( this ).val( $( this ).val() + 's' );
                }
            }
            else {
                if ( edad < 0 ) {
                    $( this ).val( 'Edad inv\u00e1lida' );
                }
                else {
                    var birthdate = moment( 
                        $( this ).parent().parent().parent().find( '.nido-fecha-nacimiento input' ).val(),
                        'MM/DD/YYYY'
                    );
                    // console.log( $( this ).attr('id') );
                    var today = moment();
                    edad = Math.floor( moment( today ).diff( birthdate, 'months', true ) );

                    $( this ).val( edad + ' mes' );

                    if ( edad != 1 )
                        $( this ).val( $( this ).val() + 'es' );
                }
            }
        } );

        $( '.nido-empleados .nido-cross' ).on( 'click', function() {
            $( this ).parent().find( '.frm_delete_link' ).click()
        } );

    } );

    /*
     *  Elementos que se tienen que verificar cuando todos los assets han sido cargados.
     */

    jQuery( window ).load(
        function () {

            nido_check_buttons( '704', '519', '427' );
            nido_check_buttons( '530', '427', '440' );
            nido_check_buttons( '438', '440', '453' );
            nido_check_buttons( '451', '453', '467' );
            nido_check_buttons( '464', '467', '480' );
            nido_check_buttons( '478', '480', '493' );
            nido_check_buttons( '491', '493', '506' );
            nido_check_buttons( '504', '506', '606' );
            nido_check_buttons( '517', '606', '532' );
            nido_check_buttons( '617', '532', null );
            nido_check_buttons( '800', '70', '299' );
            nido_check_buttons( '318', '299', '325' );
            nido_check_buttons( '420', '325', '344' );
            nido_check_buttons( '421', '344', '363' );
            nido_check_buttons( '422', '363', '382' );
            nido_check_buttons( '423', '382', '401' );
            nido_check_buttons( '424', '401', null );

            /*  Cuando se carga la información de la Forma de la Familia se configuran botones */
            function nido_check_buttons( container1, container2, container3 ) {
                if ( $( '#frm_field_' + container2 + '_container' ).is( ':visible' ) ) {
                    $( '#frm_field_' + container1 + '_container' ).hide();
                }

                if ( container3 != null ) {
                    if ( $( '#frm_field_' + container3 + '_container' ).is( ':visible' ) ) {
                        //$( '#frm_field_' + container2 + '_container .nido-cross' ).hide();
                    }
                }
            }

            nido_check_telefonos( '686', '684' );
            nido_check_telefonos( '688', '693' );

            /*  Oculta botones de familia en ciertos casos */
            function nido_check_telefonos( container1, container2 ) {
                if ( $( '#frm_field_' + container2 + '_container' ).is( ':visible' ) ) {
                    $( '#frm_field_' + container1 + '_container' ).hide();
                }
            }

            /*  Configurar sombreado de los horarios */
            for (var i = 6; i <= 12; i++) {
                nido_shadow( i.toString() );
            }

            function nido_shadow( id ) {
                var element = $( '#field_211-' + id );
                var checkbox = $( '#nido-dia-' + id );

                if ( element.attr( 'checked' ) != null ) {
                    if ( element.attr( 'checked' ).length > 0 ) {
                        checkbox.css( 'background-color', '#777' );
                        checkbox.css( 'color', 'white' );
                    }
                }
            }


            /*
             *  Las siguientes líneas de código son para programar la eliminacion de los niños de
             *  la forma de Registro de Familia. Esto es para que sea más sencillo para el usuario.
             */

            var kid_rows_all = ['519', '427', '440', '453', '467', '480', '493', '506', '606', '532'];
            var kid_rows_visible = [];
            var kid_rows_remove = ['704', '530', '438', '451', '464', '478', '491', '504', '517', '617'];

            kid_rows_all.forEach( function( current_visible_row, index ) {
                $( '#frm_field_' + current_visible_row + '_container .nido-cross' ).on( 'click', function() {
                    kid_rows_visible = [];
                    kid_rows_all.forEach( function( current_row ) {
                        if ( $( '#frm_field_' + current_row + '_container' ).css( 'display' ) == 'block' ) {
                            kid_rows_visible.push( current_row );
                        }
                    } );
                    var last = kid_rows_visible.length - 1;

                    for ( var i = index; i < last; i++ ) {
                        $( '#frm_field_' + kid_rows_visible[i] + '_container .nido-name' ).text(
                            $( '#frm_field_' + kid_rows_visible[i + 1] + '_container .nido-name' ).text()
                        );
                        $( '#frm_field_' + kid_rows_visible[i] + '_container .nido-nombre input' ).val(
                            $( '#frm_field_' + kid_rows_visible[i + 1] + '_container .nido-nombre input' ).val()
                        );
                        $( '#frm_field_' + kid_rows_visible[i] + '_container .nido-apellido input' ).val(
                            $( '#frm_field_' + kid_rows_visible[i + 1] + '_container .nido-apellido input' ).val()
                        );
                        $( '#frm_field_' + kid_rows_visible[i] + '_container .nido-genero input:first' ).attr( 'checked', 
                            $( '#frm_field_' + kid_rows_visible[i + 1] + '_container .nido-genero input:first' ).attr( 'checked' )
                        );
                        $( '#frm_field_' + kid_rows_visible[i] + '_container .nido-genero input:last' ).attr( 'checked', 
                            $( '#frm_field_' + kid_rows_visible[i + 1] + '_container .nido-genero input:last' ).attr( 'checked' )
                        );
                        $( '#frm_field_' + kid_rows_visible[i] + '_container .nido-fecha-entrada input' ).val(
                            $( '#frm_field_' + kid_rows_visible[i + 1] + '_container .nido-fecha-entrada input' ).val()
                        );
                        $( '#frm_field_' + kid_rows_visible[i] + '_container .nido-fecha-nacimiento input' ).val(
                            $( '#frm_field_' + kid_rows_visible[i + 1] + '_container .nido-fecha-nacimiento input' ).val()
                        );
                        $( '#frm_field_' + kid_rows_visible[i] + '_container .nido-edad input' ).val(
                            $( '#frm_field_' + kid_rows_visible[i + 1] + '_container .nido-edad input' ).val()
                        );
                        $( '#frm_field_' + kid_rows_visible[i] + '_container .nido-pediatra input' ).val(
                            $( '#frm_field_' + kid_rows_visible[i + 1] + '_container .nido-pediatra input' ).val()
                        );
                        $( '#frm_field_' + kid_rows_visible[i] + '_container .nido-enfermedades textarea' ).val(
                            $( '#frm_field_' + kid_rows_visible[i + 1] + '_container .nido-enfermedades textarea' ).val()
                        );
                        $( '#frm_field_' + kid_rows_visible[i] + '_container .nido-telefono input' ).val(
                            $( '#frm_field_' + kid_rows_visible[i + 1] + '_container .nido-telefono input' ).val()
                        );
                    }

                    $( '#frm_field_' + kid_rows_remove[last] + '_container .frm_radio:last-child input' ).click();
                    $( '#frm_field_' + kid_rows_remove[last] + '_container' ).show();
                } );
            } );

            var guardian_rows_all = ['70', '299', '325', '344', '363', '382', '401'];
            var guardian_rows_visible = [];
            var guardian_rows_remove = ['800', '318', '420', '421', '422', '423', '424'];

            guardian_rows_all.forEach( function( current_visible_row, index ) {
                $( '#frm_field_' + current_visible_row + '_container .nido-cross' ).on( 'click', function() {
                    guardian_rows_visible = [];
                    guardian_rows_all.forEach( function( current_row ) {
                        if ( $( '#frm_field_' + current_row + '_container' ).css( 'display' ) == 'block' ) {
                            guardian_rows_visible.push( current_row );
                        }
                    } );
                    var last = guardian_rows_visible.length - 1;

                    for ( var i = index; i < last; i++ ) {
                        $( '#frm_field_' + guardian_rows_visible[i] + '_container .nido-name' ).text(
                            $( '#frm_field_' + guardian_rows_visible[i + 1] + '_container .nido-name' ).text()
                        );
                        $( '#frm_field_' + guardian_rows_visible[i] + '_container .nido-nombre input' ).val(
                            $( '#frm_field_' + guardian_rows_visible[i + 1] + '_container .nido-nombre input' ).val()
                        );
                        $( '#frm_field_' + guardian_rows_visible[i] + '_container .nido-apellido input' ).val(
                            $( '#frm_field_' + guardian_rows_visible[i + 1] + '_container .nido-apellido input' ).val()
                        );
                        $( '#frm_field_' + guardian_rows_visible[i] + '_container .nido-telefono input' ).val(
                            $( '#frm_field_' + guardian_rows_visible[i + 1] + '_container .nido-telefono input' ).val()
                        );
                        $( '#frm_field_' + guardian_rows_visible[i] + '_container .nido-telefono-2 input' ).val(
                            $( '#frm_field_' + guardian_rows_visible[i + 1] + '_container .nido-telefono-2 input' ).val()
                        );
                        $( '#frm_field_' + guardian_rows_visible[i] + '_container .nido-email input' ).val(
                            $( '#frm_field_' + guardian_rows_visible[i + 1] + '_container .nido-email input' ).val()
                        );
                        $( '#frm_field_' + guardian_rows_visible[i] + '_container .nido-direccion input' ).val(
                            $( '#frm_field_' + guardian_rows_visible[i + 1] + '_container .nido-direccion input' ).val()
                        );
                        $( '#frm_field_' + guardian_rows_visible[i] + '_container .nido-ciudad input' ).val(
                            $( '#frm_field_' + guardian_rows_visible[i + 1] + '_container .nido-ciudad input' ).val()
                        );
                        $( '#frm_field_' + guardian_rows_visible[i] + '_container .nido-estado input' ).val(
                            $( '#frm_field_' + guardian_rows_visible[i + 1] + '_container .nido-estado input' ).val()
                        );
                        $( '#frm_field_' + guardian_rows_visible[i] + '_container .nido-pais input' ).val(
                            $( '#frm_field_' + guardian_rows_visible[i + 1] + '_container .nido-pais input' ).val()
                        );
                        $( '#frm_field_' + guardian_rows_visible[i] + '_container .nido-cp input' ).val(
                            $( '#frm_field_' + guardian_rows_visible[i + 1] + '_container .nido-cp input' ).val()
                        );
                        $( '#frm_field_' + guardian_rows_visible[i] + '_container .nido-direccion-mapa input' ).val(
                            $( '#frm_field_' + guardian_rows_visible[i + 1] + '_container .nido-direccion-mapa input' ).val()
                        );
                        $( '#frm_field_' + guardian_rows_visible[i] + '_container .nido-empresa input' ).val(
                            $( '#frm_field_' + guardian_rows_visible[i + 1] + '_container .nido-empresa input' ).val()
                        );
                        $( '#frm_field_' + guardian_rows_visible[i] + '_container .nido-relacion select' ).val(
                            $( '#frm_field_' + guardian_rows_visible[i + 1] + '_container .nido-relacion select' ).val()
                        );
                        var first_map_selector = $( '#frm_field_' + guardian_rows_visible[i] + '_container .nido-ver-mapa input' );
                        var second_map_selector = $( '#frm_field_' + guardian_rows_visible[i + 1] + '_container .nido-ver-mapa input' );
                        /*  Función XOR para determinar si es necesario hacer click en el campo anterior */
                        if ( first_map_selector.prop( 'checked' ) ? !second_map_selector.prop( 'checked' ) : second_map_selector.prop( 'checked' ) ) {
                            first_map_selector.click();
                        }
                    }

                    $( '#frm_field_' + guardian_rows_remove[last] + '_container .frm_radio:last-child input' ).click();
                    $( '#frm_field_' + guardian_rows_remove[last] + '_container' ).show();
                } );
            } );
        }
    );

})( jQuery );























