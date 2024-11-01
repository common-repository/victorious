<div id="team-stats" class="stats-tabs hub-header stats-table ng-scope">
    <div class="stats-cont" ng-hide="!dataLoaded" style="display: block;"> 
        <div class="skaters-wrapper">
            <div class="stats-table-top">
                <form id="form_filter_statistic">
                    <select class="stats-filter ng-pristine ng-untouched ng-valid" name="position_id">
                        <option value="">
                            <?php echo esc_html(__('All positions', 'victorious'));?>
                        </option>
                        <?php if($player_positions != null): ?>
                            <?php foreach($player_positions as $player_position):?>
                                <option value="<?php echo esc_attr($player_position['id']);?>" <?php if($player_position['id'] == $position_id):?>selected="selected"<?php endif;?>>
                                    <?php echo esc_html($player_position['name']);?>
                                </option>
                            <?php endforeach;?>
                        <?php endif;?>
                    </select>
                    <input class="stats-filter ng-pristine ng-untouched ng-valid" type="text" name="keyword" placeholder="<?php echo esc_html(__('Search name', 'victorious'));?>" value="<?php echo esc_attr($keyword);?>" />
                    <input type="button" value="<?php echo esc_html(__('Filter', 'victorious'));?>" onclick="jQuery.livescore.loadTeamStatistic(<?php echo esc_attr($team_id);?>)" />
                </form>
            </div>
            <?php if($players != null):?>
                <div class="stats-table-container fadeIn" style="margin-left: 180px;">
                    <table class="stats-table-scrollable double-header ng-scope kinetic-active ng-table">
                        <thead class="bottom-header ng-scope">
                            <tr class="ng-scope">
                                <th class="header stick col-1 sortable <?php echo esc_attr($sort_by == "player_name" ? "current_sortable" : "");?>" data-sortby="player_name" data-sorttype="<?php echo esc_attr($sort_by == "player_name" && $sort_type != "" ? $sort_type : "");?>" style="left: -180px; width: 180px;">
                                    <div class="ng-binding ng-scope"><?php echo esc_html(__('Name', 'victorious'));?></div>
                                </th>
                                <th class="header">
                                    <div class="ng-binding ng-scope"><?php echo esc_html(__('Team', 'victorious'));?></div>
                                </th>
                                <th class="header">
                                    <div class="ng-binding ng-scope"><?php echo esc_html(__('Pos', 'victorious'));?></div>
                                </th>
                                <?php if($scoring_categories != null): ?>
                                    <?php foreach($scoring_categories as $scoring_category):?>
                                    <th class="header sortable <?php echo esc_attr($sort_by == "scoring" && $sort_scoring_id == $scoring_category['id'] ? "current_sortable" : "");?>" data-sortby="scoring" data-sorttype="<?php echo esc_attr($sort_by == "scoring" && $sort_scoring_id == $scoring_category['id'] && $sort_type != "" ? $sort_type : "");?>" data-scoring_id="<?php echo esc_attr($scoring_category['id']);?>">
                                        <div class="ng-binding ng-scope">
                                            <?php echo esc_html($scoring_category['alias']);?>
                                        </div>
                                    </th>
                                    <?php endforeach;?>
                                <?php endif;?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($players != null):?>
                                <?php foreach($players as $k => $player):
                                    $position = $player['position'];
                                    $player_cats = $player['scoring_categories'];
                                    $team = $player['team'];
                                ?>
                                    <tr class="detail-cells ng-scope <?php echo esc_attr($k % 2 == 0 ? "odd" : "even");?>">
                                        <td class="ng-binding stick" style="left: -180px; width: 180px;">
                                            <span>
                                                <strong class="ng-binding">
                                                    <?php echo esc_html($player['name']);?>
                                                </strong>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?php echo VICTORIOUS_URL_LIVESCORE.'?detail=1&team_id='.$team['teamID'];?>" class="ng-binding" style="display:block;width: 150px;">
                                                <?php echo esc_html($team['name']);?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php echo esc_html($position['name']);?>
                                        </td>
                                        <?php if($player_cats != null):?>
                                            <?php foreach($player_cats as $player_cat):?>
                                                <td>
                                                    <?php echo esc_html($player_cat['real_points']);?>
                                                </td>
                                            <?php endforeach;?>
                                        <?php endif;?>
                                    </tr>
                                <?php endforeach;?>
                            <?php endif;?>
                        </tbody>
                    </table>
                </div>
                <?php if($players != null && $total_pages > 1):?>
                    <div class="paginationContainer">
                        <div class="ng-table-pager">
                            <ul id="player_stats_paging">
                            <?php for($i = 1; $i <= $total_pages; $i++):?>
                                <li <?php if($page == $i):?>class="disabled"<?php endif;?>>
                                    <a href="javascript:void(0)" <?php if($page != $i):?>onclick="jQuery.livescore.loadTeamStatistic(<?php echo esc_attr($team_id);?>, '<?php echo esc_attr($i);?>')"<?php endif;?>>
                                        <?php if($page == $i):?>
                                            <span><?php echo esc_html($i);?></span>
                                        <?php else:?>
                                            <?php echo esc_html($i);?>
                                        <?php endif;?>
                                    </a>
                                </li>
                            <?php endfor;?>
                            </ul>
                        </div>
                    </div>
                <?php endif;?>
            <?php else:?>
                <div style="margin-bottom:10px;"><?php echo esc_html(__('No player', 'victorious'));?></div>
            <?php endif;?>
        </div>
        <div class="legend-container fadein">
            <div class="stats-table-header">
                <h4><strong><?php echo esc_html(__('LEGEND', 'victorious'));?></strong></h4>
            </div>
            <table class="legend-chart">
                <tbody>
                    <?php if($scoring_categories != null): ?>
                        <?php foreach($scoring_categories as $k => $scoring_category):?>
                            <?php if($k == 0 || $k % 2 == 0):?>
                            <tr>
                            <?php endif;?>
                                <td><?php echo esc_html($scoring_category['alias']);?> - <?php echo esc_html($scoring_category['name']);?></td>
                            <?php if($k % 2 != 0 || $k == count($scoring_categories) - 1):?>
                            </tr>
                            <?php endif;?>
                        <?php endforeach;?>
                    <?php endif;?>
                </tbody>
            </table>
        </div>
    </div>
</div>