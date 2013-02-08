<div class="wrap">
	<p><a href="http://www.leadoutcome.com" target="_blank" title="<?php echo __('LeadOutcome','lo'); ?>"><img alt="" src="http://768fb7af815f78d7559a-1cf765037453fe7e5aa46c8e17022e63.r47.cf2.rackcdn.com/staging/sites/all/themes/rubik/cube/logo_1336174074.png" alt="<?php echo __('LeadOutcome','lo'); ?>" title="<?php echo __('LeadOutcome','lo'); ?>" style="width: 265px; height: 60px;border:none;" align="middle"></a>&nbsp;&nbsp;<input type="button" class="button-primary" value="<?php _e('View / Manage Opt-in Forms') ?>" onClick="location.href='admin.php?page=leadoutcome_optin_forms';" />&nbsp;&nbsp;&nbsp;<input type="button" class="button-primary" value="<?php _e('Lead Tracking / Conversions Options') ?>" onClick="location.href='admin.php?page=leadoutcome_lead_track_convert_options';" />
	</p>
	<p>&nbsp;</p>

	<div style="width:50%">
		<p>Use these options to set whether or not lead tracking code will automatically be inserted on pages or posts.  If the option is checked, for that respective page type we will insert the tracking code for at the bottom of each page</p>

		<?php 
		if($perform_update)
		{
			if($posts_update_success || $pages_update_success)
			{
				lo_messages(__('Tracking / Conversions code options have been updated successfully.','lo'),'message');
			}
			else
			{
				lo_messages(__('There was a problem updating the tracking / conversions code options.  Please try again.','lo'),'error');
			}
		}
		?>

		<!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
		<form id="optin-forms-filter" method="post" action="admin.php?page=leadoutcome_lead_track_convert_options">
			<input type="hidden" name="action" value="update" />
			<?php wp_nonce_field('lo_lead_track_convert_options_update','lononce'); ?>
			<table class="form-table">
			<tr>
				<td><input type="checkbox" name="lead_track_convert_posts" value="1" <?php echo $lo_lead_track_convert_posts == 1 ? 'checked="checked"' : ''; ?> />&nbsp;<strong><?php _e('Tracking On Posts','lo'); ?></strong></td>
			</tr>
			<tr>
				<td><input type="checkbox" name="lead_track_convert_pages" value="1" <?php echo $lo_lead_track_convert_pages == 1 ? 'checked="checked"' : ''; ?> />&nbsp;<strong><?php _e('Tracking On Pages','lo'); ?></strong></td>
			</tr>
			</table>

			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>
</div>
