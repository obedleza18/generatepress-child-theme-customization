<?php

/**
 *  Archivo de functions.php para Child Theme
 *
 *  Este archivo es leído por WordPress como funciones personalizadas para el tema. Este archivo
 *  contiene todas las dependencias de archivos adicionales y el código necesario para modificar
 *  la funcionalidad de WordPress. En este archivo se encuentran diferentes hooks a acciones y
 *  filtros de WordPress que logran el objetivo mencionado anteriormente.
 *
 *  @link               http://nidowp.miopr.com/
 *  @since              1.0.0
 *  @package            Nido
 *
 *  @wordpress-child-theme-functions
 *
 *  Nombre de Proyecto: Nido
 *  URL del Proyecto:   http://nidowp.miopr.com/
 *  Descripción:        Funciones Personalizadas del Tema
 *  Versión:            1.0.0
 *  Autor:              Máximo Obed Leza Correa (TUNEL)
 *  URL del Autor:      http://portafoliomax.com
 *  License:            GPL-2.0+
 *  License URI:        http://www.gnu.org/licenses/gpl-2.0.txt
 */


add_filter( 'frm_new_post', 'nido_post_nuevo', 10, 2 );

function nido_post_nuevo( $post, $args ) {

    // error_log( print_r( $post, true ) );
    // error_log( print_r( $args, true ) );

    /*
     *  Función Principal:
     *
     *  Todas la funcionalidad se deriva al ejecutarse la creación de un post nuevo como lo son las
     *  siguientes tres acciones: Crear URL de imágenes para guardar en el post y que sea compatible
     *  con el Plugin Types, Crear las imágenes de las firmas generadas por Formidable Signature y
     *  pasar su URL al Custom Post creado, Crear los usuarios respectivamente cuando se llena la
     *  forma.
     *
     *  Algunas funciones que se agregaron posteriormente fueron las funciones para registrar niños,
     *  para crear niños una vez que son agregados en la forma de registro de familia y también se
     *  ha agregado una función para actualizar los estatus de los niños cuando se llena la forma de
     *  registro de entrada / salia de los niños.
     */

    /*  Crear URL de imágenes para post */
    $post = nido_imagenes_post( $post );

    /*  Crear imágenes de firmas y pasar su URL al post */
    $post = nido_firmas_post( $post );

    /*  Registrar a los niños por medio de la forma de Entrada / Salida */
    $post = nido_registrar_ninos( $post );

    /*  Crear usuarios desde forma */
    nido_agregar_usuarios();

    /*  Crear posts de los niños */
    nido_crear_ninos();

    /*  Actualizar estatus de niños */
    nido_actualizar_estatus();

    return $post;
}


function nido_imagenes_post( $post ) {

    /*
     *  Esta función es para modificar cómo se forman los Custom Post Types debido a que no todos
     *  los campos de Formidable son compatibles con Types.
     *
     *  Los campos que se llenan a continuación son las imágenes. Para otro tipo de campo de
     *  Formidable, hay que crear nueva lógica.
     */

    $fields = [ /* $formidable_id, $types_id */
        [145, 'wpcf-fotografia-en' ],                   /* Fotografía de Encargado de Cuido */
        [176, 'wpcf-foto-del-cuido' ],                  /* Fotografía del Cuido             */
        [106, 'wpcf-foto-del-guardian-principal'],      /* Fotografía de Guardián Principal */
        [316, 'wpcf-foto-del-guardian-2'],              /* Fotografía del Guardián 2        */
        [342, 'wpcf-foto-del-guardian-3'],              /* Fotografía del Guardián 3        */
        [361, 'wpcf-foto-del-guardian-4'],              /* Fotografía del Guardián 4        */
        [380, 'wpcf-foto-del-guardian-5'],              /* Fotografía del Guardián 5        */
        [399, 'wpcf-foto-del-guardian-6'],              /* Fotografía del Guardián 6        */
        [418, 'wpcf-foto-del-guardian-7'],              /* Fotografía del Guardián 7        */
        [528, 'wpcf-foto-del-nino-a'],                  /* Fotografía del Niño 1            */
        [436, 'wpcf-foto-del-nino-a-2'],                /* Fotografía del Niño 2            */
        [449, 'wpcf-foto-del-nino-a-3'],                /* Fotografía del Niño 3            */
        [462, 'wpcf-foto-del-nino-a-4'],                /* Fotografía del Niño 4            */
        [476, 'wpcf-foto-del-nino-a-5'],                /* Fotografía del Niño 5            */
        [489, 'wpcf-foto-del-nino-a-6'],                /* Fotografía del Niño 6            */
        [502, 'wpcf-foto-del-nino-a-7'],                /* Fotografía del Niño 7            */
        [515, 'wpcf-foto-del-nino-a-8'],                /* Fotografía del Niño 8            */
        [615, 'wpcf-foto-del-nino-a-9'],                /* Fotografía del Niño 9            */
        [602, 'wpcf-foto-del-nino-a-10'],               /* Fotografía del Niño 10           */
        [563, 'wpcf-foto-del-empleado']                 /* Fotografía del Empleado          */
    ];

    /*  Checar si es familia para mantener los avatares */
    $avatars = '';
    if ( isset( $post['post_custom']['wpcf-id-familia'] ) ) {

        /* Obtener ID del post */
        $familias_args = array(
            'post_type'     => 'nido-familia',
            'meta_query'    => array(
                array(
                    'key'   => 'wpcf-id-familia',
                    'value' => $post['post_custom']['wpcf-id-familia'],
                )
            )
        );

        /*  Se efectúa el Query de WordPress */
        $familias_posts = new WP_Query( $familias_args );

        if ( ! empty( $familias_posts->posts ) ) {
            $post_id = $familias_posts->posts[0]->ID;
            $avatars = get_post_meta( $post_id, 'wpcf-nido-avatares', true );
        }
    }
    if ( isset( $post['post_custom']['wpcf-id-empleado'] ) ) {
        /* Obtener ID del post */
        $empleados_args = array(
            'post_type'     => 'nido-empleado',
            'meta_query'    => array(
                array(
                    'key'   => 'wpcf-id-empleado',
                    'value' => $post['post_custom']['wpcf-id-empleado'],
                )
            )
        );

        /*  Se efectúa el Query de WordPress */
        $empleados_posts = new WP_Query( $empleados_args );

        if ( ! empty( $empleados_posts->posts ) ) {
            $post_id = $empleados_posts->posts[0]->ID;
            $avatars = get_post_meta( $post_id, 'wpcf-avatares-empleados', true );
        }
    }
    if ( isset( $post['post_custom']['wpcf-cuido-id'] ) ) {
        /* Obtener ID del post */
        $cuido_args = array(
            'post_type'     => 'nido-cuido',
            'meta_query'    => array(
                array(
                    'key'   => 'wpcf-cuido-id',
                    'value' => $post['post_custom']['wpcf-cuido-id'],
                )
            )
        );

        /*  Se efectúa el Query de WordPress */
        $cuido_posts = new WP_Query( $cuido_args );

        if ( ! empty( $cuido_posts->posts ) ) {
            $post_id = $cuido_posts->posts[0]->ID;
            $avatars = get_post_meta( $post_id, 'wpcf-avatares-cuido', true );
        }
    }

    /*  Iterativamente guardar los datos de la forma apropiada en el Custom Field */
    foreach ( $fields as list( $formidable_id, $types_id ) ) {

        /*
         *  Si el campo existe en la forma y es llenado, entonces ese mismo campo (que es el ID del 
         *  attachment) se convierte en el URL del mismo y es asignado al Custom Post Field del 
         *  Custom Post Type que está siendo generado. Tomar en cuenta que una vez que estos campos
         *  han sido llenados con esta función, no deben aparecer en la lista de campos personaliza-
         *  dos que aparecen en las opciones 'Form Actions' de la Forma cuando uno va a generar
         *  un Custom Post Type al llenar la forma.
         */
        if ( isset( $_POST['item_meta'][$formidable_id] ) ) {
            $field_value = sanitize_text_field( $_POST['item_meta'][$formidable_id] );
            $attachment_url = wp_get_attachment_url( $field_value );
            $post['post_custom'][$types_id] = $attachment_url;
            if ( $attachment_url != '' ) {
                if ( strpos( $avatars, $attachment_url ) === false ) {
                    $avatars .= $attachment_url . ',';
                }
            }
        }
    }

    $post['post_custom']['wpcf-nido-avatares'] = $avatars;
    $post['post_custom']['wpcf-avatares-empleados'] = $avatars;
    $post['post_custom']['wpcf-avatares-cuido'] = $avatars;

    return $post;
}


function nido_firmas_post( $post ) {
    /*
     *  Las firmas de Formidable Pro no son compatibles con ningun Custom Post Field del Plugin
     *  Types. El siguiente código es utilizado para generar la imagen desde el canvas que se
     *  obtiene desde Formidable Pro.
     *
     *  La imagen es obtenida del canvas (que es el campo 261 de Formidable). Después ese canvas se
     *  convierte en imagen por medio de la función sigJsonToImage_new que básicamente lo que hace
     *  es convertir un arreglo en estilo json en una imagen por medio de manualmente trazar los
     *  puntos del canvas. Después que esta imagen es generada, se guarda en el tema principal (no
     *  en el tema Child) dentro de la carpeta /signatures. Para guardar la imagen correctamente y
     *  sin que haya ningún empalme de información, se generó un identificador de imagen compuesto
     *  de un número aleatorio entre 0 y 1,000,000 seguido de la fecha y hora. Esto garantiza que 
     *  los niños pueden salir y/o entrar de los cuidos cada segundo sin ningún problema.
     *  
     *  Probabilísticamente hablando, pueden ser registrados 1,000,000 niños cada segundo a los
     *  cuidos y todas las firmas de sus guardianes van a poder ser guardadas correctamente.
     */

    /*  Obtener la información del canvas de la forma */
    $signature = ( isset( $_POST['item_meta'][ 261 ] ) ) ? $_POST['item_meta'][ 261 ] : null;

    if ( isset( $signature ) ) {


        /*  Crear una imagen basada en el objeto json creado del canvas */
        $img = sigJsonToImage( $signature['output'] );


        /*
         *  Generando un identificador único para nombrar la imagen de la firma compuesto de un
         *  número aleatorio entre 0 y 1,000,000 seguido de la fecha y hora (con segundos)
         */
        $identifier = strval( rand( 0,1000000 ) ) . '-' .   date( 'Y' ) . 
                                                            date( 'm' ) .
                                                            date( 'd' ) . '-' .
                                                            date( 'H' ) .
                                                            date( 'i' ) .
                                                            date( 's' );

        
        /* 
         *  Guardar la imagen en el directorio del Tema Principal dentro del folder /signatures. Si
         *  el folder no existe al momento de correr la función, este folder es creado y la firma
         *  se guarda dentro de el mismo.
         */
        if ( is_dir( get_template_directory() . '/signatures' ) ) {
            if ( $img ) {
                imagepng(
                    $img,
                    get_template_directory() . '/signatures/signature-'. $identifier .'.png'
                );
                imagedestroy( $img );
            }
        }
        else {
            wp_mkdir_p(get_template_directory() . '/signatures');
        }

        /* 
         *  El mismo link generado anteriormente es asignado al Custom Post Field encargado de
         *  mostrar la imagen de la firma.
         */
        $post['post_custom']['wpcf-entrada-salida-firmas'] = (
            get_template_directory_uri() . '/signatures/signature-'. $identifier .'.png'
        );
    }

    return $post;
}


function nido_registrar_ninos( $post ) {

    /*
     *  El registro de entrada y salida de los niños tiene dos propósitos. El primero es generar
     *  un reporte de entrada y de salida con la firma del guardián responsable. El segundo
     *  propósito es que se tenga un registro de qué niño se encuentra dentro y qué niño se 
     *  encuentra fuera del cuido.
     *
     *  Esto va a servir para control, es decir, no se podrá registrar de entrada un niño que ya se
     *  encuentra dentro del Cuido. Del otro modo, tampoco se podrá registrar salida si el niño
     *  se encuentra fuera. La función nido_registrar_ninos solamente tiene efecto cuando se
     *  utiliza la forma de registro de entrada / salida.
     */

    if ( isset( $_POST['item_meta'][658] ) ) {

        /*
         *  Se crea una variabe auxiliar para determinar si nos encontramos registrando un solo 
         *  nombre o varios nombres.
         */
        $aux = $_POST['item_meta'][658];

        if ( is_array( $aux ) ) {

            $i = 1;
            foreach ($aux as $name) {
                if ( $i == 1 ) {
                    $post['post_custom']['wpcf-nino-a'] = $aux[$i-1];
                }
                else {
                    $post['post_custom']['wpcf-nino-a-es-' . $i] = $aux[$i-1];
                }
                $i++;
            }
        }
        else {
            $post['post_custom']['wpcf-nino-a'] = $aux;
        }
    }

    return $post;
}


function nido_agregar_usuarios() {
    /*
     *  Cuando se crea un nuevo post se crea un nuevo usuario en caso de que se esté llenando el
     *  formulario o los formularios para crear Cuidos, Familias o Empleados. Las instrucciones
     *  siguientes son para traer toda la información desde las formas y crear los usuarios. No hay
     *  problema si el campo no está activo en la forma. En caso de no existir, no se da de alta
     *  ningún usuario. En la descripción de todos los usuarios se agregar un identificador único
     *  del usuario y también información de quién lo creó.
     */

    /*  Traer todos los elementos de la forma. */

    $cuido_email        = ( isset( $_POST['item_meta'][172] ) ) ? $_POST['item_meta'][172] : '';
    $cuido_login        = $cuido_email;
    $cuido_password     = $cuido_email;
    $cuido_firstname    = ( isset( $_POST['item_meta'][168] ) ) ? $_POST['item_meta'][168] : '';
    $cuido_lastname     = '';
    $cuido_cuido        = $cuido_firstname;
    $cuido_description  = ( isset( $_POST['item_meta'][793] ) ) ? $_POST['item_meta'][793] : '';
    $cuido_role         = 'cuido';

    $empleado_email        = ( isset( $_POST['item_meta'][551] ) ) ? $_POST['item_meta'][551] : '';
    $empleado_login        = $empleado_email;
    $empleado_password     = $empleado_email;
    $empleado_firstname    = ( isset( $_POST['item_meta'][823] ) ) ? $_POST['item_meta'][823] : '';
    $empleado_lastname     = ( isset( $_POST['item_meta'][548] ) ) ? $_POST['item_meta'][548] : '';
    $empleado_cuido        = ( isset( $_POST['item_meta'][545] ) ) ? $_POST['item_meta'][545] : '';
    $empleado_description  = ( isset( $_POST['item_meta'][818] ) && isset( $_POST['item_meta'][547] ) ) ? 
                                $_POST['item_meta'][818] . ',' . $_POST['item_meta'][547] : '';
    $empleado_role         = 'empleado';

    $familia_email        = ( isset( $_POST['item_meta'][75] ) ) ? $_POST['item_meta'][75] : '';
    $familia_login        = $familia_email;
    $familia_password     = $familia_email;
    $familia_firstname    = ( isset( $_POST['item_meta'][620] ) ) ? $_POST['item_meta'][620] : '';
    $familia_lastname     = '';
    $familia_cuido        = ( isset( $_POST['item_meta'][266] ) ) ? $_POST['item_meta'][266] : '';
    $familia_description  = ( isset( $_POST['item_meta'][802] ) && isset( $_POST['item_meta'][803] ) ) ? 
                                $_POST['item_meta'][802] . ',' . $_POST['item_meta'][803] : '';
    $familia_role         = 'familia';

    /*
     *  Estas funciones se ejecutan pero inmediatamente dejan de ocupar procesamiento si los campos
     *  no se encuentran asignados.
     */

    nido_add_user(
        $cuido_email,
        $cuido_login,
        $cuido_password,
        $cuido_firstname,
        $cuido_lastname,
        $cuido_description,
        $cuido_role
    );

    nido_add_user(
        $empleado_email,
        $empleado_login,
        $empleado_password,
        $empleado_firstname,
        $empleado_lastname,
        $empleado_description,
        $empleado_role
    );

    nido_add_user(
        $familia_email,
        $familia_login,
        $familia_password,
        $familia_firstname,
        $familia_lastname,
        $familia_description,
        $familia_role
    );

}


function nido_crear_ninos() {

    /*
     *  Esta función es para crear CPTs con la información de los niños que se están registrando
     *  como entrada o salida del cuido. Esto es para que cada niño tenga un estatus (En Cuido o
     *  No En Cuido). Inicialmente se crea un post con el estatus No en Cuido.
     *
     *  En esta función no es necesario realizar muchas validaciones ya que la funcionalidad de
     *  Front End de la forma de Formidable hace las validaciones necesarias. Entonces podemos
     *  suponer con libertad que los niños serán registrados en dicho orden. Por ejemplo, no existi-
     *  rán casos en los que se haya llenado información del Niño 5 y no del Niño 4.
     *
     *  Lo que se está creando es un CPT de tipo nido-nino. La información que es necesaria en este
     *  caso es la información del nombre, apellidos y teléfono del niño. Con esta información el
     *  sistema puede empatar toda la información y mostrar la información correctamente dependiendo
     *  de estas variables. A continuacón se puede observar la estructura que nos ayuda a traer
     *  todos los campos necesarios de la forma. El arreglo contiene 10 listas (una para cada niño)
     *  y cada lista contiene el 'item_meta' de la forma correspondiente a los nombres, apellidos y
     *  teléfonos respectivamente.
     */
    $ninios = [
        [520, 521, 529],    // Niño 1
        [428, 429, 437],    // Niño 2
        [441, 442, 450],    // Niño 3
        [454, 455, 463],    // Niño 4
        [468, 469, 477],    // Niño 5
        [481, 482, 490],    // Niño 6
        [494, 495, 503],    // Niño 7
        [507, 508, 516],    // Niño 8
        [607, 608, 616],    // Niño 9
        [594, 534, 603]     // Niño 10
    ];

    $cuido_id = ( isset( $_POST['item_meta'][802] ) ) ? $_POST['item_meta'][802] : '';
    $familia_id = ( isset( $_POST['item_meta'][803] ) ) ? $_POST['item_meta'][803] : '';

    /*
     *  Para que la información pueda ser desplegada correctamente, tenemos que dar un id a cada
     *  niño dependiendo del orden en que fueron introducidos a la Forma. Después se recorre toda
     *  la estructura $ninios, se valida si los campos aplican (si no, entonces termina el procesa-
     *  miento para no tomar más tiempo en caso de que no haya niños registrados).
     */
    $i = 0;
    foreach ( $ninios as list( $nombre, $apellidos, $telefono ) ) {

        if ( isset( $_POST['item_meta'][$nombre]) ) {
            if ( !empty( $_POST['item_meta'][$nombre] ) ) {

                $nombre_apellidos = $_POST['item_meta'][$nombre] . ' ' . $_POST['item_meta'][$apellidos];

                /*
                 *  Antes de crear un post nuevo se verifica que no exista otro post con la misma
                 *  información. Es decir no deben aparecer dos niños con el mismo telefono
                 *  registrado.
                 */

                $args = array(
                    'post_type'     => 'nido-nino',
                    'meta_query'    => array(
                        'relation'  => 'AND',
                        array(
                            'key'   => 'wpcf-telefono-de-familia',
                            'value' => $_POST['item_meta'][$telefono],
                        ),
                        array(
                            'key'   => 'wpcf-nombre-de-nino-a',
                            'value' => $nombre_apellidos,
                        )
                    )
                );

                /*  Se efectúa el Query de WordPress */
                $query = new WP_Query( $args );

                if ( isset( $query->posts[0] ) ) {
                    $i++;
                    continue;
                }

                /*
                 *  Estos son los campos necesarios para crear nuestro CPT nido-nino. Despues, sin
                 *  más, se introduce el post por medio de la función wp_insert_post y se hace un
                 *  pos-incremento de la variable $i para que corresponda a cada renglón de la
                 *  Forma, es decir, a cada cuenta de niño. La información de este arreglo fue
                 *  traída del Codex de WordPress en lo correspondiente a la función wp_insert_post.
                 */
                $custom_post = array(
                    'post_title'    => $nombre_apellidos,
                    'post_type'     => 'nido-nino',
                    'post_status'   => 'publish',
                    'meta_input'    => array(
                        'wpcf-id-de-familia'                => $i,
                        'wpcf-nombre-de-nino-a'             => $nombre_apellidos,
                        'wpcf-telefono-de-familia'          => $_POST['item_meta'][$telefono],
                        'wpcf-estatus-de-entrada-salida'    => 'No en Cuido',
                        'wpcf-id-de-cuido-ninos'            => $cuido_id,
                        'wpcf-id-de-familia-ninos'          => $familia_id
                    )
                );

                wp_insert_post( $custom_post );
            }
            else
                return;
        }
        else
            return;

        $i++;
    }
}


function nido_actualizar_estatus() {

    $nombre = $telefono = $accion = '';

    /*  Nombres de los niños que su estatus será cambiado */
    if ( isset( $_POST['item_meta'][ 658 ] ) )
        $nombre = $_POST['item_meta'][ 658 ];
    else
        return;

    /*  Teléfono para identificar a la familia */
    if ( isset( $_POST['item_meta'][ 258 ] ) )
        $telefono = $_POST['item_meta'][ 258 ];
    else
        return;

    /*  Acción que se va a actualizar */
    if ( isset( $_POST['item_meta'][ 659 ] ) )
        $accion = $_POST['item_meta'][ 659 ];
    else
        return;

    /*  Hora del Cambio */
    if ( isset( $_POST['item_meta'][ 807 ] ) )
        $hora = $_POST['item_meta'][ 807 ];
    else
        return;

    if ( !is_array( $nombre ) ) {

        /*
         *  Argumentos para el WP Query. Con el teléfono de la familia y el nombre del niño se 
         *  localiza al niño para actualizar su estado.
         */
        $args = array(
            'post_type'     => 'nido-nino',
            'meta_query'    => array(
                'relation'  => 'AND',
                array(
                    'key'   => 'wpcf-telefono-de-familia',
                    'value' => $telefono,
                ),
                array(
                    'key'   => 'wpcf-nombre-de-nino-a',
                    'value' => $nombre,
                )
            )
        );

        /*  Se efectúa el Query de WordPress */
        $query = new WP_Query( $args );

        if ( isset( $query->posts[0] ) ) {

            $post = $query->posts[0];

            $accion = ( $accion === 'Entrada' ) ? 'En Cuido' : 'No en Cuido';
            $entrada_salida = ( $accion === 'En Cuido' ) ? 'wpcf-hora-de-entrada-ninos' : 'wpcf-hora-de-salida-ninos';

            /*  Dependiendo de la acción se actualiza el estatus del niño */
            update_post_meta( $post->ID, 'wpcf-estatus-de-entrada-salida', $accion );

            /*  Actualizar hora */
            update_post_meta( $post->ID, $entrada_salida, $hora );
        }
    }
    else {

        foreach ($nombre as $nombre_elemento) {
            /*  Argumentos para el WP Query */
            $args = array(
                'post_type'     => 'nido-nino',
                'meta_query'    => array(
                    'relation'  => 'AND',
                    array(
                        'key'   => 'wpcf-telefono-de-familia',
                        'value' => $telefono,
                    ),
                    array(
                        'key'   => 'wpcf-nombre-de-nino-a',
                        'value' => $nombre_elemento,
                    )
                )
            );

            /*  Se efectúa el Query de WordPress */
            $query = new WP_Query( $args );

            if ( isset( $query->posts[0] ) ) {

                $post = $query->posts[0];

                $meta_update = ( $accion === 'Entrada' ) ? 'En Cuido' : 'No en Cuido';
                $entrada_salida = ( $meta_update === 'En Cuido' ) ? 'wpcf-hora-de-entrada-ninos' : 'wpcf-hora-de-salida-ninos';

                /*  Dependiendo de la acción se actualiza el estatus del niño */
                update_post_meta( $post->ID, 'wpcf-estatus-de-entrada-salida', $meta_update );

                /*  Actualizar hora */
                update_post_meta( $post->ID, $entrada_salida, $hora );
            }
        }
    }
}


function nido_add_user(
    $email,
    $login,
    $password,
    $firstname,
    $lastname,
    $description,
    $role
    ) {

    /*
     *  Las siguientes instrucciones son para dar de alta un Usuario en la base de datos de
     *  WordPress. Esta función realiza todas las validaciones. En caso de que todo salga correcto
     *  la función regresa el ID del Usuario. Las demás sanitizaciones del texto las hace la función
     *  wp_insert_user.
     */

    if ( !empty( $email ) ) {

        if ( username_exists( $email ) || email_exists( $email ) ) {
            echo '<input type="hidden" class="nido_user_exists" value="true" />';
            return;
        }

        $password = wp_generate_password();

        $userdata = array(
            'user_pass'     =>  $password,
            'user_login'    =>  $login,
            'user_email'    =>  $email,
            'display_name'  =>  $firstname . ' ' . $lastname,
            'first_name'    =>  $firstname,
            'last_name'     =>  $lastname,
            'description'   =>  $description,
            'role'          =>  $role
        );

        $user_id = wp_insert_user( $userdata );

        global $wpdb;
        $content = $wpdb->get_var( "SELECT post_content FROM $wpdb->posts WHERE post_type='plantilla-de-email' AND post_title='Bienvenida'" );
        $content = preg_replace( '/%%USERNAME%%/', $login, $content );
        $content = preg_replace( '/%%PASSWORD%%/', $password, $content );
        $content = preg_replace( '/%%ROLE%%/', strtoupper( $role ), $content );

        wp_mail(
            $email,
            __( 'Bienvenido a NIDO', 'ukulele' ),
            $content
        );
    }
}


add_filter( 'frm_validate_entry', 'nido_validate_users', 1, 2 );

function nido_validate_users( $errors, $values ) {

    /*
     *  Esta función es para validad los usuarios que están llenando las formas. Debido a que
     *  Formidable solo permitía la visibilidad de las formas a un Usuario Custom, tuvo que
     *  agregarse este código a la validación. Se está utilizando el Hook de Formidable llamado
     *  frm_validate_entry. En este caso la función recibe dos parámetros: $errors y $values.
     *
     *  $errors ->  Arreglo con la lista de arreglos presentados durante la validación. Cuando el
     *              arreglo está vacío, Formidable entiende que todo está bien y continúa con subir
     *              la Forma al servidor y también otras acciones como crear el CPT.
     *
     *  $values ->  Este arreglo bidimensional contiene los valores de la forma que se está.
     *              Los valores que se están llenando en la forma pueden ser accesados por el sub-
     *              arreglo llamado 'item_meta' seguido del ID del campo de todas las formas.
     */

    $usuarios = array(
        ( isset( $values['item_meta'][172] ) ) ? $values['item_meta'][172] : '',    // Cuido
        ( isset( $values['item_meta'][551] ) ) ? $values['item_meta'][551] : '',    // Empleado
        ( isset( $values['item_meta'][75]  ) ) ? $values['item_meta'][75]  : ''     // Familia
    );

    /*
     *  En caso de que se esté haciendo una actualización, entonces no hay que hacer ningún tipo de
     *  validación ya que Formidable se va a encargar de hacer las validaciones. De todas formas,
     *  hay que evitar que el usuario modifique su username. 
     */

    if ( $values['frm_action'] == 'update' ) {

        foreach ( $usuarios as $email ) {
            if ( !empty( $email ) ) {
                if ( !username_exists( $email ) || !email_exists( $email ) ) {
                    $errors['my_error'] = 'No debes cambiar el nombre de usuario con el que iniciaste sesión.';
                }
            }
        }

        return $errors;
    }

    /*
     *  Continuar con la ejecución normal de la validación de los logins. El propósito de las si-
     *  guientes líneas de código es que no se pueda registrar un usuario si el nombre de usuario
     *  ya existe.
     */

    foreach ( $usuarios as $email ) {
        if ( !empty( $email ) ) {
            if ( username_exists( $email ) || email_exists( $email ) ) {
                $errors['my_error'] = 'Ya hay un Usuario registrado con este email.';
            }
        }
    }

    /*
     *  Checar que la validación corresponde a la forma de Registro de Familia. En este caso cuando
     *  hay un error, se muestra un asterisco para marcar los campos requeridos.
     */

    if ( $values['form_key'] == 'eug7f' ) {
        if ( ! empty( $errors ) ) {
            echo '<span></span>'; // Elemento dummy. Sin esto no se muestra el error.
            $errors['my_error'] = 'Verifique que todos los campos marcados con * esten correctos';

            return $errors;
        }
    }
    
    return $errors;
}


add_action( 'wp_enqueue_scripts', 'nido_custom_scripts' );

function nido_custom_scripts() {

    /*
     *  Esta función es para incluír en el Child Theme, definiciones a funciones de jQuery y 
     *  Javascript para programar el comportamiento del sitio web.
     */

    wp_enqueue_script(
        'nido_custom_js',
        get_stylesheet_directory_uri() . '/assets/js/custom.js', array( 'jquery' ), '1.0',
        true
    );

    wp_localize_script(
        'nido_custom_js',
        'nido_custom_object',
        array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'site_url' => get_site_url() )
    );

    wp_enqueue_script(
        'nido_ajax_js',
        get_stylesheet_directory_uri() . '/assets/js/ajax.js', array( 'jquery' ), '1.26',
        true
    );

    wp_localize_script(
        'nido_ajax_js',
        'nido_ajax_object',
        array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'site_url' => get_site_url() )
    );

    wp_enqueue_script( 'jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js' );

    wp_enqueue_style( 'jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css' );

    wp_enqueue_script( 'jquery-confirm', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.2.0/jquery-confirm.min.js' );

    wp_enqueue_script( 'jquery-mask', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.10/jquery.mask.js' );

    wp_enqueue_style( 'jquery-confirm-css', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.2.0/jquery-confirm.min.css' );

    wp_enqueue_script( 'jquery-chart', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.6.0/Chart.min.js' );

    wp_enqueue_script( 'js-pdf', 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.debug.js' );

    wp_enqueue_script( 'moment-js', 'https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.js' );
}


add_action( 'after_setup_theme', 'remove_admin_bar' );
 
function remove_admin_bar() {

    /*
     *  Esta función está ganchada al Hook 'after_setup_theme' de WordPress la cual remueve la barra
     *  de administración que por defecto WordPress agrega en el front end del sitio web en caso de
     *  que el usuario no sea un administrador ni que sea el administrador principal. Para ocultar
     *  la barra de administración se utiliza una función de WordPress llamada show_admin_bar.
     */

    if ( !current_user_can( 'administrator' ) && !is_admin() ) {
      show_admin_bar( false );
    }
}


add_action( 'init', 'blockusers_init' );

function blockusers_init() {

    /*
     *  Esta función se gancha del hook principal llamado 'init' de WordPress. Basicamente lo que 
     *  esta función es denegar el acceso al wp-admin (Dashboard de WordPress) a todos los usuarios
     *  que no sean administradores. Esto tiene sentido ya que no nos interesa que los clientes o
     *  usuarios finales del sistema tengan acceso ni se compliquen la existencia con el Dashboard
     *  de WordPress. Además la administración será muy amigable y colorida de acuerdo al tema
     *  del proyecto.
     */

    if ( is_admin() && ! current_user_can( 'administrator' ) &&
        ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
        wp_redirect( home_url() );
        exit;
    }
}


add_action( 'frm_display_form_action', 'nido_permissions_of_form', 8, 3 );

function nido_permissions_of_form( $params, $fields, $form ) {

    /*
     *  Esta función controla la decisión de si se muestra o no el contenido de la Forma. En caso de
     *  que se muestren los campos de la forma, pueden mostrarse con un mensaje de error o simple-
     *  mente una notificación al usuario. En este caso lo que nos interesa es que la forma no se 
     *  muestre en caso de que el usuario actual no tenga los permisos necesarios para llenar y
     *  subir la forma.
     *
     *  La tarea de llenar una forma es una tarea seria y complicada ya que impacta fuertemente la
     *  condición del servidor, no de una forma grave, pero si funcionalmente importante. Cuando una
     *  forma ha sido completamente validada y autorizada para enviarse, entonces esta forma crea un
     *  entry en Formidable, un Custom Post y un Usuario nuevo que puede ser Cuido, Empleado o 
     *  Familia. Estos usuarios tienen la capacidad de iniciar sesión en el sistema.
     *
     *  La función se gancha del Hook 'frm_display_form_action' de Formidable y recibe como
     *  parámetros $params, $fields y $form. Básicamente tenemos la forma que se está llenando en el
     *  momento y los campos que contiene esta forma. La función se vale de otro Hook de Formidable
     *  como lo es 'frm_continue_to_new'. Cuando no queremos permitir que la forma actualmente
     *  siendo llenada no se muestre se agrega el filtro con el hook mencionado.
     */

    /*  Inicialmente la forma se va a desplegar normalmente a menos que se indique lo contrario */
    remove_filter( 'frm_continue_to_new', '__return_false', 50 );

    /*
     *  A pesar de que solamente los usuarios Administradores pueden agregar un Cuido Nuevo, los 
     *  Cuidos que ya han sido agregados deben tener la capacidad de editar su propia información.
     *  La siguiente estructura de decisión determina si el usuario es un cuido y si quiere
     *  modificar su propia información. Cuando este sea el caso, detenemos la función para que esta
     *  permita la edición del contenido. En este momento no nos preocupamos por validaciones, ya
     *  que formidable hace dichas validaciones.
     */

    $condition1 = $form->id == 11;
    $condition1 = $condition1 && current_user_can( 'cuido' );
    $condition2 = $params['action'] == 'frm_entries_edit_entry_ajax';
    $condition2 = $condition2 || $params['action'] == 'update';
    $condition2 = $condition2 || $params['action'] == 'edit';

    /*  
     *  Estas condiciones son para validar si un usuario tipo Cuido está intentando crear un Cuido
     *  nuevo o si solamente desea modificar su misma información. El sistema debe dejar que cada
     *  usuario pueda modificar su información relacionada a las formas de Formidable, pero no puede
     *  crear elementos de su mismo tipo.
     */
    if ( $condition1 && $condition2 ) {
        return;
    }

    /*  La forma ID=11 solo puede ser subida por el Administrador ya que crea un Cuido */
    if ( $form->id == 11 && !current_user_can( 'administrator' ) ) {
        echo 'Solo los Administradores pueden registrar un Cuido Nuevo';
        add_filter( 'frm_continue_to_new', '__return_false', 50 );
    }

    /*  La forma ID=22 crea un Empleado. Solo Administrador y Empleado pueden hacer esto */
    if ( $form->id == 22 && !current_user_can( 'administrator' ) && !current_user_can( 'cuido' ) ) {
        echo 'Solo los Administradores y Cuidos pueden registrar un Empleado Nuevo';
        add_filter('frm_continue_to_new', '__return_false', 50);
    }

    /*  La forma ID=2 crea una Familia. Todos pueden crearla menos otra Familia y externos */
    if ( $form->id == 2 &&  !current_user_can( 'administrator' ) && 
                            !current_user_can( 'cuido' ) &&
                            !current_user_can( 'empleado' ) ) {
        echo 'Solo los Administradores, Cuidos y Empleados pueden registrar una Familia Nueva';
        add_filter( 'frm_continue_to_new', '__return_false', 50 );
    }

    /*  La forma ID=22 y 21 crea un registro de entrada / salida. Mismo caso que antes */
    if ( ( $form->id == 20 || $form->id == 21 ) &&  !current_user_can( 'administrator' ) && 
                                                    !current_user_can( 'cuido' ) &&
                                                    !current_user_can( 'empleado' )) {
        echo 'Solo los Administradores, Cuidos y Empleados pueden registrar Entrada / Salida';
        add_filter( 'frm_continue_to_new', '__return_false', 50 );
    }
}


add_action( 'frm_before_destroy_entry', 'nido_eliminar_usuarios' );

function nido_eliminar_usuarios( $entry_id ) {

    /*
     *  Esta función es para eliminar los usuarios que fueron creados al registrar un Cuido,
     *  Empleado o Familia. Cada uno de ellos tienen un identificador único que es con el que se
     *  registraron y es también usando este identificador como se encuentra al usuario que vamos a
     *  eliminar. Cuando se elimina un usuario, se elimina también toda su información para el 
     *  inicio de sesión.
     */

    /*  Formas de Registro de Cuido, Empleado y Familia */
    $form_id = array( 2, 11, 22 );

    $entry = FrmEntry::getOne( $entry_id, true );

    if ( ! isset( $entry->form_id ) ) {
        return;
    }

    /*  Si se encontró la forma con el ID, se continua la ejecución */
    if ( nido_check_forms( $form_id, $entry->form_id ) ) {

        /*  El identificador único es guardado como nombre de la entrada de la forma */
        $user_id = $entry->name;

        /*  
         *  Ese campo (el nombre de la forma) es utilizado para encontrar el usuario deseado.
         *  Hay que tomar en cuenta que hay que eliminar a los usuarios desde el Front End primero
         *  y después eliminar Cuidos. Si se elimina el Cuido antes de que se eliminen sus familias
         *  o empleados, se corre el riesgo de eliminar el usuario equivocado. Sin embargo, la 
         *  opción de eliminar un Cuido solamente la tiene el Administrador. 
         */
        $args = array(
            'meta_query' => array(
                array(
                    'key'       => 'description',
                    'value'     => $user_id,
                    'compare'   => 'LIKE'
                )
            )
        );

        $wp_user_query = new WP_User_Query( $args );

        if ( isset( $wp_user_query->results[0] ) ){
            
            $user_to_delete = $wp_user_query->results[0];

            /*  Cuando hemos encontrado al usuario a eliminar lo eliminamos con la función de WP */
            wp_delete_user( $user_to_delete->ID );

        }

        /*
         *  Si la forma que se está eliminando es de una Familia, se tienen que eliminar
         *  también los niños registrados con esa familia.
         */
        if ( $entry->form_id == 2 ) {
            $family_id = $entry->name;
            $kids_args = array(
                'post_type'    => 'nido-nino',
                'meta_query'    => array(
                    array(
                        'key'       => 'wpcf-id-de-familia-ninos',
                        'value'     => $family_id
                    )
                )
            );
            $kids_by_family = new WP_Query( $kids_args );
            foreach ( $kids_by_family->posts as $kid_post ) {
                wp_delete_post( $kid_post->ID, true );
            }
        }
    }

}


function nido_check_forms( $form_id_array, $form_id_cmp ) {

    /*
     *  Esta función es solamente un OR lógico iterativo para poder ser llamado como una sola
     *  función. Regresa true si la forma es la que se desea eliminar. Regresa false en todas las
     *  demás formas (las que no son para registrar Cuido, Empleado o Familia).
     */

    foreach ( $form_id_array as $form_id ) {

        if ( $form_id == $form_id_cmp ) {
            return true;
        }

    }

    return false;

}

function molc_get_field( $field, $type ) {
    switch ( $type ) {
        case 'string': {
            if ( isset( $field ) ) {
                if ( $field != '' ) {
                    return $field;
                }
            }
            return false;
        }
        
        default: break;
    }
}


/*  Esta función quita los párrafos que se crean automáticamente en el contenido. */
remove_filter( 'the_content', 'wpautop' );

include( 'shortcodes.php' );

include( 'ajax.php' );

// add_action( 'wp_head', 'nido_wp_head' );

// function nido_wp_head() {
//     var_dump('<pre>');
//     var_dump(apply_filters('nido_get_wp_id', 'FMttGv9Uxd'));
//     var_dump('</pre>');
// }


/*
 *  CUSTOM HOOKS
 */

/*  Función para obtener el ID de WordPress (Cuidos) basado en el ID de Nido */
add_filter( 'nido_get_wp_id', 'nido_get_wp_id', 10, 1 );
function nido_get_wp_id( $nido_id ) {

    if ( isset( $nido_id ) && $nido_id != null ) {
        $args = array(
        'meta_query' => array(
                array(
                    'key'       => 'description',
                    'value'     => $nido_id,
                    'compare'   => '='
                )
            )
        );

        if ( $nido_id[0] == 'F' ) {
            $args['meta_query'][0]['compare'] = 'LIKE';
        }

        $user = new WP_User_Query( $args );

        if ( ! empty( $user->results ) && $user->results != null ) {
            return $user->results[0]->ID;
        }
    }
    return -1;
}


/*  Función para obtener el ID de NIDO basado en el ID de WordPress */
add_filter( 'nido_get_nido_id', 'nido_get_nido_id', 10, 1 );
function nido_get_nido_id( $wp_id ) {

    if ( isset( $wp_id ) && $wp_id != null ) {

        $nido_id = get_user_meta( $wp_id, 'description', true );
        $userdata = get_userdata( $wp_id );

        if ( $userdata->roles[0] !== 'cuido' && $nido_id != '' ) {
            $id = preg_split( '/,/', $nido_id );
            $nido_id = isset( $id[1] ) ? $id[1] : '';
        }

        return $nido_id;

    }
    else {
        return -1;
    }

}


/*  Función para saber si una conversación está archivada */
add_filter( 'nido_conversacion_archivada', 'nido_conversacion_archivada', 10, 2 );
function nido_conversacion_archivada( $de_quien, $para_quien ) {
    
    $conversacion_args = array(
        'post_type'     => 'nido-conversaciones',
        'meta_query'    => array(
            'relation'  => 'AND',
            array(
                'key'   => 'wpcf-nido-de-quien',
                'value' => $de_quien,
            ),
            array(
                'key'   => 'wpcf-nido-para-quien',
                'value' => $para_quien
            )
        ),
        'posts_per_page' => -1
    );

    $conversacion = new WP_Query( $conversacion_args );

    if ( ! empty( $conversacion->posts ) ) {
        $archivada = get_post_meta( $conversacion->posts[0]->ID, 'wpcf-nido-archivada' );
        if ( $archivada[0] === 'si' )
            return true;
        else
            return false;
    }
    else {
        return false;
    }

}


/*  Función para sacar la conversacion del archivo */
add_action( 'nido_sacar_conversacion', 'nido_sacar_conversacion', 10, 2 );
function nido_sacar_conversacion( $de_quien, $para_quien ) {
    
    $conversacion_args = array(
        'post_type'     => 'nido-conversaciones',
        'meta_query'    => array(
            'relation'  => 'AND',
            array(
                'key'   => 'wpcf-nido-de-quien',
                'value' => $de_quien,
            ),
            array(
                'key'   => 'wpcf-nido-para-quien',
                'value' => $para_quien
            )
        ),
        'posts_per_page' => -1
    );

    $conversacion = new WP_Query( $conversacion_args );

    if ( ! empty( $conversacion->posts ) ) {
        // update_post_meta( $conversacion->posts[0]->ID, 'wpcf-nido-archivada', 'no' );
        wp_delete_post( $conversacion->posts[0]->ID );
    }
}


add_filter( 'nido_get_avatar', 'nido_get_avatar', 10, 1 );

function nido_get_avatar( $user_id ) {

    $args = array(
        'meta_query'    => array(
            array(
                'value' => $user_id,
            )
        ),
        'posts_per_page' => -1
    );

    switch( $user_id[0] ) {
        case 'F':
            $args['post_type'] = 'nido-familia';
            $args['meta_query'][0]['key'] = 'wpcf-id-familia';
            $avatar_meta_key = 'wpcf-avatar-familia';
            break;
        case 'E':
            $args['post_type'] = 'nido-empleado';
            $args['meta_query'][0]['key'] = 'wpcf-id-empleado';
            $avatar_meta_key = 'wpcf-avatar-empleado';
            break;
        case 'C':
            $args['post_type'] = 'nido-cuido';
            $args['meta_query'][0]['key'] = 'wpcf-cuido-id';
            $avatar_meta_key = 'wpcf-avatar-cuido';
            break;
        default: break;
    }

    $query_user_post_id = new WP_Query( $args );
    $user_post_id = $query_user_post_id->posts[0]->ID;

    return get_post_meta( $user_post_id, $avatar_meta_key, true );
}


add_filter( 'nido_get_name', 'nido_get_name', 10, 1 );

function nido_get_name( $nido_id ) {
    $args = array(
        'meta_query'    => array(
            array(
                'value' => $nido_id,
            )
        ),
        'posts_per_page' => -1
    );

    switch( $nido_id[0] ) {
        case 'F':
            $args['post_type'] = 'nido-familia';
            $args['meta_query'][0]['key'] = 'wpcf-id-familia';
            break;
        case 'E':
            $args['post_type'] = 'nido-empleado';
            $args['meta_query'][0]['key'] = 'wpcf-id-empleado';
            break;
        case 'C':
            $args['post_type'] = 'nido-cuido';
            $args['meta_query'][0]['key'] = 'wpcf-cuido-id';
            break;
        default: break;
    }

    $query_user_post = new WP_Query( $args );

    return $query_user_post->posts[0]->post_title;
}


add_filter( 'nido_get_post_user', 'nido_get_post_user' );

function nido_get_post_user( $nido_id ) {
    $args = array(
        'meta_query'    => array(
            array(
                'value' => $nido_id,
            )
        ),
        'posts_per_page' => -1
    );

    switch( $nido_id[0] ) {
        case 'F':
            $args['post_type'] = 'nido-familia';
            $args['meta_query'][0]['key'] = 'wpcf-id-familia';
            break;
        case 'E':
            $args['post_type'] = 'nido-empleado';
            $args['meta_query'][0]['key'] = 'wpcf-id-empleado';
            break;
        case 'C':
            $args['post_type'] = 'nido-cuido';
            $args['meta_query'][0]['key'] = 'wpcf-cuido-id';
            break;
        default: break;
    }

    $query_user_post = new WP_Query( $args );

    return $query_user_post->posts[0];
}


/*
 *  DEBUG FUNCTIONS
 */

add_action( 'wp_mail', 'nido_wp_mail' );

function nido_wp_mail( $args ) {
    error_log( print_r( $args, true ) );
}


// add_action( 'wp_head', 'nido_wp_head' );

// function nido_wp_head() {

//     global $wpdb;
//     $id = $wpdb->get_var( "SELECT post_content FROM $wpdb->posts WHERE post_type='plantilla-de-email' AND post_title='Bienvenida'" );

//     var_dump( '<pre>' );
//     var_dump( $id );
//     var_dump( '</pre>' );
// }


















