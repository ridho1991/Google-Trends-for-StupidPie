<?php
	wp_register_sidebar_widget
	(
	'gtswidget',         
	'Google Hot Trends',  
	'gtswidget_display',  
	array(                     
	'description' => 'Display google hot trends of the day'
	)
	);
	
	wp_register_widget_control
	(
	'gtswidget',    
	'gtswidget',     
	'gtswidget_control'  
	);
	
	
			
	function gtswidget_control($args=array(), $params=array()) 
	{
		if (isset($_POST['submitted'])) 
		{
			update_option('gtswidget_title', $_POST['gtstitle']);
			update_option('gtswidget_number', $_POST['number']);
		}
		
		$gtstitle = get_option('gtswidget_title');
		$number = get_option('gtswidget_number');
		?>
		Widget Title:<br />
		<input type="text" class="widefat" name="gtstitle" value="<?php echo stripslashes($gtstitle); ?>" />
		<br /><br />
		Number to show:<br />
		<input type="text" class="widefat" name="number" value="<?php echo stripslashes($number); ?>" />
		<br /><br />
		<input type="hidden" name="submitted" value="1" />
		<?php
	}
	
	function gtswidget_display($args=array(), $params=array()) 
	{
		$gtstitle = get_option('gtswidget_title');
		$number = get_option('gtswidget_number');

		if($gtstitle==''){
			$title = 'Hot Trends';
		} else {
		   $title = $gtstitle;
		}
		if($number==''){
			$num = '20';
		} else {
		   $num = $number;
		}

		echo stripslashes($args['before_widget']);
		echo stripslashes($args['before_title']);
		echo stripslashes($title);
		echo stripslashes($args['after_title']); ?>
		<?php
		$hottrends=get_hot_trends($num);
		if($hottrends !=null)
		{
			echo '<ul>';
			foreach($hottrends as $trend){
			echo '<li><a href="'.get_category_link($trend->term_id).'">'.$trend->trends.'</a></li>';
			}
			echo '</ul>';
		}
	?>
	<?php wp_reset_query(); ?>
	<?php echo stripslashes($args['after_widget']);
	}
	
	function get_hot_trends($limit)
	{
		global $wpdb;
		$datas = $wpdb->get_results( "SELECT ".$wpdb->prefix.'gts_trends'.".trends,".$wpdb->prefix.'terms'.".term_id FROM ".$wpdb->prefix.'gts_trends'.",".$wpdb->prefix.'terms'." WHERE ".$wpdb->prefix.'gts_trends'.".trends=".$wpdb->prefix.'terms'.".name order by ".$wpdb->prefix.'gts_trends'.".dates desc limit ".$limit."");
		return $datas;
	}
	
?>