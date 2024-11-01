<div class="wrap vc-wrap">

    <h2>
        <?php echo esc_html(__("Manage Sports", 'victorious'));?>
        <a class="add-new-h2" href="<?php echo esc_url(self::$urladdnew);?>"><?php echo esc_html(__("Add New", 'victorious'));?></a>
        <a class="add-new-h2" onclick="jQuery.admin.showInfoManageSport();" href="#"><?php echo esc_html(__("Read me", 'victorious'));?></a>
    </h2>
    
    <?php echo settings_errors();?>
    <div>
        <?php if($aSports != null):?>
            <select class="form-control" onchange="window.location = '<?php echo esc_url(self::$url.'&id=');?>' + jQuery('#sport_filter').val()" id="sport_filter" style="float: right">
                <option value=""><?php echo esc_html(__("All sports", 'victorious'));?></option>
                <?php foreach($aSports as $aSport):?>
                    <?php if(!empty($aSport['child']) && is_array($aSport['child']) && $aSport['id'] != $motocross_id):?>
                        <option disabled="true"><?php echo esc_html($aSport['name']);?></option>
                        <?php foreach($aSport['child'] as $aOrg):?>
                            <option value="<?php echo esc_html($aOrg['id']);?>" <?php if(!empty($_GET['id']) && $_GET['id'] == $aOrg['id']):?>selected="selected"<?php endif;?>>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?php echo esc_html($aOrg['name']);?>
                            </option>
                        <?php endforeach;?>
                    <?php endif;?>
                <?php endforeach;?>
            </select>
        <?php endif;?>
        <div class="clearfix"></div>
    </div>
    <form name="adminForm" action="<?php echo esc_url(self::$url);?>" method="post">

        <input id="submitTask" type="hidden" name="task">

        <?php $myListTable->display_vc_table();?>

        <input type="button" value="<?php echo esc_html(__("Delete Selected"));?>" class="vc-button btn-red btn-size-sm btn-radius5 mt-3"  onclick="return jQuery.admin.action('', 'delete');">

    </form>

</div>

<div id="resultDialog" title="" style="display: none"></div>
<div id="readmeDialog" title="" style="display: none">
    <p>On this page, you will set up your sports and organization. A sport would be something like "Football" and an organization would be "NFL". An organization belongs to a sport.</p>
    <p>First you must create your sport. So you would type FOOTBALL and keep the drop down as ROOT. This will create the Sport Football. It will be an option added to the Sport dropdown.</p>
    <p>Now you MUST create an organization that belongs to that sport. In this example, you would type NFL and select FOOTBALL from the drop down. Now, you have created an organization under a sport.</p>
    <p>
        Set up a Fantasy Contest</br>
        As easy as: Add Sport</br>
        Add Organization to sport</br>
        Add Team</br>
        Add Positions</br>
        Add Players to team</br>
        Add Scoring Category</br>
        Add Event and Add Fixture</br>
        Add Contest based off event/Fixtures</br>
    </p>
</div>
