<?php
/*
  Plugin Name: Restricción de Contenido
  Plugin URI: http://ajamba.org 
  Description: Restringe el contenido en base a la fecha de la compra de la membresia.
  Version: 1.0
  Author: Ing. Edward Avalos
  Author URI: https://www.linkedin.com/in/edward-avalos-severiche/

 */ 


global $wpdb;

/*tablas*/
$charset_collate = $wpdb->get_charset_collate();

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

$sql = "CREATE TABLE IF NOT EXISTS pages_restrict (

  	`cod_page` int(11) NOT NULL AUTO_INCREMENT,
	`id_page` text NOT NULL,

  PRIMARY KEY  (cod_page)

) $charset_collate;";
dbDelta( $sql );
/*tablas*/


/*tablas*/
$charset_collate = $wpdb->get_charset_collate();

require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

$sql = "CREATE TABLE IF NOT EXISTS cod_restrict (

  	`cod_id` int(11) NOT NULL AUTO_INCREMENT,
	`code` text NOT NULL,

  PRIMARY KEY  (cod_id)

) $charset_collate;";
dbDelta( $sql );
/*tablas*/


function prefix_append_support_and_faq_links( $links_array, $plugin_file_name, $plugin_data, $status ) {
	if ( strpos( $plugin_file_name, basename(__FILE__) ) ) {

		// You can still use `array_unshift()` to add links at the beginning.
		$links_array[] = '<a href="https://wa.me/59161781119" target="_blank"><span class="dashicons dashicons-whatsapp"></span> Enviame un Mensaje</a>';
		$links_array[] = '<a href="https://www.facebook.com/ajamba.web.1" target="_blank"><span class="dashicons dashicons-facebook"></span> Visita mi Facebook</a>';
	}
 
	return $links_array;
}

add_filter( 'plugin_row_meta', 'prefix_append_support_and_faq_links', 10, 4 );

/*agrear css y js al admin del plugin*/
add_action('admin_head', 'css_ajamba_admin_logo');
function css_ajamba_admin_logo() {
    ?>
<style>
li#toplevel_page_restrict-pages .wp-menu-image::before {
    content: ' ';
    background-image: url(<?php echo plugins_url( basename( __DIR__ ) . '/img/ajamba.jpg' ); ?>);
    background-clip: content-box;
    background-repeat: no-repeat;
    background-position: center center;
    background-size: 25px;
    width: 25px;
    height: 25px;
    margin-top: 5px;
    padding: 0;
    border-radius: 50%;
}       
	
	
	
	
</style>
    <?php
}

/*agrear css y js al admin del plugin*/ 

/*******coloca un menu al admin*********/ 
add_action('admin_menu', 'config_fechas');

function config_fechas() {
    add_menu_page('config_fechas', //page title
            'Restricción Paginas', //menu title
            'manage_options', //capabilities
            'restrict-pages', //menu slug
            'configuracion' //function
    );
}
/*******coloca un menu al admin*********/

/*******rutas de archivos*********/ 
define('ROOTDIR_DP_AJA', plugin_dir_path(__FILE__)); 
require_once(ROOTDIR_DP_AJA . 'configuracion.php');
/*******rutas de archivos*********/


function agregar_estilo_font_awesome() {
    wp_enqueue_style('font-awesome-1', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css', array(), '6.1.1');
}

add_action('wp_enqueue_scripts', 'agregar_estilo_font_awesome');



/*================================================================*/

function add_stylesheet_to_head() {

?>
<style>
.contenido-restringido p {
    font-size: 20px;
    line-height: normal;
}
.contenido-restringido h2 {
    margin-bottom: 15px;
}
.contenido-restringido img {
    margin-bottom: 25px;
}
.contenido-restringido {
    padding: 100px;
    text-align: center;
}

</style>

<?php

}
 
add_action( 'wp_head', 'add_stylesheet_to_head' );

add_action('admin_footer', 'js_ajamba');
function js_ajamba() {
?>

<script>
    jQuery(document).ready(function($) {
        // Encuentra el elemento con ID #diasMembresia
        var $diasMembresia = $('#diasMembresia');
        
        // Encuentra el div con ID #general-information
        var $generalInformation = $('#general-information');

        // Mueve el elemento #diasMembresia al final del div #general-information
        $diasMembresia.appendTo($generalInformation);
    });
</script>

<?php
}

function agregar_campo_dias_a_informacion_general_membresia($level) {
    // Recupera el valor actual de "dias_de_membresia" desde los metadatos de la membresía
    $dias_de_membresia = get_post_meta($level->id, $level->id.'_dias_de_membresia', true); ?>

    <div id="diasMembresia" class="pmpro_section_inside">
        <table class="form-table">
            <tr>
                <th scope="row">Días Restricción</th>
                <td>
                    <input type="number" name="dias_de_membresia" id="dias_de_membresia" value="<?php echo esc_attr($dias_de_membresia); ?>">
                </td>
            </tr>
        </table>
    </div>
<?php
}

function guardar_campo_dias_de_membresia($level_id) {
    if (isset($_POST['dias_de_membresia'])) {
        $dias_de_membresia = sanitize_text_field($_POST['dias_de_membresia']);
        update_post_meta($level_id, $level_id.'_dias_de_membresia', $dias_de_membresia);
    }
}

add_action('pmpro_membership_level_after_other_settings', 'agregar_campo_dias_a_informacion_general_membresia');
add_action('pmpro_save_membership_level', 'guardar_campo_dias_de_membresia');

/*********************************************************************************/

///json_encode($fechas_desactivadas)


function verificar_acceso_a_contenido_restringido() {
	global $woocommerce, $wp_roles, $post, $wpdb,$wp_query;
	
    // Verificar si el usuario está conectado
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();

        // Obtener la membresía actual del usuario
        $membresia_actual = pmpro_getMembershipLevelForUser($user_id);
		
		//echo "<pre>";
		//print_r($membresia_actual);
		//echo "</pre>";
        if ($membresia_actual) {
            // Obtener el valor del meta 'dias_de_membresia' de la membresía actual
           $dias_de_membresia = get_post_meta($membresia_actual->id, $membresia_actual->id.'_dias_de_membresia', true);
			
			//echo "<br>--".$dias_de_membresia."--";
			
			$fecha_inicio = date('Y-m-d', pmpro_getMemberStartdate());
			
			$MembershipLevelForUser = pmpro_getMembershipLevelForUser($user_id);
			
			
			$startdate = date('Y-m-d', $membresia_actual->startdate) . '<br>';
			
			if(!empty($membresia_actual->enddate)){
				$enddate = date('Y-m-d', $membresia_actual->enddate) . '<br>';
				
			}else{
				//echo 'sin fecha de caducidad';
			}	

			//echo "<hr>";
			
			if($dias_de_membresia === ''){
				
				//echo "<br>--dias membresia configurado en blanco";
				/*si esta sin valor*
				if(!empty($dias_de_membresia) or $dias_de_membresia!==''){
					echo "existe valor".$dias_de_membresia;
				}else{
					echo "<br>--no hay";
					return;
				}
				/*si esta sin valor*/
				
				return;
				
				
			
			
			/*************========VALOR 0=========**************/
            }elseif ($dias_de_membresia === '0') {
				
				//echo "<br>valor 0";
				
                // Obtener la fecha en que el usuario compró la membresía en formato UNIX timestamp
                $fecha_compra_timestamp = pmpro_getMemberStartdate($user_id);

                // Convertir la fecha de compra a 'Y-m-d'
                //echo "<br> fecha_compra: ".$fecha_compra = date('Y-m-d', $fecha_compra_timestamp);
				
				$fecha_compra = date('Y-m-d', $fecha_compra_timestamp);

                // Obtener la fecha de publicación del contenido actual (post o custom post type)
                //echo "<br> fecha_publicacion: ".$fecha_publicacion = get_the_date('Y-m-d');
				$fecha_publicacion = get_the_date('Y-m-d');
				
				$tipo_de_contenido = get_post_type();
				
				$post_type_archive = 1;
				if (is_post_type_archive()) {
					$post_type_archive = 0;
				}
				
				
		
				if(
					$tipo_de_contenido==='page' or 
					$tipo_de_contenido==='cartflows_step' or
					$tipo_de_contenido==='product' or
					$tipo_de_contenido==='sfwd-courses' or
					$post_type_archive === 0
				){
					
					$all_fechas = $wpdb->get_results("SELECT cod_page, id_page 
										FROM pages_restrict");
					$restrict=0;
					$current_page_id = get_the_ID();
					if($all_fechas){
						foreach ($all_fechas as $date){
							$id_page = $date->id_page;
							if($id_page==$current_page_id){
								//echo "<br>Restringida";
								$restrict = 1;
							}
						}
								
					}
					
					if($restrict===0){
						//echo "<br>no esta restringida";
						return;
					}
					
					
					
					
				}elseif($fecha_publicacion >= $fecha_compra) {
						
					return;
					
				}
				
			/*************========VALOR 0=========**************/
				
			/*************========VALOR MENOR A 0=========**************/	
            }elseif($dias_de_membresia < 0){
				
				
				
				//echo "<br>Valor Negativo";
				
				
				$fecha_compra_timestamp = pmpro_getMemberStartdate($user_id);

                // Convertir la fecha de compra a 'Y-m-d'
                $fecha_compra = date('Y-m-d', $fecha_compra_timestamp);

                // Obtener la fecha de publicación del contenido actual (post o custom post type)
                //echo "<br> fecha_publicacion: ".$fecha_publicacion = get_the_date('Y-m-d');
				$fecha_publicacion = get_the_date('Y-m-d');
				
				
				
				
$restar = intval($dias_de_membresia); // Convertir el string en un entero

// Convertir la fecha de publicación en un objeto DateTime
$fecha_compra_obj = new DateTime($fecha_compra);

// Restar días a la fecha de publicación
$fecha_resta_obj = $fecha_compra_obj->modify("$restar days");

// Obtener la fecha restada en formato 'Y-m-d'
$fecha_compra_resta = $fecha_resta_obj->format('Y-m-d');

// Imprimir la fecha restada
//echo '<br>Fecha de compra: ' . $fecha_compra_resta;	
$fecha_compra_resta;					
				
				
				$tipo_de_contenido = get_post_type();
				
				$post_type_archive = 1;
				if (is_post_type_archive()) {
					$post_type_archive = 0;
				}
		
				if(
					$tipo_de_contenido==='page' or 
					$tipo_de_contenido==='cartflows_step' or
					$tipo_de_contenido==='product' or
					$tipo_de_contenido==='sfwd-courses' or
					$post_type_archive === 0
				){
					
					$all_fechas = $wpdb->get_results("SELECT cod_page, id_page 
										FROM pages_restrict");
					$restrict=0;
					$current_page_id = get_the_ID();
					if($all_fechas){
						foreach ($all_fechas as $date){
							$id_page = $date->id_page;
							if($id_page==$current_page_id){
								//echo "<br>Restringida";
								$restrict = 1;
							}
						}
								
					}
					
					if($restrict===0){
						//echo "<br>no esta restringida";
						return;
					}
					
					
					
					
				}elseif($fecha_publicacion >= $fecha_compra_resta) {
						
					return;
					
				}
				
			/*************========VALOR MENOR A 0=========**************/	
				
				
			/*************========VALOR MAYOR A 0=========**************/		
			}elseif($dias_de_membresia > 0){
				
				//echo "<br>Valor Positivo";
				
				
				$fecha_compra_timestamp = pmpro_getMemberStartdate($user_id);

                // Convertir la fecha de compra a 'Y-m-d'
                //echo "<br> fecha_compra: ".$fecha_compra = date('Y-m-d', $fecha_compra_timestamp);
				
				$fecha_compra = date('Y-m-d', $fecha_compra_timestamp);

                // Obtener la fecha de publicación del contenido actual (post o custom post type)
                $fecha_publicacion = get_the_date('Y-m-d');
				
				
				//echo "<hr>";
				
$restar = intval($dias_de_membresia); // Convertir el string en un entero

// Convertir la fecha de publicación en un objeto DateTime
$fecha_compra_obj = new DateTime($fecha_compra);

// Restar días a la fecha de publicación
$fecha_resta_obj = $fecha_compra_obj->modify("$restar days");

// Obtener la fecha restada en formato 'Y-m-d'
$fecha_compra_resta = $fecha_resta_obj->format('Y-m-d');

// Imprimir la fecha restada
//echo '<br>Fecha de compra: ' . $fecha_compra_resta;
$fecha_compra_resta;				
				
				$tipo_de_contenido = get_post_type();
				
				$post_type_archive = 1;
				if (is_post_type_archive()) {
					$post_type_archive = 0;
				}
		
				if(
					$tipo_de_contenido==='page' or 
					$tipo_de_contenido==='cartflows_step' or
					$tipo_de_contenido==='product' or
					$tipo_de_contenido==='sfwd-courses' or
					$post_type_archive === 0
				){
					
					$all_fechas = $wpdb->get_results("SELECT cod_page, id_page 
										FROM pages_restrict");
					$restrict=0;
					$current_page_id = get_the_ID();
					if($all_fechas){
						foreach ($all_fechas as $date){
							$id_page = $date->id_page;
							if($id_page==$current_page_id){
								//echo "<br>Restringida";
								$restrict = 1;
							}
						}
								
					}
					
					if($restrict===0){
						//echo "<br>no esta restringida";
						return;
					}
					
					
					
					
				}elseif($fecha_publicacion >= $fecha_compra_resta) {
						
					return;
					
				}
				
			}
			
		/************======FIN CON MEMBRESIA=====**************/	
			
        }else{
			
			
			/************=====SIN MEMBRESIA======**************/
			echo "<br>Sin membresia";	
			
			return;
			
			/************=====SIN MEMBRESIA======**************/
			
			
			
		}
    }
	
	get_header();
	
	$all_code = $wpdb->get_results("SELECT cod_id, code 
										FROM cod_restrict
										
										WHERE cod_id = '1'
										");
	
	$cod_html="";
	if($all_code){

		foreach ($all_code as $code){
			$cod_html = $code->code;
			$cod_html = html_entity_decode($cod_html);
		}
		
		echo html_entity_decode($cod_html);
		
		
	}else{
		
		// Si el usuario no cumple con las restricciones, mostrar mensaje de acceso restringido
		echo '<div class="aaa contenido-restringido">';
		echo '<img src="'.plugins_url( basename( __DIR__ ) . '/img/logo-plugin.png' ).'" alt="Logo de la web">';
		echo '<h2><i class="fas fa-user-lock"></i> Contenido Restringido</h2>';
		echo '<p>Esta restringido debido a la configuración de su membresia Comprada o no tiene Membresia Comprada!</p>';
		echo '</div>';
		
	}

    
	
	get_footer();
	
    exit; // Detener la ejecución del script
}

add_action('template_redirect', 'verificar_acceso_a_contenido_restringido');









