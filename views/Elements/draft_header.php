<div class="vc-header">
    <div class="vc-header-left">
        <h3 class="vc-title text-uppercase"><?php echo esc_html($league['name']); ?></h3>
        <div class="d-inline-flex flex-column mr-4"><b><?php echo esc_html($league['entries']); ?> / <?php echo esc_html($league['size']); ?></b><span><?php echo esc_html(__('Entries', 'victorious'));?></span></div>
        <div class="d-inline-flex flex-column mr-4"><b><?php echo esc_html($league['entry_fee'] == 0 ? __("Free", "victorious") : VIC_FormatMoney($league['entry_fee'], $balance_type['currency_code_symbol'], $balance_type['currency_position'])); ?></b><span><?php echo esc_html(__('Entry fee', 'victorious'));?></span></div>
        <div class="d-inline-flex flex-column"><b><?php echo VIC_FormatMoney($league['prizes'], $balance_type['currency_code_symbol'], $balance_type['currency_position']); ?></b><span><?php echo esc_html(__('Prizes', 'victorious'));?></span></div>
    </div>
    <div class="vc-header-right">
        <p class="vc-des"><?php echo esc_html(__('Contest starts', 'victorious'));?></p>
        <p class="vc-des color-black"><b><?php echo VIC_DateTranslate($league['startDate']); ?></b></p>
        <p><a href="javascript:void(0)" class="color-blue" onclick="return jQuery.global.ruleScoring(<?php echo esc_html($league['leagueID']); ?>)"><?php echo esc_html(__('Rules &amp; Scoring', 'victorious'));?></a></p>
    </div>
</div>