<?php
    $main_sport_title = $main_sport_des = "";
    $sport_class_icon = "";
	$allow_draw = false;
    switch ($league['organizationID']){
            case 13:
                    break;
            case 14:
                    $main_sport_title = 'Basketball';
                    $main_sport_des = 'NBA, Euro Basketball and WNBA111';
                    $sport_class_icon = "fa-basketball-ball";
                    break;
            case 15:
                    break;
            case 16:
                    break;
            case 44:
                    break;
            case 1822:
                    $main_sport_title = 'Soccer';
                    $main_sport_des = 'Bundesliga';
                    $sport_class_icon = "fa-futbol-o";
					$allow_draw = true;
                    break;
    }
?>

<?php VIC_GetMessage(); ?>
<article class="hentry">
    <div class="vc-section">
        <div class="p-4">
            <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW_ELEMENT.'draft_header.php');?>
        </div>
        <div class="p-4 bg-gray" style="border-radius: 0 0 10px 10px">
            <input type="hidden" value="<?php echo in_array($league['organizationID'], array(1822)) ? 1 : 0;?>" id="simple_point">
            <form id="formData">
                <input type="hidden" value="<?php echo esc_attr($league['leagueID']); ?>" name="league_id" id="league_id">
                <input type="hidden" value="<?php echo esc_attr($entry_number); ?>" name="entry_number">
                <input type="hidden" value="" name="wager_type" id="wager_type_value">
                <input type="hidden" value="" name="wager" id="wager_value">
                <input type="hidden" value="" name="to_win" id="to_win_value">
                <input type="hidden" value="" name="team_id" id="team_id_value">
            </form>
            <main id="homePage" class="color-white">
                <section class="bet-board">
                    <div class="container">
                        <div class="row">
                            <div class="col-left"></div>
                            <div class="col-center">
                                <div class="bet-board-block">
                                    <div class="bet-table">
                                        <div class="title-row row">
                                            <div class="big-col"><i class="fa <?php echo esc_attr($sport_class_icon);?>"></i><?php echo esc_html(__('Games', 'victorious'));?></div>
                                            <div class="small-col"><?php echo esc_html(__('Win', 'victorious'));?></div>
                                            <div class="small-col"><?php echo esc_html(__('Spread', 'victorious'));?></div>
                                            <div class="small-col"><?php echo esc_html(__('Over/Under', 'victorious'));?></div>
                                        </div>
                                        <?php foreach ($fights as $fight):
                                            $home_team = $fight['home_team'];
                                            $away_team = $fight['away_team'];
                                            $draw_id = $fight['fightID'].'_'.$home_team['teamID'].'_draw';

                                            $home_win_id = $fight['fightID'].'_'.$home_team['teamID'].'_win';
                                            $home_spread_id = $fight['fightID'].'_'.$home_team['teamID'].'_spread';
                                            $home_over_under_id = $fight['fightID'].'_'.$home_team['teamID'].'_over';

                                            $away_win_id = $fight['fightID'].'_'.$away_team['teamID'].'_win';
                                            $away_spread_id = $fight['fightID'].'_'.$away_team['teamID'].'_spread';
                                            $away_over_under_id = $fight['fightID'].'_'.$away_team['teamID'].'_under';

                                            $home_win = $home_team['teamID'].'_win';
                                            $home_spread = $home_team['teamID'].'_spread';
                                            $home_over_under = $home_team['teamID'].'_over';
                                            $active_home_win = isset($home_team['picks'][$home_win]) ? true : false;
                                            $active_home_spread = isset($home_team['picks'][$home_spread]) ? true : false;
                                            $active_home_over_under = isset($home_team['picks'][$home_over_under]) ? true : false;

                                            $away_win = $away_team['teamID'].'_win';
                                            $away_spread = $away_team['teamID'].'_spread';
                                            $away_over_under = $away_team['teamID'].'_under';
                                            $active_away_win = isset($away_team['picks'][$away_win]) ? true : false;
                                            $active_away_spread = isset($away_team['picks'][$away_spread]) ? true : false;
                                            $active_away_over_under = isset($away_team['picks'][$away_over_under]) ? true : false;
                                            ?>
                                            <div class="content-row row sportbook_pick_item" id="fight_<?php echo esc_attr($fight['fightID']);?>" data-name="<?php echo esc_html($home_team['name']).' '.esc_html(__('At', 'victorious')).' '.esc_html($away_team['name']);?>" data-start-time="<?php echo strtotime($fight['startDate']);?>" data-locked="<?php echo esc_attr($fight['started']);?>">
                                                <div class="big-col"><?php echo esc_html($home_team['name']);?></div>
                                                <div class="small-col sportbook_game_point <?php if(empty($fight['team1_win'])):?>locked<?php endif;?> <?php echo esc_attr($active_home_win ? "active" : "");?>"id="slip_<?php echo esc_attr($home_win_id);?>"
                                                     data-id="<?php echo esc_attr($home_win_id);?>"
                                                     data-fight-id="<?php echo esc_attr($fight['fightID']);?>"
                                                     data-team-id="<?php echo esc_attr($home_team['teamID']);?>"
                                                     data-type="win"
                                                     data-type-group="group_win"
                                                     data-team-type="home"
                                                     data-name="<?php echo esc_attr($home_team['name']);?>"
                                                     data-value="<?php echo esc_attr($fight['team1_spread']);?>"
                                                     data-price="<?php echo esc_attr($fight['team1_win']);?>"
                                                     data-wager="<?php echo isset($home_team['picks'][$home_win]) ? $home_team['picks'][$home_win]['wager'] : '';?>"
                                                >
                                                    <div class="info-box sportbook_point_wrapper">
                                                        <?php if(!empty($fight['team1_win'])):?>
                                                            <span><?php echo esc_html($fight['team1_spread']);?></span>
                                                            <span><?php echo esc_html($fight['team1_win']);?></span>
                                                        <?php else:?>
                                                            <span class="igt-icon color-dark">
                                                                <span class="material-icons">lock</span>
                                                            </span>
                                                        <?php endif;?>
                                                    </div>
                                                </div>
                                                <div class="small-col sportbook_game_point <?php if(empty($fight['team1_spread_points'])):?>locked<?php endif;?> <?php echo esc_attr($active_home_spread ? "active" : "");?>"id="slip_<?php echo esc_attr($home_spread_id);?>"
                                                     data-id="<?php echo esc_attr($home_spread_id);?>"
                                                     data-fight-id="<?php echo esc_attr($fight['fightID']);?>"
                                                     data-team-id="<?php echo esc_attr($home_team['teamID']);?>"
                                                     data-type="spread"
                                                     data-type-group="group_spread"
                                                     data-team-type="home"
                                                     data-name="<?php echo esc_html($home_team['name']);?>"
                                                     data-value="<?php echo esc_attr($fight['team1_spread']);?>"
                                                     data-price="<?php echo esc_attr($fight['team1_spread_points']);?>"
                                                     data-wager="<?php echo isset($home_team['picks'][$home_spread]) ? $home_team['picks'][$home_spread]['wager'] : '';?>"
                                                >
                                                    <div class="info-box sportbook_point_wrapper">
                                                        <?php if(!empty($fight['team1_spread_points'])):?>
                                                            <span><?php echo esc_html($fight['team1_spread']);?></span>
                                                            <span><?php echo esc_html($fight['team1_spread_points']);?></span>
                                                        <?php else:?>
                                                            <span class="igt-icon color-dark">
                                                                <span class="material-icons">lock</span>
                                                            </span>
                                                        <?php endif;?>
                                                    </div>
                                                </div>
                                                <div class="small-col sportbook_game_point <?php if(empty($fight['total_over'])):?>locked<?php endif;?> <?php echo esc_attr($active_home_over_under ? "active" : "");?>" id="slip_<?php echo esc_attr($home_over_under_id);?>"
                                                     data-id="<?php echo esc_attr($home_over_under_id);?>"
                                                     data-fight-id="<?php echo esc_attr($fight['fightID']);?>"
                                                     data-team-id="<?php echo esc_attr($home_team['teamID']);?>"
                                                     data-type="over" data-name="<?php echo esc_html(__('Over', 'victorious'));?>"
                                                     data-team-type="home"
                                                     data-type-group="group_over_under"
                                                     data-value="<?php echo esc_attr($fight['total_over_under']);?>"
                                                     data-price="<?php echo esc_attr($fight['total_over']);?>"
                                                     data-wager="<?php echo isset($home_team['picks'][$home_over_under]) ? $home_team['picks'][$home_over_under]['wager'] : '';?>"
                                                >
                                                    <div class="info-box sportbook_point_wrapper">
                                                        <?php if(!empty($fight['total_over'])):?>
                                                            <span>O <?php echo esc_html($fight['total_over_under']);?></span>
                                                            <span><?php echo esc_html($fight['total_over']);?></span>
                                                        <?php else:?>
                                                            <span class="igt-icon color-dark">
                                                            <span class="material-icons">lock</span>
                                                        </span>
                                                        <?php endif;?>
                                                    </div>
                                                </div>
                                                <div class="big-col"><?php echo esc_html($away_team['name']);?></div>
                                                <div class="small-col sportbook_game_point <?php if(empty($fight['team2_win'])):?>locked<?php endif;?> <?php echo esc_attr($active_away_win ? "active" : "");?>" id="slip_<?php echo esc_attr($away_win_id);?>"
                                                     data-id="<?php echo esc_attr($away_win_id);?>"
                                                     data-fight-id="<?php echo esc_attr($fight['fightID']);?>"
                                                     data-team-id="<?php echo esc_attr($away_team['teamID']);?>"
                                                     data-type="win"
                                                     data-type-group="group_win"
                                                     data-team-type="away"
                                                     data-name="<?php echo esc_html($away_team['name']);?>"
                                                     data-value="<?php echo esc_attr($fight['team2_spread']);?>"
                                                     data-price="<?php echo esc_attr($fight['team2_win']);?>"
                                                     data-wager="<?php echo isset($away_team['picks'][$away_win]) ? $away_team['picks'][$away_win]['wager'] : '';?>"
                                                >
                                                    <div class="info-box sportbook_point_wrapper">
                                                        <?php if(!empty($fight['team2_win'])):?>
                                                            <span><?php echo esc_html($fight['team2_spread']);?></span>
                                                            <span><?php echo esc_html($fight['team2_win']);?></span>
                                                        <?php else:?>
                                                            <span class="igt-icon color-dark">
                                                                <span class="material-icons">lock</span>
                                                            </span>
                                                        <?php endif;?>
                                                    </div>
                                                </div>
                                                <div class="small-col sportbook_game_point <?php if(empty($fight['team2_spread_points'])):?>locked<?php endif;?> <?php echo esc_attr($active_away_spread ? "active" : "");?>" id="slip_<?php echo esc_attr($away_spread_id);?>"
                                                     data-id="<?php echo esc_attr($away_spread_id);?>"
                                                     data-fight-id="<?php echo esc_attr($fight['fightID']);?>"
                                                     data-team-id="<?php echo esc_attr($away_team['teamID']);?>"
                                                     data-type="spread"
                                                     data-type-group="group_spread"
                                                     data-team-type="away"
                                                     data-name="<?php echo esc_html($away_team['name']);?>"
                                                     data-value="<?php echo esc_attr($fight['team2_spread']);?>"
                                                     data-price="<?php echo esc_attr($fight['team2_spread_points']);?>"
                                                     data-wager="<?php echo isset($away_team['picks'][$away_spread]) ? $away_team['picks'][$away_spread]['wager'] : '';?>"
                                                >
                                                    <div class="info-box sportbook_point_wrapper">
                                                        <?php if(!empty($fight['team2_spread_points'])):?>
                                                            <span><?php echo esc_html($fight['team2_spread']);?></span>
                                                            <span><?php echo esc_html($fight['team2_spread_points']);?></span>
                                                        <?php else:?>
                                                            <span class="igt-icon color-dark">
                                                                <span class="material-icons">lock</span>
                                                            </span>
                                                        <?php endif;?>
                                                    </div>
                                                </div>
                                                <div class="small-col sportbook_game_point <?php if(empty($fight['total_under'])):?>locked<?php endif;?> <?php echo esc_attr($active_away_over_under ? "active" : "");?>" id="slip_<?php echo esc_attr($away_over_under_id);?>"
                                                     data-id="<?php echo esc_attr($away_over_under_id);?>"
                                                     data-fight-id="<?php echo esc_attr($fight['fightID']);?>"
                                                     data-team-id="<?php echo esc_attr($away_team['teamID']);?>"
                                                     data-type="under"
                                                     data-type-group="group_over_under"
                                                     data-team-type="away"
                                                     data-name="<?php echo esc_html(__('Under', 'victorious'));?>"
                                                     data-value="<?php echo esc_attr($fight['total_over_under']);?>"
                                                     data-price="<?php echo esc_attr($fight['total_under']);?>"
                                                     data-wager="<?php echo isset($away_team['picks'][$away_over_under]) ? $away_team['picks'][$away_over_under]['wager'] : '';?>"
                                                >
                                                    <div class="info-box sportbook_point_wrapper">
                                                        <?php if(!empty($fight['total_under'])):?>
                                                            <span>U <?php echo esc_html($fight['total_over_under']);?></span>
                                                            <span><?php echo esc_html($fight['total_under']);?></span>
                                                        <?php else:?>
                                                            <span class="igt-icon color-dark">
                                                                <span class="material-icons">lock</span>
                                                            </span>
                                                        <?php endif;?>
                                                    </div>
                                                </div>
                                                <?php if($allow_draw):?>
                                                    <div class="big-col"><?php echo esc_html(__('Draw', 'victorious'));?></div>
                                                    <div class="small-col sportbook_game_point <?php if(empty($fight['team_draw'])):?>locked<?php endif;?> <?php echo !empty($fight['draw']) ? "active" : "";?>"id="slip_<?php echo esc_attr($draw_id);?>"
                                                         data-id="<?php echo esc_attr($draw_id);?>"
                                                         data-fight-id="<?php echo esc_attr($fight['fightID']);?>"
                                                         data-team-id=""
                                                         data-type="draw"
                                                         data-type-group="group_win"
                                                         data-team-type=""
                                                         data-name="<?php echo esc_html(__('Draw', 'victorious'));?>"
                                                         data-value=""
                                                         data-price="<?php echo esc_attr($fight['team_draw']);?>"
                                                         data-wager="<?php echo !empty($fight['draw']) ? $fight['draw']['wager'] : '';?>"
                                                    >
                                                        <div class="info-box sportbook_point_wrapper">
                                                            <?php if(!empty($fight['team_draw'])):?>
                                                                <span><?php echo esc_html($fight['team_draw']);?></span>
                                                            <?php else:?>
                                                                <span class="igt-icon color-dark">
                                                                    <span class="material-icons">lock</span>
                                                                </span>
                                                            <?php endif;?>
                                                        </div>
                                                    </div>
                                                <?php endif;?>
                                                <div class="coupon-footer">
                                                    <span class="date"><?php echo VIC_DateTranslate($fight['startDate']); ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach;?>
                                    </div>
                                </div>
                            </div>
                            <div class="spacing-block"></div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </div>
</article>
<div class="col-right betslip">
    <div class="btn-betslip">
        <span class="itemCount" id="betSlipCount">0</span>
        <i class="fa fa-clipboard"></i>
        <?php echo esc_html(__('Betslip', 'victorious'));?>
    </div>
    <div class="content-betslip">
        <div class="sportbook_slip">
            <header>
                <?php echo esc_html(__('WAGER REMAINING', 'victorious'));?>: <span id="bet_credit" data-value="<?php echo esc_attr($league['sportbook_bet_credit']);?>"><?php echo VIC_FormatMoney($league['sportbook_bet_credit']);?></span>
            </header>
        </div>
        <div class="sportbook_slip parlay_slip" style="display:none">
            <?php
                $parlay = array();
                if($picks != null)
                {
                    foreach($picks as $pick)
                    {
                        if($pick['wager_type'] != 'parlay')
                        {
                            continue;
                        }
                        $parlay = $pick;
                        break;
                    }
                }
            ?>
            <header>
                <?php echo esc_html(__('Parlay', 'victorious'));?>
            </header>
            <div class="item-selected sportbook_select_item" data-wager-type="parlay" data-team-id="" data-fight-id="">
                <div class="event-name">
                    <label><span class="parlay_total"></span> <?php echo esc_html(__('Team Parlay', 'victorious'));?></label>
                </div>
                <div class="event-bet stake-line parlay-wage">
                    <div class="money-input">
                        <span class="label-txt"><?php echo esc_html(__('WAGER', 'victorious'));?></span>
                        <span class="currency">$</span>
                        <input type="number" novalidate="novalidate" inputmode="decimal" step="0.01" placeholder="" class="no-spinners wager" value="<?php echo !empty($parlay['wager']) ? $parlay['wager'] : '';?>">
                    </div>
                    <div class="money-input">
                        <span class="label-txt"><?php echo esc_html(__('TO WIN', 'victorious'));?></span>
                        <span class="currency">$</span>
                        <input type="number" novalidate="novalidate" inputmode="decimal" step="0.01" placeholder="" class="no-spinners to_win">
                    </div>
                </div>
            </div>
        </div>
        <div class="sportbook_slip straight_slip" style="display:none">
            <header>
                <?php echo esc_html(__('Straights', 'victorious'));?>
            </header>
            <div id="slipContent"></div>
        </div>
        <div class="empty-betslip">
            <img src="<?php echo VICTORIOUS__PLUGIN_URL_IMAGE.'/empty-betslip.png';?>"> 
            <div class="msg">
                <span><?php echo esc_html(__('Betslip is Empty', 'victorious'));?></span> 
            </div>
        </div>
        <div class="action-block">
            <div class="delete remove_all_slip">
                <span></span>
            </div>
            <a href="<?php echo is_user_logged_in() ? 'javascript:void(0)' : wp_login_url();?>" class="btn-green" <?php if(is_user_logged_in()):?>id="btnSubmit"<?php endif;?>><?php echo is_user_logged_in() ? esc_html(__('Submit', 'victorious')) : esc_html(__('Log in & Place Bets', 'victorious'));?></a>
        </div>
        <!-- <i class="fa fa-times"></i> -->
    </div>
</div>

<?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW . 'dlg_info.php');?>
<script>
    jQuery(document).ready(function(){
        jQuery.sportbook.initSportbook();
    })
</script>

<script type="text/template" id="templateStraight">
    <div class="item-selected sportbook_select_item" data-wager-type="" data-team-id="" data-fight-id="">
        <div class="event-name"><i class="fa <?php echo esc_attr($sport_class_icon);?>"></i><span class="select_title">Auburn @ Georgia</span></div>
        <div class="event-options">
            <div class="delete remove_slip" data-id=""></div>
            <div class="selection-info">
                <div class="selection-name">
                    <span class="name point_name">Auburn -4</span>
                    <span class="price point_price">-12</span>
                </div>
                <span class="market-name point_type">point spread</span>
            </div>                                       
        </div>
        <div class="event-bet stake-line straight-wage">
            <div class="money-input">
                <span class="label-txt"><?php echo esc_html(__('WAGER', 'victorious'));?></span>
                <span class="currency">$</span>
                <input type="number" novalidate="novalidate" inputmode="decimal" step="0.01" placeholder="" class="no-spinners wager">
            </div>
            <div class="money-input">
                <span class="label-txt"><?php echo esc_html(__('TO WIN', 'victorious'));?></span>
                <span class="currency">$</span>
                <input type="number" novalidate="novalidate" inputmode="decimal" step="0.01" placeholder="" class="no-spinners to_win">
            </div>
        </div>
    </div>
</script>