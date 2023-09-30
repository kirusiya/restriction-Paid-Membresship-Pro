<?php
/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/


function configuracion() {
	//require_once(ABSPATH . 'wp-config.php');
	global $woocommerce, $wp_roles, $post, $wpdb,$wp_query; 
	
	/*crear fechas*/
	$errorCrear = 3;
	if(isset($_POST['id_page']) and $_POST['id_page']!=""){
		
		$id_page = $_POST['id_page'];

		$sql = "
			INSERT INTO  pages_restrict  
			(id_page)

			VALUES  
			('$id_page');	
			";

		$wpdb->query($sql);
		
		$errorCrear = 0;
		
	}
	/*crear fechas*/
	
	
	/*crear fechas*/
	$errorBorrar = 3;
	if(isset($_GET['cod_page']) and $_GET['cod_page']!=""){
		
		$cod_page = $_GET['cod_page'];
		
		$sql = "
			DELETE FROM pages_restrict WHERE  cod_page = '$cod_page'	
			";

		$wpdb->query($sql);
		$errorBorrar = 0;
		
	}
	/*crear fechas*/
	
	$errorCode = 3;
	if(isset($_POST['code']) and $_POST['code']!=""){
		$codeHTML =  htmlspecialchars($_POST['code']); 
		$codeHTML = stripslashes($codeHTML);
		$codeHTML = htmlentities($codeHTML);
		//echo "<br> 4".$codeHTML = html_entity_decode($codeHTML);
		
		
		$all_code = $wpdb->get_results("SELECT cod_id, code 
										FROM cod_restrict
										
										WHERE cod_id = '1'
										");
		
		if($all_code){
		
			$sql = "
					UPDATE cod_restrict 
					SET  		
					code = '$codeHTML'

					WHERE cod_id = '1'	
				";

			$wpdb->query($sql);
			$errorCode = 0;
		}else{
			
			$sql = "
					INSERT INTO  cod_restrict  
					(cod_id, code)

					VALUES  
					('1', '$codeHTML');	
				";

			$wpdb->query($sql);
			$errorCode = 0;
			
		}
		
		
		
		
	}
	
?>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/css/select2.min.css" >

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.13.4/codemirror.css">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.13.4/theme/monokai.css">


<style>
.select2-container .select2-selection--single {
    height: 40px !important;
}
	
.select2-container .select2-selection--single .select2-selection__rendered {
    padding-top: 5px !important;
}
	
.select2-container {
    width: 100% !important;
}
	
.wc-wp-version-gte-53 .select2-container .select2-selection--single .select2-selection__arrow {
    height: 40px;
}	
	
.notice,
.fs-notice,
div.fs-notice.updated, 
div.fs-notice.success, 
div.fs-notice.promotion{
    display: none !important;
}		
	
.flatpickr-input[readonly] {
    background: #fff;
	width: 100%;
}	
	
/* Estilo para la tabla con encabezado oscuro */


/* Estilo para las celdas del encabezado */
.dark-header th {
    background-color: #222; /* Fondo oscuro más oscuro */
    color: #fff; /* Texto blanco */
    border-color: #444; /* Borde más oscuro */
}
	
table.dataTable tbody th, table.dataTable tbody td {
    vertical-align: middle;
}	
	
code {
    display: block;
    padding: 30px;
}	
	
</style>  
        

<div class="wrap">
	
	
	<div class="container mt-5">
		
		
		
		<div class="row">
			
			
			
			
			
			<!-- configurar temporadas -->
			
			<div class="col-md-6 mb-5">
				
				<?php 
				if($errorCrear==0){
				?>
				<div class="alert alert-success mb-3">Página Restringida Correctamente</div>
				<?php 
				}
				?>
				
				<?php $url = admin_url('admin.php?page=restrict-pages');?>
				<h1 class="mb-4"><strong>Restringir Páginas</strong></h1>
				
				<form action="<?php echo $url;?>" method="post" enctype="multipart/form-data">
					
				<div class="row">
					
					
					
					<div class="col-md-6 mb-3">
						
						<div class="form-group">
							<label for="mis-puntos-user-search">
								<strong>Seleccionar Pagina:</strong>
							</label>
							
							
							<?php 
							$paginas = get_pages();
							?>
							
							<select name="id_page" id="id_page" class="form-control" required>
								
								<?php 
								foreach ($paginas as $pagina) {
									echo '<option value="' . $pagina->ID . '">' . esc_html($pagina->post_title) . '</option>';	
									
								}
	
								?>
								
							</select>	
								
						</div>
						
					</div>	
					
					<div class="col-md-12 mb-3">
					
						<input type="hidden" name="crearTemporadas" value="ok">
						<button type="submit"  class="button button-primary">
							Agregar Página
						</button>
					
					</div>
						
						
					
				</div>	
					
				</form>	
			
			
			</div>
			
			
			<!-- configurar temporadas -->
			
			<!-- Tabla de Temporadas -->
			
			<div class="col-md-6 mb-5">
				
				<?php 
				if($errorBorrar==0){
				?>
				<div class="alert alert-success mb-3">Página Eliminada Correctamente</div>
				<?php 
				}
				?>
				
				<h1 class="mb-4"><strong>Páginas Restringida</strong></h1>
			
				<div class="detalle w-100 position-relative table-responsive">
					<table class="table table-striped">
						<thead  class="thead-dark">
							<tr>
								<th class="text-center">#</th>
								<th class="text-center">Página</th>
								<th class="text-center">Acciones</th>
							</tr>
						</thead>

						<tbody>
							
							<?php
							$all_fechas = $wpdb->get_results("SELECT cod_page, id_page 
										FROM pages_restrict  ");
							$cont=0;
							$url = admin_url('admin.php?page=restrict-pages');	
							if($all_fechas){

								foreach ($all_fechas as $date){
									$id_page = $date->id_page;
									$cod_page = $date->cod_page;
									
									$title = get_the_title($id_page); 
									
									$cont++;
									
									?>
									<tr>
										<td class="text-center"><?php echo $cont;?></td>
										<td class="text-center"><?php echo $title;?></td>
										<td class="text-center">
										
											<a href="<?php echo $url;?>&cod_page=<?php echo $cod_page;?>" class="btn btn-danger">
												<i class="fas fa-trash-alt"></i> Borrar
											</a>
											
										</td>
									</tr>	
							
									<?php

								}

							}
	
							?>
							
						 
						</tbody>	

					</table>		

				</div>
				
			</div>	
			
			<!-- Tabla de Temporadas -->
			
<style>
			
</style>			
			<div class="col-md-12 mt-5">
				
				<?php 
				if($errorCode==0){
				?>
				<div class="alert alert-success mb-3">Código Insertado Correctamente</div>
				<?php 
				}
				?>
				
				<h1 class="mb-4"><strong>Codigo HTML para la pantalla de restricción</strong></h1>
				
				<form id="codForm" method="post" enctype="multipart/form-data">
					
					
					<?php 
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
					}
					
					?>
					
					
					<textarea id="codigoHTML" name="codigoHTML"><?php echo $cod_html;?></textarea>


					<div class="form-group mt-4">
						<input type="hidden" id="code" name="code">
						<button type="button" onClick="guardar()" class="btn btn-primary">Guardar</button>

					</div>
					
				</form>	
				
			
			</div>
		</div>	
		
	</div>	
	
</div>

<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
 
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-beta.1/js/select2.full.min.js"></script>
 

<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.13.4/codemirror.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.13.4/mode/xml/xml.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.13.4/mode/css/css.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.13.4/mode/javascript/javascript.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.13.4/mode/htmlmixed/htmlmixed.js"></script>


<script>
	var editor = CodeMirror.fromTextArea(document.getElementById("codigoHTML"), {
		mode: "htmlmixed", // Modo HTML
		lineNumbers: true, // Números de línea
		styleActiveLine: true,
    	matchBrackets: true,
		theme: 'monokai'
	});
	
jQuery( document ).ready( function($) { 
	$( "#id_page" ).select2();
	
	
	
});
	
function guardar(){
	var codigo = editor.getValue();
	
	document.getElementById("code").value = codigo;
    
    // Envía el formulario
    document.getElementById("codForm").submit();
}	

</script>




<?php





 

//fin config 
}