<?php
    /*
     *	La mayor parte del código HTML va a estar en las Vistas de Formidable. Este layout es para
     *  mostrar los empleados de un Cuido.
     */

    $user = wp_get_current_user();

    $cuido_id = get_user_meta( $user->ID, 'description', true );

    $args = array(
        'post_type'     => 'nido-empleado',
        'meta_query'    => array(
            array(
                'key'   => 'wpcf-empleado-cuido-id',
                'value' => $cuido_id,
            )
        )
    );

    $query = new WP_Query( $args );

    $employers = array();

    /*  Obtener información de todos los empleados */
    foreach ($query->posts as $post) {

        $picture = '';

        /*  Se configura la foto del empleado si es que la tiene */
        if ( isset( get_post_meta( $post->ID )['wpcf-foto-del-empleado'] ) ) {
            $picture = get_post_meta( $post->ID )['wpcf-foto-del-empleado'];
        }

        $name = $post->post_title;

        $employer = array(
            'name' => $name
        );
        
        if ( isset( $picture[0] ) ) {
            $employer['picture'] = $picture[0];
        }
        else {
            $employer['picture'] = '/wp-content/uploads/2017/03/nido-sample-image-camera.png';
        }

        /*  Cuando la información está lista, se mete a un arreglo para despṕues mostrarlo */
        array_push( $employers, $employer );
    }

    foreach ($employers as $employer) :
?>

<div class="nido-empleados">
    <?php if ( strpos( $employer['picture'], 'nido-sample-image-camera' ) !== false ) : ?>
        <img class="nido-empleado-imagen" src="<?php echo esc_attr( $employer['picture'] ); ?>" />
    <?php else : ?>
        <div class="nido-empleado-imagen" style="background-image: url( <?php echo esc_attr( $employer['picture'] ); ?> );"></div>
    <?php endif; ?>
    <label class="nido-nombre-empleado"><?php echo esc_html( $employer['name'] ); ?></label>
    <a href="#" ><img class="nido-pencil" src="/wp-content/uploads/2017/03/nido-pencil-1.png" /></a>
    <a href="#" ><img class="nido-cross" src="/wp-content/uploads/2017/03/nido-cross-1.png"  /></a>
</div>

<?php endforeach; ?>
