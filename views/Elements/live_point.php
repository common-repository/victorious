<?php //echo "<pre>";print_r($livePoints);echo "</pre>"; ?>
<div id="team-stats" class="stats-tabs hub-header stats-table ng-scope">
    <div class="stats-cont" ng-hide="" style="display: block;">
       <div class="skaters-wrapper">
          <div class="stats-table-container fadeIn">
             <table class="double-header ng-scope kinetic-active ng-table">
                <thead class="bottom-header ng-scope">
                   <tr class="ng-scope" id="header">
                        <th class="header stick col-1 sortable <?php if($sort['name'] == "full_name"){ echo esc_attr($sort['desc'] ==1?" up-icon":" down-icon"); } ?>"
                            data-sorttype="<?php echo (($sort['name'] == "full_name")&&($sort['desc'] ==1))?"full_name":"full_name DESC" ?>">
                           <div class="ng-binding ng-scope">Name</div>
                        </th>
                        <th class="header stick col-1 sortable <?php if($sort['name'] == "entry_number"){ echo esc_attr($sort['desc'] ==1?" up-icon":" down-icon"); } ?>"
                            data-sorttype="<?php echo (($sort['name'] == "entry_number")&&($sort['desc'] ==1))?"entry_number":"entry_number DESC" ?>">
                           <div class="ng-binding ng-scope">number_entry</div>
                        </th>
                        <th class="header stick col-1 sortable <?php if($sort['name'] == "points"){ echo esc_attr($sort['desc'] ==1?" up-icon":" down-icon"); } ?> "
                            data-sorttype="<?php echo (($sort['name'] == "points")&&($sort['desc'] ==1))?"points":"points DESC" ?>">
                           <div class="ng-binding ng-scope">Point</div>
                        </th>
                   </tr>
                </thead>
                <tbody>
                    <?php foreach($scores as $k => $v): ?>
                    <tr class="detail-cells ng-scope odd <?php echo ($k % 2 == 0)?"even":"odd" ?>">
                        <td class="ng-binding stick" style="text-align: center">
                           <?php echo esc_html($v['full_name']) ?>
                        </td>
                        <td class="ng-binding stick">
                           <?php echo esc_html($v['entry_number']) ?>
                        </td>
                        <td class="ng-binding stick">
                           <?php echo esc_html($v['points']) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
             </table>
          </div>
          <div class="paginationContainer">
             <div class="ng-table-pager">
                <ul id="player_stats_paging">
                    <?php for($i = 1;$i<= $pages;$i++): ?>
                    <li>
                        <a class="pa <?php echo ($i==$_POST['page'])?'disabled':'' ?>" href="javascript:void(0)" data-page="<?php echo esc_attr($i)?>"
                            <?php echo ($i==$_POST['page'])?'style="color:#333"':'' ?> >
                            <?php echo esc_html($i) ?>
                        </a>
                    </li>
                    <?php endfor; ?>
                </ul>
             </div>
          </div>
       </div>
    </div>
 </div>