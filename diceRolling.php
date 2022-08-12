<?php
/*
Plugin Name: Lanceur de dés
Description: Lanceur de dés
Version: 0.0.1
Author: Jax
*/


// Sécurité :  Empêche un utilisateur public d'accéder directement au fichier via url :
defined('ABSPATH') or die('Plugin file cannot be accessed directly.'); 

	$result = 3;

if(isset($_POST['dice-select'])){
    add_data();
}


if (!class_exists('diceRolling')) {
	// Création d'une classe lanceur de dés, classe enfant de WP_Widget, la classe des widgets Wordpress
	class diceRolling extends WP_Widget{ 
		function __construct() { // Constructor, fait appel à la classe parent pour définir le widget
			parent::__construct(
				'diceRolling',
				'Lanceur de dés',
				['description' => 'Lanceur de dés']
			);
		}

		function enqueue_js() { // Importation du js
			if (!wp_script_is('diceRoller', 'enqueued')) { // Si le script n'est pas chargé
				wp_register_script( // Enregistre un script pour être utiliser plus tard
					'diceRoller', // Nom du script
				    plugin_dir_url(__FILE__) . 'js/diceRoller.js' // Chemin du ou des scripts
				);
				wp_enqueue_script('diceRoller');// Enregistre le script
			}
		}

		function widget($args, $instance) {  // Définit le front-end 
			?>
			<div class="widget">
				<form method="POST">
			    <p id="placeholder"> 
					
					<?php 
						global $wpdb;
	
						$table_name = $wpdb->prefix . 'dicerolling';
					    $results = $wpdb->get_results( "SELECT results FROM $table_name ORDER BY time"); 
						if(!empty($results)) {
						//	print_r($results);

							echo $results[sizeof($results)-1]->results;
						}
						?> 
				</p>
				<p>Type de dés:<br>
    				<select name="dice-select" id="dice-select">
    				    <option value="1">--Choisissez un dés--</option>
    				    <option value="2">D2</option>
    				    <option value="4">D4</option>
    				    <option value="6">D6</option>
    				    <option value="8">D8</option>
    				    <option value="10">D10</option>
    				    <option value="20">D20</option>
    				    <option value="100">D100</option>
    				</select>
		        </p>
				<p>Nombre de lancers:<br>
    			    <input id="roll-step" name="roll-step" min="1" max="100" type="number" value="1"/>
				</p>

				<input type="submit" id="roll-button" name="roll-button" />
				</form>
		    </div>

            <?php

      		$this->enqueue_js(); 
//			require_once('form/diceRolling.html'); // Importation du html
		}
	} 
}


if(class_exists('diceRolling')){
	function wpb_load_widget() {
	   register_widget( 'diceRolling' );
	}
	add_action( 'widgets_init', 'wpb_load_widget' ); // Hook wordpress, on relie la fonction widgets_init à wpb_load_widget pour charger mon widget 
}


function init_database() {
	global $wpdb; // Classe qui permet d'intéragir avec la bdd
	global $jal_db_version;

	$table_name = $wpdb->prefix . 'dicerolling'; 
	
	$charset_collate = $wpdb->get_charset_collate(); // Encodage de la bdd

	// Ajout d'une requête 
	$sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time DATETIME NOT NULL,	
		uid INT NOT NULL,
		dice INT NOT NULL,
        rolls INT NOT NULL,
		results INT NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";

	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	dbDelta( $sql ); // Requête

	add_option( 'jal_db_version', $jal_db_version );
}

function add_data() {
	global $wpdb;
	
	$table_name = $wpdb->prefix . 'dicerolling';
	$current_user_id = get_current_user_id();

	$rolls = $_POST['roll-step'];
	$dice = $_POST['dice-select'];
	
	$throws = 0;
	for ($index = 0; $index < $rolls; $index++) {
		$roll = rand(1, $dice);
		$throws += $roll; 
	}
	
	$wpdb->insert( 
		$table_name, 
		array( 
			'time' => date('Y-m-d H:i:s'), 
			'uid' => $current_user_id, 
			'dice' => $dice,
			'rolls' => $rolls,
			'results' => $throws,
		) 
	);
}


register_activation_hook( __FILE__, 'init_database' ); // Création d'une nouvelle table dans la base de donnée lors de l'activation du plugin
register_activation_hook( __FILE__, 'init_dattest_dataabase' ); 

?>