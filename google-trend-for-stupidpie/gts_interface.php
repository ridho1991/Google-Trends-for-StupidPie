<div class="wrap bootstrap-wpadmin" >
	<div class="container-fluid">
		<h2 style="margin-bottom:10px">Google Trends for StupidPie</h2>
		<div class="row">
		
			<div class="col-sm-12">
			<?php if($this->alert){
				echo $this->alert;
			} ?>
			<?php //if($trends_count < 1){ ?>
			<div class="alert alert-warning">
				<input type="hidden" value="" name="run_gts"/>
				<?php wp_nonce_field( "run_gts" , "_run_gts" ); ?>
				<button class="btn btn-default" data-toggle="modal" data-target="#run_gts" ><span class="glyphicon glyphicon-play"></span>RUN</button> Run this to get updated google hot trends.
			</div>
			<?php //} ?>
			
			</div>
		</div>
		<div class="row">
			<div class="col-sm-9">
				<div class="panel panel-default panel-collapse collapse" id="gts_setting" >
					<div class="panel-heading">
						<h3 class="panel-title">Settings</h3>
					</div>
					<div class="panel-body">
						<form class="form-horizontal" method="post" role="form">
							<fieldset>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="gts_setting[trends_schedule]">Trends Schedule</label>
									<div class="col-sm-7">
									<select name="gts_setting[trends_schedule]" id="gts_setting[trends_schedule]" class="form-control">
										<?php foreach( $schedules as $sched_name=>$sched_data ) { ?>
										<option <?php selected($gts_settings->trends_schedule, $sched_name); ?> value="<?php echo $sched_name ?>"><?php echo $sched_data['display'] ?></option>
										<?php }?>
									</select>
									 <p class="help-block">How often the plugin fetch the hot trends</p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="gts_setting[trends_country]">Trends Country</label>
									<div class="col-sm-7">
									<select name="gts_setting[trends_country]" id="gts_setting[trends_country]" class="form-control">
										<?php
										foreach ($gts_trends_country as $country):?>
										<option value="<?php echo $country->code; ?>" <?php if($gts_settings->trends_country === $country->code): ?>selected="selected"<?php endif; ?>><?php echo $country->country; ?></option>
										<?php endforeach; ?>
									</select>
									 <p class="help-block">What country do you use to fetch the hot trends </p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="gts_setting[keywords_domain]">Domain</label>
									<div class="col-sm-7">
									<select name="gts_setting[keywords_domain]" id="gts_setting[keywords_domain]" class="form-control">
										<?php
										foreach ($gts_domains as $domains):?>
										<option value="<?php echo $domains->domain; ?>" <?php if($gts_settings->keywords_domain === $domains->domain): ?>selected="selected"<?php endif; ?>><?php echo $domains->country; ?></option>
										<?php endforeach; ?>
									</select>
									 <p class="help-block">What Domain do you use to fetch the keywords</p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="gts_setting[keywords_language]">Language</label>
									<div class="col-sm-7">
									<select name="gts_setting[keywords_language]" id="gts_setting[keywords_language]" class="form-control">
										<?php
										foreach ($gts_languages as $languages):?>
										<option value="<?php echo $languages->code; ?>" <?php if($gts_settings->keywords_language === $languages->code): ?>selected="selected"<?php endif; ?>><?php echo $languages->country; ?></option>
										<?php endforeach; ?>
									</select>
									 <p class="help-block">What language do you use to fetch the keywords</p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="gts_setting[campaign_template]">Post Template</label>
									<div class="col-sm-7">
										<select name="gts_setting[campaign_template]" id="gts_setting[campaign_template]" class="postform">
										<?php foreach($templates as $template):?>
										<option value="<?php echo $template; ?>" <?php if($gts_settings->campaign_template === $template): ?>selected="selected"<?php endif; ?>><?php echo $template; ?></option>
										<?php endforeach;?>
										</select>
										<p class="help-block">Choose a template for the post</p>
									</div>
								</div>
								<div class="form-group">
								  <label class="col-sm-3 control-label" for="gts_setting[campaign_hack]">Hack</label>
								  <div class="col-sm-7">
									<input type="text" class="form-control" id="gts_setting[campaign_hack]" name="gts_setting[campaign_hack]" value="<?php echo $gts_settings->campaign_hack; ?>">
									  <p class="help-block">
										  i.e. 
										  filetype:pdf to filter only pdf file<br> 
										  site:amazon.com to filter only from amazon.com site  <br>
										  Leave empty if you don't want to use this</p>
								  </div>
							  </div>
							  <div class="form-group">
								<label class="col-sm-3 control-label" for="gts_setting[campaign_count]">Post Per Request</label>
								<div class="col-sm-7">
								  <input type="text" class="form-control" id="gts_setting[campaign_count]" name="gts_setting[campaign_count]" value="<?php echo $gts_settings->campaign_count; ?>">
								  <p class="help-block">How many post created everytime campaign is run?</p>
								</div>
							  </div>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="gts_setting[campaign_schedule]">Post Schedule</label>
									<div class="col-sm-7">
										<select name="gts_setting[campaign_schedule]" id="gts_setting[campaign_schedule]" class="form-control">
											<?php foreach( $schedules as $sched_name=>$sched_data ) { ?>
															<option <?php selected($gts_settings->campaign_schedule, $sched_name); ?> value="<?php echo $sched_name ?>"><?php echo $sched_data['display'] ?></option>
															<?php } ?>
											</select>
										<p class="help-block">Schedule your post</p>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-3 control-label" for="gts_setting[campaign_delete]">Delete Schedule</label>
									<div class="col-sm-7">
											<select name="gts_setting[campaign_delete]" id="gts_setting[campaign_delete]" class="form-control">
											  <option value="daily" <?php if($gts_settings->campaign_delete === 'daily'): ?>selected="selected"<?php endif; ?>>Daily</option>
											  <option value="3days" <?php if($gts_settings->campaign_delete === '3days'): ?>selected="selected"<?php endif; ?>>3 Days</option>
											  <option value="weekly" <?php if($gts_settings->campaign_delete === 'weekly'): ?>selected="selected"<?php endif; ?>>Weekly</option>
											</select>
										<p class="help-block">Schedule delete your campaigns</p>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-offset-3 col-sm-7">
										<div class="checkbox" >
											<label>
												<input type="checkbox" id="active" value="1" checked="checked" name="gts_setting[campaign_active]">
												Make all inserted campaign active
											</label>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="col-sm-offset-3 col-sm-7">
										<?php wp_nonce_field( "gts_setting" , "_gts_setting" ); ?>
										<button type="submit" class="btn btn-primary" value="submit">Save</button>
									</div>
								</div>
							</fieldset>
						</form>
					</div>
				</div>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Trends</h3>
					</div>
					<table class="table table-hover">
						<thead>
							<tr>
								<th class="col-sm-1">Trends</th>
								<th class="col-sm-1">Keywords</th>
								<th class="col-sm-1">Date</th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ($trends as $trend):?>
							<tr>
								<td class="col-sm-3"><?php echo $trend->trends; ?></td>
								<td class="col-sm-3"><?php echo $trend->keywords; ?></td>
								<td class="col-sm-3"><?php echo $trend->dates; ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				
				<div id="run_gts" class="modal fade" style="z-index:9999" tabindex="-1" role="dialog" >
					<div class="row"  >
						<div class="col-sm-offset-4 col-sm-4" style="margin-top:100px" >
							<div class="modal-dialog modal-sm">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal">&times;</button>
										<h4 class="modal-title" >Run Google Trends</h4>
									</div>
									<form class="form-horizontal" method="post">
										<div class="modal-body">
											<fieldset>
												Please confirm you want to run Google Trends. This will create 20 trends once run.
												<input type="hidden" value="" name="run_gts"/>
											</fieldset>
										</div>
										<div class="modal-footer">
											<?php wp_nonce_field( "run_gts" , "_run_gts" ); ?>
											<button type="submit" class="btn btn-info" value="submit">RUN</button>
											<button type="button" class="btn btn-default" data-dismiss="modal" >Cancel</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
				
			</div>
			<div class="col-sm-3">
				<div class="well" >
					<button id="postnow" type="button" class="btn btn-success btn-block" data-toggle="collapse" data-target="#gts_setting" >Settings</button>
				</div>
				<div class="panel panel-default">
					<div class="panel-body">
						<p>Thank you for using Google Trends for StupidPie from <a href="https://www.wpblogdev.com">WPBlog Dev</a></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
