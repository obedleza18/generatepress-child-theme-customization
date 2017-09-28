( function( $ ) {
    /*
     *  Las funciones en este archivo serán las relacionadas con AJAX que se tienen que se tienen
     *  que ejecutar para el sistema de mensajes. Los eventos AJAX que se atienden en este sistema
     *  son:
     *      1) Enviar mensajes.
     *      2) Desplegar mensajes.
     *      3) Actualizar mensajes nuevos.
     *      4) Cargar mensajes neuvos.
     */

    $( document ).ready( function() {

        nido_refresh_avatars();
        function nido_refresh_avatars() {
            $( '.nido-avatar' ).each( function() {
                var url = '';
                if ( $( this ).attr( 'nido-avatar-in' ) != undefined ) {
                    if ( $( $( '#' + $( this ).attr( 'nido-avatar-in' ) ) ) != undefined ) {
                        url = $( '#' + $( this ).attr( 'nido-avatar-in' ) ).val();

                        /*  No hay avatar configurado, poner predeterminado */
                        if ( url == '' ) {
                            if ( $( this ).hasClass( 'nido-familia' ) ) {
                                url = nido_ajax_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/avatars/nido_familia.png'
                            }
                            if ( $( this ).hasClass( 'nido-kid' ) ) {
                                url = nido_ajax_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/avatars/bebe.png'
                            }
                            if ( $( this ).hasClass( 'nido-guardian' ) ) {
                                url = nido_ajax_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/avatars/papa.png'
                            }
                            if ( $( this ).hasClass( 'nido-empleado' ) ) {
                                url = nido_ajax_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/avatars/mama.png'
                            }
                            if ( $( this ).hasClass( 'nido-cuido' ) ) {
                                url = nido_ajax_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/avatars/nido-icono-nido.png'
                            }
                        }
                    }
                }
                $( this ).attr( 'src', url );
            } );
        }

        /*
         *  1) Enviar mensajes
         */

        $( '#nido_forma_mensaje' ).on( 'submit', function( e ) {

            var active_content = $( 'li.ui-state-active' ).attr( 'aria-controls' );

            var nuevo_mensaje = $( this ).find( 'input#nido_mensaje' ).val();

            $( '.nido-message-uploads img' ).hide();
            $( '.nido-message-uploads .nido-loading' ).show();

            if ( nuevo_mensaje == '' ) {
                nuevo_mensaje = $( '#nido-attachment' ).val();
            }

            $.ajax( { 
                data: {
                    action: 'nido_enviar_mensaje',
                    nuevo_mensaje: nuevo_mensaje,
                    de_nombre: $( 'input#de_nombre' ).val(),
                    hora: $( 'input#hora' ).val(),
                    remitente: $( 'input#remitente' ).val(),
                    destinatario: $( 'input#destinatario' ).val()
                },
                type: 'post',
                url: nido_ajax_object.ajax_url,
                success: function( response ) {
                    
                    if ( response.success ) {

                        var div_wrapper = $( '<div>' ).addClass( 'nido-mensaje-mio-wrapper' );
                        var div_mensaje = $( '<div>' ).
                            addClass( 'nido-mensaje-individual nido-mensaje-mio' ).
                            html( nuevo_mensaje );

                        var last = $( 'div#' + active_content + ' div.nido-contenido-mensaje div' ).last();

                        if ( last.hasClass( 'nido-mensaje-mio' ) ) {
                            div_wrapper.addClass( 'nido-same-message-right' );
                        }

                        div_wrapper.append( div_mensaje );

                        if ( response.data.indexOf( '[Incidente' ) !== -1 ) {
                            div_wrapper.addClass( 'nido-color-incidente' );
                        }

                        $( 'div#' + active_content + ' div.nido-contenido-mensaje' ).append( div_wrapper );
                        scrollDown();

                        /*  Esconde loading */
                        $( '.nido-message-uploads img' ).show();
                        $( '.nido-message-uploads .nido-loading' ).hide();
                    }
                },
                error: function( response ) {
                    alert( 'Hay un error, inténtelo nuevamente.' );
                }
            } );

            return false;
        } );

        /*
         *  3) Actualizar mensajes nuevos
         */

        if ( $( '.nido-lable-title' ).text() !== '' )
            nido_actualizar_mensajes_nuevos();

        function nido_actualizar_mensajes_nuevos() {
            var from = new Array();
            $( '[role=tab]' ).each( function() {
                var id = $( this ).find( 'input.nido-id-de-familia-mensajes' ).val();
                from.push( id );
            } );

            $.ajax( {
                data: {
                    action: 'nido_actualizar_mensajes_nuevos',
                    from: JSON.stringify( from )
                },
                type: 'post',
                url: nido_ajax_object.ajax_url,
                success: function( response ) {

                    var total_mensajes_nuevos = 0;
                    var hora_mayor = new Date( Date.parse( '2017-01-01 1:00:00 AM' ) );
                    var tab_id;

                    from.forEach( function( current_value ) {
                        var burbuja = $( '#' + current_value + ' img.nido-bubble' );
                        var numero = $( '#' + current_value + ' label.nido-mensajes-no-leidos' );
                        burbuja.css( 'visibility', 'hidden' );
                        numero.css( 'visibility', 'hidden' );
                    } );

                    response.data.forEach( function( current_value ) {

                        /*  Check if exists. If not, create the element and prepend it */
                        if ( $( '#' + current_value['id'] ).length ) {
                            /*  Elementos a modificar */
                            var burbuja = $( '#' + current_value['id'] + ' img.nido-bubble' );
                            var numero = $( '#' + current_value['id'] + ' label.nido-mensajes-no-leidos' );
                            var preview = $( '#' + current_value['id'] + ' label.nido-preview' );
                            var hora = $( '#' + current_value['id'] + ' label.nido-hora-mensaje' );

                            /*  Tiene mensajes nuevos el usuario? */
                            var nuevos_mensajes_por_usuario = current_value['cantidad_mensajes'];
                            total_mensajes_nuevos += nuevos_mensajes_por_usuario;

                            /*  Si no tiene mensajes nuevos, no mostrar notificación */
                            if ( nuevos_mensajes_por_usuario == 0 ) {
                                burbuja.css( 'visibility', 'hidden' );
                                numero.css( 'visibility', 'hidden' );
                                return;
                            }

                            burbuja.css( 'visibility', 'visible' );
                            numero.css( 'visibility', 'visible' );

                            if ( nuevos_mensajes_por_usuario < 10 ) {
                                numero.text( nuevos_mensajes_por_usuario );
                                numero.css( 'margin-left', '-15px' );
                            }
                            else {
                                numero.text( '9+' );
                                numero.css( 'margin-left', '-17px' );
                            }

                            /*  Modificar preview */
                            if ( current_value['ultimo_mensaje'].length > 35 ) {
                                preview.text( current_value['ultimo_mensaje'].substr( 0, 35 ) + ' ...' );
                            }
                            else {
                                preview.text( current_value['ultimo_mensaje'] );
                            }

                            /*  Modificar preview si es imagen */
                            var allowed_formats = [ 'jpg', 'png', 'gif', 'bmp', 'jpeg' ];
                            try {
                                eval( $( current_value['ultimo_mensaje'] ) );
                            }
                            catch ( error ) {
                                current_value['ultimo_mensaje'] = '';
                            }

                            if ( current_value['ultimo_mensaje'] != '' ) {
                                if ( $( current_value['ultimo_mensaje'] ).text() != '' ) {
                                    var flag_out = false;
                                    var mensaje_actual_html = $( current_value['ultimo_mensaje'] ).text();
                                    
                                    allowed_formats.forEach( function( current_format ) {
                                        preview.text( 'Imagen' );

                                        if ( flag_out ) return;

                                        if ( mensaje_actual_html.indexOf( current_format ) !== -1 ) {
                                            preview.text( 'Imagen' );
                                            flag_out = true;
                                        }
                                        else
                                            preview.text( 'Documento' );
                                    } );
                                }
                            }

                            /*  Modificar hora */
                            var fecha_mensaje = ( current_value['ultima_hora'] ).split( ' ' );
                            var fecha_hoy = ( $( '#nido-tod' ).text() ).split( ' ' );

                            if ( fecha_mensaje[0] === fecha_hoy[0] ) {
                                var hora_texto = ( fecha_mensaje[1] ).split( ':' );
                                hora.text( hora_texto[0] + ':' + hora_texto[1] + ' ' + fecha_mensaje[2] );
                            }
                            else {
                                var fecha_texto = ( fecha_mensaje[0] ).split( '/' );
                                var meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                                // hora.text( fecha_texto[0] + ' ' + meses[parseInt( fecha_texto[1] ) - 1] );
                                hora.text( fecha_texto[0] + '/' + fecha_texto[1] );
                            }

                            /*  Encontrar la conversación con el mensaje más reciente */
                            var fecha_mensaje_splitted = fecha_mensaje[0].split( '/' );
                            var fecha_mensaje_date = new Date( Date.parse( 
                                fecha_mensaje_splitted[2] + '-' + fecha_mensaje_splitted[1] + '-' + 
                                fecha_mensaje_splitted[0] + ' ' + fecha_mensaje[1] + ' ' + fecha_mensaje[2]
                            ) );

                            /*  Comparar horas y verificar tab con mensaje más reciente */
                            if ( hora_mayor < fecha_mensaje_date ) {
                                hora_mayor = fecha_mensaje_date;
                                tab_id = current_value['id'];
                            }
                        }
                        else {
                            console.log( 'Creando conversacion' );
                            nido_crear_tab_conversacion( 
                                current_value['id'], 
                                current_value['cantidad_mensajes'],
                                current_value['nombre_familia'],
                                current_value['ultimo_mensaje'],
                                current_value['ultima_hora'],
                                'visible',
                                current_value['avatar']
                            );
                        }

                    } );

                    var bigBubble = $( '.nido-nuevos-mensajes' );
                    if ( total_mensajes_nuevos == 0 ) {
                        bigBubble.text( '' );
                    }
                    else if( total_mensajes_nuevos < 10 ) {
                        bigBubble.text( total_mensajes_nuevos );
                        bigBubble.css( 'margin-left', '-35px' );
                    }
                    else {
                        bigBubble.text( '9+' );
                        bigBubble.css( 'margin-left', '-38px' );
                    }

                    /*  Modificar posición de tab que tiene el último mensaje */
                    $( '#' + tab_id ).parent().parent( 'li.ui-tabs-tab' ).prependTo( 'ul.ui-tabs-nav' );
                },
                error: function( response ) {
                    console.log( 'El servidor no respondió o no hay conversaciones.' );
                },
                complete: function( request, status ) {

                    // if ( status == 'success' ) {
                        setTimeout( nido_actualizar_mensajes_nuevos, 10000 );
                    // }
                }
            } );
        }


        /*    Avisar de mensajes nuevos */

        nido_cantidad_mensajes_nuevos();

        function nido_cantidad_mensajes_nuevos() {
            $.ajax( {
                data: {
                    action: 'nido_cantidad_mensajes_nuevos'
                },
                type: 'post',
                url: nido_ajax_object.ajax_url,
                success: function( response ) {
                    if ( response.success ) {
                        var total_mensajes_nuevos = response.data;
                        var bigBubble = $( '.nido-nuevos-mensajes' );

                        if ( $( window ).width() <= 768 ) {
                            if ( total_mensajes_nuevos != 0 ) {
                                var small_bubble = $( '<img>' ).
                                    attr( 'src', nido_ajax_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/nido-bubble.png' ).
                                    css( { 'height': '16px', 'padding-left': '5px' } );

                                if ( $( 'button.menu-toggle img' ).length == 0 ) {
                                    $( 'button.menu-toggle' ).append( small_bubble );
                                }
                            }
                            else {
                                $( 'button.menu-toggle img' ).remove();
                            }
                        }

                        if ( total_mensajes_nuevos == 0 ) {
                            bigBubble.text( '' );
                        }
                        else if( total_mensajes_nuevos < 10 ) {
                            bigBubble.text( total_mensajes_nuevos );
                            bigBubble.css( 'margin-left', '-35px' );
                        }
                        else {
                            bigBubble.text( '9+' );
                            bigBubble.css( 'margin-left', '-38px' );
                        }
                    }

//                     <img src="/nido/wp-content/themes/generatepress_child/assets/icons/nido-bubble.png" style="
//     height: 16px;
//     padding-left: 5px;
// ">
                },
                error: function( response ) {
                    console.log( 'El servidor no respondió o no hay conversaciones' );
                },
                complete: function( request, status ) {
                    setTimeout( nido_cantidad_mensajes_nuevos, 10000 );

                }
            } );
        }


        /*
         *  4) Cargar mensajes nuevos
         */

        if ( $( '.nido-lable-title' ).text() !== '' )
            nido_cargar_mensajes_nuevos();

        function nido_cargar_mensajes_nuevos() {

            /*  Obtener el ID de la pestaña activa */
            var active_id = $( 'li.ui-state-active input.nido-id-de-familia-mensajes' ).val();
            var active_content = $( 'li.ui-state-active' ).attr( 'aria-controls' );

            /*  Si no existe pestaña, checar después */
            if ( active_id === undefined ) {
                setTimeout( nido_cargar_mensajes_nuevos, 10000 );
                return;
            }

            $.ajax( {
                data: {
                    action: 'nido_cargar_mensajes_nuevos',
                    active_id: JSON.stringify( active_id )
                },
                type: 'post',
                url: nido_ajax_object.ajax_url,
                success: function( response ) {

                    response.data.forEach( function( current_message ) {

                        var div_wrapper = $( '<div>' ).
                            addClass( 'nido-mensaje-individual nido-mensaje-otro' );
                        var img_avatar = $( '<img>' ).
                            addClass( 'nido-avatar-individual' ).
                            attr( 'src', $( '#nido-mensajes li.ui-state-active .avatar' ).attr( 'src' ) );
                        var div_mensage = $( '<div>' ).
                            addClass( 'nido-mensaje-texto' ).
                            html( current_message );

                        /*  Visualmente agrupar mensajes del mismo destinatario */
                        var last = $( 'div#' + active_content + ' div.nido-contenido-mensaje div' ).last();

                        if ( last.hasClass( 'nido-mensaje-texto' ) ) {
                            div_wrapper.append( div_mensage );
                            div_wrapper.addClass( 'nido-same-message-left' );
                        }
                        else {
                            div_wrapper.append( img_avatar, div_mensage );
                        }

                        div_wrapper.appendTo( 'div#' + active_content + ' div.nido-contenido-mensaje' );

                        if ( current_message[0] == '[' ) {
                            if ( current_message.indexOf( '[Incidente' ) !== -1 ) {
                                div_wrapper.addClass( 'nido-color-incidente' );
                            }
                        }

                        scrollDown();

                        /*  Actualizar pestaña */
                        var preview = $( '#' + active_id + ' label.nido-preview' );
                        var hora = $( '#' + active_id + ' label.nido-hora-mensaje' );

                        var fecha_hoy = ( $( '#nido-tod' ).text() ).split( ' ' );
                        var hora_hoy = fecha_hoy[1].split( ':' );
                        var meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

                        hora.text( hora_hoy[0] + ':' + hora_hoy[1] + ' ' + fecha_hoy[2] );

                        if ( current_message.length > 35 ) {
                            preview.text( current_message.substr( 0, 35 ) + ' ...' );
                        }
                        else {
                            preview.text( current_message );
                        }

                        /*  Modificar preview si es imagen */
                        var allowed_formats = [ 'jpg', 'png', 'gif', 'bmp', 'jpeg' ];
                        
                        try {
                            eval( $( current_message ) );
                        }
                        catch ( error ) {
                            current_message = '';
                        }

                        if ( current_message != '' ) {
                            if ( $( current_message ).text() != '' ) {
                                var flag_out = false;
                                var mensaje_actual_html = $( current_message ).text();
                                
                                allowed_formats.forEach( function( current_format ) {
                                    preview.text( 'Imagen' );

                                    if ( flag_out ) return;

                                    if ( mensaje_actual_html.indexOf( current_format ) !== -1 ) {
                                        preview.text( 'Imagen' );
                                        flag_out = true;
                                    }
                                    else
                                        preview.text( 'Documento' );
                                } );
                            }
                        }

                    } );
                },
                error: function( response ) {
                    console.log( 'El servidor no respondió o no hay conversaciones' );
                },
                complete: function( request, status ) {

                    // if ( status == 'success' ) {
                        setTimeout( nido_cargar_mensajes_nuevos, 10000 );
                    // }

                }
            } );
        }


        /*
         *  Función para agregar nuevas conversaciones
         */

        $( 'img#nido-crear-nueva-conversacion' ).on( 'click', function() {

            /*  Animación */
            $( '.nido-mensajes-encabezado .nido-loading' ).show();

            var data = [];
            $.ajax( {
                url: nido_ajax_object.ajax_url + '/?action=nido_crear_conversacion',
                success: function( result ) {

                    result.data.forEach( function( current_element ) {
                        console.log( current_element );
                        data.push( { 
                            label: current_element['Nombre'], 
                            category: current_element['Rol'],
                            id: current_element['ID'],
                            avatar: current_element['Avatar'] },
                         );
                    } );

                    $( '.nido-para-fields' ).show();
                    $( '.nido-mensajes-encabezado .nido-loading' ).hide();

                    $( "#search" ).catcomplete( {
                        delay: 0,
                        source: data,
                        minLength: 0,
                        open: function( event, ui ) {
                            var position = $( '.ui-autocomplete' ).position();
                            var left = Math.round( position['left'] - 88 );
                            $( '.ui-autocomplete' ).css( 'left', left.toString() + 'px' );
                        },
                        select: function ( event, ui ) {

                            var nido_tabs = $( 'div#nido-mensajes li' );
                            var existe = false;
                            var index = 0;

                            $( '.nido-para-fields' ).hide();

                            /*  Checar los tabs para ver si ya existe la conversación */
                            nido_tabs.each( function() {

                                /*  Checar si la conversación actual existe */
                                var current_id = $( this ).find( '.nido-mensaje' ).attr( 'id' );
                                if ( ui.item.id === current_id ) {
                                    existe = true;
                                    return false;
                                }

                                index++;
                            } );

                            /*  Si la conversación existe, seleccionarla. Si no, crearla */
                            if ( existe ) {
                                /*  Seleccionar el tab */
                                $( 'div#nido-mensajes' ).tabs( 'option', 'active', index );

                                /*  Limpiar el campo para crear conversación */
                                $( 'input#search' ).val( '' );
                            }
                            else {
                                console.log( ui );
                                nido_crear_tab_conversacion( ui.item.id, '', ui.item.value, '', '', 'hidden', ui.item.avatar );

                                /*  Seleccionar el tab */
                                $( 'div#nido-mensajes').tabs('option', 'active', 0 );

                                /*  Configurar todo lo demás cuando se selecciona el tab nuevo */
                                if ( $( window ).width() <= 768 ) {
                                    $( '.nido-buscar-mensajes .nido-busqueda' ).hide();
                                    $( '.nido-buscar-mensajes .nido-encabezado-conversacion' ).show();
                                    $( '.nido-mensajes-encabezado .fa.fa-arrow-left' ).show();
                                }

                                /*  Nombre de la conversación agregada */
                                $( 'input#search' ).val( ui.item.value );
                            }

                            /*  Configurar campos para enviar el mensaje */
                            $( '.nido-enviar-mensaje .nido-message-content input#nido_mensaje' ).val( '' );
                            $( '.nido-enviar-mensaje' ).show();
                            $( 'input#destinatario' ).val( ui.item.id );

                            $( '.nido-encabezado-conversacion .nido-titulo-conversacion' ).text(
                                ui.item.value
                            );

                            return false;
                        }
                    } ).bind( 'focus', function() { $( this ).catcomplete( 'search' ) } );
                }
            } );
        } );


        /*
         *  Función para eliminar conversaciones
         */

        /*  Función para el callback de eliminación de mensajes */
        $( 'i.nido-trash' ).on( 'click', nido_eliminar_conversacion );

        function nido_eliminar_conversacion( event ) {

            if ( event.type === 'click' ) {
                event.stopPropagation();

                var id, nombre;

                if ( event.data === undefined ) {
                    id = $( this ).parent().find( '.nido-mensaje' ).attr( 'id' );
                    nombre = $( this ).parent().find( '.nombre-chat' ).text().trim();
                }
                else {
                    id = event.data.id;
                    nombre = event.data.nombre;
                }

                $.confirm( {
                    title: 'Confirmar',
                    theme: 'nido',
                    icon: 'fa fa-exclamation-triangle',
                    content: '&iquest;Desea eliminar la conversaci&oacute;n de ' + nombre + '?',
                    buttons: {
                        Si: function () {
                            $.ajax( {
                                url: nido_ajax_object.ajax_url + '/?action=nido_eliminar_conversacion&destinatario=' + id,
                                dataType: 'json',
                                success: function( response ) {

                                    if ( response.success ) {
                                        $( 'div#nido-mensajes' ).tabs( 'option', 'active', false );
                                        $( 'div#nido-mensajes li a div#' + id ).parent().parent().remove();
                                        $( '.nido-encabezado-conversacion img' ).attr( 'src', nido_ajax_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/nido-huevo-1.png' );
                                        $( '.nido-titulo-conversacion' ).text( 'Conversacion' );
                                        $( 'div#nido-mensajes' ).tabs( 'refresh' );
                                    }
                                    else {
                                        $.confirm( {
                                            title: 'Error',
                                            content: 'Hubo un error al intentar eliminar la conversación.',
                                            type: 'red',
                                            theme: 'nido',
                                            typeAnimated: true,
                                            buttons: {
                                                tryAgain: {
                                                    text: 'Reintentar',
                                                    btnClass: 'btn-red',
                                                    action: function() {
                                                        event.data = { id: id, nombre: nombre };
                                                        nido_eliminar_conversacion( event );
                                                    }
                                                },
                                                Cerrar: function () {
                                                }
                                            }
                                        } );
                                    }
                                },
                                error: function( response ) {
                                    $.confirm( {
                                        title: 'Error',
                                        content: 'Hubo un error en el servidor o la conversación todavía no tiene mensajes. Intente nuevamente.',
                                        type: 'red',
                                        theme: 'nido',
                                        typeAnimated: true,
                                        buttons: {
                                            close: function () {
                                            }
                                        }
                                    } );
                                }
                            } );
                        },
                        No: function () {
                        },
                        Archivar: {
                            text: 'Archivar',
                            btnClass: 'btn-predeterminado',
                            keys: ['enter', 'shift'],
                            action: function() {
                                $.ajax( {
                                url: nido_ajax_object.ajax_url + '/?action=nido_archivar_conversacion&para_quien=' + id,
                                dataType: 'json',
                                success: function( response ) {

                                    $( 'div#nido-mensajes' ).tabs( 'option', 'active', false );
                                    $( 'div#nido-mensajes li a div#' + id ).parent().parent().remove();
                                    $( 'div#nido-mensajes' ).tabs( 'refresh' );

                                    $.confirm( {
                                        title: 'Conversación Archivada',
                                        content: 'La conversación ha sido archivada con éxito.',
                                        type: 'green',
                                        theme: 'nido',
                                        typeAnimated: true,
                                        buttons: {
                                            OK: function () {
                                            }
                                        }
                                    } );
                                },
                                error: function( response ) {
                                    $.confirm( {
                                        title: 'Error',
                                        content: 'Hubo un error en el servidor o la conversación todavía no tiene mensajes. Intente nuevamente.',
                                        type: 'red',
                                        theme: 'nido',
                                        typeAnimated: true,
                                        buttons: {
                                            close: function () {
                                            }
                                        }
                                    } );
                                }
                            } );
                            }
                        }
                    }
                } );
            }

        }


        /*
         *  Funcionalidad de los tabs
         */

        var div_contenido_wrapper = $( '<div>' ).addClass( 'nido-contenido-wrapper' );
        $( "#nido-mensajes" ).tabs( {
            beforeLoad: function( event, ui ) {

                if ( ui.tab.data( "loaded" ) ) {
                    event.preventDefault();
                    return;
                }

                if ( $( window ).width() <= 768 ) {
                    $( '.nido-loading' ).show();
                }
                else {
                    ui.panel.html( '<div style="width: 100%; text-align: center; padding-top: 10px;">Cargando Conversación <img src="' + nido_ajax_object.site_url + '/wp-content/plugins/formidable/images/ajax_loader.gif"></div>' );
                }

                ui.jqXHR.fail( function() {
                    ui.panel.html( '<div style="width: 100%; text-align: center; padding-top: 10px;"><img src="' + nido_ajax_object.site_url + '/wp-content/plugins/formidable/images/ajax_loader.gif"></div>' );
                } );

                ui.ajaxSettings.dataFilter = function( response ) {

                    var mensajes = JSON.parse( response );
                    var div_contenido_mensaje = $( '<div>' ).addClass( 'nido-contenido-mensaje' );
                    var div_wrapper, div_mensaje, img_avatar, ultimo_de_quien = '';

                    mensajes.data.forEach( function( mensaje_actual ) {

                        if ( mensaje_actual['de_quien'] == 'otro' ) {
                            div_wrapper = $( '<div>' ).
                                addClass( 'nido-mensaje-individual nido-mensaje-otro' );
                            img_avatar = $( '<img>' ).
                                addClass( 'nido-avatar-individual' ).
                                attr( 'src', $( '#nido-mensajes li.ui-state-active .avatar' ).attr( 'src' ) );
                            div_mensaje = $( '<div>' ).
                                addClass( 'nido-mensaje-texto' ).
                                html( mensaje_actual['mensaje'].replace( /&lt;/g, '<' ).replace( /&gt;/g, '>' ).replace( /&quot;/g, '"' ) );

                            if ( ultimo_de_quien == 'otro' ) {
                                div_wrapper.append( div_mensaje );
                                div_wrapper.addClass( 'nido-same-message-left' );
                            }
                            else {
                                div_wrapper.append( img_avatar, div_mensaje );
                            }
                        }
                        else if ( mensaje_actual['de_quien'] == 'mio' ) {
                            div_wrapper = $( '<div>' ).addClass( 'nido-mensaje-mio-wrapper' );
                            div_mensaje = $( '<div>' ).
                                addClass( 'nido-mensaje-individual nido-mensaje-mio' ).
                                html( mensaje_actual['mensaje'].replace( /&lt;/g, '<' ).replace( /&gt;/g, '>' ).replace( /&quot;/g, '"' ) );

                            if ( ultimo_de_quien == 'mio' ) {
                                div_wrapper.addClass( 'nido-same-message-right' );
                            }

                            div_wrapper.append( div_mensaje );

                        }

                        if ( mensaje_actual['mensaje'][0] == '[' ) {
                            if ( mensaje_actual['mensaje'].indexOf( '[Incidente' ) !== -1 ) {
                                div_wrapper.addClass( 'nido-color-incidente' );
                            }
                        }

                        ultimo_de_quien = mensaje_actual['de_quien'];

                        div_contenido_mensaje.append( div_wrapper );

                    } );

                    div_contenido_wrapper.append( div_contenido_mensaje );

                }

                ui.jqXHR.success( function() {
                    ui.tab.data( "loaded", true );
                    $( '.nido-loading' ).hide();
                } );
            },
            load: function( event, ui ) {

                ui.panel.html( div_contenido_wrapper );

                div_contenido_wrapper = $( '<div>' ).addClass( 'nido-contenido-wrapper' );

                scrollDown();
            },
            collapsible: true,
            active: false
        } );

        $( "#nido-mensajes" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
        $( "#nido-mensajes li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
        nido_update_messages_callbacks();


        /*
         *  Funciones Auxiliares para AJAX
         */

        /*  Función para hacer scroll down */
        function scrollDown() {
            var height = 0;
            $( ".nido-contenido-mensaje" ).each( function( i, value ) {
                height += parseInt( $( this ).height() );
            } );

            height += "";

            $( ".nido-contenido-wrapper" ).animate( { scrollTop: height } );

            $( '#nido_mensaje' ).val( '' );
        }

        /*  Función para limpiar el campo de búsqueda cuando se da click en el */
        // $( 'input#search' ).on( 'click', function() { $( this ).val( '' ) } );

        /*  Función para agregar la funcionalidad de callbacks a las conversaciones nuevas */
        function nido_update_messages_callbacks() {
            $( '#nido-mensajes li' ).on( 'click',
                function() {
                    var id = $( this ).find( 'input.nido-id-de-familia-mensajes' ).val();
                    $( '.nido-enviar-mensaje .nido-message-content input#nido_mensaje' ).val( '' );
                    $( '.nido-encabezado-conversacion .nido-titulo-conversacion' ).text(
                        $( this ).find( 'label.nombre-chat' ).text()
                    );
                    $( '.nido-encabezado-conversacion img' ).attr( 'src', $( '#nido-mensajes li.ui-state-active .avatar' ).attr( 'src' ) );

                    /*
                     *  Funcionalidad para móvil
                     */

                    if ( $( window ).width() <= 768 ) {
                        $( '.nido-buscar-mensajes .nido-busqueda' ).hide();
                        $( '.nido-buscar-mensajes .nido-encabezado-conversacion' ).show();
                        $( '.nido-mensajes-encabezado .fa.fa-arrow-left' ).show();
                    }

                    var esconde_escritura = false;
                    $( 'div#nido-mensajes li' ).each( function() {
                        esconde_escritura = esconde_escritura || $( this ).hasClass( 'ui-state-active' );
                    } );

                    if ( esconde_escritura ) {
                        $( '.nido-enviar-mensaje' ).show();
                        $( 'input#destinatario' ).val( id );
                    }
                    else {
                        $( '.nido-enviar-mensaje' ).hide();
                        $( 'input#destinatario' ).val( '' );
                    }
                }
            );
        }

        function nido_crear_tab_conversacion( $id, $cantidad_mensajes, $nombre_familia, $ultimo_mensaje, $ultima_hora, $visibilidad, $avatar ) {
            var input_nido_id_familia_mensajes = $( '<input type="hidden">' ).
                addClass( 'nido-id-de-familia-mensajes' ).
                val( $id );
            
            var _image_nido_bubble = $( '<img>' ).
                addClass( 'nido-bubble' ).
                attr( 'src', nido_ajax_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/nido-bubble.png' ).
                css( 'visibility', $visibilidad );
            var _label_nido_mensajes_no_leidos = $( '<label>' ).
                addClass( 'nido-mensajes-no-leidos' ).
                css( { 
                    'visibility' :  $visibilidad,
                    'margin-left' : '-15px'
                } ).
                text( $cantidad_mensajes );

            var new_div = $( '<div>' ).
                css( 'visibility', $visibilidad ).
                append( _image_nido_bubble, _label_nido_mensajes_no_leidos );

            var image_avatar = $( '<img>' ).
                addClass( 'avatar' ).
                attr( 'src', $avatar );

            var _label_nombre_chat = $( '<label>' ).
                addClass( 'nombre-chat' ).
                text( $nombre_familia );
            var _label_nido_preview = $( '<label>' ).
                addClass( 'nido-preview' ).
                text( $ultimo_mensaje );

            var div_nido_after_avatar = $( '<div>' ).
                addClass( 'nido-after-avatar' ).
                append( _label_nombre_chat, '<hr>', _label_nido_preview );

            var label_nido_hora_mensaje = $( '<label>' ).
                addClass( 'nido-hora-mensaje' );

            if ( $ultima_hora != '' ) {
                var ultima_fecha_registrada = $ultima_hora.split( ' ' );
                var ultima_hora_registrada = ultima_fecha_registrada[1].split( ':' );

                label_nido_hora_mensaje.text( 
                    ultima_hora_registrada[0] + ':' + 
                    ultima_hora_registrada[1] + ' ' + 
                    ultima_fecha_registrada[2]
                );
            }
            else {
                label_nido_hora_mensaje.text( '' );
            }

            var div_nido_mensaje = $( '<div>' ).
                addClass( 'nido-mensaje' ).
                attr( 'id', $id ).
                append( 
                    input_nido_id_familia_mensajes, 
                    new_div, 
                    image_avatar, 
                    div_nido_after_avatar, 
                    label_nido_hora_mensaje 
                );

            var anchor = $( '<a>' ).
                attr( 
                    'href', 
                    nido_ajax_object.ajax_url + 
                        '?action=nido_cargar_conversacion&to=' +
                        $id
                ).
                append( div_nido_mensaje );

            var li = $( '<li>' ).append( anchor, '<i class="fa fa-trash-o nido-trash" aria-hidden="true"></i>' );

            var num_tabs = $( 'div#nido-mensajes ul li' ).length + 1;
            $( "div#nido-mensajes ul" ).prepend( li );
            $( "div#nido-mensajes" ).tabs( "refresh" );
            nido_update_messages_callbacks();
            $( 'i.nido-trash' ).unbind( 'click' );
            $( 'i.nido-trash' ).on( 'click', nido_eliminar_conversacion );
            $( '.nido-encabezado-conversacion img' ).attr( 'src', $avatar );
        }

        $( '#nido-mensajes').bind( 'tabsselect', function( event, ui ) { 
            // console.log( event ); 
        } );


        /*
         *  Código del Widget para el AutoComplete
         */

        $.widget( "custom.catcomplete", $.ui.autocomplete, {
            _create: function() {
                this._super();
                this.widget().menu( "option", "items", "> :not(.ui-autocomplete-category)" );
            },
            _renderMenu: function( ul, items ) {
                var that = this,
                currentCategory = "";
                $.each( items, function( index, item ) {
                    var li;
                    if ( item.category != currentCategory ) {
                        ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
                        currentCategory = item.category;
                    }
                    li = that._renderItemData( ul, item );
                    if ( item.category ) {
                        li.attr( "aria-label", item.category + " : " + item.label );
                    }
                } );
            }
        } );


        /*
         *  Código de las gráficas de Chart.js
         */

        // $( 'select#nido-reportes-meses' ).on( 'change', nido_actualizar_graficas );
        // $( 'input#nido-reportes-year' ).on( 'change', nido_actualizar_graficas );

        // if ( $( 'select#nido-reportes-meses' ).length != 0 )
        //     nido_actualizar_graficas();

        // /*  Llamada AJAX para llenar gráficas */
        // function nido_actualizar_graficas() {
        //     var meses_string = [
        //         'Enero', 
        //         'Febrero', 
        //         'Marzo', 
        //         'Abril', 
        //         'Mayo', 
        //         'Junio', 
        //         'Julio', 
        //         'Agosto', 
        //         'Septiembre', 
        //         'Octubre', 
        //         'Noviembre', 
        //         'Diciembre'
        //     ];
        //     var meses = $( 'select#nido-reportes-meses' ).val();
        //     var years = $( 'input#nido-reportes-year' ).val();

        //     $( '.nido-loading' ).show();

        //     $.ajax( { 
        //         data: {
        //             action: 'nido_actualizar_graficas',
        //             meses: meses,
        //             years: years,
        //         },
        //         type: 'post',
        //         url: nido_ajax_object.ajax_url,
        //         success: function( response ) {

        //             $( '#nido-grafica-entrada' ).replaceWith( '<canvas id="nido-grafica-entrada"></canvas>' );
        //             $( '#nido-grafica-salida' ).replaceWith( '<canvas id="nido-grafica-salida"></canvas>' );
        //             $( '#nido-grafica-incidentes' ).replaceWith( '<canvas id="nido-grafica-incidentes"></canvas>' );
        //             $( '.nido-grafica-wrapper iframe' ).remove();

        //             var entradas = response.data.entradas;
        //             var salidas = response.data.salidas;
        //             var incidentes = response.data.incidentes;
        //             var indices = [];

        //             for ( var i = 0; i < entradas.length; i++ ) {
        //                 indices.push( ( i + 1 ).toString() );
        //             }

        //             Chart.defaults.global.tooltips.enabled = false;

        //             var canvas_entradas = $( '#nido-grafica-entrada' );
        //             var chart_entradas = new Chart( canvas_entradas, {
        //                 "type" : "line",
        //                 "data" : {
        //                     "labels" : indices,
        //                     "datasets" : [
        //                         {"label" : 
        //                             "Entradas " + meses_string[ meses-1 ],
        //                             "data" : entradas,
        //                             "fill" : true,
        //                             "borderColor" : "rgb( 0, 204, 173 )",
        //                             'backgroundColor' : 'rgb( 0, 209, 178 )',
        //                             "lineTension" : 0.1
        //                         }
        //                     ]
        //                 },
        //                 "options" : {
        //                     'scales': {
        //                         'yAxes': [{
        //                             'ticks': {
        //                                 'beginAtZero' : true,
        //                                 userCallback : function( label, index, labels ) {
        //                                     if ( Math.floor( label ) === label ) {
        //                                         return label;
        //                                     }
        //                                 },
        //                             }
        //                         }]
        //                     }
        //                 },
        //                 "scaleStepWidth" : 1
        //             } );

        //             var canvas_salidas = $( '#nido-grafica-salida' );
        //             var chart_salidas = new Chart( canvas_salidas, {
        //                 "type" : "line",
        //                 "data" : {
        //                     "labels" : indices,
        //                     "datasets" : [
        //                         {"label" : 
        //                             "Salidas " + meses_string[meses - 1],
        //                             "data" : salidas,
        //                             "fill" : true,
        //                             "borderColor" : "rgb( 255, 87, 101 )",
        //                             'backgroundColor' : 'rgb( 255, 101, 113 )',
        //                             "lineTension" : 0.1
        //                         }
        //                     ]
        //                 },
        //                 "options": {
        //                     'scales': {
        //                         'yAxes': [{
        //                             'ticks': {
        //                                 'beginAtZero': true,
        //                                 userCallback : function( label, index, labels ) {
        //                                     if ( Math.floor( label ) === label ) {
        //                                         return label;
        //                                     }
        //                                 },
        //                             }
        //                         }]
        //                     }
        //                 } } );

        //             var canvas_incidentes = $( '#nido-grafica-incidentes' );
        //             var chart_incidentes = new Chart( canvas_incidentes, {
        //                 "type" : "line",
        //                 "data" : {
        //                     "labels" : indices,
        //                     "datasets" : [
        //                         {"label" : 
        //                             "Salidas " + meses_string[meses - 1],
        //                             "data" : incidentes,
        //                             "fill" : true,
        //                             "borderColor" : "rgb(230, 149, 0)",
        //                             'backgroundColor' : 'rgb(255, 165, 0)',
        //                             "lineTension" : 0.1
        //                         }
        //                     ]
        //                 },
        //                 "options": {
        //                     'scales': {
        //                         'yAxes': [{
        //                             'ticks': {
        //                                 'beginAtZero': true,
        //                                 userCallback : function( label, index, labels ) {
        //                                     if ( Math.floor( label ) === label ) {
        //                                         return label;
        //                                     }
        //                                 },
        //                             }
        //                         }]
        //                     }
        //                 } } );

        //             $( '.nido-loading' ).hide();

        //             $( '.nido-entradas-boton label' ).text( '►' );
        //             $( '.nido-salidas-boton label' ).text( '►' );
        //             $( '.nido-incidentes-boton label' ).text( '►' );

        //             nido_toggle_button_entrada.call( $( '.nido-entradas-boton' ) );
        //             nido_toggle_button_salida.call( $( '.nido-salidas-boton' ) );
        //             nido_toggle_button_salida.call( $( '.nido-incidentes-boton' ) );
                    
        //         },
        //         error: function( response ) {
        //             console.log( response );
        //         }
        //     } );
        // }


        /*
         *  Modificar el funcionamiento de las gráficas
         */

        // $( '.nido-reportes-numericos-entradas img' ).on( 'click', function() {
        //     var html = '<canvas id="nido-grafica-entrada"></canvas>';
        //     nido_actualizar_graficas();
        //     $.confirm( {
        //         title: 'Confirmar',
        //         theme: 'nido',
        //         icon: 'fa fa-exclamation-triangle',
        //         content: html,
        //         buttons: {
        //             Si: function () {
        //             },
        //             No: function () {
        //             }
        //         }
        //     } );
        // } );


        /*
         *  Funcionalidad de la página de las gráficas
         */

        $( '.nido-reportes-tabs .nido-reportes-grupal' ).on( 'click', function() { 
            $( '#nido-reportes-grupal' ).show(); 
            $( this ).css( 'border-bottom', '8px solid orange' );
            $( '.nido-reportes-tabs .nido-reportes-individual' ).css( 'border-bottom', '8px solid transparent' );
        } );

        $( '.nido-reportes-tabs .nido-reportes-individual' ).on( 'click', function() { 
            $( '#nido-reportes-grupal' ).hide(); 
            $( this ).css( 'border-bottom', '8px solid orange' );
            $( '.nido-reportes-tabs .nido-reportes-grupal' ).css( 'border-bottom', '8px solid transparent' );
        } );

        $( '.nido-entradas-boton' ).on( 'click', nido_toggle_button_entrada );
        $( '.nido-salidas-boton' ).on( 'click', nido_toggle_button_salida );
        $( '.nido-incidentes-boton' ).on( 'click', nido_toggle_button_incidentes );

        function nido_toggle_button_entrada() {
            var boton = $( '#nido-grafica-entrada' );
            var boton_flecha = $( this ).find( 'label' );

            if ( boton_flecha.text() == '▼' ) {
                boton_flecha.animateRotate( -90, 200, 'linear', function () {
                    boton_flecha.text( '►' );
                    boton_flecha.css( 'transform', 'rotate( 0 )' );
                } );
                // boton.hide();
                boton.slideUp();
            }
            else {
                boton_flecha.animateRotate( 90, 200, 'linear', function () {
                    boton_flecha.text( '▼' );
                    boton_flecha.css( 'transform', 'rotate( 0 )' );
                } );
                // boton.show();
                boton.slideDown();
            }
        }

        function nido_toggle_button_salida() {
            var boton = $( '#nido-grafica-salida' );
            var boton_flecha = $( this ).find( 'label' );

            if ( boton_flecha.text() == '▼' ) {
                boton_flecha.animateRotate( -90, 200, 'linear', function () {
                    boton_flecha.text( '►' );
                    boton_flecha.css( 'transform', 'rotate( 0 )' );
                } );
                // boton.hide();
                boton.slideUp();
            }
            else {
                boton_flecha.animateRotate( 90, 200, 'linear', function () {
                    boton_flecha.text( '▼' );
                    boton_flecha.css( 'transform', 'rotate( 0 )' );
                } );
                // boton.show();
                boton.slideDown();
            }
        }

        function nido_toggle_button_incidentes() {
            var boton = $( '#nido-grafica-incidentes' );
            var boton_flecha = $( this ).find( 'label' );

            if ( boton_flecha.text() == '▼' ) {
                boton_flecha.animateRotate( -90, 200, 'linear', function () {
                    boton_flecha.text( '►' );
                    boton_flecha.css( 'transform', 'rotate( 0 )' );
                } );
                // boton.hide();
                boton.slideUp();
            }
            else {
                boton_flecha.animateRotate( 90, 200, 'linear', function () {
                    boton_flecha.text( '▼' );
                    boton_flecha.css( 'transform', 'rotate( 0 )' );
                } );
                // boton.show();
                boton.slideDown();
            }
        }

        $.fn.animateRotate = function( angle, duration, easing, complete ) {
          var args = $.speed( duration, easing, complete );
          var step = args.step;
          return this.each( function( i, e ) {
            args.complete = $.proxy( args.complete, e );
            args.step = function( now ) {
              $.style( e, 'transform', 'rotate(' + now + 'deg)' );
              if ( step ) return step.apply( e, arguments );
            };

            $( {deg: 0} ).animate( {deg: angle}, args );
          } );
        };

        /*  Funciones para subir archivos */
        $( '.nido-boton-subir' ).on( 'click', function( e ){
            e.preventDefault;

            var fd = new FormData();
            var files_data = $( '.nido-archivos' ); // The <input type="file" /> field
            
            // Loop through each data and create an array file[] containing our files data.
            $.each( $( files_data ), function( i, obj ) {
                $.each( obj.files,function( j,file ){
                    fd.append( 'files[' + j + ']', file );
                } )
            } );
            
            // our AJAX identifier
            fd.append( 'action', 'nido_subir_archivos' );  
            
            $.ajax( {
                type: 'POST',
                url: nido_ajax_object.ajax_url,
                data: fd,
                contentType: false,
                processData: false,
                success: function( response ) {

                    if ( response.success ) {

                        var src_attachment_img = '';

                        switch( response.data[1] ) {
                            case 'jpg': 
                            case 'png': 
                            case 'gif': 
                            case 'bmp': 
                            case 'jpeg': 
                                src_attachment_img = response.data[0];
                                break;
                            case 'pdf': 
                                src_attachment_img = nido_ajax_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/pdf-icon.png';
                                break;
                            case 'docx': 
                            case 'doc': 
                                src_attachment_img = nido_ajax_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/DOCX.png';
                                break;
                            case 'xlsx': 
                            case 'xls': 
                                src_attachment_img = nido_ajax_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/xlsx.png';
                                break;
                            case 'ppt': 
                            case 'pptx':
                                src_attachment_img = nido_ajax_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/pptx.png';
                                break;
                            default: src_attachment_img = response.data[0];
                        }

                        // alert( src_attachment_img );
                        var visible_string = '';

                        if ( src_attachment_img != response.data[0] )
                            visible_string = response.data[0].substr(response.data[0].lastIndexOf('/') + 1);
                        else
                            visible_string = '';

                        var html_element = '<a href="' + response.data[0] + '" target="_blank"><img src="' + src_attachment_img + '"><br>' + visible_string + '</a>';

                        $( '#nido-attachment' ).val( html_element );

                        $( '#nido_forma_mensaje' ).submit();
                    }
                }
            } );
        } );

        $( '.nido-archivos' ).on( 'change', function( e ) {
            $( '.nido-boton-subir' ).click();
            $( '.nido-message-uploads img' ).hide();
            $( '.nido-message-uploads .nido-loading' ).show();
        } );

        $( '.nido-adjuntar' ).on( 'click', function() {
            $( '.nido-archivos' ).click();
        } );

        $( '.nido-flaggear' ).on( 'click', function() {
            $.confirm( {
                title: '¡Incidente!',
                content: function () {
                    var self = this;
                    return $.ajax( {
                        data: {
                            action: 'nido_opciones_ninos',
                            familia: $( '#destinatario' ).val()
                        },
                        type: 'post',
                        url: nido_ajax_object.ajax_url,
                    } ).done( function ( response ) {

                        if ( response.success ) {
                            /*  Crear el elemento de HTML select */
                            var checkboxes_kids = '';
                            var index = 0;
                            response.data.forEach( function( current_kid )  {
                                checkboxes_kids += '<div class="nido-checkbox-wrapper">';
                                checkboxes_kids += '<input id="problem-kid-' + index + '" class="nido-checkbox-2" type="checkbox" name="problem-kid" value="' + current_kid + '"></input>';
                                checkboxes_kids += '<label for="problem-kid-' + index + '"></label>';
                                checkboxes_kids += '<span class="nido-nino-first-name">' + current_kid + '</span>';
                                checkboxes_kids += '</div>';
                                index++;
                            } );
                            self.setContent( '' +
                                '<form action="" class="nido-forma-incidente">' +
                                '<div class="nido-incidente-wrapper">' +
                                '<label class="nido-incidente-label">Por favor, reporte cuál fue el incidente y quién estuvo involucrado</label><br>' +
                                checkboxes_kids +
                                '<input type="text" placeholder="Incidente" class="nido-incidente" required />' +
                                '<div class="nido-validate"></div>' +
                                '</div>' +
                                '</form>'
                            );
                        }
                        else {
                            console.log( response );
                            self.setContent( 'Falta información para esta solicitud: Obtener opciones de niños.' );
                        }
                    } ).fail( function() {
                        self.setContent( 'Algo salió mal. Vuelva a intentar.' );
                    } );
                },
                theme: 'nido',
                icon: 'fa fa-exclamation-triangle',
                buttons: {
                    formSubmit: {
                        text: 'Enviar',
                        btnClass: 'btn-blue',
                        action: function () {
                            var incidente = this.$content.find( '.nido-incidente' ).val();
                            if ( incidente ) {
                                $( '.nido-incidente-wrapper input.nido-incidente' ).css( 'border-color', '#cccccc' );

                                /*  Obtener los nombres de los niños involucrados */
                                var problem_kids = [];
                                this.$content.find( '.nido-checkbox-2' ).each( function() {
                                    if ( $( this ).context.checked ) {
                                        problem_kids.push( $( this ).context.value );
                                    }
                                } );

                                if ( problem_kids.length == 0 ) {
                                    $( '.nido-validate' ).text( '* Seleccione los niños envueltos en el incidente' );
                                    $( '.nido-checkbox-2 + label' ).addClass( 'nido-error' );
                                    return false;
                                }
                                else {
                                    $( '.nido-checkbox-2 + label' ).removeClass( 'nido-error' );
                                }

                                $( '#nido_mensaje' ).val( '[Incidente con ' + problem_kids.join( ' y ' ) + ']: ' + incidente );
                                $( '#nido_forma_mensaje' ).submit();

                                /*  Agregar el incidente a la base de datos */
                                $.ajax( { 
                                    data: {
                                        action: 'nido_reportar_incidente',
                                        incidente: '[Incidente con ' + problem_kids.join( ' y ' ) + ']: ' + incidente,
                                        familia: $( '#destinatario' ).val(),
                                        fecha_hora: $( '#nido-tod' ).text()
                                    },
                                    type: 'post',
                                    url: nido_ajax_object.ajax_url,
                                    success: function( response ) {
                                        $.confirm( {
                                            title: 'Incidente Guardado',
                                            content: 'El incidente se ha guardado y se reportará.',
                                            type: 'green',
                                            theme: 'nido',
                                            typeAnimated: true,
                                            buttons: {
                                                OK: function () {
                                                }
                                            }
                                        } );
                                        // console.log( response );
                                    },
                                    error: function( response ) {
                                        $.confirm( {
                                            title: 'Incidente no Guardado',
                                            content: 'Por un error inesperado del servidor o de la conexión, el incidente no ha sido guardado. Intente nuevamente.',
                                            type: 'red',
                                            theme: 'nido',
                                            typeAnimated: true,
                                            buttons: {
                                                OK: function () {
                                                }
                                            }
                                        } );
                                        // console.log( response );
                                    }
                                } );
                            }
                            else {
                                $( '.nido-validate' ).text( '* Registre un incidente.' );
                                $( '.nido-incidente-wrapper input.nido-incidente' ).css( 'border-color', 'red' );
                                return false;
                            }

                            return true;
                        }
                    },
                    Cancelar: function () {
                    },
                },
                onContentReady: function () {
                    // bind to events
                    var jc = this;
                    this.$content.find( 'form' ).on( 'submit', function ( e ) {
                        // if the user submits the form by pressing enter in the field.
                        e.preventDefault();
                        jc.$$formSubmit.trigger( 'click' ); // reference the button and click it
                    } );
                  $( '.nido-incidente' ).focus();
                }
            } );
        } );


        /*  Funcion para los avatares */
        $( '.nido-avatar' ).on( 'click', function() {

            var this_avatar = $( this );

            var avatares = '';
            var dir = nido_ajax_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/avatars/';
            if ( $( this ).hasClass( 'nido-familia' ) ) {
                avatares = [
                    dir + 'nido_familia.png'
                ];
            }
            if ( $( this ).hasClass( 'nido-kid' ) ) {
                avatares = [
                    dir + 'bebe.png',
                    dir + 'nino_lentes_cuadradros.png',
                    dir + 'nino_lentes_redondos.png',
                    dir + 'bebe_brown_guino.png',
                    dir + 'bebe_brown_ojos_redondo.png',
                    dir + 'nino_brown_lentes_redondos.png',
                    dir + 'nina_pelo_negro_rizo.png',
                    dir + 'nina_pelo_negro.png',
                    dir + 'nina_pelo_rojo_rizo.png',
                    dir + 'nina_pelo_rojo.png',
                    dir + 'nina_pelo_rubio_rizo.png',
                    dir + 'nina_pelo_rubio.png',
                    dir + 'nina_brown_pelo_rizo.png',
                    dir + 'nina_brown.png'
                ];
            }
            if ( $( this ).hasClass( 'nido-guardian' ) ) {
                avatares = [
                    dir + 'papa.png',
                    dir + 'mama.png',
                    dir + 'papa_brown.png',
                    dir + 'mamá_brown.png'
                ];
            }
            if ( $( this ).hasClass( 'nido-empleado' ) ) {
                avatares = [
                    dir + 'papa.png',
                    dir + 'mama.png',
                    dir + 'papa_brown.png',
                    dir + 'mamá_brown.png'
                ];
            }
            if ( $( this ).hasClass( 'nido-empleado' ) ) {
                avatares = [
                    dir + 'papa.png',
                    dir + 'mama.png',
                    dir + 'papa_brown.png',
                    dir + 'mamá_brown.png'
                ];
            }
            if ( $( this ).hasClass( 'nido-cuido' ) ) {
                avatares = [
                    dir + 'nido-icono-nido.png'
                ];
            }

            $.confirm( {
                title: 'Selecciona un Avatar',
                content: function () {
                    var self = this;
                    return $.ajax( {
                        data: {
                            action: 'nido_opciones_avatares',
                            source: $( '.nido-database-avatars' ).val()
                        },
                        type: 'post',
                        url: nido_ajax_object.ajax_url,
                    } ).done( function ( response ) {

                        if ( response.success ) {

                            //console.log( avatares_ajax );
                            //console.log( avatares );

                            var avatares_ajax = response.data.split( ',' );
                            var avatares_final = avatares.concat( avatares_ajax );
                            var avatares_html = '';

                            avatares_final.forEach( function( current_avatar ) {
                                avatares_html += '<img class="nido-avatar-option" src="' + current_avatar + '"></img>';
                            } );

                            /*  Crear el elemento de HTML */
                            self.setContent(
                                '<div class="nido-avatars-wrapper">' +
                                    avatares_html +
                                    '<input type="hidden" id="nido-save-temp-avatar"/>' +
                                '</div>' +

                                '<script>' +
                                    "jQuery( '.nido-avatar-option' ).on( 'click', function() {" +
                                        "jQuery( '.nido-avatar-option' ).removeClass( 'nido-active' );" +
                                        "jQuery( this ).addClass( 'nido-active' );" +
                                        "jQuery( '#nido-save-temp-avatar' ).val( jQuery( this ).attr( 'src' ) );" +
                                    "} );" +
                                '</script>'
                            );
                        }
                        else {
                            if ( $( '.nido-database-avatars' ).val() == undefined ) {
                                self.setContent( 'Editar información de Empleado dando click en el ícono <img style="width: 20px" src="' + nido_ajax_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/nido-pencil-1.png" /> para seleccionar Avatar o subir una nueva fotografía como Avatar.' );
                                $( '.jconfirm-buttons button' ).hide();
                                $( '.jconfirm-buttons button' ).last().show();

                            }
                            else {
                                self.setContent( 'Falta información para esta solicitud: Obtener Avatares.' );
                            }
                        }
                    } ).fail( function() {
                        self.setContent( 'Algo salió mal. Vuelva a intentar.' );
                    } );
                },
                theme: 'nido',
                icon: 'fa fa-user-circle',
                buttons: {
                    Seleccionar: {
                        text: 'Seleccionar',
                        btnClass: 'btn-blue',
                        action: function () {
                            if ( this_avatar.attr( 'nido-avatar-in' ) != undefined ) {
                                var find_avatar = this_avatar.attr( 'nido-avatar-in' );
                                if ( $( '#' + find_avatar ) != undefined ) {
                                    $( '#' + find_avatar ).val( this.$content.find( '#nido-save-temp-avatar' ).val() );
                                }
                            }
                            nido_refresh_avatars();
                        }
                    },
                    Cancelar: function () {
                        console.log( 'Cancelar' );
                    },
                }
            } );
        } );

        function nido_avatar_click_events() {
            $( '.nido-avatar-option' ).on( 'click', function() {
                $( '.nido-avatar-option' ).removeClass( 'nido-active' );
                $( this ).addClass( 'nido-active' );
                console.log( 'yes' );
            } );
        }


        /*
         *  Funcionamiento del calendario de los reportes.
         */

        // $( '#nido-datepicker' ).datepicker();

        $( '.daterangepicker .ranges li:not(:last-child)' ).on( 'click', function() {
            setTimeout( nido_actualizar_numeros, 100 );
        } );

        $( '.daterangepicker .btn-success' ).on( 'click', function() {
            setTimeout( nido_actualizar_numeros, 100 );
        } );

        if ( $( '#nido-daterangepicker' ).length > 0 ) {
            nido_actualizar_numeros();
        }

        $( '.nido-seccion-grupal' ).on( 'click', function() {
            $( '#nido-reporte-individual' ).val( '' );
            setTimeout( nido_actualizar_numeros, 100 );
        } );

        function nido_actualizar_numeros() {
            $( '.nido-reportes-number' ).hide();
            $( '.nido-loading-apple' ).not( $( '.nido-seccion-individual .nido-loading-apple' ) ).show();
            /*  Limpiar los elementos de los registros por dia */
            $( '.nido-reportes-diarios' ).empty().append(
                $( '<img>' ).addClass( 'nido-loading-apple' ).attr( 'src', nido_ajax_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/loading_apple.gif' )
            );

            // console.log( JSON.stringify( $( '#nido-daterangepicker' ).val().split( ' - ' ) ) );

            $.ajax( { 
                data: {
                    action: 'nido_reportes_numeros',
                    daterange: JSON.stringify( $( '#nido-daterangepicker' ).val().split( ' - ' ) ),
                    kid: $( '#nido-reporte-individual' ).val()
                },
                type: 'post',
                url: nido_ajax_object.ajax_url,
                success: function( response ) {
                    if ( response.success ) {
                        $( '.nido-reportes-number' ).show();
                        $( '.nido-loading-apple' ).hide();

                        /*  Reemplazar las entradas y salidas */
                        $( '.nido-reportes-numericos-entradas .nido-reportes-number' ).text( response.data.T.e );
                        $( '.nido-reportes-numericos-salidas .nido-reportes-number' ).text( response.data.T.s );
                        $( '.nido-reportes-numericos-tardanzas .nido-reportes-number' ).text( response.data.T.t );
                        $( '.nido-reportes-numericos-incidentes .nido-reportes-number' ).text( response.data.T.i );

                        /*  Limpiar div */
                        $( '.nido-reportes-diarios' ).empty();

                        /*  Crear elementos HTML para los registros por dia */
                        var mes_prev = '0';
                        var meses_string = [
                            'Enero', 
                            'Febrero', 
                            'Marzo', 
                            'Abril', 
                            'Mayo', 
                            'Junio', 
                            'Julio', 
                            'Agosto', 
                            'Septiembre', 
                            'Octubre', 
                            'Noviembre', 
                            'Diciembre'
                        ];

                        /*  Recorrer el arreglo y crear los elementos en HTML */
                        for ( var fecha_reporte in response.data.D ) {

                            var mes = fecha_reporte.split( '/' )[1];
                            var class_aux = '';

                            if ( response.data.D[fecha_reporte]['e'] == 0 &&
                                 response.data.D[fecha_reporte]['s'] == 0 &&
                                 response.data.D[fecha_reporte]['t'] == 0 &&
                                 response.data.D[fecha_reporte]['i'] == 0 )
                                continue;

                            if ( mes_prev !== mes ) {
                                $( '<div>' ).addClass( 'nido-reporte-mes' ).append(
                                    $( '<label>' ).addClass( 'nido-reportes-mes' ).text( meses_string[parseInt( mes ) - 1] )
                                ).appendTo( '.nido-reportes-diarios' );
                                class_aux = ' nido-add-border';
                            }

                            $( '<div>' ).addClass( 'nido-reporte-dia' + class_aux ).append(
                                $( '<label>' ).addClass( 'nido-reporte-numero-dia' ).text( fecha_reporte.split( '/' )[0] ),
                                $( '<div>' ).addClass( 'nido-reportes-record-diario' ).append(
                                    $( '<div>' ).addClass( 'nido-record-entradas' ).append(
                                        $( '<div>' ).append(
                                            $( '<img>' ).attr( 'src', nido_ajax_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/nido-reportes-entrada.png' ),
                                            $( '<label>' ).addClass( 'nido-record-accion' ).text( 'Entradas' )
                                        ),
                                        $( '<label>' ).addClass( 'nido-record-cantidad' ).text( response.data.D[fecha_reporte]['e'] )
                                    ),
                                    $( '<div>' ).addClass( 'nido-record-salidas' ).append(
                                        $( '<div>' ).append(
                                            $( '<img>' ).attr( 'src', nido_ajax_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/nido-reportes-salidas.png' ),
                                            $( '<label>' ).addClass( 'nido-record-accion' ).text( 'Salidas' )
                                        ),
                                        $( '<label>' ).addClass( 'nido-record-cantidad' ).text( response.data.D[fecha_reporte]['s'] )
                                    ),
                                    $( '<div>' ).addClass( 'nido-record-tardanzas' ).append(
                                        $( '<div>' ).append(
                                            $( '<img>' ).attr( 'src', nido_ajax_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/nido-reportes-tardanzas.png' ),
                                            $( '<label>' ).addClass( 'nido-record-accion' ).text( 'Tardanzas' )
                                        ),
                                        $( '<label>' ).addClass( 'nido-record-cantidad' ).text( response.data.D[fecha_reporte]['t'] )
                                    ),
                                    $( '<div>' ).addClass( 'nido-record-incidentes' ).append(
                                        $( '<div>' ).append(
                                            $( '<img>' ).attr( 'src', nido_ajax_object.site_url + '/wp-content/themes/generatepress_child/assets/icons/nido-reportes-incidentes.png' ),
                                            $( '<label>' ).addClass( 'nido-record-accion' ).text( 'Incidentes' )
                                        ),
                                        $( '<label>' ).addClass( 'nido-record-cantidad' ).text( response.data.D[fecha_reporte]['i'] )
                                    )
                                )
                            ).appendTo( '.nido-reportes-diarios' );

                            mes_prev = fecha_reporte.split( '/' )[1];
                        }

                        /*  Actualizar las gráficas */
                        $( '#nido-grafica-entrada-2' ).replaceWith( '<canvas id="nido-grafica-entrada-2"></canvas>' );
                        $( '.nido-grafica-wrapper iframe' ).remove();

                        /*  Llenar los datos para las gráficas */
                        var labels = response.data.R;
                        var entradas = [];
                        var salidas = [];
                        var tardanzas = [];
                        var incidentes = [];

                        labels.forEach( function( fecha_it ) {
                            if ( response.data.D[fecha_it] === undefined ) {
                                entradas.push( 0 );
                                salidas.push( 0 );
                                tardanzas.push( 0 );
                                incidentes.push( 0 );
                            }
                            else {
                                entradas.push( response.data.D[fecha_it]['e'] );
                                salidas.push( response.data.D[fecha_it]['s'] );
                                // tardanzas.push( Math.floor(Math.random() * 10 ) + 1 );
                                tardanzas.push( response.data.D[fecha_it]['t'] );
                                incidentes.push( response.data.D[fecha_it]['i'] );
                            }
                        } );

                        nido_crear_grafica( 
                            $( '#nido-grafica-entradas' ),      // Canvas
                            'Entradas',                         // Título
                            labels,                             // Eje X
                            entradas,                           // Eje Y
                            'rgb( 0, 204, 173 )',               // Color de Línea
                            'rgb( 0, 209, 178 )'                // Color de Fondo
                        );
                        nido_crear_grafica( 
                            $( '#nido-grafica-salidas' ),       // Canvas
                            'Salidas',                          // Título
                            labels,                             // Eje X
                            salidas,                            // Eje Y
                            'rgb( 255, 87, 101 )',              // Color de Línea
                            'rgb( 255, 101, 113 )'              // Color de Fondo
                        );
                        nido_crear_grafica( 
                            $( '#nido-grafica-tardanzas' ),     // Canvas
                            'Tardanzas',                        // Título
                            labels,                             // Eje X
                            tardanzas,                          // Eje Y
                            'rgb(218, 124, 55)',                // Color de Línea
                            'rgb(252, 142, 61)'                 // Color de Fondo
                        );
                        nido_crear_grafica( 
                            $( '#nido-grafica-incidentes' ),    // Canvas
                            'Incidentes',                       // Título
                            labels,                             // Eje X
                            incidentes,                         // Eje Y
                            'rgb(230, 149, 0)',                 // Color de Línea
                            'rgb(255, 165, 0)'                  // Color de Fondo
                        );
                    }
                },
                error: function( response ) {
                    $.confirm( {
                        title: 'Error',
                        content: 'Hubo un error en el servidor. Intente nuevamente.',
                        type: 'red',
                        theme: 'nido',
                        typeAnimated: true,
                        buttons: {
                            close: function () {
                            }
                        }
                    } );
                }
            } );
        }

        /*
         *  Función para el autocomplete de los reportes individuales
         */

        $( '#nido-reporte-individual' ).autocomplete( {
            source: function( request, response ) {
                $( '.nido-sombra-individual' ).hide();
                $( '.nido-seccion-individual .nido-loading-apple' ).show();
                $.ajax( { 
                    data: {
                        action: 'nido_opciones_ninos_cuido',
                        cuido: $( '#nido-cuido-id' ).val()
                    },
                    type: 'post',
                    url: nido_ajax_object.ajax_url,
                    success: function( ajax_response ) {
                        $( '.nido-sombra-individual' ).show();
                        $( '.nido-seccion-individual .nido-loading-apple' ).hide();
                        if ( ajax_response.success ) {
                            response( ajax_response.data );
                        }
                    },
                    error: function( ajax_response ) {
                        alert( 'Hay un error, inténtelo nuevamente.' );
                    }
                } );
            },
            select: function( event, ui ) {
                /*  Trigger la función para actualizar los reportes */
                setTimeout( nido_actualizar_numeros, 100 );
            }
        } );

        $( '#nido-reporte-individual' ).on( 'click', function() { $( this ).val( '' ); } );

    } );

    /*  Funciones Auxiliares */
    function nido_crear_grafica( canvas, title, labels, data, line_color, background_color ) {
        var chart_entradas = new Chart( canvas, {
            "type" : "line",
            "data" : {
                "labels" : labels,
                "datasets" : [
                    {"label" : 
                        title,
                        "data" : data,
                        "fill" : true,
                        "borderColor" : line_color,
                        'backgroundColor' : background_color,
                        "lineTension" : 0.1
                    }
                ]
            },
            "options" : {
                'scales': {
                    'yAxes': [{
                        'ticks': {
                            'beginAtZero' : true,
                            userCallback : function( label, index, labels ) {
                                if ( Math.floor( label ) === label ) {
                                    return label;
                                }
                            },
                        }
                    }]
                }
            },
            "scaleStepWidth" : 1
        } );
    }

} )( jQuery );