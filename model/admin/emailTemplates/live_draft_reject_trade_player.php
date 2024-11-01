<p>
    <?php echo sprintf(esc_html(__('Unfortunately, the following trade that you previously proposed has been rejected in %s.','victorious')), $league_name);?>
</p>
<p>
    <?php echo sprintf(esc_html(__('%s trade proposed for %s','victorious')), $change, $with);?>
</p>
<p>
    <?php echo esc_html(__("Visit your league's trading block view to other teams' trade priorities and needs, then propose another trade.",'victorious'));?>
</p>
<p>
    <?php echo esc_html(__('Sincerely,','victorious'));?>
    <br/>
    <?php echo esc_html($site_name);?>
</p>