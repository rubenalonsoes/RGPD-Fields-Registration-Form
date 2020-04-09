<?php
/*
Plugin Name: RGPD Fields Registration Form
Description: With this plugin you can add some extra fields on your default registration form of WordPress to adapt it to the GDPR
Version: 0.1
Author: Rubén Alonso
Author URI: https://miposicionamientoweb.es/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

//Exit if accessed directly

if( !defined( 'ABSPATH' ) ) exit;

// CSS

function rgpd_frf_css() {
    wp_register_style( 'rgpd-frf-style', plugins_url( '/rgpd-frf-style.css', __FILE__ ), '', '0.1' );
    wp_enqueue_style( 'rgpd-frf-style' );
}
add_action( 'wp_enqueue_scripts', 'rgpd_frf_css' );

// Field adding
add_action('register_form', 'new_rgpd_item_register_form');
function new_rgpd_item_register_form() {
	
	$privacy_url = get_option('privacy_url_rgpd_frf');
	if(!$privacy_url)
		$privacy_url = "#";
		
	$init_text = get_option('init_text_rgpd_frf');
	if($init_text)
		$init_text .= "<br>";
	
	$responsible = get_option('responsible_rgpd_frf');
	$purpose = get_option('purpose_rgpd_frf');
	$legitimacy = get_option('legitimacy_rgpd_frf');
	$target = get_option('target_rgpd_frf');
	$rights = get_option('rights_rgpd_frf');
	?>
	
	<div class="rgpd_frf_check"><input id="field_rgpd_yes" type="checkbox" name="rgpd_yes" value="yes" required=""> Acepto la <a href="<?php echo $privacy_url ?>" target="_blank" rel="noopener noreferrer">Política de Privacidad</a></div><br>
	<div class="rgpd_frf_info">
	<?php echo $init_text ?>
	<strong>Responsable » </strong> <?php echo $responsible ?><br>
	<strong>Finalidad » </strong> <?php echo $purpose ?><br>
	<strong>Legitimación » </strong> <?php echo $legitimacy ?><br>
	<strong>Destinatarios » </strong> <?php echo $target ?><br>
	<strong>Derechos » </strong> <?php echo $rights ?></div>
	<br>
	<?php
}

// Field validation
add_filter( 'registration_errors', 'new_rgpd_item_register_validation', 10, 3 );
function new_rgpd_item_register_validation( $errors, $sanitized_user_login, $user_email ) {

	if ( empty( $_POST['rgpd_yes'] ) ) {
		$errors->add( 'empty_rgpd_yes', __( '<strong>ERROR</strong>: Debes leer y aceptar la política de privacidad', 'rgpd-frf' ) );
	}

	return $errors;
}

add_filter( 'learndash-registration-errors', 'new_rgpd_item_register_errors');
function new_rgpd_item_register_errors( $errors_conditions ) {

	$new_errors_conditions = array_merge($errors_conditions, array(
			'empty_rgpd_yes'   => __( 'Debes leer y aceptar la política de privacidad.', 'rgpd-frf' ),
		));

	return $new_errors_conditions;
}

//////////////////////
//// Admin ///////////
//////////////////////

function rgpd_frf_menu(){
 
    add_menu_page( 'RGPD Fields', 'RGPD Fields', 'manage_options', 'rgpd-frf', 'rgpd_frf_page_options', 'dashicons-forms' );


}
add_action('admin_menu', 'rgpd_frf_menu');

function rgpd_frf_page_options() {

	if (!current_user_can ('manage_options')) wp_die (__ ('No tienes suficientes permisos para acceder a esta página.'));
?>

	<div class="wrap">
<?php 
		$settings_saved = false;

		if ( isset( $_POST[ 'save' ] ) ) {

			update_option('privacy_url_rgpd_frf', $_POST['privacy_url_rgpd_frf']);
			update_option('init_text_rgpd_frf', $_POST['init_text_rgpd_frf']);
			update_option('responsible_rgpd_frf', $_POST['responsible_rgpd_frf']);
			update_option('purpose_rgpd_frf', $_POST['purpose_rgpd_frf']);
			update_option('legitimacy_rgpd_frf', $_POST['legitimacy_rgpd_frf']);
			update_option('target_rgpd_frf', $_POST['target_rgpd_frf']);
			update_option('rights_rgpd_frf', $_POST['rights_rgpd_frf']);

			$settings_saved = true;
		}
		
		if ( $settings_saved ) : ?>
			<div id="message" class="updated fade">
				<p><strong><?php _e( 'Opciones guardadas', 'rgpd-frf' ) ?></strong></p>
			</div>
		<?php endif ?>
		<h1><?php _e( 'RGPD Fields Registration Form', 'rgpd-frf' ) ?></h1>
		<p style="width:80%;">Aquí puedes configurar la URL de tu política de privacidad y la capa informativa que se mostrará justo debajo del formulario de registro de tu WordPress y otros plugins que lo usen, como LearnDash.</p>
		<br>
	</div>

	<div class="wrap">
	
		<form method="post" action="">
			<p>
				<strong><?php _e( 'URL de la política de privacidad', 'rgpd-frf' ) ?></strong><br>
				<input type="text" name="privacy_url_rgpd_frf" style="width: 70%" value="<?php echo get_option( 'privacy_url_rgpd_frf' ); ?>">
			</p>
			<br>
			<p>
				<strong><?php _e( 'Texto inicial (opcional)', 'rgpd-frf' ) ?></strong><br>
				<input type="text" name="init_text_rgpd_frf" style="width: 70%" value="<?php echo get_option( 'init_text_rgpd_frf' ); ?>">
			</p>
			<p>
				<strong><?php _e( 'Responsable', 'rgpd-frf' ) ?></strong><br>
				<input type="text" name="responsible_rgpd_frf" style="width: 70%" value="<?php echo get_option( 'responsible_rgpd_frf' ); ?>">
			</p>
			<p>
				<strong><?php _e( 'Finalidad', 'rgpd-frf' ) ?></strong><br>
				<input type="text" name="purpose_rgpd_frf" style="width: 70%" value="<?php echo get_option( 'purpose_rgpd_frf' ); ?>">
			</p>
			<p>
				<strong><?php _e( 'Legitimación', 'rgpd-frf' ) ?></strong><br>
				<input type="text" name="legitimacy_rgpd_frf" style="width: 70%" value="<?php echo get_option( 'legitimacy_rgpd_frf' ); ?>">
			</p>
			<p>
				<strong><?php _e( 'Destinatarios', 'rgpd-frf' ) ?></strong><br>
				<input type="text" name="target_rgpd_frf" style="width: 70%" value="<?php echo get_option( 'target_rgpd_frf' ); ?>">
			</p>
			<p>
				<strong><?php _e( 'Derechos', 'rgpd-frf' ) ?></strong><br>
				<input type="text" name="rights_rgpd_frf" style="width: 70%" value="<?php echo get_option( 'rights_rgpd_frf' ); ?>">
			</p>
			<br>
			<p>
				<input name="save" type="submit" value="<?php _e( 'Guardar', 'rgpd-frf' ) ?>" />
			</p>
		</form>
	</div>
	<div class="wrap-bottom">
		<p>
		  Este plugin ha sido creado por Rubén Alonso (<a href="https://miposicionamientoweb.es/" target="_blank" rel="noopener noreferrer">miposicionamientoweb.es</a>) solo para cumplir con la adaptación legal RGPD en el <strong>formulario de registro de WordPress</strong> y otros plugins que lo usen, como LearnDash.
		</p>
		<p>
		  Para una adaptación legal <strong>completa</strong> de tu sitio web, te recomiendo los <a href="https://miposicionamientoweb.es/visitar/kitslegales" target="_blank" rel="noopener noreferrer">kits legales de Marina Brocca</a>, especialista en RGPD y marketing legal.
		</p>
	</div>
<?php
}

// plugin uninstallation
register_uninstall_hook( __FILE__, 'rgpd_frf_uninstall' );
function rgpd_frf_uninstall() {
    delete_option('privacy_url_rgpd_frf');
	delete_option('init_text_rgpd_frf');
	delete_option('responsible_rgpd_frf');
	delete_option('purpose_rgpd_frf');
	delete_option('legitimacy_rgpd_frf');
	delete_option('target_rgpd_frf');
	delete_option('rights_rgpd_frf');
}