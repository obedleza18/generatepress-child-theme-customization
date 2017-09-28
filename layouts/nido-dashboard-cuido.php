<?php
    /*
     *  Este layout para el shortcode [nido-dashboard] cuando el usuario es Cuido muestra tres
     *  imágenes para ligar al usuario a modificar su propia información, para modificar sus grupos
     *  familiares o para ver los reportes.
     */

    $user = wp_get_current_user();

    $cuido_id = get_user_meta( $user->ID, 'description', true );
?>

<div class="nido-title">
    <img 
        class="nido-title" 
        src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-huevo-cuenta.png"
    />
    <label class="nido-lable-title">Mi Cuenta</label>
    
</div>

<div class="nido-dashboard-cuido"> 
    <a href="<?php echo get_site_url() ?>/mi-cuenta/?cuido_id=<?php esc_attr_e( $cuido_id ) ?>">
        <img  
            id="nido-dashboard-mi-cuido" 
            class="nido-imagen-dashboard" 
            src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-imagen-cuido.png"
            style="visibility: hidden;"
        />
    </a>
    <a href="<?php echo get_site_url() ?>/mis-grupos-familiares?cuido_id=<?php $atts['field'] = 'id'; echo esc_attr( nido_current( $atts ) ); ?>">
        <img 
            id="nido-dashboard-mis-grupos-familiares" 
            class="nido-imagen-dashboard" 
            src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-grupos-familiares.png"
            style="visibility: hidden;"
        />
    </a>
    <a href="<?php echo get_site_url() ?>/reportes/">
        <img 
            id="nido-dashboard-reportes" 
            class="nido-imagen-dashboard" 
            src="<?php echo get_template_directory_uri() ?>_child/assets/icons/nido-reportes.png"
            style="visibility: hidden;"
        />
    </a>
</div>