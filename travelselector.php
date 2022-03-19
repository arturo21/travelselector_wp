<?php
error_reporting(0);
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           travelselector
 *
 * @wordpress-plugin
 * Plugin Name:       Travel Selector
 * Plugin URI:        http://example.com/travelselector-uri/
 * Description:       Widget travel selector
 * Version:           1.0.45
 * Author:            AV Soluciones Web
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       travelselector
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'TRAVELSELECTOR_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-travelselector-activator.php
 */
function activate_travelselector() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-travelselector-activator.php';
	travelselector_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-travelselector-deactivator.php
 */
function deactivate_travelselector() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-travelselector-deactivator.php';
	travelselector_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_travelselector' );
register_deactivation_hook( __FILE__, 'deactivate_travelselector' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-travelselector.php';

function javascript_variables(){ ?>
    <script type="text/javascript">
        var ajax_url = '<?php echo admin_url( "admin-ajax.php" ); ?>';
        var ajax_nonce = '<?php echo wp_create_nonce( "secure_nonce_name" ); ?>';
    </script><?php
}
add_action ( 'wp_head', 'javascript_variables' );

/**
 * In this function we will handle the form inputs and send our email.
 *
 * @return void
 */

function sendform_dvnv(){
    // This is a secure process to validate if this request comes from a valid source.
	check_ajax_referer( 'secure_nonce_name', 'security' );
    /**
     * First we make some validations,
     * I think you are able to put better validations and sanitizations. =)
     */

    if ( empty( $_POST["desdeinput"] ) ) {
        echo "Insert your name please";
        wp_die();
    }

    if ( ! filter_var( $_POST["emaildest"], FILTER_VALIDATE_EMAIL ) ) {
        echo 'Insert your email please';
        wp_die();
    }

    if ( empty( $_POST["haciainput"] ) ) {
        echo "Insert your comment please";
        wp_die();
    }
	$content="";
	$email=get_option('myemailtravselector');
	$title   = 'Solicitud de Compra de Boletos';
	$content = '<strong>Solicitud de Boletería</strong><br><br>';
	$desde=$_POST['desdeinput'];
	$hasta=$_POST['haciainput'];
	$ida=$_POST['salidainput'];
	$vuelta=$_POST['vueltainput'];
	$ninos=$_POST['ninosinput'];
	$adultos=$_POST['adultosinput'];
	$nombre=$_POST['nombreinput'];
	$telefono=$_POST['telefinput'];
	$emaildest=$_POST['emaildest'];
	$content.="Datos de la Solicitud<br>";
	$content.="Desde: ".$desde."<br>";
	$content.="Hasta: ".$hasta."<br>";
	$content.="Salida: ".$ida."<br>";
	$content.="Vuelta: ".$vuelta."<br>";
	$content.="Niños: ".$ninos."<br>";
	$content.="Adultos: ".$adultos."<br>";
	$content.="Nombre: ".$nombre."<br>";
	$content.="Teléfono: ".$telefono."<br>";
	$content.="Email: ".$emaildest."<br><br>";
	if ($_POST['comoviajainput']=='iyv'){
	    $content .= "Tipo de Viaje: Ida y Vuelta<br>";
	}
	else{
	    $content .= "Tipo de Viaje: Solo Ida\n";
	}
	$content.="De Viaje En Viaje Agencia\n\n";

    // This are the message headers.
    $headers = array('Content-Type: text/html; charset=UTF-8');
    wp_mail( $emaildest, "SOLICITUD DE COTIZACION", $content, $headers );
    wp_die();
}

add_action( 'wp_ajax_sendform_dvnv', 'sendform_dvnv' );    //execute when wp logged in
add_action('wp_ajax_nopriv_sendform_dvnv', 'sendform_dvnv'); // This is for unauthenticated users.

function my_menu_travelselector(){
	add_menu_page('Travel Selector', 'Travel Selector', 'manage_options', 'travelselector_menu_plugin_slug', 'output_menu_travelselector','dashicons-admin-site');
}

function output_menu_travelselector() {
		if ( !current_user_can('manage_options') )  {
			wp_die( __('You do not have sufficient permissions to access this page.') );
		}
	?>
	<div class="wrap">
		<h1>Travel Selector</h1>
		<h2>Options</h2>
		<p></p>
		  <?php
		  	//Seleccionar tabs
			if( isset( $_GET[ 'tab' ] ) ) {
				$active_tab = $_GET[ 'tab' ];
			} // end if
			$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general';
		  ?>
		<h2 class="nav-tab-wrapper">
			<a href="?page=<?php echo($_GET['page']); ?>&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
        </h2>
		<div class="adentro_div">
			<form method="post" action="options.php">
				<?php
					settings_errors();
					// This prints out all hidden setting fields
				  	switch($active_tab){
						case "general":
							?>
							  	<label>Correo Electrónico</label><br>
							  	<input name="myemailtravselector" id="myemailtravselector" type="text" value="<?php echo esc_attr( get_option('myemailtravselector') ); ?>"/><br>
							<?php
							settings_fields('general-travelselector-group');
				  			do_settings_sections('general-travelselector-group');
							break;
				  	}
					submit_button();
				  ?>
			  </form>
			</div>
		</div>
	  <?php
	}

	add_action('admin_menu', 'my_menu_travelselector');

	function register_mysettings_travelselector() { // whitelist options
		//GENERAL
		register_setting(
            'general-travelselector-group', // Option group
            'myemailtravselector', // Option name
            array('sanitize') // Sanitize
        );
	}
	add_action('admin_init', 'register_mysettings_travelselector');


	function import_travelselector(){
		$template='<style type="text/css">
	.mainclass{
		position:absolute;
		float:none;
		display:none;
		width:100%;
		height:auto;
	}
	#my_travelselector{
		position: relative;
		display: block;
		float: none;
		z-index: 0;
		background: #fff;
		padding-top: 10px;
		max-width: 1124px;
		min-height: 300px;
		margin: 0 auto;
		padding-top: 20px;
		padding-left: 20px;
		padding-bottom: 30px;
		min-width: 320px;
	}
</style>

<section id="my_travelselector" class="mainclass" style="display:none;">
	<!-- This file should primarily consist of HTML with a little bit of PHP. -->
	<form action="" method="POST" name="solicotiz" id="solicotiz" enctype="multipart/form-data">
		<div class="container">
			<div class="container">
			<div class="row pb-3">
					<div class="col-xs-2 pl-2">
						<input name="comoviajainput" id="idayvuelta" type="radio" value="iyv"> Ida y Vuelta
					</div>
					<div class="col">
						<input name="comoviajainput" id="soloida" type="radio" value="si"> Solo Ida
					</div>
			  </div>
				<div class="row"></div>
				</div>
		</div>
		<div class="container">
		<div class="container">
			<div class="row pl-4">
			<div class="col-md">
				<div class="row">
					<div class="row">
						<p><h4>¿A Dónde Viajas?</h4></p>
					</div>
					<div class="row">
						<div class="col" style="padding-left:0px;">
							<label>Desde</label><br>
							<input name="desdeinput" id="desdeinput" type="text" class="form-control" placeholder="Bogotá, Colombia">
						</div>
						<div class="col">
							<label>Hacia</label><br>
							<input name="haciainput" id="haciainput" type="text" class="form-control" placeholder="Miami, USA" style="width: 120px;" >
						</div>
					</div>
				</div>
			</div>
			<div class="col-md">
				<div class="row">
					<div class="row">
						<p><h4>¿Cuándo Viajas?</h4></p>
				  </div>
					<div class="row">
						<div class="col-4" style="padding-left:0px;">
							<label>Salida</label><br>
							<input name="salidainput" id="salidainput" type="date" class="form-control" placeholder="DD/MM/YYYY"
        onfocus="(this.type="date")"
        onblur="(this.type="text")">
						</div>
						<div class="col-4">
							<label>Vuelta</label><br>
							<input name="vueltainput" id="vueltainput" type="date" class="form-control" placeholder="DD/MM/YYYY"
        onfocus="(this.type="date")"
        onblur="(this.type="text")">
						</div>
				  </div>
			  </div>
			</div>
			<div class="col-md">
			<div class="row pe-4">
				<div class="row">
					<p><h4>¿Quiénes Viajan?</h4></p>
				</div>
				<div class="row">
						<div class="col" style="padding-left:0px;">
							<label>Adultos</label><br>
							<input name="adultosinput" id="adultosinput" type="number" class="form-control" placeholder="1">
						</div>
						<div class="col">
							<label>Niños</label><br>
							<input name="ninosinput" id="ninosinput" type="number" class="form-control" placeholder="0">
						</div>
					</div>
			</div>
		</div>
		</div>
		</div>
		<div class="container">
		<div class="row">
			<div class="col-12">
				<p><h4>Datos Personales</h4></p>
			</div>
	  </div>
		<div class="row">
			<div class="col-4">
				<label>Nombre</label><br>
				<input name="nombreinput" id="nombreinput" type="text" class="form-control" placeholder="Juan Díaz">
			</div>
			<div class="col-4">
				<label>Teléfono</label><br>
				<input name="telefinput" id="telefinput" type="text" class="form-control" placeholder="+574125211">
			</div>
			<div class="col-4">
				<label>Email</label><br>
				<input name="emaildest" id="emaildest" type="text" class="form-control" placeholder="abc@hello.com">
			</div>
			<div class="col-4">
				<label></label><br>
				<input type="hidden" id="action" name="action" value="sendform_dvnv" style="display: none; visibility: hidden; opacity: 0;">
				<button id="enviarbtn" type="submit" class="thm-btn tour-search-one__btn" style="width:150px;">COTIZAR</button>
			</div>
		</div>
	</div>
	</div>
	<div id="successalert" class="alert alert-success" role="alert">
	  Se ha enviado exitosamente su solicitud.
	</div>
	<div id="dangeralert" class="alert alert-danger" role="alert">
		Ha ocurrido un error al enviar
	</div>
	</form>
</section>
<script>
	jQuery("#body").ready(function(){
		jQuery("#my_travelselector").show();
		jQuery("#successalert").hide();
		jQuery("#dangeralert").hide();
	});

	jQuery( "form[name=\"solicotiz\"]" ).on( "submit", function(e) {
        e.preventDefault();
        var form_data = jQuery(this).serializeArray();
        form_data.push({"name" : "security", "value" : ajax_nonce });
        console.log("CONSOLE DATA");
        console.log(form_data);
        console.log("----------CONSOLE DATA");
        // Here is the ajax petition
        jQuery.ajax({
            url : ajax_url,
            type : "POST",
            data : form_data,
            success : function( response ) {
                // You can craft something here to handle the message return
				jQuery("#successalert").show();
            },
            fail : function( err ) {
				jQuery("#dangeralert").show();
            }
        });
        // This return prevents the submit event to refresh the page.
        return false;
	});
</script>';
		return $template;
	}
	add_shortcode( "travel_selector", "import_travelselector");
 
/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_travelselector() {

	$plugin = new travelselector();
	$plugin->run();

}
run_travelselector();
