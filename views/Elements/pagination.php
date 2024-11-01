<?php if (!empty($total_page) && $total_page > 1):
    $min = $current_page - 2 > 1 ? $current_page - 2 : 1;
    $max = $current_page + 2 < $total_page ? $current_page + 2 : $total_page;
    if($current_page == 1){
        $max += 2;
    }
    if($current_page == 2){
        $max += 1;
    }
    if($current_page == $total_page - 1){
        $min -= 1;
    }
    if($current_page == $total_page){
        $min -= 2;
    }
    if($min <= 1){
        $min = 1;
    }
    if($max >= $total_page){
        $max = $total_page;
    }

    $query_params = '';
    if(!empty($_GET)){
        $tmp = $_GET;
        if(isset($tmp['pg'])){
            unset($tmp['pg']);
        }
        $query_params = $tmp != null ? http_build_query($tmp) : '';
    }

    ?>
    <div id="pagination" class="vc-table-paginator text-center">
        <ul class="vc-pagination">
            <?php if($current_page > 1):?>
                <?php if(!empty($page_link)):?>
                    <li><a href="<?php echo esc_attr($page_link.'?pg='.($current_page - 1).'&'.$query_params);?>"><?php echo esc_html(__('Previous', 'victorious'));?></a></li>
                <?php else:?>
                    <li><a href="javascript:void(0)" data-page="<?php echo esc_attr($current_page - 1);?>"><?php echo esc_html(__('Previous', 'victorious'));?></a></li>
                <?php endif;?>
            <?php endif;?>
            <?php for ($page = $min; $page <= $max; $page++):
                $class_active = '';
                if ($current_page == $page)
                {
                    $class_active = 'active';
                }
                ?>
                <?php if(!empty($page_link)):?>
                <li class="<?php echo esc_html($class_active);?>"><a href="<?php echo esc_attr($page_link.'?pg='.$page.'&'.$query_params);?>"><?php echo esc_html($page);?></a></li>
            <?php else:?>
                <li class="<?php echo esc_html($class_active);?>"><a href="javascript:void(0)" data-page="<?php echo esc_html($page);?>"><?php echo esc_html($page);?></a></li>
            <?php endif;?>
            <?php endfor;?>
            <?php if($current_page < $total_page):?>
                <?php if(!empty($page_link)):?>
                    <li><a href="<?php echo esc_url($page_link.'?pg='.($current_page + 1).'&'.$query_params);?>"><?php echo esc_html(__('Next', 'victorious'));?></a></li>
                <?php else:?>
                    <li><a href="javascript:void(0)" data-page="<?php echo esc_attr($current_page + 1);?>"><?php echo esc_html(__('Next', 'victorious'));?></a></li>
                <?php endif;?>
            <?php endif;?>
        </ul>
    </div>
<?php endif;?>