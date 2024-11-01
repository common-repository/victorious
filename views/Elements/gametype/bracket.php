<div class="group_left">
    <?php if($group_left != null):?>
        <?php foreach($group_left as $group):?>
            <ul class="group_item">
                <li class="group_name">
                    <?php echo esc_html($group['name']);?>
                </li>
                <?php if(!empty($group['teams'])):?>
                    <?php foreach($group['teams'] as $team):?>
                    <li data-id="<?php echo esc_attr($team['teamID']);?>" class="item" id="item_<?php echo esc_attr($team['teamID']);?>">
                        <?php echo esc_html($team['name']);?>
                        <div class="item_action">
                            <a class="item_action_button item_action_add" id="item_action_add_<?php echo esc_attr($team['teamID']);?>">
                                <i class="fa fa-plus-circle"></i>
                            </a>
                            <a class="item_action_button item_action_remove" id="item_action_remove_<?php echo esc_attr($team['teamID']);?>" style="display: none;">
                                <i class="fa fa-minus-circle"></i>
                            </a>
                        </div>
                    </li>
                    <?php endforeach;?>
                <?php endif;?>
            </ul>
        <?php endforeach;?>
    <?php endif;?>
</div>
<div class="bracket_image">
    <img src="<?php echo VICTORIOUS__PLUGIN_URL_IMAGE."wc_bracket.png"?>" />
</div>
<div class="group_right">
    <?php if($group_right != null):?>
        <?php foreach($group_right as $group):?>
            <ul class="group_item">
                <li class="group_name">
                    <?php echo esc_html($group['name']);?>
                </li>
                <?php if(!empty($group['teams'])):?>
                    <?php foreach($group['teams'] as $team):?>
                    <li data-id="<?php echo esc_attr($team['teamID']);?>" class="item" id="item_<?php echo esc_attr($team['teamID']);?>">
                        <?php echo esc_html($team['name']);?>
                        <div class="item_action">
                            <a class="item_action_button item_action_add" id="item_action_add_<?php echo esc_attr($team['teamID']);?>">
                                <i class="fa fa-plus-circle"></i>
                            </a>
                            <a class="item_action_button item_action_remove" id="item_action_remove_<?php echo esc_attr($team['teamID']);?>" style="display: none;">
                                <i class="fa fa-minus-circle"></i>
                            </a>
                        </div>
                    </li>
                    <?php endforeach;?>
                <?php endif;?>
            </ul>
        <?php endforeach;?>
    <?php endif;?>
</div>
<div class="clear"></div>
<form id="formData">
    <input type="hidden" name="league_id" value="<?php echo esc_attr($league['leagueID']);?>" />
    <input type="hidden" id="team_ids" name="team_ids" value="" />
</form>
<div class="f-contest-enter-button-container">
    <input type="button" id="btnSubmit" value="<?php echo esc_html(__('Save', 'victorious'));?>" class="button button_jumbo">
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery.bracket.initBracket();
        <?php if($my_picks != null):?>
            jQuery.bracket.setDefaultPicks('<?php echo implode(',', $my_picks);?>');
        <?php endif;?>
    })
</script>