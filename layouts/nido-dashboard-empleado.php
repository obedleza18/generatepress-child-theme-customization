<?php

    $user_id = apply_filters( 'nido_get_nido_id', wp_get_current_user()->ID );
    $user_post = apply_filters( 'nido_get_post_user', $user_id );

    $tel = get_post_meta( $user_post->ID, 'wpcf-empleado-telefono', true );
    $tel2 = get_post_meta( $user_post->ID, 'wpcf-empleado-telefono-secundario', true );
    $email = get_post_meta( $user_post->ID, 'wpcf-empleado-email', true );
?>

<div id="nido-tod"></div>
<div class="nido-family-view">
    <div class="nido-top">
        <img src="<?php esc_attr_e( apply_filters( 'nido_get_avatar', $user_id ) ) ?>">
        <label><?php esc_html_e( apply_filters( 'nido_get_name', $user_id ) ) ?></label>
    </div>
    <div class="nido-details">
        <?php if ( $tel != '' ): ?>
            <div class="nido-view-field">
                <label>Teléfono: <?php esc_html_e( $tel ) ?></label>
            </div>
        <?php endif ?>
        <?php if ( $tel2 != '' ): ?>
            <div class="nido-view-field">
                <label>Teléfono Secundario: <?php esc_html_e( $tel2 ) ?></label>
            </div>
        <?php endif ?>
        <div class="nido-view-field">
            <label>Email: <?php esc_html_e( $email ) ?></label>
        </div>
        <div class="nido-view-field">
            <label>Contraseña: <a href="<?php echo esc_attr( get_site_url() . '/tu-perfil?back=' . get_permalink() ) ?>">Cambiar Contraseña</a></label>
        </div>
    </div>
</div>