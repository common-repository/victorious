<?php if(!empty($indicators)):?>
    <p><?php echo esc_html(__('Indicator legend', 'victorious'));?></p>
    <div class="vc-indicator-list">
        <?php foreach ($indicators as $indicator): ?>
            <?php
            $indicator_class = '';
            switch ($indicator['alias']) {
                case 'IR':
                    $indicator_class = 'vc-ir';
                    break;
                case 'O':
                    $indicator_class = 'vc-o';
                    break;
                case 'D':
                    $indicator_class = 'vc-d';
                    break;
                case 'Q':
                    $indicator_class = 'vc-q';
                    break;
                case 'P':
                    $indicator_class = 'vc-p';
                    break;
                case 'NA':
                    $indicator_class = 'vc-na';
                    break;
                case 'DtD':
                    $indicator_class = 'vc-dtd';
                case 'S':
                    $indicator_class = 'vc-s';
                    break;
                case 'St':
                    $indicator_class = 'vc-st';
                    break;
                case 'DL':
                    $indicator_class = 'vc-dl';
                    break;
            }
            ?>
            <div class="vc-indicator-item">
                <span class="vc-player-status mr-2 <?php echo esc_attr($indicator_class); ?>"><?php echo esc_html($indicator['alias']); ?></span>
                <?php echo esc_html($indicator['name']); ?>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif;?>