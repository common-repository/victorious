<?php if(!empty($aFights)):?>
<div class="f-column-12" id="f-live-scoring-fixture-info">
    <section>
        <ul>
            <?php foreach($aFights as $aFight):?>
            <li class="f-fixture-card">
                <ul class="f-fixture-card-live-status f-pending">
                    <li class="f-fixture-card-away"><?php echo esc_html($aFight['nickName1']);?> <?php echo esc_html($aFight['team1score']);?></li>
                    <li class="f-fixture-card-home"><?php echo esc_html($aFight['nickName2']);?> <?php echo esc_html($aFight['team2score']);?></li>
                    <li class="f-fixture-card-time">
                        <?php if($aFight['is_closed'] == 1):?>
                            <?php echo esc_html(__('FINAL', 'victorious'));?>
                        <?php elseif($aFight['is_closed'] == 2):?>
                            <?php echo esc_html(__('POSTPONE', 'victorious'));?>
                        <?php endif;?>&nbsp;
                    </li>
                </ul>
            </li>
            <?php endforeach;?>
        </ul>
    </section>
</div>
<?php endif;?>
<?php if(!empty($aRounds)):?>
<div class="f-column-12" id="f-live-scoring-fixture-info">
    <section>
        <ul>
            <?php foreach($aRounds as $aRound):?>
            <li class="f-fixture-card">
                <ul class="f-fixture-card-live-status f-pending">
                    <li class="f-fixture-card-away"><?php echo esc_html($aRound['name']);?></li>
                    <li class="f-fixture-card-time">
                        <?php if($aRound['is_closed'] == 1):?>
                            <?php echo esc_html(__('FINAL', 'victorious'));?>
                        <?php elseif($aFight['is_closed'] == 2):?>
                            <?php echo esc_html(__('POSTPONE', 'victorious'));?>
                        <?php endif;?>&nbsp;
                    </li>
                </ul>
            </li>
            <?php endforeach;?>
        </ul>
    </section>
</div>
<?php endif;?>