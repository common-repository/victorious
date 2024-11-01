<div class="info mb-4">
    <div class="vc-tabpane-container-header mt-0">
        <?php echo esc_html(__('Victorious info:', 'victorious'));?>
    </div> 
    <div class="vc-tabpane-container">
        <div class="vc-tabpane-item">
            <div class="vc-tabpane-item-l">
                <?php echo esc_html(__('Your IP', 'victorious'));?>
            </div>
            <div class="vc-tabpane-item-r">
                <div class="description">
                    <?php 
                        $ipaddress = '';
                        if (getenv('HTTP_CLIENT_IP'))
                            $ipaddress = getenv('HTTP_CLIENT_IP');
                        else if(getenv('HTTP_X_FORWARDED_FOR'))
                            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
                        else if(getenv('HTTP_X_FORWARDED'))
                            $ipaddress = getenv('HTTP_X_FORWARDED');
                        else if(getenv('HTTP_FORWARDED_FOR'))
                            $ipaddress = getenv('HTTP_FORWARDED_FOR');
                        else if(getenv('HTTP_FORWARDED'))
                            $ipaddress = getenv('HTTP_FORWARDED');
                        else if(getenv('REMOTE_ADDR'))
                            $ipaddress = getenv('REMOTE_ADDR');
                        else
                            $ipaddress = 'UNKNOWN';

                        echo esc_html($ipaddress);
                        ?>
                </div>
            </div>
        </div>
        <div class="vc-tabpane-item">
            <div class="vc-tabpane-item-l">
                <?php echo esc_html(__('Your Api Token', 'victorious'));?>
            </div>
            <div class="vc-tabpane-item-r">
                <div class="description">
                    <?php 
                        echo get_option('victorious_api_token');
                    ?>
                </div>
            </div>
        </div>
        <div class="vc-tabpane-item">
            <div class="vc-tabpane-item-l">
                <?php echo esc_html(__('Victorious shop', 'victorious'));?>
            </div>
            <div class="vc-tabpane-item-r">
                <a style="color: #0073aa;" href="http://victorious.club/shop/">http://victorious.club/shop/</a>
            </div>
        </div>
        <div class="vc-tabpane-item">
            <div class="vc-tabpane-item-l">
                <?php echo esc_html(__('Support portal', 'victorious'));?>
            </div>
            <div class="vc-tabpane-item-r">
                <a style="color: #0073aa;" href="http://victorious.club/support/">http://victorious.club/support/</a>
            </div>
        </div>
        <input type="button" value="<?php echo esc_html(__('Test connection to the Victorious servers', 'victorious'));?>" class="vc-button btn-blue btn-size-md btn-radius5 text-uppercase font-weight-normal" onclick="return jQuery.admin.testConnection(this);">
    </div> 
</div>

<div class="info mb-4">    
    <div class="vc-tabpane-container-header mt-0">
        <?php echo esc_html(__('Available Sports', 'victorious'));?>
    </div> 
    <div class="vc-tabpane-container"> 
        <?php
            if(!empty($list_sport)){
                $xhtml = '<div>';
                foreach ($list_sport as $item) {
                    $checked = ( ($item['is_active'] == 1) ? 'checked="checked"' : '' );
                    $xhtml .= '<div class="sport-parent">';
                    $xhtml .= '<h3 class="vc-tabpane-title">'.esc_html($item['name']).'</h3>';
                    $xhtml .= '</div>';

                    if(count($item['child']) > 0){
                        $xhtml .= '<div class="list-sport">';
                        foreach ($item['child'] as $val) {
                            $checked = ( ($val['is_active'] == 1) ? 'checked' : '' );
                            $xhtml .= '<div class="list-sport-item">';
                            $xhtml .= '<div class="list-sport-item-content">';
                            $xhtml .= '<input type="checkbox" name="'.esc_attr($val['id']).'" '.$checked.'>';
                            $xhtml .= '<label>|-- '.esc_html($val['name']).'</label>';
                            if($checked == ''){
                                $xhtml .= '<span class="above_checkbox" onclick="jQuery.admin.showInfoContactSport();"></span>';
                            }else{
                                $xhtml .= '<span class="above_checkbox"></span>';
                            }                    
                            $xhtml .= '</div></div>';
                        }
                        $xhtml .= '</div>';
                    }

                    $xhtml .= '<hr>';
                }
                $xhtml .= '</div>';
                echo $xhtml;
            }
        ?>
        <p><?php echo sprintf(esc_html(__('To get more data feeds please go to %s', 'victorious')), '<a style="color: #0073aa;" href="http://victorious.club/product-category/sport-feeds">http://victorious.club/product-category/sport-feeds</a>');?></p>
    </div> 
</div>

<div class="info">
    <div class="vc-tabpane-container-header mt-0">
        <?php echo esc_html(__('Available Premium Features', 'victorious'));?>
    </div> 
    <div class="vc-tabpane-container"> 
        <?php if(!empty($list_premium_feature)):?>
            <?php foreach($list_premium_feature as $key => $premium_feature):?>
            <div class="vc-dashboard-item border-white pb-0">
                <h3 class="vc-tabpane-title d-inline-block mt-0 w-200">
                    <?php echo esc_html($premium_feature);?>
                </h3>
                <div style="position: relative;" class="d-inline-block">
                    <input type="checkbox" name="val[<?php echo esc_attr($key);?>]" <?php if(!empty($site_setting[$key]) && $site_setting[$key]):?>checked="true"<?php endif;?> value="1" />
                    <div class="above_checkbox"  <?php if(empty($site_setting[$key])):?> onclick="jQuery.admin.showInfoContactSport2();"<?php endif;?>></div>
                </div>
            </div>
            <?php endforeach;?>
        <?php endif;?>
        <p><?php echo sprintf(esc_html(__('All premium features can me purchased at %s', 'victorious')), '<a style="color: #0073aa;" href="https://victorious.club/shop/plug-ins/victorious-club-premiumfeatures">https://victorious.club/shop/plug-ins/victorious-club-premiumfeatures</a>');?></p>
    </div> 
</div>

<style type="text/css" media="screen">
    #tabs{
        background: transparent;
    }
    .sport-parent{
        padding: 5px 0;
    }

    .sport-parent label{
        display: inline-block;
        width: 20%;
    }

    .sport-child{
        position: relative;
        padding: 5px 0;
    }

    .sport-child label{
        margin-left: 1%;
        display: inline-block;
        width: 19%;
    }

    .form-table th{
        padding: 0;
        width: 20%;
    }

    .form-table td{
        padding: 5px;
    }

    #tabs .ui-widget-header{
        background-color: transparent;
    }

    .ui-widget-content{
        border: 0 !important;
    }

    .ui-tabs .ui-tabs-panel{
        padding: 10px 0 !important;
    }

    .above_checkbox{
        width: 25px;
        height: 25px;
        position: absolute;
        top: 0;
        left: 0;
    }
</style>

<script type="text/javascript">
    jQuery(window).load(function(){
        jQuery('.list-sport input[type=checkbox]:checked').attr('disabled', 'disabled');
    })
</script>