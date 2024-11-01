<?php if(empty(VIC_GetUserId())):?>
    <?php echo esc_html(__("Please login to view this page", "victorious"));?>
<?php else:?>
    <div class="stats-table ng-scope" id="scores">
        <div class="container">
            <div class="stats-table-top">
                <?php if($gameType != null):?>
                    <select name="gameType" id="gameType" class="stats-filter">
                        <?php foreach($gameType as $type): ?>
                            <option value="<?php echo esc_attr($type['gameType']) ?>"><?php echo esc_html($type['gameType']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="league" id="league" class="stats-filter">
                        <option value=""><?php echo esc_html(__("Select Contest", "victorious"));?></option>
                    </select>
                    <select name="city" id="city" class="stats-filter">
                        <option value=""><?php echo esc_html(__("All Countries", "victorious"));?></option>
                        <?php foreach($citys as $k => $city): ?>
                        <option value="<?php echo esc_attr($k) ?>"><?php echo esc_html($city) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <span id="daily_events"></span>
                <?php else:?>
                    <?php echo esc_html(__("No game types", "victorious"));?>
                <?php endif;?>
            </div>
        </div>
    </div>
    <br/>
    <div class="content-main" id="team_detail">

    </div>

    <?php if($gameType != null):?>
        <script>
            jQuery(window).load(function(){
                loadContestByGameType();
            });
        </script>
    <?php endif;?>
<?php endif;?>