<link type="text/css" href="/assets/css/jplayer.blue.monday.css" rel="stylesheet" />
<link type="text/css" href="/assets/css/style.jplayer.override.css" rel="stylesheet" />
<link type="text/css" href="/assets/css/toastr.min.css" rel="stylesheet" />
<script type="text/javascript" src="/assets/js/highcharts/highcharts.js"></script>
<script type="text/javascript" src="/assets/js/jplayer/jquery.jplayer.min.js"></script>
<script type="text/javascript" src="/assets/js/toastr.min.js"></script>
<?php 
	function toLocalTime($timestamp, $usersTimezone = null) {
		if(!$usersTimezone) {
			$usersTimezone = new DateTimeZone('America/Chicago');
		}
		$l10nDate = new DateTime($timestamp);
		$l10nDate->setTimeZone($usersTimezone);
		$timestamp = $l10nDate->format('Y-m-d H:i:s');
		return $timestamp;
	}

	echo '<div class="widgets row"><div class="pull-right span4">';
	
	if($hasAlerts)
	{
		echo '<div class="alert alert-error widget"><h1>Alerts</h1>';
		foreach ($companions as $companion) 
		{
			if($companion->emergency_alert)
			{
				?>
				<div class="alert alert-error">
					<?php if($leader) { ?>
					<a href="#clear-alert-modal-<?php echo $companion->id?>" title="Clear Alert" role="button" class="close" data-toggle="modal"><i class="fa fa-times fa-2x"></i></a>
					<?php } ?>
					<?php echo $companion->name; ?> reported a Serious Situation with <?php echo $companionToGroup[$companion->id]->name; if($companionToLastEmergencyUpdate[$companion->id]) { ?>&nbsp;&nbsp;<code class="humanTiming" data-time="<?php echo toLocalTime($companionToLastEmergencyUpdate[$companion->id]['update']->created_at); ?>"><?php echo $companionToLastEmergencyUpdate[$companion->id]['timeElapsed']; ?> ago</code><?php } ?>
					<?php if($leader) { ?>
						<!-- Modal -->
						<div id="clear-alert-modal-<?php echo $companion->id?>" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="clear-alert-modal-label" aria-hidden="true">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times fa-lg"></i></button>
							<h3 id="clear-alert-modal-label" class="text-error">Are you sure about this?</h3>
						  </div>
						  <div class="modal-body">
							<p>This will clear the serious situation alert for <?php echo $companionToGroup[$companion->id]->name; ?>.</p>
						  </div>
						  <div class="modal-footer">
							<button class="btn" data-dismiss="modal" aria-hidden="true">No</button>
							<a href="/dashboard?clear_alert=<?php echo $companion->id?>" class="btn btn-primary">Yes</a>
						  </div>
						</div>
					<?php } ?>
				</div>
				<?php
			}
			
			if(array_key_exists($companion->id, $companionToLastCurfewUpdate))
			{
				$firstUpdate = $companionToLastCurfewUpdate[$companion->id]['update'];
				$timeElapsed = $companionToLastCurfewUpdate[$companion->id]['timeElapsed'];
				?>
				<div class="alert"><span class="close no-link"><i class="fa fa-clock-o text-error fa-2x"></i></span><?php echo $companion->name; ?> is out past curfew.&nbsp;&nbsp;<code class="humanTiming" data-time="<?php echo toLocalTime($firstUpdate->created_at)?>"><?php echo $timeElapsed; ?> ago</code></div>
				<?php
			}
			
			if(array_key_exists($companion->id, $companionToLowBattery))
			{
				$firstUpdate = $companionToLowBattery[$companion->id]['update'];
				$timeElapsed = $companionToLowBattery[$companion->id]['timeElapsed'];
				?>
				<div class="alert"><span class="close no-link"><i class="fa fa-dashboard text-error fa-2x"></i></span><?php echo $companion->name; ?> has a low battery.&nbsp;&nbsp;<code class="humanTiming" data-time="<?php echo toLocalTime($firstUpdate->created_at)?>"><?php echo $timeElapsed; ?> ago</code></div>
				<?php
			}
		}
		echo '</div>';
	}

	echo "<div class='alert alert-info widget'><h1>Child's Play</h1>";
	foreach ($companions as $companion) 
	{ 
		if(array_key_exists($companion->id, $companionToGroup))
		{
			$group = $companionToGroup[$companion->id];
			
			if(array_key_exists($companion->id, $companionToLastUpdateWithEmotion))
			{
				$firstUpdate = $companionToLastUpdateWithEmotion[$companion->id]['update'];
				$timeElapsed = $companionToLastUpdateWithEmotion[$companion->id]['timeElapsed'];
				
				switch($firstUpdate->emotional_state)
				{
					case 0: echo '<div class="alert"><span class="close no-link"><i class="fa fa-meh-o fa-2x"></i></span>'.$group->name.' has not shared with the team yet.</div>'; break;
					case 1: echo '<div class="alert alert-info"><span class="close no-link"><i class="fa fa-smile-o fa-2x"></i></span>'.$group->name.' shared a happy moment.&nbsp;&nbsp;<code class="humanTiming" data-time="'.toLocalTime($firstUpdate->created_at).'">'.$timeElapsed.' ago</code></div>'; break;
					case 2: echo '<div class="alert alert-error"><span class="close no-link"><i class="fa fa-frown-o fa-2x"></i></span>'.$group->name.' shared an unhappy moment.&nbsp;&nbsp;<code class="humanTiming" data-time="'.toLocalTime($firstUpdate->created_at).'">'.$timeElapsed.' ago</code></div>'; break;
					case 3: echo '<div class="alert alert-error"><span class="close no-link"><i class="fa fa-frown-o fa-2x"></i></span>'.$group->name.' shared a serious moment.&nbsp;&nbsp;<code class="humanTiming" data-time="'.toLocalTime($firstUpdate->created_at).'">'.$timeElapsed.' ago</code></div>'; break;
				}
			}
			else
			{
				echo '<div class="alert"><span class="close no-link"><i class="fa fa-meh-o fa-2x"></i></span>'.$group->name.'  has not shared with the team yet.</div>';
			}
			
			if(array_key_exists($companion->id, $companionToLastPlayMessageOnUserUpdate))
			{
				$firstUpdate = $companionToLastPlayMessageOnUserUpdate[$companion->id]['update'];
				$timeElapsed = $companionToLastPlayMessageOnUserUpdate[$companion->id]['timeElapsed'];
				
				echo '<div class="alert"><span class="close no-link" style="right: -24px;"><i class="fa fa-volume-up fa-2x"></i></span>'.$group->name.' was listening to messages.&nbsp;&nbsp;<code class="humanTiming" data-time="'.toLocalTime($firstUpdate->created_at).'">'.$timeElapsed.' ago</code></div>';
			}
		}
	}
	echo '</div>';
	
	$replyModals = array();
	
	echo "<div class='alert alert-success widget'><h1>Safety Sam</h1>";
	foreach ($companions as $companion) 
	{ 
		if(array_key_exists($companion->id, $companionToGroup))
		{
			$group = $companionToGroup[$companion->id];
			
			if(array_key_exists($companion->id, $companionToLastSaid))
			{
				$firstUpdate = $companionToLastSaid[$companion->id]['update'];
				$timeElapsed = $companionToLastSaid[$companion->id]['timeElapsed'];
				$text = $companionToLastSaid[$companion->id]['text'];
				$player = $companionToLastSaid[$companion->id]['player'];
				?>
				<div class="alert" style="min-height:80px;">
					<span class="close no-link" style="right: -26px;">
						<?php echo $player; ?>
						<a href="#send-a-message-modal-<?php echo $companion->id; ?>" title="Reply" role="button" class="close-mimic" data-toggle="modal">
							<i class="fa fa-cloud-upload fa-2x"></i>
						</a>
						<?php $replyModal = '<!-- Modal -->
						<div id="send-a-message-modal-'.$companion->id.'" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="send-a-message-modal-label" aria-hidden="true">
						  <div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times fa-lg"></i></button>
							<h3 id="send-a-message-modal-label">Reply</h3>
						  </div>
						  <div class="modal-body" >
						  	<div class="row-fluid">
								<p class="muted"><em>"'.$text.'"</em></p>
								<p></p>
								<div class="row-fluid">
									<select class="dont-auto-render span11 companion-messages-select" id="companion-messages-'.$companion->id.'"></select>
								</div>
							</div>
							<div class="send-message-instructions">
								<hr/>
								<div class="send-message-steps">
									<div class="pull-left"><p class="text-warning">Step 1 <i class="fa fa-reply"></i></p><p>You can reply to '.$group->name.' by selecting a message to send.</p></div>
									<div class="pull-left"><p class="text-warning">Step 2 <i class="fa fa-cloud"></i></p><p>We\'ll have our CloudCarriers&#153; deliver it to '.$companion->name.'.</p></div>
									<div class="pull-left last"><p class="text-warning">Step 3 <i class="fa fa-volume-up"></i></p><p>'.$companion->name.' will play it back for '.$group->name.', it\'s as simple as that.</p></div>
									<span class="clearfix"></span>
								</div>
								<div class="row-fluid send-message-tagline">
									<hr/>
									<span class="text-info pull-right">That\'s the power of the Hug Community.</span>
									<p>&nbsp;</p>
								</div>
							</div>
						  </div>
						  <div class="modal-footer">
							<button class="btn" data-dismiss="modal" aria-hidden="true">Cancel</button>
							<button class="btn btn-primary btn-send-reply" data-companion-id="'.$companion->id.'">Send</button>
						  </div>
						</div>'; array_push($replyModals, $replyModal); ?>
					</span>
					<?php echo $companion->name;?> had this to say, <em>"<?php echo $text; ?>"</em>
					&nbsp;&nbsp;
					<code class="humanTiming" data-time="<?php echo toLocalTime($firstUpdate->created_at)?>"><?php echo $timeElapsed; ?> ago</code>
				</div>
				<?php
			}
			
			if(array_key_exists($companion->id, $companionToLastChargingUpdate))
			{
				$firstUpdate = $companionToLastChargingUpdate[$companion->id]['update'];
				$timeElapsed = $companionToLastChargingUpdate[$companion->id]['timeElapsed'];
		
				if(!$firstUpdate->is_charging)
				{
					echo '<div class="alert"><span class="close no-link"><i class="fa fa-dashboard fa-2x"></i></span>'.$companion->name.' is battery powered at '.$firstUpdate->voltage.' Volts.&nbsp;&nbsp;<code class="humanTiming" data-time="'.toLocalTime($firstUpdate->created_at).'">'.$timeElapsed.' ago</code></div>';
				}
				else
				{
					echo '<div class="alert clearfix"><span class="close no-link" style="right: -14px;"><i class="fa fa-flash fa-2x"></i></span>'.$companion->name.' is recharging at '.$firstUpdate->voltage.' Volts.&nbsp;&nbsp;<code class="humanTiming" data-time="'.toLocalTime($firstUpdate->created_at).'">'.$timeElapsed.' ago</code></div>';
				}
			}
		}
	}
	?>
	</div></div><div class="span8">
	
	<?php
	foreach ($groups as $group) 
	{ 
		if(array_key_exists($group->id, $groupToCompanion))
		{
			$companion = $groupToCompanion[$group->id];
			
			echo '<h2>'.$group->name.'</h2>';
			?>
				<div id="chart-<?php echo $companion->id; ?>"></div>
				<script type="text/javascript" >
					var emotions<?php echo $companion->id; ?> = [];
					var messages<?php echo $companion->id; ?> = [];
					<?php
						$emotionUpdates = array_key_exists($companion->id, $companionToEmotionUpdates);
						$messageUpdates = array_key_exists($companion->id, $companionToMessagesSaidUpdates);
						if($emotionUpdates)
						{
							$updates = $companionToEmotionUpdates[$companion->id];
							
							foreach ($updates as $update) 
							{ 
								?>
								emotions<?php echo $companion->id; ?>.push({x: new Date(<?php echo strtotime(toLocalTime($update->created_at))*1000;?>), <?php
											switch($update->emotional_state)
											{
												case 3: echo 'name: "Serious", y: 0'; break;
												case 2: echo 'name: "Unhappy", y: 1'; break;
												case 1: echo 'name: "Happy", y: 2'; break;
											}?>});
							<?php
							}
						}
						
						if($messageUpdates)
						{
							$updates = $companionToMessagesSaidUpdates[$companion->id];
							
							foreach ($updates as $update) 
							{ 
								$usersname = $update->last_name.', '.$update->first_name;
								$saysText = implode('<br/><i>', explode('<br/>', wordwrap($update->text,39,'<br/>')));
								$middleText = '<br/>-------------------------------<br/><b>'.$usersname.'<\/b><br/>-------------------------------<br/><i>'.$saysText.'<\/i>", ';
								?>
								messages<?php echo $companion->id; ?>.push({x: new Date(<?php echo strtotime(toLocalTime($update->created_at))*1000;?>), <?php
											switch($update->emotional_state)
											{
												case 3: echo 'name: "Serious'.$middleText.'y: 0'; break;
												case 2: echo 'name: "Unhappy'.$middleText.'y: 1'; break;
												case 1: echo 'name: "Happy'.$middleText.'y: 2'; break;
											}?>});
							<?php
							}
						}
						
						if($emotionUpdates || $messageUpdates)
						{
						?>
							$(function () {
								$('#chart-<?php echo $companion->id; ?>').highcharts({
									chart: {
										type: 'spline',
										zoomType: 'x',
										spacingRight: 20
									},
									title: {
										text: '<?php echo $group->name; ?> emotional health <i class="fa fa-stethoscope"></i>',
										useHTML: true
									},
									subtitle: {
										text: document.ontouchstart === undefined ?
											'Click and drag in the plot area to zoom in' :
											'Pinch the chart to zoom in'
									},
									xAxis: {
										type: 'datetime',
										dateTimeLabelFormats: {
											millisecond: '%I:%M:%S.%L %P',
											second: '%I:%M:%S %P',
											minute: '%I:%M %P',
											hour: '%I:%M %P'
										},
										title: {
											text: null
										}
									},
									yAxis: {
										title: {
											text: null
										},
										categories: ['Serious', 'Unhappy', 'Happy'],
										gridLineColor: '#FFFFFF'
									},
									tooltip: {
										formatter: function() {
												return '<b>'+ Highcharts.dateFormat('%a, %d %b %Y %I:%M %P', this.x) +'</b><br/>'+this.key;
										}
									},
									legend: {
										//enabled: false
									},
									credits: {
										  enabled: false
									},
									series: [{name: 'Emotion', data: emotions<?php echo $companion->id; ?>}, {name: 'Message', data: messages<?php echo $companion->id; ?>}]
								});
							});
						</script><?php
					} else {
							echo '</script>No Updates.';
					}
		}
	}
	echo '</div>';
	?>
		<div id="success-modal" class="modal hide fade" tabindex="-1" style="text-align:center;">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times fa-lg"></i></button>
			<h3>Success!</h3>
		  </div>
		  <div class="modal-body">
			<p><i class="fa fa-heart fa-5x fa-lg"></i></p>
		  </div>
		</div>
		
		<div id="error-modal" class="modal hide fade" tabindex="-1" style="text-align:center;">
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times fa-lg"></i></button>
			<h3>A Problem Was Encountered</h3>
		  </div>
		  <div class="modal-body">
			<p><i class="fa fa-bug fa-5x fa-lg"></i></p>
			<p>Please try again.</p>
		  </div>
		</div>
	<?php
	foreach( $replyModals as $replyModal)
	{
		echo $replyModal;
	}
	?>
	
	<script type="text/javascript" src="/assets/js/widgets.js"></script>

	<?php
	echo '</div>';
?>