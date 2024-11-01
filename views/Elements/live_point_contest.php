<?php if(!empty($leagues)):?>
    <option value=""><?php echo esc_html(__("Select Contest", "victorious"));?></option>
    <?php foreach($leagues as $k => $league): ?>
        <option value="<?php echo esc_attr($league['leagueID']);?>">
            <?php echo esc_html($league['name']);?>
        </option>
    <?php endforeach; ?>
<?php else:?>
    <option value=""><?php echo esc_html(__("No contest", "victorious"));?></option>
<?php endif; ?>
