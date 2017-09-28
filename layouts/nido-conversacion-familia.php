<?php
    /*
     *
     */

    $user_id = ( isset( $_GET['to'] ) ) ? $_GET['to'] : '';

    $familia = wp_get_current_user();
    $cuido_familia_id = get_user_meta( $familia->ID, 'description', true );
    list( $cuido_id, $familia_id ) = preg_split( '/,/', $cuido_familia_id );

    $args = array(
        'post_type'     => 'nido-mensaje',
        'meta_query'    => array(
            'relation'  => 'OR',
            array(
                'relation'  => 'AND',
                array(
                    'key'   => 'wpcf-de-mensajes',
                    'value' => $familia_id,
                ),
                array(
                    'key'   => 'wpcf-para-mensajes',
                    'value' => $user_id,
                )
            ),
            array(
                'relation'  => 'AND',
                array(
                    'key'   => 'wpcf-de-mensajes',
                    'value' => $user_id,
                ),
                array(
                    'key'   => 'wpcf-para-mensajes',
                    'value' => $familia_id,
                )
            ),
        ),
        'orderby'           => 'post_date',
        'order'             => 'ASC',
        'posts_per_page'    => -1
    );

    $query = new WP_Query( $args );
?>

<div id="nido-tod"></div>

<style>
    #menu-item-902 {
        background: linear-gradient(#eae7c4, rgba(255,0,0,0));
    }
    .menu-item-902 {
        background-color: #eae7c4;
    }
</style>

<div class="nido-conversacion">

    <?php if ( empty( $query->posts ) ): ?>

        <div class="nido-mensaje-conversacion">
            <label>No hay mensajes</label>
        </div>
        
    <?php else : ?>

        <?php foreach ( $query->posts as $post ): ?>

            <div class="nido-mensaje-conversacion">
                <label><?php esc_html_e( $post->post_title ) ?></label>
                <label><?php esc_html_e( get_post_meta( $post->ID, 'wpcf-fecha-mensajes', true ) ) ?></label>
                <label><?php esc_html_e( get_post_meta( $post->ID, 'wpcf-mensaje-mensajes', true ) ) ?></label>
            </div>

        <?php endforeach ?>

    <?php endif ?>

</div>

<?php 
    /*
     *  Marcar todos los mensajes como leídos.
     */

    $args = array(
        'post_type'     => 'nido-mensaje',
        'meta_query'    => array(
            'relation'  => 'AND',
            array(
                'key'   => 'wpcf-leido-mensajes',
                'value' => 'no',
            ),
            array(
                'key'   => 'wpcf-para-mensajes',
                'value' => $familia_id,
            ),
            array(
                'key'   => 'wpcf-de-mensajes',
                'value' => $user_id,
            )
        ),
        'posts_per_page' => -1
    );

    $query = new WP_Query( $args );

    if ( ! empty( $query->posts ) ) {

        foreach ( $query->posts as $post ) {
            
            update_post_meta( $post->ID, 'wpcf-leido-mensajes', 'si' );

        }

    }
?>