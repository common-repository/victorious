<?php $creator = get_user_by("id", $league['creator_userID']);?>
<div class="vc-header align-items-end">
    <div class="vc-header-left">
        <h3 class="vc-title text-uppercase"><?php echo esc_html($league['name']);?></h3>
        <div class="d-inline-flex flex-column mr-4"><b class="b-text"><?php echo esc_html($creator->data->user_login);?></b><span class="color-lightgray"><?php echo esc_html(__('Creator', 'victorious'));?></span></div>
        <div class="d-inline-flex flex-column mr-4"><b class="b-text"><?php echo esc_html($league['sport_name']);?></b><span class="color-lightgray"><?php echo esc_html(__('Sport', 'victorious'));?></span></div>
        <div class="d-inline-flex flex-column mr-4"><b class="b-text"><?php echo VIC_ParseGameTypeName($league['gameType']);?>
                <?php if($league['multi_entry'] == 1):?>
                    - <?php echo esc_html(__('Multi entry', 'victorious'));?>
                <?php endif;?></b><span class="color-lightgray"><?php echo esc_html(__('Game Type', 'victorious'));?></span></div>
        <div class="d-inline-flex flex-column mr-4"><b class="b-text"><?php echo VIC_DateTranslate($league['startDate']); ?></b><span class="color-lightgray"><?php echo esc_html(__('Start date', 'victorious'));?></span></div>
        <div class="d-inline-flex flex-column mr-4"><b class="b-text"><?php echo esc_html($league['size']);?> player game, <?php echo esc_html($league['entries']);?> entries</b><span class="color-lightgray"><?php echo esc_html(__('Players', 'victorious'));?></span></div>
        <div class="d-inline-flex flex-column mr-4"><b class="b-text"><?php echo esc_html($league['prize_structure']);?></b><span class="color-lightgray"><?php echo esc_html(__('Prize structure', 'victorious'));?></span></div>
        <div class="d-inline-flex flex-column mr-4"><b class="b-text"><?php echo VIC_FormatMoney($league['entry_fee'], $balance_type['currency_code_symbol'], $balance_type['currency_position']);?></b><span class="color-lightgray"><?php echo esc_html(__('Entry Fee', 'victorious'))?></span></div>
    </div>
    <div class="vc-header-right">
        <a href="#"><b class="b-text"><?php echo esc_html(__('Prizes', 'victorious'));?>: <?php echo VIC_FormatMoney($league['prizes'], $balance_type['currency_code_symbol'], $balance_type['currency_position']);?></b></a>
    </div>
</div>