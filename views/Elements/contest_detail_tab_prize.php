<div class="prize-list">    <?php foreach($prizes as $prize):?>        <div class="prize-list-item">            <div><?php echo esc_html($prize['place']);?></div>            <b class="b-text"><?php echo VIC_FormatMoney($prize['prize'], $balance_type['currency_code_symbol'], $balance_type['currency_position']);?></b>        </div>    <?php endforeach;?></div><?php if(!empty($guaranteed_prizes)):?><h3><?php echo esc_html(__("Guaranteed prize", "victorious"));?> </h3><div class="prize-list">    <?php foreach($guaranteed_prizes as $prize):?>        <div class="prize-list-item">            <div><?php echo esc_html($prize['place']);?></div>            <b class="b-text"><?php echo VIC_FormatMoney($prize['prize'], $balance_type['currency_code_symbol'], $balance_type['currency_position']);?></b>        </div>    <?php endforeach;?></div><?php endif;?>