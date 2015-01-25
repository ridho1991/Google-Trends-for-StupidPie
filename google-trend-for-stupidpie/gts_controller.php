<?php
ini_set('max_execution_time', 0);
ini_set('memory_limit', '999M');

class Gts_Controller 
{
	var $gts_data;
	var $gtrends;

	public function __construct() 
	{
		$this->gts_data = new Gts_Database();
		$this->gtrends= new GoogleHotrends();
	}

	public function gts_table_trends_exist()
	{
		global $wpdb;
		$table_name = $this->gts_data->gts_trends_table();
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name){
			return true;
		} else {
			return false;
		}
	}

	public function gts_table_settings_exist()
	{
		global $wpdb;
		$table_name = $this->gts_data->gts_settings_table();
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name){
			return true;
		} else {
			return false;
		}
	}
	
	public function gts_table_domains_exist()
	{
		global $wpdb;
		$table_name = $this->gts_data->gts_domains_table();
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name){
			return true;
		} else {
			return false;
		}
	}
	
	public function gts_table_languages_exist()
	{
		global $wpdb;
		$table_name = $this->gts_data->gts_languages_table();
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name){
			return true;
		} else {
			return false;
		}
	}
	
	public function gts_table_trends_country_exist()
	{
		global $wpdb;
		$table_name = $this->gts_data->gts_trends_country_table();
		if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name){
			return true;
		} else {
			return false;
		}
	}
	
	public function create_gts_trends($data)
	{
		$this->gts_data->create_gts_trends($data);
    }

	public function scheduler($name) 
	{
		$this->run_delete();
		
		if($this->gts_data->get_scheduled($name)!=null)
		{
			$this->run_gts(); 
		}
	}
	
	public function run_delete()
	{
		$delete_interval=$this->get_scheduled_delete();
		$interval_delete=0;
		$now_date=current_time('mysql');
		
		if ($delete_interval=="daily")
		{
			$interval_delete=1;
		}
		elseif ($delete_interval=="3days")
		{
			$interval_delete=3;
		}
		elseif ($delete_interval=="weekly")
		{
			$interval_delete=7;
		}
		
		$trends = $this->get_all_trends();	
		foreach ($trends as $trend)
		{
			$past_date=$trend->dates;
			if ($this->get_difference_day($past_date,$now_date)==$interval_delete)
			{
				$this->gts_data->delete_stupidbot_campaign($trend->trends);
				$this->gts_data->delete_gts_trends($trend->trends);
			}
		}
	}
	
	public function get_difference_day($past_date,$now_date)
	{
		$diff = abs(strtotime($now_date) - strtotime($past_date));
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
		return $days;
	}
	
	public function run_gts()
	{
		$gts_settings=$this->get_gts_settings();
		$trends_country=$gts_settings->trends_country;
		$domain=$gts_settings->keywords_domain;
		$lang_country=$gts_settings->keywords_language;
		
		$campaign_hack=$gts_settings->campaign_hack;
		$campaign_count=$gts_settings->campaign_count;
		$campaign_schedule=$gts_settings->campaign_schedule;
		$campaign_template=$gts_settings->campaign_template;
		$campaign_active=$gts_settings->campaign_active;
		
		$message="";		
		$hottrends=$this->gtrends->fetch_trends($trends_country);
		if ($hottrends != '') 
		{
			foreach ($hottrends as $trend)
			{			
				if ($this->get_category_exist($trend)==null)
					{
						$this->create_category($trend);
					}

				$category_id=$this->get_category($trend);
				$keywords=array();
				$keywords=$this->gtrends->fetch_keyword($domain,$lang_country,$trend);
				if ($keywords != '')
				{
						$data = array();
						$data2=	array();
						$data3=	array();
						$key=implode("\n",$keywords);
						$date=current_time('mysql');
						$data[]       = array(
							'trends'     => trim($trend),
							'keywords'   => $key,
							'dates'      => $date
						);
						
						foreach ($data as $trends) 
						{
							if ($this->get_gts_trends_exist($trends['trends'])==null)
							{
								$this->create_gts_trends($trends);
								$message .= $this->get_alert( true ,"Trend '".$trends['trends']."' inserted successfully");
							}
						}
						
						$data2[]       = array(
						'keywords'    => trim($key),
						'template'    => $campaign_template,
						'hack'        => $campaign_hack,
						'counter'     => 0,
						'count'       => $campaign_count,
						'category_id' => $category_id,
						'schedule'    => $campaign_schedule,						
						'active'      => $campaign_active
						);

						foreach ($data2 as $keyword) 
						{
							if ($this->get_campaign_exist($trend)==null)
							{
								$this->create_stupidbot_campaign($keyword);
								$message .= $this->get_alert( true ,"Campaign '".$keyword['keywords']."' inserted successfully");
							}
						}
			
						foreach($keywords as $keyword)
						{
							$data3[]       = array(
							'term'    => trim($keyword)
							);
						}
									
						foreach ($data3 as $keyword) 
						{
							if ($this->get_spp_exist($keyword['term'])==null)
							{
								$this->create_spp_term($keyword);
								$message .= $this->get_alert( true ,"Term '".$keyword['term']."' inserted successfully");
							}
						}
				}
			}
		}
		return $message;
    }

	public function get_category_exist($trend){
		return $this->gts_data->get_category_exist($trend);
	}
	
	public function get_category($trend){
		return $this->gts_data->get_category($trend);
	}
	
	public function create_category($trend){
		return $this->gts_data->create_category($trend);
	}
	
	public function get_all_trends(){
		return $this->gts_data->get_all_trends();
	}

	public function get_gts_settings(){
		return $this->gts_data->get_gts_settings();
	}
	
	public function get_gts_domains(){
		return $this->gts_data->get_gts_domains();
	}
	
	public function get_gts_languages(){
		return $this->gts_data->get_gts_languages();
	}
	
	public function get_gts_trends_country(){
		return $this->gts_data->get_gts_trends_country();
	}
	
	public function get_trends_count(){
		return $this->gts_data->get_trends_count();
	}

	public function create_gts_trends_table()
	{
		$this->gts_data->create_gts_trends_table();
	}

	public function create_gts_settings_table()
	{
		$this->gts_data->create_gts_settings_table();
	}
	
	public function create_gts_domains_table()
	{
		$this->gts_data->create_gts_domains_table();
	}
	
	public function create_gts_trends_country_table()
	{
		$this->gts_data->create_gts_trends_country_table();
	}
	
	public function create_gts_languages_table()
	{
		$this->gts_data->create_gts_languages_table();
	}
	
	public function create_gts_languages()
	{
		$this->gts_data->create_gts_languages();
	}
	
	public function create_gts_domains()
	{
		$this->gts_data->create_gts_domains();
	}
	
	public function create_gts_trends_country()
	{
		$this->gts_data->create_gts_trends_country();
	}
		
	public function create_stupidbot_campaign($keyword)
	{
		$this->gts_data->create_stupidbot_campaign($keyword);
	}
	
	public function create_spp_term($keyword)
	{
		$this->gts_data->create_spp_term($keyword);
	}
	
	public function create_gts_settings()
	{
		global $wpdb;
		$data = array();
		$data[]       = array(
			'id'=>1,
			'trends_schedule' 		=> 'daily',
			'trends_country'		=> 'p1',
			'keywords_domain'		=> 'google.com',
			'keywords_language'		=> 'us',
			'campaign_template'    	=> 'default.html',
			'campaign_hack'       	=> '',
			'campaign_count'       	=> 100,
			'campaign_schedule'    	=> 'daily',
			'campaign_active'      => 1,
			'campaign_delete'      => 'weekly'
		);
		
		foreach ($data as $settings) 
		{
			$this->gts_data->create_gts_settings($settings);
		}
	}
		
	public function update_gts_settings($settings){
		$updated = $this->gts_data->update_gts_settings($settings);
		if ( $updated ) {
			$message = $this->get_alert( true , "Setting updated" );
		} else {
			$message = $this->get_alert( false , "Failed to update setting" );
		}
		return $message;
    }
	
	public function get_alert($type, $message) {
		$alert = "";
		if ($type) {
			$alert .= "<div class=\"alert alert-success alert-dismissable\">\n";
		} else {
			$alert .= "<div class=\"alert alert-danger alert-dismissable\">\n";
		}
		$alert .= "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-hidden=\"true\">&times;</button>\n";
		$alert .= $message."\n";
		$alert .= "</div>\n";
		return $alert;
	}
	
	public function get_gts_settings_exist(){
		return $this->gts_data->get_gts_settings_exist();
	}
	
	public function get_gts_domains_exist(){
		return $this->gts_data->get_gts_domains_exist();
	}
	
	public function get_gts_languages_exist(){
		return $this->gts_data->get_gts_languages_exist();
	}
	
	public function get_gts_trends_country_exist(){
		return $this->gts_data->get_gts_trends_country_exist();
	}
	
	public function get_gts_trends_exist($trend){
		return $this->gts_data->get_gts_trends_exist($trend);
	}
	
	public function get_campaign_exist($keyword){
		return $this->gts_data->get_campaign_exist($keyword);
	}
	
	public function get_spp_exist($keyword){
		return $this->gts_data->get_spp_exist($keyword);
	}	
	
	public function get_scheduled_delete()
	{
		return $this->gts_data->get_scheduled_delete();
	}
}
