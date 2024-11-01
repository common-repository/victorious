<div class="wrap vc-wrap">

    <h2><?php echo esc_html(__("Event Statistics", 'victorious'));?></h2>

    <?php echo settings_errors();?>

    <form method="get">

        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']);?>" />

        <?php $myListTable->search_box('search', 'search_id'); ?>

    </form>

    <?php $myListTable->display_vc_table();$data = $myListTable->getData()?>
    <div class="vc-tabpane-container p-3 mt-1 rounded text-right f-14">

        <b><?php echo esc_html(__("Total Cash Processed", 'victorious'));?>:</b>

        <?php echo esc_html($data['accumCash']);?>

        <br>

        <b>Total Pay Out:</b>

        <?php echo esc_html($data['accumPayOut']);?>

        <br>

        <b>Total Profit:</b>

        <?php echo esc_html($data['accumProfit']);?>

    </div>
    
</div>

<div id="dlgStatistic" style="display: none"><center><?php echo esc_html(__("Loading...Please wait!", 'victorious'));?></center></div>