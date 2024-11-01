<?php if(!empty($scores)):?>
    <div class="vc-pick-your-team" id="inDrafting" data-can-draft="<?php echo $can_draft;?>" data-interval="<?php echo $interval;?>">
        <section data-role="fixture-picker" class="f-fixture-picker">
            <div class="vc-pick-your-team-container f-fixture-picker-button-container">
                <div class="vc-pick-your-team-item vc-pick-your-team-item-all" style="display: block">
                    <div><?php echo esc_html(__('Drafting In', 'victorious'));?></div>
                    <div id="playoff-countdown"></div>
                </div>
                <?php foreach ($scores as $score):
                    $user = $score['user'];
                    $current_turn = $score['playoff_current_turn'];
                ?>
                    <div class="vc-pick-your-team-item draft-turn <?php echo $current_turn ? "f-is-active" : "";?>" data-id="<?php echo esc_attr($score['userID']);?>">
                        <span class="vc-pick-team-home"><?php echo esc_html(__('Pick', 'victorious')).' '.esc_html($score['playoff_draft_turn']); ?></span>
                        <div class="vc-start-time"><?php echo esc_html($user['username']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
<?php endif;?>