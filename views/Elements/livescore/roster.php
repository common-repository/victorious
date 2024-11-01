<div class="stats-table ng-scope" id="team-roster">
    <div class="team-roster">
        <div class="stats-table-header">
            <h4>
                <strong>
                    <?php echo esc_html(__("PLAYERS", "victorious"));?>
                </strong>
            </h4>
        </div>
        <?php if($players != null):?>
            <div class="stats-table-container" style="margin-left: 0;">
                <table cellpadding="0" cellspacing="0" border="0" class="double-header ng-scope kinetic-active ng-table stats-table-full"style="cursor: default; opacity: 1;">
                    <thead class="bottom-header ng-scope">
                        <tr class="ng-scope">
                            <th class="ng-binding ng-scope sortable <?php echo esc_attr($sort_by == "player_name" ? "current_sortable" : "");?>" data-sortby="player_name" data-sorttype="<?php echo esc_attr($sort_by == "player_name" && $sort_type != "" ? $sort_type : "");?>" style="width:60%">
                                <div class="ng-binding ng-scope"><?php echo esc_html(__("Name", "victorious"));?></div>
                            </th>
                            <th class="ng-binding ng-scope sortable <?php echo esc_attr($sort_by == "player_number" ? "current_sortable" : "");?>" data-sortby="player_number" data-sorttype="<?php echo esc_attr($sort_by == "player_number" && $sort_type != "" ? $sort_type : "");?>" style="width:10%">
                                <div class="ng-binding ng-scope"><?php echo esc_html(__("No", "victorious"));?></div>
                            </th>
                            <th  class="ng-binding ng-scope sortable <?php echo esc_attr($sort_by == "player_age" ? "current_sortable" : "");?>" data-sortby="player_age" data-sorttype="<?php echo esc_attr($sort_by == "player_age" && $sort_type != "" ? $sort_type : "");?>" style="width:10%">
                                <div class="ng-binding ng-scope"><?php echo esc_html(__("Age", "victorious"));?></div>
                            </th>
                            <th class="ng-binding ng-scope " style="width:10%">
                                <div class="ng-binding ng-scope"><?php echo esc_html(__("Pos", "victorious"));?></div>
                            </th>
                            <th class="ng-binding ng-scope sortable <?php echo esc_attr($sort_by == "player_salary" ? "current_sortable" : "");?>" data-sortby="player_salary" data-sorttype="<?php echo esc_attr($sort_by == "player_salary" && $sort_type != "" ? $sort_type : "");?>" style="width:10%">
                                <div class="ng-binding ng-scope"><?php echo esc_html(__("Salary", "victorious"));?></div>
                            </th>
                        </tr> 
                    </thead>
                    <tbody>
                        <?php foreach ($players as $k => $player):
                            $position = $player['position'];
                        ?>
                        <tr class="detail-cells ng-scope <?php echo esc_attr($k % 2 == 0 ? "odd" : "even");?>">
                            <td class="ng-binding">
                                <span>
                                    <strong>
                                            <?php echo esc_html($player['name']);?>
                                    </strong>
                                </span>
                            </td>
                            <td class="ng-binding">
                                <?php echo esc_html($player['player_number']);?>
                            </td>
                            <td class="ng-binding">
                                <?php echo esc_html($player['age']);?>
                            </td>
                            <td class="ng-binding">
                                <?php echo esc_html($position['name']);?>
                            </td>
                            <td lass="ng-binding">
                                <?php echo VIC_FormatMoney($player['salary']);?>
                            </td>
                        </tr>
                        <?php endforeach;?>
                    </tbody>
                </table>
            </div>
            <div class="legend-container">
                <div class="stats-table-header">
                    <h4><strong><?php echo esc_html(__("LEGEND", "victorious"));?></strong></h4>
                </div>
                <table class="legend-chart">  
                    <tbody>
                        <tr>
                            <td><?php echo esc_html(__("No", "victorious"));?> - <?php echo esc_html(__("Number", "victorious"));?></td>
                            <td><?php echo esc_html(__("Age", "victorious"));?> - <?php echo esc_html(__("Age", "victorious"));?></td>
                        </tr>
                        <tr>
                            <td><?php echo esc_html(__("Pos", "victorious"));?> - <?php echo esc_html(__("Position", "victorious"));?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php else:?>
            <div class="no-data-found">
                <?php echo esc_html(__("The information you requested is not available at this time, please check back again soon.", "victorious"));?>
            </div>
        <?php endif;?>
    </div>
</div>