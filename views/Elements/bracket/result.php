<?php if($scores != null):?>
    <table class="f-condensed">
        <thead>
            <tr>
                <th style="width:5%;"></th>
                <th class="f-text-align-left"><?php echo esc_html(__('User', 'victorious'));?></th>
                <?php if ($league['multi_entry'] == 1): ?>
                    <th style="width:7%;"><?php echo esc_html(__('Entry', 'victorious'));?></th>
                <?php endif; ?>
                <th style="width:10%;"><?php echo esc_html(__('Score', 'victorious'));?></th>
                <?php if (get_option('victorious_no_cash') == 0): ?>
                    <th style="width:12%;"><?php echo esc_html(__('Prizes', 'victorious'));?></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach($scores as $score):?>
                <tr id="user_result_item_<?php echo esc_attr($score['id']);?>" class="user_result_item <?php if($score['current']):?>f-user-highlight<?php endif;?>" href="javascript:void(0)" data-user_id="<?php echo esc_attr($score['userID']);?>" data-entry_number="<?php echo esc_attr($score['entry_number']);?>" >
                    <td style="text-align:center">
                        <?php echo esc_html($score['rank'] > 0 ? $score['rank'] : "-");?>
                    </td>
                    <td>
                        <div style="background-image: url('<?php echo esc_url($score['avatar']);?>')" class="f-avatar">
                        </div>
                        <a class="f-truncate">
                            <?php echo esc_html($score['username']);?>
                        </a>
                    </td>
                    <?php if($league['multi_entry']):?>
                        <td class="f-text-align-center">
                            <?php echo esc_html($score['entry_number']);?>
                        </td>
                    <?php endif;?>
                    <td class="f-text-align-center">
                        <?php echo esc_html($score['points']);?>
                    </td>
                    <?php if($no_cash == 0):?>
                        <td class="f-text-align-center">
                            <?php echo esc_html($score['amount']);?>
                        </td>
                    <?php endif;?>
                </tr>
            <?php endforeach;?>
        </tbody>
    </table>
        
    <div id="paging_ranking" class="pg-wrapper">
        <?php if ($total_page > 1):?>
            <?php for ($page = 1; $page <= $total_page; $page++):
                $class_active = '';
                if ($current_page == $page)
                {
                    $class_active = 'active';
                }
            ?>
                <div class="dib <?php echo esc_attr($class_active);?>" onclick="jQuery.bracket.loadResult('<?php echo esc_attr($page);?>')">
                    <?php echo esc_html($page);?>
                </div>
            <?php endfor;?>
        <?php endif;?>
    </div>
<?php endif; ?>
