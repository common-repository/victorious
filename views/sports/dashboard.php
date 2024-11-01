<script>
    jQuery(document).ready(function($) {
        jQuery( "#tabs").tabs();
    }); 
</script>

<div class="wrap vc-wrap-settings">
    <div class="vc-header bg-transparent">
        <div class="vc-header-left">
            <!-- <div class="logo-admin">
                <img src="/custom_layout/assets/images/logo.png" alt="">
            </div> -->
            <h3 class="vc-title-admin"><?php echo esc_html(__('Victorious Dashboard', 'victorious'));?></h3>
            <p class="vc-des mb-0"><?php echo esc_html(__('Ip', 'victorious')).': '.VIC_IpAddress();?> | <?php echo esc_html(__('Token', 'victorious')).': '.get_option('victorious_api_token');?></p>
        </div>
        <div class="vc-header-right">
            <a href="#" class="color-blue">
                Victorious shop
            </a>
            |
            <a href="#" class="color-blue">
                Support portal
            </a>
        </div>
    </div>
    <form method="post" id="tabs">
    <?php settings_fields( 'victorious-settings-group' ); ?>
        <?php do_settings_sections( 'victorious-settings-group' ); ?>
        <div class="vc-admin-tabs">
            <h3 class="vc-title-admin vc-title-admin-tab"><?php echo esc_html(__('Dashboard', 'victorious'));?></h3>
            <ul class="nav nav-tabs">
                <li class="nav-item"><a class="nav-link" href="#tabs-1"><?php echo esc_html(__('Site Stats', 'victorious'));?></a></li>
                <li class="nav-item"><a class="nav-link" href="#tabs-2"><?php echo esc_html(__('Dashboard', 'victorious'));?></a></li>
            </ul>
        </div>

        <div id="tabs-1">
            <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW . 'Elements/dashboard_stats.php'); ?>
        </div>
        <div id="tabs-2">
            <?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW . 'Elements/dashboard_dashboard.php'); ?>
        </div> 
    </form>
</div>

<div id="contactmeDialog" title="" style="display: none">
    <p>
        <?php echo esc_html(__("Please contact our Support Team at support@victorious.club to enable this sport. <br> To get more data feeds please go to: http://victorious.club/product-category/sport-feeds", 'victorious')); ?>
    </p>
</div>

<div id="contactmeDialog2" title="" style="display: none">
    <p>
        <?php echo esc_html(__("Please contact our Support Team at support@victorious.club to enable this feature. <br> See more Premium product at: http://victorious.club/shop/add-ons/fan-victor-premium", 'victorious')); ?>
    </p>
</div>

