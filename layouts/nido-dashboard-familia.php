<?php

    $user_id = apply_filters( 'nido_get_nido_id', wp_get_current_user()->ID );
    $user_post = apply_filters( 'nido_get_post_user', $user_id );

    $cuido = get_post_meta( $user_post->ID, 'wpcf-nombre-cuido-familia', true );
    $tel1 = get_post_meta( $user_post->ID, 'wpcf-nido-telefono-1', true );
    $tel2 = get_post_meta( $user_post->ID, 'wpcf-nido-telefono-2', true );
    $tel3 = get_post_meta( $user_post->ID, 'wpcf-nido-telefono-3', true );
    $guardian = get_post_meta( $user_post->ID, 'wpcf-nombre-de-guardian', true );
    $apellidos_guardian = get_post_meta( $user_post->ID, 'wpcf-apellidos-del-guardian-paterno-y-materno', true );
    $tel_guardian = get_post_meta( $user_post->ID, 'wpcf-telefono-del-guardian', true );
    $relacion = get_post_meta( $user_post->ID, 'wpcf-relacion-con-el-los-nino-s', true );
?>

<div id="nido-tod"></div>
<div class="nido-family-view">
    <div class="nido-top">
        <img src="<?php esc_attr_e( apply_filters( 'nido_get_avatar', $user_id ) ) ?>">
        <label><?php esc_html_e( apply_filters( 'nido_get_name', $user_id ) ) ?></label>
    </div>
    <div class="nido-details">
        <div class="nido-view-field">
            <label>Nombre de Cuido: <?php esc_html_e( $cuido ) ?></label>
        </div>
        <?php if ( $tel1 != '' ): ?>
            <div class="nido-view-field">
                <label>Teléfono 1: <?php esc_html_e( $tel1 ) ?></label>
            </div>
        <?php endif ?>
        <?php if ( $tel2 != '' ): ?>
            <div class="nido-view-field">
                <label>Teléfono 2: <?php esc_html_e( $tel2 ) ?></label>
            </div>
        <?php endif ?>
        <?php if ( $tel3 != '' ): ?>
            <div class="nido-view-field">
                <label>Teléfono 3: <?php esc_html_e( $tel3 ) ?></label>
            </div>
        <?php endif ?>
        <div class="nido-view-field">
            <label>Guardián: <?php esc_html_e( $guardian . ' ' . $apellidos_guardian ) ?></label>
        </div>
        <div class="nido-view-field">
            <label>Teléfono de Guardián: <?php esc_html_e( $tel_guardian ) ?></label>
        </div>
        <div class="nido-view-field">
            <label>Relación: <?php esc_html_e( $relacion ) ?></label>
        </div>
        <div class="nido-view-field">
            <label>Contraseña: <a href="<?php echo esc_attr( get_site_url() . '/tu-perfil?back=' . get_permalink() ) ?>">Cambiar Contraseña</a></label>
        </div>
    </div>
</div>