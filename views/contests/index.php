<div class="wrap vc-wrap">

    <h2>

        <?php echo esc_html(__("Manage Contests", 'victorious'));?>

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

<div id="dlgPicks" style="display: none"></div>
<div id="dlgFeatureContest" style="display: none;"></div>
<div id="dlgUploadPhotResult" style="display: none">
    <div id="standing" style="margin-top: 5px;"></div>
    <div class="clear"></div>
    <div id="result" style="margin-top: 5px;"></div>
    <div class="clear"></div>
</div>
<?php require_once('dlg_info_friends.php');?>
<?php require_once(VICTORIOUS__PLUGIN_DIR_VIEW."Elements/qq_template.php");?>