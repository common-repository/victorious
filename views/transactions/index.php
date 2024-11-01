<div class="wrap vc-wrap">

    <h2><?php echo esc_html(__("Manage Transactions", 'victorious'));?></h2>

    <?php echo settings_errors();?>

    <form method="get">

        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']);?>" />

        <?php $myListTable->search_box('search', 'search_id'); ?>

    </form>

    <?php $myListTable->display_vc_table();?>

</div>

<div id="dlgStatistic" style="display: none"><center><?php echo esc_html(__("Loading...Please wait!", 'victorious'));?></center></div>