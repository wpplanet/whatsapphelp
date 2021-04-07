<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.wpplanet.com.br
 * @since      1.0.0
 *
 * @package    Whatwpp
 * @subpackage Whatwpp/admin/partials
 */
?>
<?php

/**
 * The form to be loaded on the plugin's admin page
 */
if( current_user_can( 'edit_pages' ) ) {

global $wpdb;
$table_name = $wpdb->prefix . "whatsapp_popup";
$sql = $wpdb->prepare("SELECT * FROM $table_name");
$results = $wpdb->get_results($sql);

$results = $results[0];

?>				
<h2><?php _e( 'Configurações do plugin WhatsApp'); ?></h2>


		
<div class="nds_add_user_meta_form" style="width: 40%; float: left;">

<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" id="wtp_ajax_form" >			
    <input type="hidden" name="action" value="nds_form_response">
    <input type="hidden" name="id_wtp" value="<?php echo $results->id; ?>">
    <div>
        <br>
        <label> <?php _e('Número de telefone (WhatsApp)'); ?> </label><br>
        <input required id="wt_phone_num" type="text" name="wt_phone_num" value="<?php echo $results->phone; ?>" placeholder="WhatsApp" style="width: 250px;" /><br>
    </div>

    <div>
        <label> <?php _e('Titulo do PopUp'); ?> </label><br>
        <textarea required id="wt_tilte_pop" type="text" name="wt_tilte_pop" placeholder="Titulo" style="width: 250px;"><?php echo $results->title; ?></textarea>
    </div>        
    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Salvar configurações"><div id="nds_form_feedback" style="color: green;"></div></p>
</form>
		
</div>
<div style="width: 40%;  float: left;background: #fff;padding: 15px;border: 1px solid blueviolet;">
<img src="https://avatars.githubusercontent.com/u/82115831?v=4" style="width: 70px;" ><br />
<b>WP Planet </b>
<p>A WP Planet é uma empresa especializada em desenvolvimento e suporte de sites, loja virtual, plugins, temas, WordPress e WooCommerce.</p>
<p><a href="https://www.wpplanet.com.br" target="_blank">wpplanet.com.br</a></p>
</div>
<script>
jQuery( document ).ready( function( $ ) {

"use strict";
/**
 * The file is enqueued from inc/admin/class-admin.php.
*/        
$( '#wtp_ajax_form' ).submit( function( event ) {
    
    event.preventDefault(); // Prevent the default form submit.            
    var ajaxurl = $(this).attr("action");
   
    // serialize the form data
    var ajax_form_data = $(this).serialize();
    
    //add our own ajax check as X-Requested-With is not always reliable
    ajax_form_data = ajax_form_data+'&ajaxrequest=true&submit=Submit+Form';
   
    
    $.ajax({
        url:    ajaxurl, // domain/wp-admin/admin-ajax.php
        type:   'post',                
        data:   ajax_form_data
    }).done( function( response ) { // response from the PHP action
        $("#nds_form_feedback").html( "Atualizado com sucesso.");
    })
    
    // something went wrong  
    .fail( function() {
        $("#nds_form_feedback").html( "<h2>Something went wrong.</h2><br>" );                  
    })

    // after all this time?
    .always( function() {
        //event.target.reset();
    });

});

});
</script>
<?php    
}
else {  
?>
<p> <?php __("You are not authorized to perform this operation.","DDD") ?> </p>
<?php   
}?>

