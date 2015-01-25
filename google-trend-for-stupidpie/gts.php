<?php
/*
Plugin Name: Google Trends for StupidPie
Plugin URI: https://www.wpblogdev.com/
Description: Automatically find hot topic and save them for StupidBot campaigns and StupidPie Search Terms.
Plugin URI: http://agctools.blogkita.co.id
Author: WPBlog Dev
Author URI: https://www.wpblogdev.com/
Version: 1.2.3
*/

/** 
Copyright 2014 Mutasim Ridlo, S.Kom
*/

/*
## 1.0.1 Release Note:
* Initial Release

## 1.2.0 Release Note:
* Fix Google Domain

## 1.2.1 Release Note:
* Fix Form Setting

## 1.2.2 Release Note:
* Add more schedule
* Add delete schedule for stupidbot campaigns

## 1.2.2 Release Note:
* Fix undefined variable
* Fix error syntax '
*/

if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'GTS_DB_VERSION', '1.0.1' );
define( 'GTS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GTS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once ( GTS_PLUGIN_DIR.'gts_database.php' );
require_once ( GTS_PLUGIN_DIR.'gts_controller.php' );
require_once ( GTS_PLUGIN_DIR.'gts_trends.php' );
require_once ( GTS_PLUGIN_DIR.'gts_widget.php' );

class Gts 
	{
	var $alert = '';
	var $controller;

	public function __construct() 
	{
		$this->controller = new Gts_Controller();

		add_action( 'admin_init', array( $this, 'post_catcher' ) ); 
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) ); 
		add_action( 'plugins_loaded', array( $this, 'create_schedule' ) ); 
		add_action( 'admin_menu', array( $this, 'gts_menu' ) ); 
		
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
	}
	
	public function post_catcher()
	{
		$message = "";
		if ( isset( $_POST['run_gts'] ) ) 
		{
			check_admin_referer( 'run_gts', '_run_gts' );
			$message .= $this->controller->run_gts();
		}
		else if ( isset( $_POST['gts_setting'] ) ) 
		{
			check_admin_referer( 'gts_setting', '_gts_setting' );
			$edit_settings = $_POST['gts_setting'];
			$message= $this->controller->update_gts_settings($edit_settings);
		}
		
		$this->alert .= $message;
	}
	
	public function gts_menu() 
	{
		add_menu_page( 'Google Trends for StupidPie' , 'SPP Trends' , 'manage_options' , 'gts' , array( $this , 'gts_interface' )  );
	}

	public function enqueue_styles($hook) 
	{
	
		if( 'toplevel_page_gts' != $hook )
			return;
		wp_register_style( 'gts_bootstrap' , plugins_url( 'bootstrap/css/bootstrap.min.css' , __FILE__ ) , false , '3.1.1' );
		wp_register_style( 'gts_bootstrap_wpadmin' , plugins_url( 'bootstrap/css/bootstrap-wpadmin.min.css' , __FILE__ ) , false , '3.0.2' );
		wp_register_style( 'gts_gts_style' , plugins_url( 'style.css' , __FILE__ ) , false , null );
		wp_enqueue_style( 'gts_bootstrap' );
		wp_enqueue_style( 'gts_bootstrap_wpadmin' );
		wp_enqueue_style( 'gts_gts_style' );
		wp_enqueue_script( 'gts_bootstrap_js', plugins_url( 'bootstrap/js/bootstrap.min.js' , __FILE__ ) , false , '3.1.1' , true );
	}

	public function create_schedule()
	{
		$schedules = wp_get_schedules();
		foreach( $schedules as $name=>$data ) 
		{
			$schname = 'gts_'.$name;
			add_action($schname, array($this->controller, 'scheduler' ) );

			if ( ! wp_next_scheduled( $schname, array( $name ) ) ) 
			{
				wp_schedule_event( strtotime("+30 seconds") , $name, $schname, array( $name ) );
			}
		}
	}

	public function activate()
	{
		if (get_option('gts_db_version') !== GTS_DB_VERSION || !$this->controller->gts_table_trends_exist() || !$this->controller->gts_table_settings_exist() || !$this->controller->gts_table_domains_exist() || !$this->controller->gts_table_languages_exist() || !$this->controller->gts_table_trends_country_exist()) 
		{
			$this->controller->create_gts_trends_table();
			$this->controller->create_gts_settings_table();
			$this->controller->create_gts_domains_table();
			$this->controller->create_gts_languages_table();
			$this->controller->create_gts_trends_country_table();
		}
		if( $this->controller->get_gts_settings_exist() < 1 )$this->controller->create_gts_settings();;
		if( $this->controller->get_gts_domains_exist() < 1 )$this->controller->create_gts_domains();;
		if( $this->controller->get_gts_languages_exist() < 1 )$this->controller->create_gts_languages();;
		if( $this->controller->get_gts_trends_country_exist() < 1 )$this->controller->create_gts_trends_country();;
	}

	public function deactivate()
	{
        $schedules = wp_get_schedules();
		foreach( $schedules as $name=>$data ) {
			$schname = 'gts_'.$name;
			remove_action($schname, array($this->controller, 'scheduler' ) );
			wp_clear_scheduled_hook( $schname, array( $name ) );
		}
    }

	public function gts_interface()
	{
		$trends = $this->controller->get_all_trends();
		$gts_settings=$this->controller->get_gts_settings();
		$gts_trends_country=$this->controller->get_gts_trends_country();
		$trends_count = $this->controller->get_trends_count();
		$gts_domains=$this->controller->get_gts_domains();
		$gts_languages=$this->controller->get_gts_languages();
		$templates = glob(SPP_PATH . "/templates/*.html");
        foreach($templates as $index => $template)
		{
            $templates[$index] = str_replace(SPP_PATH . "/templates/", '', $template);
        }
		
		$schedules = wp_get_schedules();
		uasort($schedules, create_function('$a,$b', 'return $a["interval"]-$b["interval"];'));
		include('gts_interface.php');
		;
	}
}

$gts = new Gts();
