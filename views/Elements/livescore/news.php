<?php if($news != null):?>
<ul class="feed non-stack  hide-lead ">
    <?php foreach($news as $item):?>
        <li class="3 feed-item index-2">                
            <article class="article-feed promo-image-related">
                <div class="headline">
                    <a href="<?php echo esc_attr($item['link']);?>">
                        <div class="media">
                            <img title="<?php echo esc_html($item['title']);?>" height="135" alt="<?php echo esc_attr($item['title']);?>" width="240" align="" src="<?php echo esc_attr($item['image']);?>">
                        </div>
                    </a>
                    <div class="article-content">                                
                        <a href="<?php echo esc_attr($item['link']);?>">
                            <h3>
                                <?php echo esc_html($item['title']);?>
                            </h3>
                        </a>
                        <p class="lead">
                            <?php echo esc_html($item['brief']);?>
                        </p>
                    </div>
                </div>
            </article>
        </li>
    <?php endforeach;?>
</ul>
<?php echo esc_html($paging);?>
<?php endif;?>