<?php if(!empty($indicators)):?>
    <ul>
        <?php foreach ($indicators as $indicator): ?>
            <?php
            $indicator_class = '';
            switch ($indicator['alias']) {
                case 'IR':
                    $indicator_class = 'f-player-badge f-player-badge-injured-out';
                    break;
                case 'O':
                    $indicator_class = 'f-player-badge f-player-badge-injured-out';
                    break;
                case 'D':
                    $indicator_class = 'f-player-badge f-player-badge-injured-possible';
                    break;
                case 'Q':
                    $indicator_class = 'f-player-badge f-player-badge-injured-possible';
                    break;
                case 'P':
                    $indicator_class = 'f-player-badge f-player-badge-injured-probable';
                    break;
                case 'NA':
                    $indicator_class = 'f-player-badge f-player-badge-injured-out';
                    break;
                case 'DtD':
                    $indicator_class = 'f-player-badge f-player-badge-injured-possible';
                case 'S':
                    $indicator_class = 'f-player-badge f-player-badge-injured-possible';
                    break;
                case 'St':
                    $indicator_class = 'f-player-badge f-player-badge-injured-possible';
                    break;
                case 'DL':
                    $indicator_class = 'f-player-badge f-player-badge-injured-possible';
                    break;
            }
            ?>
            <li>
                <span class="<?php echo esc_attr($indicator_class); ?>">
                    <?php echo esc_html($indicator['alias']); ?>
                </span> 
                <?php echo esc_html($indicator['name']); ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif;?>