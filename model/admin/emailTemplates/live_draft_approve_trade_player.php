<p>
    <?php echo sprintf(esc_html(__('Great news! The following trade that you proposed has been accepted in %s by %s.','victorious')), $league_name, $target->display_name);?>
</p>
<p>
    <?php echo sprintf(esc_html(__('%s trade proposed for %s','victorious')), $change, $with);?>
</p>
<p>
    <?php echo esc_html(__("Visit your league's trading block to view other teams' trade priorities and needs, and make another great trade!",'victorious'));?>
</p>
<p>
    <?php echo esc_html(__('Sincerely,','victorious'));?>
    <br/>
    <?php echo esc_html($site_name);?>
</p>