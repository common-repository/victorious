<?php VIC_GetMessage(); ?>
<input type="hidden" id="type_league" value="single">
<div class="f-contest-title-date">
    <h1 class="f-contest-title f-heading-styled"><?php echo esc_html($league['name']); ?></h1>
    <div class="f-contest-date-container">
        <div class="f-contest-date-start-time">
            <?php echo esc_html(__('Contest starts', 'victorious'));?> <?php echo VIC_DateTranslate($league['startDate']); ?>
        </div>
    </div>
</div>
<ul class="f-contest-information-bar">
    <li class="f-contest-entries-league"><?php echo esc_html(__('Entries:', 'victorious'));?> 
        <b>
            <a class="f-lightboxLeagueEntries_show" href="javascript:void(0)" onclick="return jQuery.global.ruleScoring(<?php esc_attr(league['leagueID']); ?>, 2)"><?php echo esc_html($league['entries']); ?></a>
        </b> / <?php echo esc_html($league['size']); ?>
        <span class="f-entries-player-league"> <?php echo esc_html(__('player league', 'victorious'));?></span>
    </li>
    <?php if (get_option('victorious_no_cash') == 0): ?>
        <li class="f-contest-entry-fee-container">
            <?php echo esc_html(__('Entry fee', 'victorious'));?>:
            <span class="f-entryFee-value amount"><?php echo esc_attr($league['entry_fee'] == 0 ? __("Free", "victorious") : VIC_FormatMoney($league['entry_fee'], $balance_type['currency_code_symbol'], $balance_type['currency_position'])); ?></span>
        </li>
        <li class="f-contest-prize-container  f-gameEntry-inner-entryFeeSelected">
            <?php echo esc_html(__('Prizes', 'victorious'));?>:
            <span class="f-content-prize-amount">
                <a class="f-lightboxPrizeList_show" href="#"  onclick="return jQuery.global.ruleScoring(<?php echo esc_attr($league['leagueID']); ?>, 3)">
                    <?php echo VIC_FormatMoney($league['prizes'], $balance_type['currency_code_symbol'], $balance_type['currency_position']); ?>
                </a>
            </span>
        </li>
    <?php endif; ?>
    <li class="f-contest-rules-link-container">
        <a class="f-lightboxRulesAndScoring_show" onclick="return jQuery.global.ruleScoring(<?php echo esc_attr($league['leagueID']); ?>)" href="#">
            <?php echo esc_html(__('Rules &amp; Scoring', 'victorious'));?>
        </a>
    </li>
</ul>
<div class="clear"></div>
<div class="f-pick-your-team">
    <section data-role="fixture-picker" class="f-fixture-picker">
        <?php if (!empty($fights)): ?>
            <h1><?php echo esc_html(__('Teams available from (click to filter):', 'victorious'));?></h1>
            <div class="f-fixture-picker-button-container">
                <a class="f-button f-mini f-is-active fixture-item all-fixtures" onclick="jQuery.teamdraft.setActiveFixture(this);return jQuery.teamdraft.loadTeams();"><?php echo esc_html(__('All Games', 'victorious'));?></a>
                <?php foreach ($fights as $fight): 
                    $home_team = $fight['home_team'];
                    $away_team = $fight['away_team'];
                ?>
                    <a class="f-button f-mini fixture-item" data-team-id1="<?php echo esc_attr($home_team['teamID']); ?>" data-team-id2="<?php echo esc_attr($away_team['teamID']); ?>" onclick="jQuery.teamdraft.setActiveFixture(this);return jQuery.teamdraft.loadTeams();">
                        <span class="f-fixture-team-home"><?php echo esc_html($away_team['nickName']); ?></span>
                        @
                        <span class="f-fixture-team-away"><?php echo esc_html($home_team['nickName']); ?></span>
                        <span class="f-fixture-start-time"><?php echo VIC_DateTranslate($fight['startDate']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>
<div class="f-row">
    <section class="f-contest-player-list-container" data-role="player-list">
        <div class="f-row">
            <h1>
                <?php echo esc_html(__('Available Teams', 'victorious'));?>
            </h1>
            <ul class="f-player-list-position-tabs f-tabs f-row">
                <li class="f-player-search">
                    <label class="f-is-hidden" for="team-search"><?php echo esc_html(__('Find a team', 'victorious'));?></label>
                    <input type="search" id="team-search" placeholder="<?php echo esc_html(__('Find a team...', 'victorious'));?>">
                </li>
            </ul>
            <ul class="f-player-list-position-tabs f-tabs f-row" style="margin-top:0" id="lineup_cat">
                <li>
                    <a href="javascript:void(0)" data-id="0" class="f-is-active"><?php echo esc_html(__('All', 'victorious')) ?></a>
                </li>
                <?php if ($lineups != null): ?>
                    <?php foreach ($lineups as $lineup):?>
                    <li>
                        <a href="javascript:void(0)" data-id="<?php echo esc_attr($lineup['id']); ?>" data-no_duplicate_with="<?php echo esc_attr($lineup['no_duplicate_with']); ?>">
                            <?php echo esc_html($lineup['name']); ?>
                        </a>
                    </li>
                    <?php endforeach;?>
                <?php endif;?>
            </ul>
            <div data-role="scrollable-header">
                <table class="f-condensed f-player-list-table-header f-header-fields">
                    <thead>
                        <tr>
                            <th class="f-player-name table-sorting">
                                <?php echo esc_html(__('Name', 'victorious'));?>
                                <i class="f-icon f-sorted-asc fa fa-angle-up" style="display: none;"></i>
                                <i class="f-icon f-sorted-desc fa fa-angle-down" style="display: none;"></i>
                            </th>
                            <th class="f-player-fixture table-sorting hidden-xs">
                                <?php echo esc_html(__('Game', 'victorious'));?>                                    
                                <i class="f-icon f-sorted-asc fa fa-angle-up" style="display: none;"></i>
                                <i class="f-icon f-sorted-desc fa fa-angle-down" style="display: none;"></i>
                            </th>
                            <?php if(!$league['salary_cap_unlimited']): ?>
                            <th class="f-player-salary table-sorting">
                                <i class="f-icon f-sorted-asc fa fa-angle-up" style="display: none;"></i>
                                <i class="f-icon f-sorted-desc fa fa-angle-down" style="display: inline;"></i>
                                <?php echo esc_html(__('Salary', 'victorious'));?>
                            </th>
                            <?php endif;?>
                            <th class="f-player-add"></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="f-errorMessage"></div>
            <div data-role="scrollable-body" id="listTeams">
                <div class="f-player-list-empty"><?php echo esc_html(__('No matching teams. Try changing your filter settings.', 'victorious'));?></div>
                <table class="f-condensed f-player-list-table">
                    <thead class="f-is-hidden">
                        <tr>
                            <th class="f-player-name">
                                <?php echo esc_html(__('Name', 'victorious'));?>
                            </th>
                            <th class="f-player-fixture">
                                <?php echo esc_html(__('Game', 'victorious'));?>
                            </th>
                            <th class="f-player-salary">
                                <?php echo esc_html(__('Salary', 'victorious'));?>
                            </th>
                            <th class="f-player-add"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            foreach ($teams as $team): 
                                $fixture = $team['fixture'];
                                $home_team = $fixture['home_team'];
                                $away_team = $fixture['away_team'];
                        ?>
                            <tr class="team_item" id="team_<?php echo esc_attr($team['teamID']);?>" data-image="<?php echo esc_attr($team['image_url']);?>" data-name="<?php echo esc_attr($team['name']);?>" data-salary="<?php echo esc_attr($team['salary']);?>">
                                <td class="f-player-name">
                                    <?php echo esc_html($team['name']);?>
                                </td>
                                <td class="f-player-fixture hidden-xs">
                                    <span class="f-fixture-team-home" style="<?php echo esc_attr($team['teamID'] == $home_team['teamID'] ? 'font-weight:bold' : ""); ?>"><?php echo esc_html($home_team['nickName']); ?></span>
                                    @
                                    <span class="f-fixture-team-away" style="<?php echo esc_attr($team['teamID'] == $away_team['teamID'] ? 'font-weight:bold' : ""); ?>"><?php echo esc_html($away_team['nickName']); ?></span>
                                </td>
                                <td style="display: none"><?php echo esc_html($team['salary']);?></td>
                                <?php if(!$league['salary_cap_unlimited']): ?>
                                <td class="f-player-salary"><?php echo VIC_FormatMoney($team['salary']);?></td>
                                <?php endif;?>
                                <td class="f-player-add" style="width: 61px;">
                                    <a class="f-button f-tiny f-text f-player-add-button btn_add_lineup" id="btn_add_lineup_<?php echo esc_attr($team['teamID']);?>" data-id="<?php echo esc_attr($team['teamID']);?>">
                                        <i class="fa fa-plus-circle"></i>
                                    </a>
                                    <a class="f-button f-tiny f-text f-player-remove-button btn_delete_lineup" id="btn_delete_lineup_<?php echo esc_attr($team['teamID']);?>" data-id="<?php echo esc_attr($team['teamID']);?>" style="display:none">
                                        <i class="fa fa-minus-circle"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <section class="f-roster-container" data-role="team">
        <header>
            <div class="f-lineup-text-container">
                <h1><?php echo esc_html(__('Your lineup', 'victorious'));?></h1>
                <p class="f-lineup-lock-message">
                    <i class="fa fa-lock"></i> <?php echo esc_html(__('Locks @', 'victorious'));?> <?php echo VIC_DateTranslate($league['startDate']); ?>
                    <span class="f-game_status_open"></span>
                </p>
            </div>
            <?php if (!$league['salary_cap_unlimited']): ?>
            <div class="f-salary-remaining">
                <div class="f-salary-remaining-container" <?php if ($league['salary_cap_unlimited']): ?>style="width:100%"<?php endif; ?>>
                    <span class="f-salary-remaining-amount" id="salaryRemaining" data-value="<?php echo esc_attr($league['salary_remaining']);?>">
                        <?php if ($league['salary_cap_unlimited']): ?>
                            <?php echo esc_html(__('Unlimited', 'victorious'));?>
                        <?php else: ?>
                            <?php echo VIC_FormatMoney($league['salary_remaining'], "USD|$"); ?>
                        <?php endif; ?>
                    </span><?php echo esc_html(__('Salary Remaining', 'victorious'));?>
                </div>
                <div class="f-player-average-container">
                    <span class="f-player-average-amount" id="AvgTeam">
                        <?php if ($league['salary_remaining'] == 0): ?>
                            <?php echo esc_html(__('Unlimited', 'victorious'));?>
                        <?php endif; ?>
                    </span><?php echo esc_html(__('Avg/Team', 'victorious'));?>
                </div>
            </div>
            <?php endif; ?>
        </header>
        <section class="f-roster">
            <ul>
                <?php if ($lineups != null): ?>
                    <?php foreach ($lineups as $lineup): 
                        $team = !empty($lineup['team']) ? $lineup['team'] : array();
                    ?>
                        <li class="f-roster-position <?php echo esc_attr($team != null ? "filled" : "");?> lineup_<?php echo esc_attr($lineup['id']); ?>" data-id="<?php echo esc_attr($lineup['id']); ?>" data-no_duplicate_with="<?php echo esc_attr($lineup['no_duplicate_with']); ?>" data-team_id="<?php echo esc_attr($team != null ? $team['teamID'] : "");?>" data-team_salary="<?php echo esc_attr($team != null ? $team['salary'] : "");?>" data-lead_team="<?php echo esc_attr($lineup['lead_team']);?>">
                            <div class="f-player-image">
                                <?php if($team != null):?>
                                    <img src="<?php echo esc_url($team['image_url']);?>" onerror="jQuery.teamdraft.setNoImage(jQuery(this))"/>
                                <?php endif;?>
                            </div>
                            <div class="f-position">
                                <div><?php echo esc_html($lineup['name']); ?></div>
                                <div class="f-empty-roster-slot-instruction" style="<?php echo esc_attr($team != null ? 'display:none' : "");?>"><?php echo esc_html(__('Add team', 'victorious'));?></div>
                                <div class="f-player"><?php echo esc_attr($team != null ? esc_html($team['name']) : "");?></div>
                                <?php if(!$league['salary_cap_unlimited']): ?>
                                <div class="f-salary" style="<?php echo esc_attr($team != null ? 'visibility:visible' : "");?>"><?php echo esc_html($team != null ? VIC_FormatMoney($team['salary']) : "");?></div>
                                <?php endif;?>
                            </div>
                            <a class="f-button f-tiny f-text btn_remove_lineup" style="<?php echo esc_attr($team != null ? 'visibility:visible' : "");?>">
                                <i class="fa fa-minus-circle"></i>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
            <div class="f-row import-clear-button-container">
                <button class="f-button f-mini f-text f-right" id="btn_clear_all_lineup">
                    <small><i class="fa fa-minus-circle"></i> <?php echo esc_html(__('Clear all', 'victorious'));?></small>
                </button>
            </div>
        </section>
        <footer class="f-">
            <div class="f-contest-entry-fee-container">
                <form id="formLineup">
                    <div class="f-form_error"></div>
                    <input type="hidden" value="<?php echo esc_attr($league['leagueID']); ?>" name="league_id">
                    <input type="hidden" value="<?php echo esc_attr($entry_number); ?>" name="entry_number">
                    <input type="hidden" value="" name="lineup_ids" id="lineup_ids_value">
                    <input type="hidden" value="" name="team_ids" id="team_ids_value">
                </form>
            </div>
            <div class="f-contest-enter-button-container">
                <input type="button" id="btnSubmit" value="<?php echo esc_html(__('Enter', 'victorious'));?>" class="f-button f-jumbo f-primary">
            </div>
        </footer>
    </section>
</div>
<?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW . 'dlg_info.php');?>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery.teamdraft.initTeamDraft();
    })
</script>