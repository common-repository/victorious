<?php 
if($league['is_live_draft']){
    include VICTORIOUS__PLUGIN_DIR_VIEW.'Elements/contest_live_draft.php';
}
else{
    include VICTORIOUS__PLUGIN_DIR_VIEW.'Elements/contest_normal.php';
}
?>