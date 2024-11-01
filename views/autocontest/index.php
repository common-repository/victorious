<div class="wrap vc-wrap">

    <h2>
        <?php echo esc_html(__("Manage Auto Contests", 'victorious'));?>
        <a class="add-new-h2" href="<?php echo self::$urladdnewautocontest;?>"><?php echo esc_html(__("Add New", 'victorious'));?></a>
    </h2>
    
    <?php echo settings_errors();?>
    <form name="adminForm" action="<?php echo esc_url(self::$urlmanageautocontest);?>" method="post">
		<input id="submitTask" type="hidden" name="task">
        <?php $myListTable->display_vc_table();?>
		<input type="button" value="<?php echo esc_html(__("Delete", 'FV-Bluejay'));?>" class="vc-button btn-red btn-size-sm btn-radius5 mt-3"  onclick="return jQuery.admin.action('', 'delete');">
    </form>

</div>
