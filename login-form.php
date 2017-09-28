<?php
    /*
     *  Este script de PHP modifica la forma de login por defecto de Theme My Login.
     */
?>

<div class="nido-login">
    <div class="tml tml-login" id="theme-my-login<?php $template->the_instance(); ?>">
        <div class="nido-logo">
            <img src="<?php echo get_template_directory_uri() ?>_child/assets/icons/logo-2x.png" alt="" width="769" height="837" class="alignnone size-full wp-image-889" />
        </div>
        <?php $template->the_action_template_message( 'login' ); ?>
        <?php $template->the_errors(); ?>
        <form   name="loginform"
                id="loginform<?php $template->the_instance(); ?>"
                action="<?php $template->the_action_url( 'login', 'login_post' ); ?>"
                method="post"
            >
            <div>
                <input  class="nido-input-username"
                        type="text"
                        name="log"
                        id="user_login<?php $template->the_instance(); ?>"
                        placeholder="<?php esc_attr_e( 'Usuario', 'nido' ); ?>"
                        value="<?php $template->the_posted_value( 'log' ); ?>"
                        size="50" 
                />
            </div>

            <div>
                <input  class="nido-input-password"
                        type="password"
                        name="pwd"
                        id="user_pass<?php $template->the_instance(); ?>"
                        placeholder="<?php esc_attr_e( 'Contraseña', 'nido' ); ?>"
                        value=""
                        size="50"
                        autocomplete="off"
                />
            </div>

            <?php do_action( 'login_form' ); ?>

            <div class="nido-login-links">
                <a class="nido-register-link" href="/register-2/">
                    <?php esc_html_e( 'Crea tu cuenta', 'nido' ); ?>
                </a>
                <a class="nido-forgot-link" href="/lostpassword/">
                    <?php esc_html_e( '¿Lo olvidaste?', 'nido' ); ?>
                </a>
            </div>

            <br>

            <div class="tml-rememberme-submit-wrap">
                <input  class="nido-input-submit"
                        type="submit"
                        name="wp-submit"
                        id="wp-submit<?php $template->the_instance(); ?>"
                        value="<?php esc_attr_e( 'Iniciar Sesión', 'nido' ); ?>"
                />
                <input  type="hidden"
                        name="redirect_to"
                        value="<?php $template->the_redirect_url( 'login' ); ?>"
                />
                <input  type="hidden"
                        name="instance"
                        value="<?php $template->the_instance(); ?>"
                />
                <input  type="hidden"
                        name="action"
                        value="login"
                />
            </div>
        </form>
    </div>
</div>
