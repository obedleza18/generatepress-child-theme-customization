<?php

/*
 *  Este archivo contiene toda el código relacionado a los shortcodes creados. En la primera sección
 *  de este archivo podemos encontrar las Funciones Auxiliares a los Shortcodes. La primer función,
 *  nido_shortcode_layouts, sirve para cargar los layouts que el shortcode requiera.
 *
 *  Si pensamos nuestro sistema como un MVC (Model, View, Controller). La función
 *  nido_shortcode_layouts es nuestro Controlador quien conecta los Modelos con las Vistas. Por
 *  simplicidad y por la forma en la que trabaja WordPress, algunos Modelos están en el mismo lugar
 *  que las Vistas. Dichos archivos se encuentran dentro de la carpeta layouts. En otros casos los
 *  Modelos son WordPress.
 */

function nido_shortcode_layouts( $layout ) {

    $page_html = '';

    /*  Lo primero es determinar el usuario que ha iniciado sesión */
    $user = wp_get_current_user();

    /*  Esta estructura nos va a ayudar con la detección de errores en la ejecución */
    $results = array(
        'html'      => '',
        'error'     => ''
    );

    /*
     *  Si se accesa la página en donde está este shortcode por un usuario que no está registrado
     *  el sistema los va a redireccionar al Login y termina la ejecución del código.
     */
    if ( !isset( $user->roles[0] ) ) {
        $results['error'] = 
            '<p>Esta página es para Usuarios Registrados. Será redirigido.</p>' . 
            '<meta http-equiv="refresh" content="0; url=/login/" />';
        return $results;
    }

    $role = $user->roles[0];

    /*
     *  Por default, el administrador va a efectuar la administración por el Dashboard de WP. Si el
     *  Administrador intenta accesar las páginas donde se encuentra el shortcode actual, se invita-
     *  rá al Admin a visitar el Dashboard de WordPress.
     */
    /*if ( $role == 'administrator' ) {
        $results['error'] = 
            '<p>Hola Administrador. Puedes hacer lo que quieras en esta página pero te ' .
            'recomendamos entrar al <a href="/wp-admin/">' .
            'Dashboard exclusivo de Administradores</a>.</p>';
        return $results;
    }*/

    /*  Si el usuario es Cuido entonces cargar los layouts relacionados al Cuido */
    // if ( $role == 'cuido' || $role == 'familia' ) {
        $filepath = get_stylesheet_directory() . '/layouts/nido-' . $layout . '-' . $role . '.php';

        if ( file_exists( $filepath ) ) {
            ob_start();
            include( $filepath );
            $page_html .= ob_get_clean();
        } 
    // }

    /*  Regresar al shortcode el HTML creado por el Layout */
    $results['html'] = $page_html;

    return $results;
}


function get_the_meta( $array ) {
    /*  Función sencilla para accesar metadata de los posts */
    return $array[0];
}


/*
 *  Shortcodes: A partir de esta línea de código están listados todos los ShortCodes que serán
 *  puestos en WordPress o Formidable para garantizar la funcionalidad completa del sistema. Para
 *  cada shortcode se podrá encontrar una descripción de su funcionalidad.
 *
 *  Todos los shortcodes
 *  creados cuentan con una estructura para manejar los errores o el código HTML que devuelven.
 *  Dicha estructura es un arreglo asociativo llamado $results que contiene los campos de 'error' y
 *  'html'. Como sus nombres lo indican, el campo 'error' devuelve a la página el HTML de algún 
 *  error que haya surgido durante la ejecución o validación del layout. Cuando no existe error,
 *  entonces simplemente el shortcode va a regresar el HTML a como lo haya creado el archivo PHP
 *  del layout.
 *
 *  Cabe mencionar que cada layout tiene la capacidad de generar contenido dinámico dependiendo del
 *  usuario que está generando el layout. Por ejemplo, si el usuario es un cuido y se accesa la
 *  página de 'Mi Cuenta', el layout para este Dashboard va a crear tres botones que nos ligan a 
 *  páginas con diferentes funcionalidades solamente para Cuido.
 */


add_shortcode( 'nido-dashboard', 'nido_dashboard' );

function nido_dashboard( $atts ){

    /*  Este shortcode carga el layout para el dashboard */

    $results = nido_shortcode_layouts( 'dashboard' );

    if ( !empty( $results['error'] ) ) {
        return $results['error'];
    }

    return $results['html'];
}


add_shortcode( 'nido-mi-cuenta', 'nido_mi_cuenta' );

function nido_mi_cuenta() {

    /*  Este shortcode carga el layout para la página 'Mi Cuenta' */

    $results = nido_shortcode_layouts( 'mi-cuenta' );

    if ( !empty( $results['error'] ) ) {
        return $results['error'];
    }

    return $results['html'];
}


add_shortcode( 'nido-empleados', 'nido_empleados' );

function nido_empleados() {

    /*
     *  Este shortcode carga el layout para los empleados. En sí este shortcode no genera una página
     *  completa, sino que completa la información de la página de Mi Cuido para los usuarios Cuido.
     *  Este shortcode es actualmente llamado desde una Vista de Formidable.
     */

    $results = nido_shortcode_layouts( 'empleados' );

    if ( !empty( $results['error'] ) ) {
        return $results['error'];
    }

    return $results['html'];
}


add_shortcode( 'nido-asistencia-hoy', 'nido_asistencia_hoy' );

function nido_asistencia_hoy() {

    /*  Este shortcode carga el layout para la página de Asistencia de Hoy */

    $results = nido_shortcode_layouts( 'asistencia-hoy' );

    if ( !empty( $results['error'] ) ) {
        return $results['error'];
    }

    return $results['html'];
}


add_shortcode( 'nido-mensajes', 'nido_mensajes' );

function nido_mensajes() {

    /*  Este shortcode carga el layout para la página de los Mensajes */

    $results = nido_shortcode_layouts( 'mensajes' );

    if ( !empty( $results['error'] ) ) {
        return $results['error'];
    }

    return $results['html'];
}



add_shortcode( 'nido-conversacion', 'nido_conversacion' );

function nido_conversacion() {

    /*  Este shortcode carga el layout para la página de los Mensajes */

    $results = nido_shortcode_layouts( 'conversacion' );

    if ( !empty( $results['error'] ) ) {
        return $results['error'];
    }

    return $results['html'];
}


add_shortcode( 'nido-tod', 'nido_tod' );

function nido_tod( $atts ){
    /*  Agrega HTML para que jQuery pueda agregar la hora a las páginas */
    return '<div id="nido-tod"></div>';
}


add_shortcode( 'nido-family-count', 'nido_family_count' );

function nido_family_count( $atts ){

    /*  Esta función cuenta las familias que hay por cuido */

    $page_html = '';

    $atts['field'] = 'id';

    $cuido_id_actual = nido_current( $atts );

    $args = array(
        'post_type'     => 'nido-familia',
        'meta_query'    => array(
            array(
                'key'   => 'wpcf-cuido-id-familia',
                'value' => $cuido_id_actual,
            )
        )
    );

    /*  Se efectúa el Query de WordPress */
    $query = new WP_Query( $args );

    $page_html .= count( $query->posts );

    return $page_html;
}


add_shortcode( 'nido-esc-email', 'nido_esc_email' );

function nido_esc_email( $atts ){

    /*
     *  Esta función no se utiliza ya que en lugar de localizar a los usuarios por su email se loca-
     *  lizan por su ID único.
     */

    $page_html = '';

    $email = ( isset( $atts['id'] ) ) ? $atts['id'] : '';

    $email = str_replace( '.', '-', $email );

    $email = str_replace( '@', '-', $email );

    $page_html .= $email;

    return $page_html;
}


add_shortcode( 'nido-display-on-edit-family', 'nido_display_on_edit_family' );

function nido_display_on_edit_family( $atts, $content ){

    /*
     *  Shortcode para sombrear el elemento de la barra de navegación de 'Mi Cuenta' cuando se está
     *  utilindo la forma de Registro de Familia para editar el contenido de alguna familia.
     */

    $page_html = '';
    $action = '';
    $family_name = '';

    if ( isset( $_GET['frm_action'] ) )
        $action = $_GET['frm_action'];
    else
        return '';

    if ( $action == 'edit' ) {
        $page_html .= $content;

        $page_html .= 
            '<style>' . 
                '#menu-item-917 { background: linear-gradient(#eae7c4, rgba(255,0,0,0)); }' .
                '.menu-item-917 { background-color: #eae7c4; }' .
            '</style>';
    }

    return $page_html;
}


add_shortcode( 'nido-estatus-nino', 'nido_estatus_nino' );

function nido_estatus_nino( $atts ) {

    /*
     *  Muestra el estatus de los niños. Si se encuentran dentro o fuera del Cuido. Esto es para que
     *  los usuarios puedan determinar qué función ejecutar (Entrada o Salida).
     */

    $html = '';

    /*  Normalizar los parámetros del shortcode */
    $atts = array_change_key_case( ( array ) $atts, CASE_LOWER );

    /*  Establecer parámetros por defecto */
    $attributes = shortcode_atts( [
        'id'    => '0',
        'num'   => '0'
    ], $atts );


    /*
     *  Correr el shortcode que nos va a conseguir el teléfono de la familia que es el principal
     *  identificador de la familia. Después se procederá a tomar el otro atributo y buscar el
     *  estatus de dicho niño.
     */

    $shortcode_para_id = '[frm-field-value field_id=' . $attributes['id'] . ' entry=pass_entry]';

    /*  Se obtiene el teléfono de la familia */
    $telefono_familia = do_shortcode( $shortcode_para_id );

    /*
     *  Se mandan los argumentos para el WP_Query. Aquí se tienen que cumplir dos condiciones para
     *  que se encuentre el post deseado. La primer condicion es que el teléfono de la familia
     *  se encuentre dentro del post. Con esto aseguramos que efectivamente esta familia tiene
     *  niños. Después se busca el offset del niño dependiendo el orden en el que se inscribieron.
     *  De esta forma se puede encontrar el respectivo niño y mostrar su estatus.
     */

    $args = array(
        'post_type'     => 'nido-nino',
        'meta_query'    => array(
            'relation'  => 'AND',
            array(
                'key'   => 'wpcf-telefono-de-familia',
                'value' => $telefono_familia,
            ),
            array(
                'key'   => 'wpcf-id-de-familia',
                'value' => $attributes['num'],
            )
        )
    );

    /*  Se efectúa el Query de WordPress */
    $query = new WP_Query( $args );

    /*  Si existen posts que hayan cumplido las condiciones, entonces mostrar el estatus */
    if ( isset( $query->posts[0] ) ) {

        $post = $query->posts[0];

        $meta = get_post_meta( $post->ID );

        $html = $meta['wpcf-estatus-de-entrada-salida'][0];
    }
    
    return $html;
}


add_shortcode( 'nido-get-avatar', 'nido_get_avatar_shortcode' );

function nido_get_avatar_shortcode( $atts ) {

    $attributes = shortcode_atts( [
        'id'    => '0'
    ], $atts );

    $shortcode_para_id = '[frm-field-value field_id=' . $attributes['id'] . ' entry=pass_entry]';

    $id_familia = do_shortcode( $shortcode_para_id );

    return apply_filters( 'nido_get_avatar', $id_familia );
}


add_shortcode( 'nido-foto-nino', 'nido_foto_nino' );

function nido_foto_nino( $atts ) {

    $local = false;
    $path = ( $local ) ? '/nido' : '';

    /*
     *  Muestra las fotografías de los niños si es que existen.
     */

    $html = '';

    /*  Normalizar los parámetros del shortcode */
    $atts = array_change_key_case( ( array ) $atts, CASE_LOWER );

    /*  Establecer parámetros por defecto */
    $attributes = shortcode_atts( [
        'id'    => '0',
        'num'   => '0'
    ], $atts );


    /*
     *  Correr el shortcode que nos va a conseguir el teléfono de la familia que es el principal
     *  identificador de la familia. Después se procederá a tomar el otro atributo y buscar el
     *  estatus de dicho niño.
     */

    $shortcode_para_id = '[frm-field-value field_id=' . $attributes['id'] . ' entry=pass_entry]';

    /*  Se obtiene el teléfono de la familia */
    $id_familia = do_shortcode( $shortcode_para_id );


    /*
     *  Se mandan los argumentos para el WP_Query. Aquí se tienen que cumplir dos condiciones para
     *  que se encuentre el post deseado. La primer condicion es que el teléfono de la familia
     *  se encuentre dentro del post. Con esto aseguramos que efectivamente esta familia tiene
     *  niños. Después se busca el offset del niño dependiendo el orden en el que se inscribieron.
     *  De esta forma se puede encontrar el respectivo niño y mostrar su estatus.
     */

    $args = array(
        'post_type'     => 'nido-familia',
        'meta_query'    => array(
            array(
                'key'   => 'wpcf-id-familia',
                'value' => $id_familia,
            )
        )
    );

    /*  Se efectúa el Query de WordPress */
    $query = new WP_Query( $args );

    /*  Si existen posts que hayan cumplido las condiciones, entonces mostrar el estatus */
    if ( isset( $query->posts[0] ) ) {

        $post = $query->posts[0];

        $html = '<div class="nido-imagen-nino" style="background-image: url(\'';
        $html .= get_post_meta( $post->ID, 'wpcf-avatar-nino-' . $atts['num'], true  );
        $html .= '\')"></div>';
    }
    
    return $html;
}


add_shortcode( 'nido-email-usuario', 'nido_email_usuario' );

function nido_email_usuario() {

    /*
     *  Este shortcode solamente escribe en HTML el usuario actual. Esto nos sirve para las vistas de
     *  Formidable. Para que su sistema pueda entender la información que se quiere ver.
     */

    $user = wp_get_current_user();
    $email = '';

    if ( isset( $user->user_login ) ) {
        $email = $user->user_login;
    }

    return $email;
}


add_shortcode( 'nido-cuido-actual', 'nido_cuido_actual' );

function nido_cuido_actual() {

    /*
     *  Este shortcode solamente escribe en HTML el nombre del Cuido actual. Esto nos sirve para las 
     *  vistas de Formidable. Para que su sistema pueda entender la información que se quiere ver.
     */

    $user = wp_get_current_user();

    $cuido_name = '';

    if ( isset( $user->display_name ) ) {
        $cuido_name = $user->display_name;
    }

    return $cuido_name;
}


function nido_dashboard_aux( $atts ){

    /*
     *  Función Legacy: Esta función es para programar el Shortcode del Dashboard. En esta función se configuran
     *  los Paneles de Control de cada Usuario.
     */

    /*  Variable principal para regresar la cadena de HTML a la página en donde está el Shortcode */
    $page_html = '';

    /*  Traer información del Usuario */
    $user = wp_get_current_user();

    /*  Esta página solo puede ser accesada por usuarios con sesión iniciada */
    if ( !isset( $user->roles[0] ) ) {
        $error =    '<p>Esta página es para Usuarios Registrados. Será redirigido.</p>' . 
                    '<meta http-equiv="refresh" content="0; url=/login/" />';

        return $error;
    }

    $role = $user->roles[0];

    /*  Si el Administrador intenta ver su Dashboard dirigirlo manualmente al Dashboard de WP */
    if ( $role == 'administrator' ) {
        $message =  'Hola Administrador. Puedes hacer lo que quieras en esta página pero te ' .
                    'recomendamos entrar al <a href="/wp-admin/">' .
                    'Dashboard exclusivo de Administradores</a>.';

        return $message;
    }

    /*
     *  Traer información del Cuido. Esta función conecta la base de datos de usuarios con la base
     *  de datos de los Custom Post Types.
     */

    if ( $role == 'cuido' || $role == 'empleado' || $role == 'familia' ) {

        /*  Establecer el $meta_key con el que se va a buscar el POST */
        switch ($role) {

            case 'cuido': {
                $meta_key = 'wpcf-email-del-cuido';
                break;
            }

            case 'empleado': {
                $meta_key = 'wpcf-empleado-email';
                break;
            }
            
            case 'familia': {
                $meta_key = 'wpcf-email-del-guardian';
                break;
            }

            default: break;
        }

        $args = array(
            'post_status'       => 'published',
            'post_type'         => 'nido-' . $role,
            'meta_key'          => $meta_key,
            'meta_value'        => $user->data->user_login,
            'suppress_filters'  => true
        );

        $posts = get_posts( $args );

        /*  En teoría solamente va a haber un usuario con ese login. Reportar error en contrario. */
        if ( !isset( $posts[0] ) ) {
            $error =    '<p>Estas viendo este error porque no hay información en la base de datos' .
                        ' que concuerde con tu nombre de usuario. Por favor reporta esto ' .
                        'inmediatamente al Administrador del sistema</p>';

            return $error;
        }

        /* 
         *  A partir de esta línea se va a traer toda la información del post seleccionado usando
         *  la variable global de WordPress $post. Una de las formas más intuitivas y seguras de
         *  traer información de un custom post es por medio de las funciones de WordPress
         *  get_the_title() y get_post_meta().
         */

        if ( $role == 'cuido' ) {

            /*
             *  Traer información del Cuido. La variable global $post se utiliza para poder accesar
             *  la información del Post que nosotros seleccionemos. En el primer caso, la función
             *  get_posts( ... ) nos va a traer un post de cuido (el que concuerde con el nombre
             *  de usuario).
             */

            global $post;

            $post = $posts[0];

            /*  El post original tiene que ser guardado para usos posteriores */
            $original_post = $post;

            $meta = get_post_meta( $post->ID );

            /*
             *  Los siguientes campos son los que se van a mostrar en el Dashboard del Cuido. Se han
             *  introducido en un arreglo asociativo para poder tener reference al nombre del campo
             *  y a la etiqueta.
             */

            $meta_values_array = array(
                'Cuido'                 =>  '',
                'Nombre del Dueño'      =>  'wpcf-nombre',
                'Apellidos del Dueño'   =>  'wpcf-apellidos',
                'Teléfono del Dueño'    =>  'wpcf-telefono',
                'Email del Dueño'       =>  'wpcf-email',
                'Fotografía del Dueño'  =>  'wpcf-fotografia-en',
                'Teléfono del Cuido'    =>  'wpcf-telefono-cuido',
                'Teléfono Alternativo'  =>  'wpcf-telefono-alternativo',
                'Email del Cuido'       =>  'wpcf-email-del-cuido',
                'Horario de Apertura'   =>  'wpcf-de',
                'Horario de Cierra'     =>  'wpcf-a',
                'Fotografía del Cuido'  =>  'wpcf-foto-del-cuido',
                'Dirección'             =>  'wpcf-direccion',
                'Ciudad'                =>  'wpcf-ciudad',
                'Estado'                =>  'wpcf-estado',
                'País'                  =>  'wpcf-pais',
                'Notas'                 =>  'wpcf-notas',
                'Direccion'             =>  'wpcf-direccion-con-mapa'
            );

            /*
             *  Traer el valor de forma segura para evitar warnings en el código y hacerlo más
             *  eficiente.
             */
            foreach ( $meta_values_array as $label => $value ) {
                
                if ( $label == 'Cuido' ) {
                    $meta_values[$label] = get_the_title( $post->ID );
                    continue;
                }

                if ( isset( $meta[$value] ) ) {
                    $meta_values[$label] = get_the_meta( $meta[$value] );
                }
            }

            /*
             *  Comienza la escritura de HTML en el Shortcode. La variable $page_html les la que va
             *  a retornar la función encargada del Shortcode.
             */
            $page_html .= '<h2>Información del Cuido</h2>';
            $page_html .= '<table>';

            /*  Se despliegan todos los campos del Cuido por medio de un ciclo for */
            foreach ( $meta_values as $label => $value ) {
                if ( $value != '' && $value != ' ' && $value != '<img src="" />' ) {
                    $page_html .= 
                        '<tr>
                            <td><strong>' . $label . '</strong></td>
                            <td>' . $value . '</td>
                        </tr>';
                }
            }

            $page_html .= '</table>';

            /*
             *  Traer información de los Empleados del Cuido
             */

            $args = array(
                'post_status'       => 'published',
                'post_type'         => 'nido-empleado',
                'meta_key'          => 'wpcf-empleado-cuido',
                'meta_value'        => get_the_title( $post->ID ),
                'suppress_filters'  => true
            );

            $posts = get_posts( $args );

            if ( !empty( $posts ) ) {

                $page_html .= '<h2>Información de los Empleados</h2>';

                foreach ( $posts as $post ) {

                    $meta = get_post_meta( $post->ID );

                    /*
                     *  Los siguientes campos son los que se van a mostrar en el Dashboard del
                     *  Cuido. Se han introducido en un arreglo asociativo para poder tener
                     *  reference al nombre del campo y a la etiqueta.
                     */

                    $meta_values_array = array(
                        'Nombre del Empleado'   => '',
                        'Pertenece al Cuido'    => 'wpcf-empleado-cuido',
                        'Teléfono'              => 'wpcf-empleado-telefono',
                        'Teléfono Secundario'   => 'wpcf-empleado-telefono-secundario',
                        'Email'                 => 'wpcf-empleado-email',
                        'Dirección'             => 'wpcf-empleado-direccion',
                        'Ciudad'                => 'wpcf-empleado-ciudad',
                        'País'                  => 'wpcf-empleado-pais',
                        'Código Postal'         => 'wpcf-empleado-codigo-postal',
                        'Dirección del Mapa'    => 'wpcf-empleado-resultado-de-direccion-entrada-con-el-mapa',
                        'Foto'                  => 'wpcf-foto-del-empleado'
                    );

                    /*
                     *  Traer el valor de forma segura para evitar warnings en el código y hacerlo más
                     *  eficiente.
                     */
                    $meta_values = array();
                    foreach ( $meta_values_array as $label => $value ) {
                        
                        if ( $label == 'Nombre del Empleado' ) {
                            $meta_values[$label] = get_the_title( $post->ID );
                            continue;
                        }

                        if ( isset( $meta[$value] ) ) {
                            $meta_values[$label] = get_the_meta( $meta[$value] );
                        }
                    }

                    $page_html .= '<table>';

                    /*  Se despliegan todos los valores por medio de un for loop */
                    foreach ( $meta_values as $label => $value ) {
                        if ( $value != '' && $value != '<img src="" />') {
                            $page_html .= 
                                '<tr>
                                    <td><strong>' . $label . '</strong></td>
                                    <td>' . $value . '</td>
                                </tr>';
                        }
                    }

                    $page_html .= '</table>';
                }
            }
            else {
                $page_html .= '<h2>No hay Empleados Registrados</h2>';
            }

            /*
             *  Traer información de las familias del Cuido
             */

            $args = array(
                'post_status'       => 'published',
                'post_type'         => 'nido-familia',
                'meta_key'          => 'wpcf-cuido',
                'meta_value'        => get_the_title( $original_post->ID ),
                'suppress_filters'  => true
            );

            $posts = get_posts( $args );

            if ( !empty( $posts ) ) {

                $page_html .= '<h2>Información de las Familias</h2>';

                foreach ( $posts as $post ) {

                    $meta = get_post_meta( $post->ID );

                    $meta_values_array = array(
                        'Cuido' 
                            => 'wpcf-cuido',
                        'Nombre del Guardián Principal' 
                            => 'wpcf-nombre-de-guardian',
                        'Apellidos del Guardián Principal' 
                            => 'wpcf-apellidos-del-guardian-paterno-y-materno',
                        'Teléfono del Guardián Principal' 
                            => 'wpcf-telefono-del-guardian',
                        'Teléfono Alternativo del Guardián Principal' 
                            => 'wpcf-telefono-secundario-del-guardian',
                        'Email del Guardián Principal' 
                            => 'wpcf-email-del-guardian',
                        'Relación con el(los) niño(s)' 
                            => 'wpcf-relacion-con-el-los-nino-s',
                        'Dirección del Guardián Principal (Mapa)' 
                            => 'wpcf-direccion-del-mapa-del-guardian',
                        'Dirección del Guardián Principal' 
                            => 'wpcf-direccion-del-guardian',
                        'Ciudad' 
                            => 'wpcf-ciudad-del-guardian',
                        'Estado' 
                            => 'wpcf-estado-territorio-del-guardian',
                        'País' 
                            => 'wpcf-pais-del-guardian',
                        'Código Postal' 
                            => 'wpcf-codigo-postal-del-guardian',
                        'Empleador del Guardián Principal' 
                            => 'wpcf-nombre-del-empleador-empresa-del-guardian',
                        'Foto del Guardián Principal' 
                            => 'wpcf-foto-del-guardian-principal',
                    );

                    for ( $index = 2; $index <= 7 ; $index++ ) { 
                        $meta_values_array['Nombre del Guardián ' . $index] =
                            'wpcf-nombre-de-guardian-' . $index;

                        $meta_values_array['Apellidos del Guardián ' . $index] =
                            'wpcf-apellidos-del-guardian-' . $index . '-paterno-y-materno';

                        $meta_values_array['Teléfono del Guardián ' . $index] =
                            'wpcf-telefono-del-guardian-' . $index;

                        $meta_values_array['Teléfono Secundario de Guardián ' . $index] =
                            'wpcf-telefono-secundario-del-guardian-' . $index;

                        $meta_values_array['Email del Guardián ' . $index] = 
                            'wpcf-email-del-guardian-' . $index;
                        
                        $meta_values_array['Relación del Guardián ' . $index . ' con el(los) niño(s)'] =
                            'wpcf-relacion-con-el-los-nino-s-' . $index;

                        $meta_values_array['Dirección del Guardián ' . $index] =
                            'wpcf-direccion-del-guardian-' . $index;

                        $meta_values_array['Ciudad del Guardián ' . $index] =
                            'wpcf-ciudad-del-guardian-' . $index;
                        
                        $meta_values_array['Estado del Guardián ' . $index] =
                            'wpcf-estado-territorio-del-guardian-' . $index;

                        $meta_values_array['País del Guardián ' . $index] =
                            'wpcf-pais-del-guardian-' . $index;
                        
                        $meta_values_array['Código Postal del Guardián ' . $index] =
                            'wpcf-codigo-postal-del-guardian-' . $index;
                        
                        $meta_values_array['Dirección del Guardián ' . $index . ' (Mapa)'] =
                            'wpcf-direccion-del-mapa-del-guardian-' . $index;
                        
                        $meta_values_array['Empleador del Guardián ' . $index] =
                            'wpcf-nombre-del-empleador-empresa-del-guardian-' . $index;
                        
                        $meta_values_array['Foto del Guardián ' . $index] =
                            'wpcf-foto-del-guardian-' . $index;
                    }

                    for ( $i = 2; $i <= 10 ; $i++ ) { 
                        $meta_values_array['Nombre del Niño(a) ' . $i] =
                            'wpcf-nombre-del-nino-a-' . $i;

                        $meta_values_array['Apellidos del Niño(a) ' . $i] =
                            'wpcf-apellidos-del-nino-a-' . $i;

                        $meta_values_array['Género del Niño(a) ' . $i] =
                            'wpcf-genero-de-nino-a-' . $i;

                        $meta_values_array['Fecha de Entrada Niño(a) ' . $i] =
                            'wpcf-fecha-de-entrada-nino-a-' . $i;

                        $meta_values_array['Fecha de Nacimiento del Niño(a) ' . $i] = 
                            'wpcf-fecha-de-nacimiento-de-nino-a-' . $i;

                        $meta_values_array['Edad del Niño(a) ' . $i] =
                            'wpcf-edad-del-nino-a-' . $i;

                        $meta_values_array['Pediatra del Niño(a) ' . $i] =
                            'wpcf-pediatra-del-nino-a-' . $i;

                        $meta_values_array['Condiciones / Enfermedades del Niño(a) ' . $i] =
                            'wpcf-condiciones-enfermedades-del-nino-a-' . $i;

                        $meta_values_array['Fotografía del Niño(a) ' . $i] =
                            'wpcf-foto-del-nino-a-' . $i;

                        $meta_values_array['Teléfono del Niño(a) ' . $i] =
                            'wpcf-telefono-del-nino-a-' . $i;
                    }

                    $meta_values = array();
                    foreach ( $meta_values_array as $label => $value ) {
                        if ( isset( $meta[$value] ) ) {
                            $meta_values[$label] = get_the_meta( $meta[$value] );
                        }
                    }

                    $page_html .= '<table>';

                    foreach ( $meta_values as $label => $value ) {
                        if ( $value != '' && $value != ' ' && $value != '<img src="" />' ) {
                            $page_html .= 
                                '<tr>
                                    <td><strong>' . $label . '</strong></td>
                                    <td>' . $value . '</td>
                                </tr>';
                        }
                    }

                    $page_html .= '</table>';


                    /*
                     *  Traer información de las entradas y salidas del Cuido
                     */

                    $args = array(
                        'post_status'       => 'published',
                        'post_type'         => 'nido-entrada-salida',
                        'meta_key'          => 'wpcf-entrada-salida-cuido',
                        'meta_value'        => get_the_title( $original_post->ID ),
                        'suppress_filters'  => true
                    );

                    $posts = get_posts( $args );

                    if ( !empty( $posts ) ) {

                        $page_html .= '<h2>Todas las Entradas y Salidas</h2>';

                        foreach ( $posts as $post ) {

                            $meta = get_post_meta( $post->ID );

                            /*
                             *  Los siguientes campos son los que se van a mostrar en el Dashboard 
                             *  del Cuido. Se han introducido en un arreglo asociativo para poder
                             *  tener reference al nombre del campo y a la etiqueta.
                             */

                            $meta_values_array = array(
                                'Movimiento'            => '',
                                'Cuido'                 => 'wpcf-entrada-salida-cuido',
                                'Teléfono Principal'    => 'wpcf-entrada-salida-telefono-principal',
                                'Familia'               => 'wpcf-entrada-salida-familia',
                                'Guardián'              => 'wpcf-entrada-salida-guardian',
                                'Niño(a)'               => 'wpcf-entrada-salida-nino-a',
                                'Firma'                 => 'wpcf-entrada-salida-firmas'
                            );

                            /*
                             *  Traer el valor de forma segura para evitar warnings en el código y 
                             *  hacerlo más eficiente.
                             */
                            $meta_values = array();
                            foreach ( $meta_values_array as $label => $value ) {
                                
                                if ( $label == 'Movimiento' ) {
                                    $meta_values[$label] = get_the_title( $post->ID );
                                    continue;
                                }

                                if ( isset( $meta[$value] ) ) {
                                    $meta_values[$label] = get_the_meta( $meta[$value] );
                                }
                            }

                            $page_html .= '<table>';

                            /*  Se despliegan todos los valores por medio de un for loop */
                            foreach ( $meta_values as $label => $value ) {
                                if ( $value != '' && $value != '<img src="" />') {
                                    $page_html .= 
                                        '<tr>
                                            <td><strong>' . $label . '</strong></td>
                                            <td>' . $value . '</td>
                                        </tr>';
                                }
                            }

                            $page_html .= '</table>';
                        }
                    }
                    else {
                        $page_html .= '<h2>No hay Entradas / Salidas Registrados</h2>';
                    }


                }
            }
            else {
                $page_html .= '<h2>No hay Familias Registradas</h2>';
            }
        }

        if ( $role == 'empleado' ) {

            /*
             *  Traer información del Empleado. La variable global $post se utiliza para poder
             *  accesar la información del Post que nosotros seleccionemos. En el primer caso, la
             *  función get_posts( ... ) nos va a traer un post de cuido (el que concuerde con el
             *  nombre de usuario).
             */

            global $post;

            $post = $posts[0];

            /*  El post original tiene que ser guardado para usos posteriores */
            $original_post = $post;

            $meta = get_post_meta( $post->ID );

            /*
             *  Los siguientes campos son los que se van a mostrar en el Dashboard del
             *  Cuido. Se han introducido en un arreglo asociativo para poder tener
             *  reference al nombre del campo y a la etiqueta.
             */

            $meta_values_array = array(
                'Nombre del Empleado'   => '',
                'Pertenece al Cuido'    => 'wpcf-empleado-cuido',
                'Teléfono'              => 'wpcf-empleado-telefono',
                'Teléfono Secundario'   => 'wpcf-empleado-telefono-secundario',
                'Email'                 => 'wpcf-empleado-email',
                'Dirección'             => 'wpcf-empleado-direccion',
                'Ciudad'                => 'wpcf-empleado-ciudad',
                'País'                  => 'wpcf-empleado-pais',
                'Código Postal'         => 'wpcf-empleado-codigo-postal',
                'Dirección del Mapa'    => 'wpcf-empleado-resultado-de-direccion-entrada-con-el-mapa',
                'Foto'                  => 'wpcf-foto-del-empleado'
            );

            /*
             *  Traer el valor de forma segura para evitar warnings en el código y hacerlo más
             *  eficiente.
             */
            $meta_values = array();
            foreach ( $meta_values_array as $label => $value ) {
                
                if ( $label == 'Nombre del Empleado' ) {
                    $meta_values[$label] = get_the_title( $post->ID );
                    continue;
                }

                if ( isset( $meta[$value] ) ) {
                    $meta_values[$label] = get_the_meta( $meta[$value] );
                }
            }

            $page_html .= '<h2>Información del Empleado</h2>';
            $page_html .= '<table>';

            /*  Se despliegan todos los valores por medio de un for loop */
            foreach ( $meta_values as $label => $value ) {
                if ( $value != '' && $value != '<img src="" />') {
                    $page_html .= 
                        '<tr>
                            <td><strong>' . $label . '</strong></td>
                            <td>' . $value . '</td>
                        </tr>';
                }
            }

            $page_html .= '</table>';

            /*
             *  Traer información de las familias del Cuido
             */

            $cuido_name = $meta_values['Pertenece al Cuido'];

            $args = array(
                'post_status'       => 'published',
                'post_type'         => 'nido-familia',
                'meta_key'          => 'wpcf-cuido',
                'meta_value'        => $cuido_name,
                'suppress_filters'  => true
            );

            $posts = get_posts( $args );

            if ( !empty( $posts ) ) {

                $page_html .= '<h2>Información de las Familias</h2>';

                foreach ( $posts as $post ) {

                    $meta = get_post_meta( $post->ID );

                    $meta_values_array = array(
                        'Cuido' 
                            => 'wpcf-cuido',
                        'Nombre del Guardián Principal' 
                            => 'wpcf-nombre-de-guardian',
                        'Apellidos del Guardián Principal' 
                            => 'wpcf-apellidos-del-guardian-paterno-y-materno',
                        'Teléfono del Guardián Principal' 
                            => 'wpcf-telefono-del-guardian',
                        'Teléfono Alternativo del Guardián Principal' 
                            => 'wpcf-telefono-secundario-del-guardian',
                        'Email del Guardián Principal' 
                            => 'wpcf-email-del-guardian',
                        'Relación con el(los) niño(s)' 
                            => 'wpcf-relacion-con-el-los-nino-s',
                        'Dirección del Guardián Principal (Mapa)' 
                            => 'wpcf-direccion-del-mapa-del-guardian',
                        'Dirección del Guardián Principal' 
                            => 'wpcf-direccion-del-guardian',
                        'Ciudad' 
                            => 'wpcf-ciudad-del-guardian',
                        'Estado' 
                            => 'wpcf-estado-territorio-del-guardian',
                        'País' 
                            => 'wpcf-pais-del-guardian',
                        'Código Postal' 
                            => 'wpcf-codigo-postal-del-guardian',
                        'Empleador del Guardián Principal' 
                            => 'wpcf-nombre-del-empleador-empresa-del-guardian',
                        'Foto del Guardián Principal' 
                            => 'wpcf-foto-del-guardian-principal',
                    );

                    for ( $index = 2; $index <= 7 ; $index++ ) { 
                        $meta_values_array['Nombre del Guardián ' . $index] =
                            'wpcf-nombre-de-guardian-' . $index;

                        $meta_values_array['Apellidos del Guardián ' . $index] =
                            'wpcf-apellidos-del-guardian-' . $index . '-paterno-y-materno';

                        $meta_values_array['Teléfono del Guardián ' . $index] =
                            'wpcf-telefono-del-guardian-' . $index;

                        $meta_values_array['Teléfono Secundario de Guardián ' . $index] =
                            'wpcf-telefono-secundario-del-guardian-' . $index;

                        $meta_values_array['Email del Guardián ' . $index] = 
                            'wpcf-email-del-guardian-' . $index;
                        
                        $meta_values_array['Relación del Guardián ' . $index . ' con el(los) niño(s)'] =
                            'wpcf-relacion-con-el-los-nino-s-' . $index;

                        $meta_values_array['Dirección del Guardián ' . $index] =
                            'wpcf-direccion-del-guardian-' . $index;

                        $meta_values_array['Ciudad del Guardián ' . $index] =
                            'wpcf-ciudad-del-guardian-' . $index;
                        
                        $meta_values_array['Estado del Guardián ' . $index] =
                            'wpcf-estado-territorio-del-guardian-' . $index;

                        $meta_values_array['País del Guardián ' . $index] =
                            'wpcf-pais-del-guardian-' . $index;
                        
                        $meta_values_array['Código Postal del Guardián ' . $index] =
                            'wpcf-codigo-postal-del-guardian-' . $index;
                        
                        $meta_values_array['Dirección del Guardián ' . $index . ' (Mapa)'] =
                            'wpcf-direccion-del-mapa-del-guardian-' . $index;
                        
                        $meta_values_array['Empleador del Guardián ' . $index] =
                            'wpcf-nombre-del-empleador-empresa-del-guardian-' . $index;
                        
                        $meta_values_array['Foto del Guardián ' . $index] =
                            'wpcf-foto-del-guardian-' . $index;
                    }

                    for ( $i = 2; $i <= 10 ; $i++ ) { 
                        $meta_values_array['Nombre del Niño(a) ' . $i] =
                            'wpcf-nombre-del-nino-a-' . $i;

                        $meta_values_array['Apellidos del Niño(a) ' . $i] =
                            'wpcf-apellidos-del-nino-a-' . $i;

                        $meta_values_array['Género del Niño(a) ' . $i] =
                            'wpcf-genero-de-nino-a-' . $i;

                        $meta_values_array['Fecha de Entrada Niño(a) ' . $i] =
                            'wpcf-fecha-de-entrada-nino-a-' . $i;

                        $meta_values_array['Fecha de Nacimiento del Niño(a) ' . $i] = 
                            'wpcf-fecha-de-nacimiento-de-nino-a-' . $i;

                        $meta_values_array['Edad del Niño(a) ' . $i] =
                            'wpcf-edad-del-nino-a-' . $i;

                        $meta_values_array['Pediatra del Niño(a) ' . $i] =
                            'wpcf-pediatra-del-nino-a-' . $i;

                        $meta_values_array['Condiciones / Enfermedades del Niño(a) ' . $i] =
                            'wpcf-condiciones-enfermedades-del-nino-a-' . $i;

                        $meta_values_array['Fotografía del Niño(a) ' . $i] =
                            'wpcf-foto-del-nino-a-' . $i;

                        $meta_values_array['Teléfono del Niño(a) ' . $i] =
                            'wpcf-telefono-del-nino-a-' . $i;
                    }

                    $meta_values = array();
                    foreach ( $meta_values_array as $label => $value ) {
                        if ( isset( $meta[$value] ) ) {
                            $meta_values[$label] = get_the_meta( $meta[$value] );
                        }
                    }

                    $page_html .= '<table>';

                    foreach ( $meta_values as $label => $value ) {
                        if ( $value != '' && $value != ' ' && $value != '<img src="" />' ) {
                            $page_html .= 
                                '<tr>
                                    <td><strong>' . $label . '</strong></td>
                                    <td>' . $value . '</td>
                                </tr>';
                        }
                    }

                    $page_html .= '</table>';


                    /*
                     *  Traer información de las entradas y salidas del Cuido
                     */

                    $args = array(
                        'post_status'       => 'published',
                        'post_type'         => 'nido-entrada-salida',
                        'meta_key'          => 'wpcf-entrada-salida-cuido',
                        'meta_value'        => $cuido_name,
                        'suppress_filters'  => true
                    );

                    $posts = get_posts( $args );

                    if ( !empty( $posts ) ) {

                        $page_html .= '<h2>Todas las Entradas y Salidas</h2>';

                        foreach ( $posts as $post ) {

                            $meta = get_post_meta( $post->ID );

                            /*
                             *  Los siguientes campos son los que se van a mostrar en el Dashboard 
                             *  del Cuido. Se han introducido en un arreglo asociativo para poder
                             *  tener reference al nombre del campo y a la etiqueta.
                             */

                            $meta_values_array = array(
                                'Movimiento'            => '',
                                'Cuido'                 => 'wpcf-entrada-salida-cuido',
                                'Teléfono Principal'    => 'wpcf-entrada-salida-telefono-principal',
                                'Familia'               => 'wpcf-entrada-salida-familia',
                                'Guardián'              => 'wpcf-entrada-salida-guardian',
                                'Niño(a)'               => 'wpcf-entrada-salida-nino-a',
                                'Firma'                 => 'wpcf-entrada-salida-firmas'
                            );

                            /*
                             *  Traer el valor de forma segura para evitar warnings en el código y 
                             *  hacerlo más eficiente.
                             */
                            $meta_values = array();
                            foreach ( $meta_values_array as $label => $value ) {
                                
                                if ( $label == 'Movimiento' ) {
                                    $meta_values[$label] = get_the_title( $post->ID );
                                    continue;
                                }

                                if ( isset( $meta[$value] ) ) {
                                    $meta_values[$label] = get_the_meta( $meta[$value] );
                                }
                            }

                            $page_html .= '<table>';

                            /*  Se despliegan todos los valores por medio de un for loop */
                            foreach ( $meta_values as $label => $value ) {
                                if ( $value != '' && $value != '<img src="" />') {
                                    $page_html .= 
                                        '<tr>
                                            <td><strong>' . $label . '</strong></td>
                                            <td>' . $value . '</td>
                                        </tr>';
                                }
                            }

                            $page_html .= '</table>';
                        }
                    }
                    else {
                        $page_html .= '<h2>No hay Entradas / Salidas Registrados</h2>';
                    }


                }
            }
            else {
                $page_html .= '<h2>No hay Familias Registradas</h2>';
            }
        }

        if ( $role == 'familia' ) {
            /*
             *  Traer información del Empleado. La variable global $post se utiliza para poder
             *  accesar la información del Post que nosotros seleccionemos. En el primer caso, la
             *  función get_posts( ... ) nos va a traer un post de cuido (el que concuerde con el
             *  nombre de usuario).
             */

            global $post;

            $post = $posts[0];

            /*  El post original tiene que ser guardado para usos posteriores */
            $original_post = $post;

            $meta = get_post_meta( $post->ID );

            var_dump('<pre>');
            var_dump($meta);
            var_dump('</pre>');
        }
    }
    else {
        $error =    '<p>Esta página es para Usuarios Reconocidos. Será redirigido.</p>' . 
                    '<meta http-equiv="refresh" content="0; url=/login/" />';

        return $error;
    }

    return $page_html;
}


add_shortcode( 'nido-mis-grupos-familiares', 'nido_mis_grupos_familiares' );

function nido_mis_grupos_familiares() {

    /*
     *  Este shortcode carga el layout para los grupos familiares de los cuidos. 
     */

    $results = nido_shortcode_layouts( 'mis-grupos' );

    if ( !empty( $results['error'] ) ) {
        return $results['error'];
    }

    return $results['html'];
}


add_shortcode( 'nido-reportes', 'nido_reportes' );

function nido_reportes() {

    /*
     *  Este shortcode carga el layout para los grupos familiares de los cuidos. 
     */

    $results = nido_shortcode_layouts( 'reportes' );

    if ( !empty( $results['error'] ) ) {
        return $results['error'];
    }

    return $results['html'];
}


add_shortcode( 'nido-unique-id', 'nido_unique_id' );

function nido_unique_id( $atts ) {

    /*
     *  Este shortcode es para crear un identificador único aleatorio para identificar los usuarios.
     *  El identificador único se genera por 10 caracteres. El primer caracter es 'C', 'E' o 'F' 
     *  dependiendo de si se está registrando un nuevo Cuido, Empleado o Familia respectivamente.
     *  Los demás caracteres son completamente aleatorios entre números y letras del abecedario en
     *  letras minúsculas y mayúsculas sin incluír letras o caracteres especiales.
     */

    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';

    $user = wp_get_current_user();

    /*  En el atributo del shortcode se indica qué es lo que se quiere registrar */
    switch ( $atts['registrar'] ) {
        case 'cuido': $randomString .= 'C'; break;
        case 'empleado': $randomString .= 'E'; break;
        case 'familia': $randomString .= 'F'; break;
        default: break;
    }

    /*  Se generan aleatoriamente con la función 'rand' */
    for ($i = 1; $i < 10; $i++) {
        $randomString .= $characters[rand( 0, $charactersLength - 1 )];
    }
    return $randomString;
}


add_shortcode( 'nido-current', 'nido_current' );

function nido_current( $atts ) {

    /*
     *  La función nido-current es para obtener el ID de los usuarios actuales.
     */

    $field = ( isset( $atts['field'] ) ) ? $atts['field'] : '';
    $extract = ( isset( $atts['extract'] ) ) ? $atts['extract'] : '';
    $by_role = ( isset( $atts['by_role'] ) ) ? $atts['by_role'] : '';
    $meta_key = '';
    $usermeta = '';

    /*  Se obtiene la información del usuario actual */
    $user = wp_get_current_user();

    /*  Switch para que el Shortcode pueda entregar un ID de usuario o su nombre */
    switch ( $field ) {
        case 'id': 
            $usermeta = get_user_meta( $user->ID, 'description', true );
            if ( $extract === 'familia' ) {
                list( $cuido_id, $familia_id ) = preg_split( '/,/', $usermeta );
                $usermeta = $familia_id;
            }

            if ( $extract === 'empleado' ) {
                list( $cuido_id, $empleado_id ) = preg_split( '/,/', $usermeta );
                $usermeta = $empleado_id;
            }
            
            if ( $by_role == 'true' ) {

                if ( $user->roles[0] !== 'cuido' ) {
                    list( $cuido_id, $user_id ) = preg_split( '/,/', $usermeta );
                    $usermeta = $user_id;
                }
            }

            break;
        case 'nombre': $usermeta = $user->display_name; break;
        default: break;
    }

    return $usermeta;
}


add_shortcode( 'nido-allow-families-view', 'nido_allow_families_view' );

function nido_allow_families_view( $atts ) {

    /*
     *  Shortcode para permitir o denegar que un Cuido vea Grupos Familiares que no le corresponden.
     *  También permite que los Grupos Familiares sean visibles para el Cuido que los ha registrado
     *  solamente.
     */

    $user = wp_get_current_user();

    $cuido_id = get_user_meta( $user->ID, 'description', true );

    $html = '';

    /*  Si el Cuido tiene permisos para ver los Grupos Familiares se efectúa el Shortcode */
    if ( isset( $_GET['cuido_id'] ) && isset( $cuido_id ) ) {
        if ( $_GET['cuido_id'] == $cuido_id ) {
            $id = ( isset( $atts['id'] ) ) ? $atts['id'] : '';
            $filter = ( isset( $atts['filter'] ) ) ? $atts['filter'] : '';
            
            $html = do_shortcode( '[display-frm-data id=' . $id . ' filter=' . $filter . ']' );

            return $html;
        }
    }

    /*  Si hay un prolema se muestra un mensaje de error con el mismo formato. */
    $html = '<div class="nido-grupos-familiares">' .
                '<div class="nido-grupos-familiares-count">' .
                    '<label id="nido-grupos-familiares-label">Grupos Familiares</label>' .
               '</div>' .
                '<div class="nido-family-list">' .
                    '<div class="nido-family-element">' .
                        '<div class="nido-family-name">Hubo un error, no se pueden mostrar las familias</div>' .
                    '</div>' .
                '</div>' .
            '</div>';

    return $html;
}


add_shortcode( 'nido-allow-employers-view', 'nido_allow_employers_view' );

function nido_allow_employers_view( $atts ) {

    /*
     *  Shortcode para permitir o denegar que un Cuido vea Empleados que no le corresponden.
     *  También permite que los Empleados sean visibles para el Cuido que los ha registrado
     *  solamente.
     */

    $user = wp_get_current_user();

    $cuido_id = get_user_meta( $user->ID, 'description', true );

    $html = '';

    /*  Si el Cuido tiene permisos para ver los Empleados se efectúa el Shortcode */
    if ( isset( $_GET['cuido_id'] ) && isset( $cuido_id ) ) {
        if ( $_GET['cuido_id'] == $cuido_id ) {
            $id = ( isset( $atts['id'] ) ) ? $atts['id'] : '';
            $filter = ( isset( $atts['filter'] ) ) ? $atts['filter'] : '';
            
            $html = do_shortcode( '[display-frm-data id=' . $id . ' filter=' . $filter . ']' );

            return $html;
        }
    }

    /*  Si hay un prolema se muestra un mensaje de error con el mismo formato. */
    $html = '<label style="font-family: \'Roboto\' !important;font-size: 15px !important;font-weight: 400 !important;padding-left: 20px;color: #787878;">No se encontraron empleados.</label>';

    return $html;
}


add_shortcode( 'nido-puede-registrar', 'nido_puede_registrar' );

function nido_puede_registrar( $atts ) {

    /*
     *  Función para determinar si el Cuido tiene los permisos necesarios para registrar una entrada
     *  o una salida.
     */

    $user = wp_get_current_user();

    $user_id = get_user_meta( $user->ID, 'description', true );

    $user_role = $user->roles[0];

    $registrant_field_id = ( isset( $atts['registrant_field_id'] ) ) ? $atts['registrant_field_id'] : 0;

    if ( $user_role == 'cuido' && $registrant_field_id != 0) {

        $registrant_id = do_shortcode( '[frm-field-value field_id=' . $registrant_field_id . ' entry=pass_entry]' );

        /*
         *  Si todo está bien, se muestra la forma para registrar la entrad o salida. Si hay un
         *  error, entonces se muestran los mensajes correspondientes.
         */
        if ( $registrant_id == $user_id ) {
            return do_shortcode( '[formidable id=20]' );
        }
        else {
            return 'Esta información es confidencial. De click <a href="/registrar-entrada-salida/">aquí</a> para intentar nuevamente con un dato al que tenga acceso.';
        }

    }

    return 'Hubo un problema. Dar click <a href="/registrar-entrada-salida/">aquí</a> para comenzar nuevamente.';
}


add_shortcode( 'nido-get-icons-uri', 'nido_get_icons_uri' );

function nido_get_icons_uri() {
    return get_template_directory_uri() . '_child/assets/icons';
}


add_shortcode( 'nido-get-site-url', 'nido_get_site_url' );

function nido_get_site_url() {
    return get_site_url();
}


add_shortcode( 'nido-get-avatars', 'nido_get_avatars' );

function nido_get_avatars( $atts ) {
    return $atts['id'];
}


add_shortcode( 'nido-get-permalink-with-parameters', 'nido_get_permalink_with_parameters' );

function nido_get_permalink_with_parameters() {
    $html = '';
    foreach ( $_GET as $name => $parameter ) {
        $html .= $name . '=' . $parameter . '&';
    }
    return get_permalink() . '?' . $html;
}


add_shortcode( 'nido-current-user', 'nido_current_user' );

function nido_current_user() {
    return apply_filters( 'nido_get_nido_id', wp_get_current_user()->ID );
}


add_shortcode( 'nido-send-email', 'nido_send_email' );

function nido_send_email() {
    wp_mail( 'obedleza18@gmail.com', 'Test Email', 'This is a test email' );
}


add_shortcode( 'nido-tardanzas', 'nido_tardanzas' );

function nido_tardanzas( $atts ) {
    $cuido_id = apply_filters( 'nido_get_nido_id', wp_get_current_user()->ID );

    if ( $cuido_id[0] != 'C' )
        return;

    $args_cuido = array(
        'post_type'     => 'nido-cuido',
        'meta_query'    => array(
            array(
                'key'   => 'wpcf-cuido-id',
                'value' => $cuido_id,
            )
        ),
        'posts_per_page' => -1
    );
    
    $query_cuido = new WP_Query( $args_cuido );

    $hora_entrada = get_post_meta( $query_cuido->posts[0]->ID, 'wpcf-de', true );

    $args_kids = array(
        'post_type'     => 'nido-nino',
        'meta_query'    => array(
            'relation'  => 'AND',
            array(
                'key'   => 'wpcf-id-de-cuido-ninos',
                'value' => $cuido_id,
            ),
            array(
                'key'   => 'wpcf-estatus-de-entrada-salida',
                'value' => 'No en Cuido',
            ),
            // array(
            //     'key'   => 'wpcf-nombre-de-nino-a',
            //     'value' => 'Obed Leza',
            // ),
            // array(
            //     'key'   => 'wpcf-nido-email-enviado',
            //     'value' => 'no',
            // )
        ),
        'posts_per_page' => -1
    );
    
    $query_kids = new WP_Query( $args_kids );

    $ahora = new DateTime( 'now', new DateTimeZone( 'America/Managua' ) );
    $entrada = DateTime::createFromFormat( 'G:i a', $hora_entrada, new DateTimeZone( 'America/Managua' ) );
    $entrada->modify( '+5 minutes' );

    if ( empty( $query_kids->posts ) )
        return;

    $kids_info = '';
    foreach ( $query_kids->posts as $post ) {
        $kid_name = $post->post_title;
        $kid_phone = get_post_meta( $post->ID, 'wpcf-telefono-de-familia', true );
        $kids_info .= $kid_name . ': ' . $kid_phone . '<br>';
    }

    $cuido_email = wp_get_current_user()->user_email;

    if ( $ahora > $entrada ) {
        $last_sent = DateTime::createFromFormat( 
            'j/n/Y g:i:s a', 
            get_post_meta( $query_kids->posts[0]->ID, 'wpcf-nido-email-enviado', true ), 
            new DateTimeZone( 'America/Managua' ) 
        );

        if ( $last_sent != false ) {
            if ( $last_sent->format( 'd' ) == $ahora->format( 'd' ) ) {
                return;
            }
        }

        global $wpdb;
        $content = $wpdb->get_var( "SELECT post_content FROM $wpdb->posts WHERE post_type='plantilla-de-email' AND post_title='Tardanza'" );
        $content = preg_replace( '/%%CUIDO_NAME%%/', wp_get_current_user()->display_name, $content );
        $content = preg_replace( '/%%KIDS%%/', $kids_info, $content );

        wp_mail(
            $cuido_email,
            'Tardanzas del día de Hoy',
            $content
        );

        foreach ( $query_kids->posts as $post ) {
            update_post_meta( $post->ID, 'wpcf-nido-email-enviado', $ahora->format( 'j/n/Y g:i:s a' ) );

            $postarr = array(
                'post_title'    => $post->post_title . ' ' . $ahora->format( 'j/n/Y g:i:s a' ),
                'post_status'   => 'publish',
                'post_type'     => 'nido-tardanza',
                'meta_input'    => array(
                        'wpcf-nido-tardanza-cuido-id'       => $cuido_id,
                        'wpcf-nido-tardanza-familia-id'     => get_post_meta( $post->ID, 'wpcf-id-de-familia-ninos', true ),
                        'wpcf-nido-tardanza-nombre'         => $post->post_title,
                        'wpcf-nido-tardanza-hora-llegada'   => $ahora->format( 'j/n/Y g:i:s a' )
                    )
            );

            wp_insert_post( $postarr );
        }
    }

    return '';
}





































