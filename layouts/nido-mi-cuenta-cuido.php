<?php
    /*
     *	Layout para el Shortcode que muestra los íconos de Mi Cuenta. También se configuran
     *	redireccionamientos dinámicos dependiendo de la página en la que se encuentre el usuario y
     *	de la acción que quiera realizar.
     */

    $atts = array( 'field' => 'id' );

    $cuido_id = nido_current( $atts );

    $href = get_site_url() . '/panel-de-control/';

    if ( isset( $_GET['frm_action'] ) ) {
    	$href = ( $_GET['frm_action'] == 'edit' ) ? get_site_url() . '/mi-cuenta/?cuido_id=' . $cuido_id : get_site_url() . '/panel-de-control/' ;
    }

    $permalink = get_permalink();

	if ( strpos( $permalink, 'registrar-un-empleado' ) !== false ) {
	    $href = get_site_url() . '/mi-cuenta/?cuido_id=' . $cuido_id;
	}
?>

<style>
	#menu-item-917 {
	    background: linear-gradient(#eae7c4, rgba(255,0,0,0));
	}
	.menu-item-917 {
	    background-color: #eae7c4;
	}
</style>

<div class="nido-header-image">
    <a href="<?php esc_attr_e( $href ); ?>">
    <img id="nido-back-button" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-boton-atras.png" /></a>
    <img id="nido-vertical-line" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-vertical-line.png" />
    <img id="nido-mi-cuido-icon" src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-mi-cuido-icon.png" />
</div>

<div id="nido-tod"></div>