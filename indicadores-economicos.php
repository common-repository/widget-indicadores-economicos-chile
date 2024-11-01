<?php
/*
Plugin Name: Indicadores Económicos (Chile)
Plugin URI: http://wordpress.org/plugins/widget-indicadores-economicos-chile/
Description: Muestra los principales indicadores económicos para Chile.
Version: 2.5
Author: Cristhopher Riquelme
Author URI: mailto:cristriq@gmail.com
License: GPL2
*/

global $table_db_version;
$table_db_version = "2.2";

function table_install() {
	global $wpdb;
	global $table_db_version;
	$table_name = $wpdb->prefix . "indica_econo";

	$sql = "CREATE TABLE $table_name (
			id int(5) NOT NULL AUTO_INCREMENT,
			cod_indi varchar(20) DEFAULT '',
			nom_indi varchar(50) DEFAULT '',
			val_indi decimal(12,2) DEFAULT 0,
			unidad_medida varchar(5) DEFAULT '',
			fecha_indi datetime DEFAULT '0000-00-00 00:00:00',
			UNIQUE KEY id (id)
			);";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );

	$class = new Indicadores_Widget();
	$class->actualizar_indicadores(); //$class->comprobar_ultima_actualizacion();

	add_option( "table_db_version", $table_db_version );
}
register_activation_hook( __FILE__, 'table_install' );

function table_uninstall() {
	global $wpdb;
	$table_name = $wpdb->prefix . "indica_econo";

	$sql = "DROP TABLE $table_name";
	$wpdb->query($sql);
}
register_deactivation_hook( __FILE__, 'table_uninstall' );

function myplugin_update_db_check() {
    global $table_db_version;
    if (get_site_option( 'table_db_version' ) != $table_db_version) {
        table_install();
        update_site_option( 'table_db_version', $table_db_version );
    }
}
add_action( 'plugins_loaded', 'myplugin_update_db_check' );

function carga_estilos_plugin() {
	wp_register_style('estilos_indicadores',
						plugins_url( 'css/style-indicadores.css' , __FILE__ ),
						array(),
						'1.0',
						'all');
	wp_enqueue_style('estilos_indicadores');
}
add_action('wp_print_styles', 'carga_estilos_plugin');

/*--------------------------------------------------------------------------------------------*/

function widget_register_indicadores_economicos() {
    register_widget('Indicadores_Widget');
}
add_action('widgets_init', 'widget_register_indicadores_economicos');

class Indicadores_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'indicadores-economicos-cl', //ID
			'Indicadores Económicos (Chile)', //Nombre
			array(
				'classname' => 'widget_indicadores_economicos',
				'description' => 'Mostrar los principales indicadores económicos para Chile'
				) //Descripción
		);
	}

	public function widget( $args, $instance ) {
		$this->comprobar_ultima_actualizacion();
		$this->mostrar_indicadores($instance);
	}

	public function form( $instance ) {
		global $wpdb;

		// Obligamos a $instance a ser un array con todas las opciones disponibles
		$instance = wp_parse_args( (array)$instance, array(
			'id_contenedor'  => '',
			'separador'      => '',
			'indicadores'    => '',
			'titulo_widget'  => '',
			'aplicar_diseno' => 0
		));

		// Filtramos los valores para que se muestren correctamente en los formularios
		$instance['id_contenedor'] = esc_attr($instance['id_contenedor']);
		$instance['separador'] = esc_attr($instance['separador']);
		$instance['indicadores'] = unserialize($instance['indicadores']);
		$instance['titulo_widget'] = esc_attr($instance['titulo_widget']);
		$instance['aplicar_diseno'] = esc_attr($instance['aplicar_diseno']);
		//echo'<pre>'; print_r($instance); echo'</pre>';

		// Mostramos el formulario
		?>
		<p>
			<label for="<?php echo $this->get_field_id('titulo_widget'); ?>">Título:</label>
			<input value="<?php echo $instance['titulo_widget']; ?>" class="widefat" type="text" id="<?php echo $this->get_field_id('titulo_widget'); ?>" name="<?php echo $this->get_field_name('titulo_widget'); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('id_contenedor'); ?>">ID lista de indicadores (&lt;ul id=""&gt;):</label>
			<input value="<?php echo $instance['id_contenedor']; ?>" class="widefat" type="text" id="<?php echo $this->get_field_id('id_contenedor'); ?>" name="<?php echo $this->get_field_name('id_contenedor'); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('separador'); ?>">Separador de indicadores:</label>
			<input value="<?php echo $instance['separador']; ?>" class="widefat" type="text" id="<?php echo $this->get_field_id('separador'); ?>" name="<?php echo $this->get_field_name('separador'); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('indicadores'); ?>">Indicadores a mostrar:</label><br />
			<?php
			$table_name = $wpdb->prefix . "indica_econo";
			$result = $wpdb->get_results("SELECT id, cod_indi FROM $table_name ORDER BY id ASC");
			foreach ( $result as $i => $row ):
			?>
			<input type="checkbox" name="<?php echo $this->get_field_name('indicadores').'['.$row->id.']'; ?>" id="<?php echo $this->get_field_id('indicadores').$row->id; ?>" value="<?php echo $row->cod_indi; ?>" <?php checked(isset($instance['indicadores'][$row->id]) ? 1 : 0); ?> /><span> <?php echo $row->cod_indi; ?></span><br />
			<?php
			endforeach;
			?>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('aplicar_diseno'); ?>">¿Diseño por defecto?:</label>
			<input type="checkbox" name="<?php echo $this->get_field_name('aplicar_diseno'); ?>" id="<?php echo $this->get_field_id('aplicar_diseno'); ?>" value="1" <?php echo ($instance['aplicar_diseno'] == 1) ? 'checked="checked"' : ''; ?> />
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		return array(
			'id_contenedor' => strip_tags($new_instance['id_contenedor']),
			'separador'     => strip_tags($new_instance['separador']),
			'indicadores'   => serialize($new_instance['indicadores']),
			'titulo_widget' => strip_tags($new_instance['titulo_widget']),
			'aplicar_diseno' => strip_tags($new_instance['aplicar_diseno'])
		);
	}

	public function mostrar_indicadores( $instance ) {
		global $wpdb;
		$table_name = $wpdb->prefix . "indica_econo";
		$sql_indicadores = '';
		$total_registros = 0;

		$instance['indicadores'] = unserialize($instance['indicadores']);
		if ( !empty($instance['indicadores']) ) {
			$sql_indicadores = "'".implode("','", $instance['indicadores'])."'";
		}

		$result = $wpdb->get_results("SELECT cod_indi, nom_indi, val_indi, unidad_medida FROM $table_name WHERE cod_indi IN({$sql_indicadores}) ORDER BY id ASC");
		$total_registros = $wpdb->num_rows;
		?>
		<div id="widget-indicadores-economicos-chile" <?php echo ( $instance['aplicar_diseno'] == 1 ) ? 'class="default-style"' : ''; ?>>
			<?php if ( !empty($instance['titulo_widget']) ) echo '<h3>'.$instance['titulo_widget'].'</h3>'; ?>
			<span class="fecha-hoy"><?php echo $this->fecha_actual(); ?></span>
			<ul <?php echo ( !empty($instance['id_contenedor']) ) ? 'id="'.$instance['id_contenedor'].'"' : '' ; ?>>
			<?php
			$i = 1;
			foreach ( $result as $row ) {
				if ($row->unidad_medida == '%') {
					echo '<li><span class="indicador" title="' . $row->nom_indi . '">' . $row->cod_indi . ':</span> ' . number_format($row->val_indi, 2, ',', '.') . '%</li>';
				} elseif ($row->cod_indi == 'Libra de Cobre') {
					echo '<li><span class="indicador" title="' . $row->nom_indi . '">' . $row->cod_indi . ':</span> ' . number_format($row->val_indi, 2, ',', '.') . '</li>';
				} else {
					echo '<li><span class="indicador" title="' . $row->nom_indi . '">' . $row->cod_indi . ':</span> ' . $row->unidad_medida . number_format($row->val_indi, 2, ',', '.') . '</li>';
				}
				if ( $instance['separador'] != '' && $i != $total_registros ) {
					echo '<li class="separator">'.$instance['separador'].'</li>';
				}
				$i++;
			}
			?>
			</ul>
		</div>
		<?php
	}

	public function fecha_actual() {
		$dias = Array("Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado");
		$meses = Array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");

		$dia_semana = date("w");
		$dia = date("j");
		$mes = date("n") - 1;
		$anio = date("Y");

		return $dias[$dia_semana] . " " . $dia . " de " . $meses[$mes] . " de " . $anio;
	}

	public function comprobar_ultima_actualizacion() {
		global $wpdb;
		$table_name = $wpdb->prefix . "indica_econo";

		$result = $wpdb->get_row("SELECT count(id) as total_registros FROM $table_name WHERE ADDTIME(fecha_indi, '02:00:00') > '".current_time('mysql')."'");
		
		if ( $result->total_registros == 0 ) {
			$this->actualizar_indicadores();
		}
	}

	public function actualizar_indicadores() {
		global $wpdb;

		$indicadores = array();
		$fecha_hoy = current_time('mysql'); //date("Y-m-d H:i:s");
        $apiUrl = 'http://www.mindicador.cl/api';

        // Es necesario tener habilitada la directiva allow_url_fopen para usar file_get_contents
        if ( ini_get('allow_url_fopen') ) {
            $json = file_get_contents($apiUrl);
        } else {
            // De otra forma utilizamos cURL
            $curl = curl_init($apiUrl);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $json = curl_exec($curl);
            curl_close($curl);
        }

        if ( !empty($json) ) {
            $dailyIndicators = json_decode($json);
            //echo '<pre>'; print_r($dailyIndicators); echo '</pre>';

            $indicadores = array(
                array(
                    'codigo' => 'UF',
                    'nombre' => $dailyIndicators->uf->nombre,
                    'valor' => $dailyIndicators->uf->valor,
                    'unidad_medida' => $dailyIndicators->uf->unidad_medida
                ),
                array(
                    'codigo' => 'Dólar',
                    'nombre' => $dailyIndicators->dolar->nombre,
                    'valor' => $dailyIndicators->dolar->valor,
                    'unidad_medida' => $dailyIndicators->dolar->unidad_medida
                ),
                array(
                    'codigo' => 'Euro',
                    'nombre' => $dailyIndicators->euro->nombre,
                    'valor' => $dailyIndicators->euro->valor,
                    'unidad_medida' => $dailyIndicators->euro->unidad_medida
                ),
                array(
                    'codigo' => 'IPC',
                    'nombre' => $dailyIndicators->ipc->nombre,
                    'valor' => $dailyIndicators->ipc->valor,
                    'unidad_medida' => $dailyIndicators->ipc->unidad_medida
                ),
                array(
                    'codigo' => 'UTM',
                    'nombre' => $dailyIndicators->utm->nombre,
                    'valor' => $dailyIndicators->utm->valor,
                    'unidad_medida' => $dailyIndicators->utm->unidad_medida
                ),
                array(
                    'codigo' => 'IVP',
                    'nombre' => $dailyIndicators->ivp->nombre,
                    'valor' => $dailyIndicators->ivp->valor,
                    'unidad_medida' => $dailyIndicators->ivp->unidad_medida
                ),
                array(
                    'codigo' => 'Imacec',
                    'nombre' => $dailyIndicators->imacec->nombre,
                    'valor' => $dailyIndicators->imacec->valor,
                    'unidad_medida' => $dailyIndicators->imacec->unidad_medida
                ),
                array(
                    'codigo' => 'TPM',
                    'nombre' => $dailyIndicators->tpm->nombre,
                    'valor' => $dailyIndicators->tpm->valor,
                    'unidad_medida' => $dailyIndicators->tpm->unidad_medida
                ),
                array(
                    'codigo' => 'Libra de Cobre',
                    'nombre' => $dailyIndicators->libra_cobre->nombre,
                    'valor' => $dailyIndicators->libra_cobre->valor,
                    'unidad_medida' => $dailyIndicators->libra_cobre->unidad_medida
                ),
                array(
                    'codigo' => 'Tasa de desempleo',
                    'nombre' => $dailyIndicators->tasa_desempleo->nombre,
                    'valor' => $dailyIndicators->tasa_desempleo->valor,
                    'unidad_medida' => $dailyIndicators->tasa_desempleo->unidad_medida
                )
            );
            //echo '<pre>'; print_r($indicadores); echo '</pre>';
        }

		if ( !empty($indicadores) ) {
			$table_name = $wpdb->prefix . "indica_econo";

			$wpdb->query("TRUNCATE TABLE $table_name"); //$wpdb->query("DELETE FROM $table_name");

			foreach ( $indicadores as $indicador ):
				if ($indicador['unidad_medida'] == 'Porcentaje') {
					$indicador['unidad_medida'] = '%';
				} else {
					$indicador['unidad_medida'] = '$';
				}
				$result = $this->join($indicador['codigo'], $indicador['nombre'], $indicador['valor'], $indicador['unidad_medida'], $fecha_hoy);
			endforeach;
		}
	}

	public function join($cod_indicador, $nom_indicador, $val_indicador, $unidad_medida, $fecha_indicador) {
		global $wpdb;
		$table_name = $wpdb->prefix . "indica_econo";

		//$result = $wpdb->query("INSERT INTO $table_name (cod_indi, nom_indi, val_indi, unidad_medida, fecha_indi) VALUES ('{$cod_indicador}', '{$nom_indicador}', {$val_indicador}, '{$unidad_medida}', {$fecha_indicador})");
        //metodo que se encarga de insertar datos en la base de datos.
        $result = $wpdb->insert($table_name, 
        	array(
                'cod_indi' => $cod_indicador,
                'nom_indi' => $nom_indicador,
                'val_indi' => $val_indicador,
                'unidad_medida' => $unidad_medida,
                'fecha_indi' => $fecha_indicador
            )
        );
		return $result;
	}

}