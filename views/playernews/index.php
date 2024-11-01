<div class="wrap vc-wrap">
    <h2>
        <?php echo esc_html(__("Manage Player News", 'victorious'));?>
        <a class="add-new-h2" href="<?php echo esc_url(self::$urladdnew);?>"><?php echo esc_html(__("Add New", 'victorious'));?></a>
    </h2>
    <?php echo settings_errors();?>
    <form method="get">
        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']);?>" />
        <?php $myListTable->search_box('search', 'search_id'); ?>
    </form>
    <form name="adminForm" action="<?php echo esc_url(self::$url);?>" method="post">
        <input id="submitTask" type="hidden" name="task">
        <?php $myListTable->display_vc_table();?>
        <input type="button" value="<?php echo esc_html(__("Delete Selected"));?>" class="vc-button btn-red btn-size-sm btn-radius5 mt-3"  onclick="return jQuery.admin.action('', 'delete');">
    </form>
</div>
<div id="resultDialog" title="" style="display: none"></div>