<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.wpplanet.com.br
 * @since             1.0.0
 * @package           Whatwpp
 *
 * @wordpress-plugin
 * Plugin Name:       whatsapp msg
 * Plugin URI:        https://www.wpplanet.com.br
 * Description:       WhatsApp Help Chat para WordPress permite aos seus clientes abrir uma conversa do seu site diretamente para o seu número de telefone WhatsApp. 
 * Version:           1.0.0
 * Author:            WP Planet
 * Author URI:        https://www.wpplanet.com.br
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       whatwpp
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
define( 'WHATWPP_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-whatwpp-activator.php
 */

global $jal_db_version;
$jal_db_version = '1.0';

function jal_install() {
	global $wpdb;
	global $jal_db_version;

	$table_name = $wpdb->prefix . 'whatsapp_popup';
	
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		phone varchar(20) NOT NULL,
		title text NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	add_option( 'jal_db_version', $jal_db_version );
}

function jal_install_data() {
	global $wpdb;
	
	$phone = '';
	$title = 'Congratulations, you just completed the installation!';
	
	$table_name = $wpdb->prefix . 'whatsapp_popup';
	
	$wpdb->insert( 
		$table_name, 
		array( 
			'phone' => $phone, 
			'title' => $title, 
		) 
	);
}


register_activation_hook( __FILE__, 'jal_install' );
register_activation_hook( __FILE__, 'jal_install_data' );


function activate_whatwpp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-whatwpp-activator.php';
	Whatwpp_Activator::activate();
}

function whatsapp_popup() {

 wp_enqueue_script( 'whatsapp_popup', plugin_dir_url( __FILE__ ) . 'public/js/whatwpp-public.js', '', true );
 wp_enqueue_style( 'whatsapp_popup' );
}
add_action( 'wp_enqueue_scripts', 'whatsapp_popup' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-whatwpp-deactivator.php
 */
function deactivate_whatwpp() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-whatwpp-deactivator.php';
	Whatwpp_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_whatwpp' );
register_deactivation_hook( __FILE__, 'deactivate_whatwpp' );


function wpdocs_register_my_custom_menu_page(){
	$img = plugin_dir_url( __FILE__ )."admin/img/icon_menu.png";
    add_menu_page( 
        __( 'Configurações do plugin WhatsApp', 'textdomain' ),
        'WhatsApp',
        'manage_options',
        'page_optw',
        'my_custom_menu_page',
        $img,
        220
    ); 
}
add_action( 'admin_menu', 'wpdocs_register_my_custom_menu_page' );


function the_form_response() {	
	global $wpdb;
    $table_name = $wpdb->prefix .'whatsapp_popup';

	if (!empty($_POST["id_wtp"])) {
        // Upadate data
        $wpdb->update($table_name, array(
                'phone' => $_POST["wt_phone_num"],
                'title' => $_POST["wt_tilte_pop"]
            ), array(
                'ID' => $_POST["id_wtp"]
            )
        );
    } else {
        // Insert data
        $wpdb->insert($table_name, array(
            'phone' => $_POST["wt_phone_num"],
			'title' => $_POST["wt_tilte_pop"]
            )
        );
    }

	//global $wpdb;
	//$wpdb->update($wpdb->prefix . 'whatsapp_popup', array('phone'=>'55555', 'title'=>'aaaa'));
}

add_action( 'admin_post_nds_form_response', 'the_form_response');

 
/**
 * Display a custom menu page
 */
function my_custom_menu_page(){
   // esc_html_e( 'Admin Page Test', 'textdomain' );  
   require plugin_dir_path( __FILE__ ) . 'admin/partials/whatwpp-admin-display.php';
}

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-whatwpp.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_whatwpp() {

	$plugin = new Whatwpp();
	$plugin->run();
	
}

add_action( 'wp_footer', 'my_footer_scripts' );

function my_footer_scripts(){

global $wpdb;
$table_name = $wpdb->prefix . "whatsapp_popup";
$sql = $wpdb->prepare("SELECT * FROM $table_name");
$results = $wpdb->get_results($sql);
$results = $results[0];

  ?>

  <div class="nav-bottom">
            <div class="popup-whatsapp fadeIn is-active-whatsapp-popup">
                <div class="content-whatsapp -top">
					<button type="button" class="closePopup">
						<b>X</b>
                    </button>
					<?php if($results->phone>0){ ?>
                    	<p style="font-size: 16px;"><?php echo $results->title; ?></p>
					<?php }else{ ?>
						<p style="font-size: 14px;">Seu WatsApp ainda não foi configurado, configure <a href="<?php echo get_site_url(); ?>/wp-admin?page=page_optw">Aqui</a></p>
					<?php } ?>
                </div>
				<?php if($results->phone>0){ ?>
                <div class="content-whatsapp -bottom">
                  <input class="whats-input" id="whats-in" type="text" placeholder="Digite uma mensagem...">
				  <input class="whats-input-phone" id="whats-in-phone" value="<?php echo $results->phone; ?>" type="hidden">
				  
                    <button class="send-msPopup" id="send-btn" type="button">
					<img class="icon-whatsapp" src="<?php echo plugin_dir_url( __FILE__ );?>public/img/bt-send.png">
                    </button>

                </div>
				<?php } ?>
            </div>
            <button type="button" id="whats-openPopup" class="whatsapp-button">
                <img class="icon-whatsapp" src="<?php echo plugin_dir_url( __FILE__ );?>public/img/bt-whats.svg">
            </button>
            <div class="circle-anime"></div>
		</div>
<script>
popupWhatsApp = () => {
  
	let btnClosePopup = document.querySelector('.closePopup');
	let btnOpenPopup = document.querySelector('.whatsapp-button');
	let popup = document.querySelector('.popup-whatsapp');
	let sendBtn = document.getElementById('send-btn');
  
	btnClosePopup.addEventListener("click",  () => {
	  popup.classList.toggle('is-active-whatsapp-popup')
	})
	
	btnOpenPopup.addEventListener("click",  () => {
	  popup.classList.toggle('is-active-whatsapp-popup')
	   popup.style.animation = "fadeIn .3s 0.0s both";
	})
	
	sendBtn.addEventListener("click", () => {
	let msg = document.getElementById('whats-in').value;
	let wtp_phone = document.getElementById('whats-in-phone').value;
	let relmsg = msg.replace(/ /g,"%20");
	  //just change the numbers "1515551234567" for your number. Don't use +001-(555)1234567     
	 window.open('https://wa.me/'+wtp_phone+'?text='+relmsg, '_blank'); 
	
	});
  
	setTimeout(() => {
	  popup.classList.toggle('is-active-whatsapp-popup');
	}, 3000);

	gtag_report_conversion("https://www.joelmachado.com.br");
	
  }
popupWhatsApp();
</script>

  <?php
}
run_whatwpp();
