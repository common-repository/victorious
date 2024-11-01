<div>
    <b><?php echo sprintf(esc_html(__("%s's picks", "victorious")), $user->data->user_login);?></b>
</div>
<div class="group_left">
    <?php if($group_left != null):?>
        <?php foreach($group_left as $group):?>
            <ul class="group_item">
                <li class="group_name">
                    <?php echo esc_html($group['name']);?>
                </li>
                <?php if(!empty($group['teams'])):?>
                    <?php foreach($group['teams'] as $team):?>
                    <li class="item <?php if(in_array($team['teamID'], $picks)):?>selected<?php endif;?>">
                        <?php echo esc_html($team['rank'])." ".esc_html($team['name']);?>
                    </li>
                    <?php endforeach;?>
                <?php endif;?>
            </ul>
        <?php endforeach;?>
    <?php endif;?>
</div>
<div class="bracket_image">
    <img src="<?php echo VICTORIOUS__PLUGIN_URL_IMAGE."wc_bracket.png"?>" />
    <?php if($group_left16 != null):?>
    <div class="group_left_16">
        <?php foreach($group_left16 as $item):
            $home_team = $item['home_team'];
            $away_team = $item['away_team'];
        ?>
            <ul class="group_item group_item_16">
                <li class="item <?php if(in_array($home_team['teamID'], $picks)):?>selected<?php endif;?>">
                    <?php echo esc_html($home_team['name']);?>
                </li>
                <li class="item <?php if(in_array($away_team['teamID'], $picks)):?>selected<?php endif;?>">
                    <?php echo esc_html($away_team['name']);?>
                </li>
            </ul>
        <?php endforeach;?>
    </div>
    <?php endif;?>
    <?php if($group_right16 != null):?>
    <div class="group_right_16">
        <?php foreach($group_right16 as $item):
            $home_team = $item['home_team'];
            $away_team = $item['away_team'];
        ?>
            <ul class="group_item group_item_16">
                <li class="item <?php if(in_array($home_team['teamID'], $picks)):?>selected<?php endif;?>">
                    <?php echo esc_html($home_team['name']);?>
                </li>
                <li class="item <?php if(in_array($away_team['teamID'], $picks)):?>selected<?php endif;?>">
                    <?php echo esc_html($away_team['name']);?>
                </li>
            </ul>
        <?php endforeach;?>
    </div>
    <?php endif;?>
    <?php if($group_left8 != null):?>
    <div class="group_left_8">
        <?php foreach($group_left8 as $item):
            $home_team = $item['home_team'];
            $away_team = $item['away_team'];
        ?>
            <ul class="group_item group_item_8">
                <li class="item <?php if(in_array($home_team['teamID'], $picks)):?>selected<?php endif;?>">
                    <?php echo esc_html($home_team['name']);?>
                </li>
                <li class="item <?php if(in_array($away_team['teamID'], $picks)):?>selected<?php endif;?>">
                    <?php echo esc_html($away_team['name']);?>
                </li>
            </ul>
        <?php endforeach;?>
    </div>
    <?php endif;?>
    <?php if($group_right8 != null):?>
    <div class="group_right_8">
        <?php foreach($group_right8 as $item):
            $home_team = $item['home_team'];
            $away_team = $item['away_team'];
        ?>
            <ul class="group_item group_item_8">
                <li class="item <?php if(in_array($home_team['teamID'], $picks)):?>selected<?php endif;?>">
                    <?php echo esc_html($home_team['name']);?>
                </li>
                <li class="item <?php if(in_array($away_team['teamID'], $picks)):?>selected<?php endif;?>">
                    <?php echo esc_html($away_team['name']);?>
                </li>
            </ul>
        <?php endforeach;?>
    </div>
    <?php endif;?>
    <?php if($group_left4 != null):?>
    <div class="group_left_4">
        <?php foreach($group_left4 as $item):
            $home_team = $item['home_team'];
            $away_team = $item['away_team'];
        ?>
            <ul class="group_item group_item_4">
                <li class="item <?php if(in_array($home_team['teamID'], $picks)):?>selected<?php endif;?>">
                    <?php echo esc_html($home_team['name']);?>
                </li>
                <li class="item <?php if(in_array($away_team['teamID'], $picks)):?>selected<?php endif;?>">
                    <?php echo esc_html($away_team['name']);?>
                </li>
            </ul>
        <?php endforeach;?>
    </div>
    <?php endif;?>
    <?php if($group_right4 != null):?>
    <div class="group_right_4">
        <?php foreach($group_right4 as $item):
            $home_team = $item['home_team'];
            $away_team = $item['away_team'];
        ?>
            <ul class="group_item group_item_4">
                <li class="item <?php if(in_array($home_team['teamID'], $picks)):?>selected<?php endif;?>">
                    <?php echo esc_html($home_team['name']);?>
                </li>
                <li class="item <?php if(in_array($away_team['teamID'], $picks)):?>selected<?php endif;?>">
                    <?php echo esc_html($away_team['name']);?>
                </li>
            </ul>
        <?php endforeach;?>
    </div>
    <?php endif;?>
    <?php if($fixture2 != null):?>
    <div class="group_left_2">
        <?php foreach($fixture2 as $item):
            $home_team = $item['home_team'];
        ?>
            <ul class="group_item group_item_2">
                <li class="item <?php if(in_array($home_team['teamID'], $picks)):?>selected<?php endif;?>">
                    <?php echo esc_html($home_team['name']);?>
                </li>
            </ul>
        <?php endforeach;?>
    </div>
    <?php endif;?>
    <?php if($fixture2 != null):?>
    <div class="group_right_2">
        <?php foreach($fixture2 as $item):
            $away_team = $item['away_team'];
        ?>
            <ul class="group_item group_item_2">
                <li class="item <?php if(in_array($away_team['teamID'], $picks)):?>selected<?php endif;?>">
                    <?php echo esc_html($away_team['name']);?>
                </li>
            </ul>
        <?php endforeach;?>
    </div>
    <?php endif;?>
    <?php if($fixture1st != null):?>
    <div class="group_1st">
        <?php foreach($fixture1st as $item):
            $team = $item['team1score'] > $item['team2score'] ? $item['home_team'] : $item['away_team'];
        ?>
            <ul class="group_item group_item_2">
                <li class="item <?php if(in_array($team['teamID'], $picks)):?>selected<?php endif;?>">
                    <?php echo esc_html($team['name']);?>
                </li>
            </ul>
        <?php endforeach;?>
    </div>
    <?php endif;?>
    <?php if($fixture3rd != null):?>
    <div class="group_3rd">
        <?php foreach($fixture3rd as $item):
            $team = $item['team1score'] > $item['team2score'] ? $item['home_team'] : $item['away_team'];
        ?>
            <ul class="group_item group_item_2">
                <li class="item <?php if(in_array($team['teamID'], $picks)):?>selected<?php endif;?>">
                    <?php echo esc_html($team['name']);?>
                </li>
            </ul>
        <?php endforeach;?>
    </div>
    <?php endif;?>
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
                    <li class="item <?php if(in_array($team['teamID'], $picks)):?>selected<?php endif;?>">
                        <?php echo esc_html($team['rank'])." ".esc_html($team['name']);?>
                    </li>
                    <?php endforeach;?>
                <?php endif;?>
            </ul>
        <?php endforeach;?>
    <?php endif;?>
</div>
<div class="clear"></div>