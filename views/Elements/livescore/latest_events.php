<?php if($events != null):?>
    <select name="event_id" id="event_id" class="stats-filter" onchange="jQuery.livescore.loadFixtureScores();">
        <?php foreach($events as $event):?>
            <option value="<?php echo esc_attr($event['poolID']);?>">
                <?php echo date('M d Y', strtotime($event['startDate']));?>
            </option>
        <?php endforeach;?>
    </select>
<?php else:?>
    <?php echo esc_html(__('No events', 'victorious'));?>
<?php endif; ?>
