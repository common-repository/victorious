<div class="my-3">
	<span>
		<?php 
			echo esc_html(__('Site Stats for', 'victorious'));
		 ?>
	</span>
	<span style="padding-left: 20px">
		<select name="stats_type" onchange="jQuery.admin.loadDashboardStats();">
			<option value="total_contest" <?php echo ($stats_type == 'total_contest') ? 'selected' : '';?> ><?php echo esc_html(__('Total Contests', 'victorious'));?></option>
			<option value="total_money" <?php echo ($stats_type == 'total_money') ? 'selected' : '';?>><?php echo esc_html(__('Total Money', 'victorious'));?></option>
			<option value="total_users_played" <?php echo ($stats_type == 'total_users_played') ? 'selected' : '';?>><?php echo esc_html(__('Total Users Played', 'victorious'));?></option>
		</select>
	</span>
	<span style="padding-left: 20px">
		On   
		<select name="stats_year" onchange="jQuery.admin.loadDashboardStats();">
			<?php 
				$i = date('Y') - 2;
				for($i > 0; $i <= date('Y'); $i++){
					$selected = ($stats_year == $i) ? 'selected' : '';
					echo '<option value="'.$i.'" '.$selected.'>'.$i.'</option>';
				}
			 ?>
		</select>
	</span>
</div>
<div class="row">
	<div class="col-md-12" id="dash_board_chart">

	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function($) {
		jQuery.admin.loadDashboardStats();
	});
</script>