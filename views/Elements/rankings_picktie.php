<script type="text/javascript">
    var isLive = <?php echo esc_html($aLeague['is_live']);?>;
    var status = '<?php echo esc_html($aPool['status']);?>';
    var showInviteFriends = '<?php echo esc_html($showInviteFriends);?>';
    if (isLive == 1 && status == 'NEW')
    {
        jQuery.ranking.liveEntriesResult('<?php echo esc_html($aLeague['poolID']);?>', '<?php echo esc_html($aLeague['leagueID']);?>', '<?php echo esc_html($entry_number);?>', '<?php echo esc_html($date_type);?>', '<?php echo esc_html($date_type_number); ?>');
        setInterval(function(){ 
            jQuery.ranking.liveEntriesResult('<?php echo esc_html($aLeague['poolID']);?>', '<?php echo esc_html($aLeague['leagueID']);?>', '<?php echo esc_html($entry_number);?>', '<?php echo esc_html($date_type);?>', '<?php echo esc_html($date_type_number); ?>');
        },60000);
    }
    else 
    {
        jQuery.ranking.enterLeagueHistory('<?php echo esc_html($entry_number);?>');
    }
    if(showInviteFriends)
    {
        //jQuery.ranking.inviteFriends();
        jQuery.playerdraft.showDialog('#dlgFriends');
        <?php if(get_option('victorious_get_email_from_better_join_contest')): ?>
            jQuery.playerdraft.sendUserJoincontestEmail('<?php echo esc_html($aLeague['leagueID']); ?>','<?php echo esc_html($entry_number); ?>');
        <?php endif; ?>
    }

    <?php if($allow_pick_email):?>
    jQuery(window).load(function(){
        jQuery.playerdraft.sendUserPickEmail('<?php echo esc_html($aLeague['leagueID']);?>');
    });
    <?php endif;?>
</script>