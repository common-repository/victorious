<p>
    <?php echo sprintf(esc_html(__('The following trade has been proposed to you in %s from %s.','victorious')), $league_name, $user_profile->display_name);?>
</p>
<p>
    <?php echo sprintf(esc_html(__('%s trade proposed for %s','victorious')), $s_user_players, $s_target_players);?>
</p>
<?php if((float)$amount > 0):?>
<p>
    <?php echo sprintf(esc_html(__('%s has included %s Tokens with this trade proposal.','victorious')), $user_profile->display_name, $amount);?>
</p>
<?php endif;?>
<p>
    <?php echo sprintf(esc_html(__('You can accept or reject this offer by visiting your %s Contest.','victorious')), $site_name);?>
    <br/>
    <?php echo esc_html(__('Note that if the trade proposal is no longer visible on your team page, it has been canceled by the manager who originally proposed it.','victorious'));?>
</p>
<p>
    <?php echo sprintf(esc_html(__('Contest page: %s','victorious')), '<a href="'.$href_trade.'">'.$href_trade.'</a>');?>
</p>
<p>
    <?php echo esc_html(__("If the link isn't working, please copy and paste the entire URL into your browser.",'victorious'));?>
</p>
<p>
    <?php echo esc_html(__("Visit your league's trading block to view other teams' trade priorities and needs, and make another great trade!",'victorious'));?>
</p>
<p>
    <?php echo esc_html(__('Sincerely,','victorious'));?>
    <br/>
    <?php echo esc_html($site_name);?>
</p>