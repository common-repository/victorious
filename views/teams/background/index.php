<div class="wrap">    <h2>        <?php echo esc_html(__("Manage Teams Background", 'victorious'));?>    </h2>    <?php echo settings_errors();?>    <form method="get">        <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']);?>" />        <?php $myListTable->search_box('search', 'search_id'); ?>    </form>    <form name="adminForm" action="" method="post" enctype="multipart/form-data">        <input id="submitTask" type="hidden" name="task">        <?php $myListTable->display();?>        <input type="submit" value="<?php echo esc_html(__('Upload','victorious')) ?>" name="action_upload" class="button button-primary">    </form></div><div id="resultDialog" title="" style="display: none"></div>