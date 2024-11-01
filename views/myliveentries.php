<script type="text/javascript">
    jQuery.league.loadLiveEntries();
    setInterval(function() { jQuery.league.loadLiveEntries() }, 60000);
</script>

<?php VIC_GetMessage();?>

<div id="main" class="site-main site-info">
    <div id="primary" class="content-area">
        <div id="content" class="site-content" role="main">
            <article class="hentry">
                <div class="vc-section p-3">
                    <div class="vc-header">
                        <div class="vc-header-left">
                            <h3 class="vc-title"><?php echo esc_html(__("My Live Entries", 'victorious'));?></h3>
                        </div>
                    </div>
                    <div class="vc-table">
                        <table cellspacing="0" cellpadding="0">
                            <thead>
                                <tr>
                                    <th style="width: 6%"><?php echo esc_html(__('ID', 'victorious'))?></th>
                                    <th style="width: 13%"><?php echo esc_html(__('Date', 'victorious'))?></th>
                                    <th style="width: <?php echo get_option('victorious_no_cash') == 0 ? '34%' : '50%';?>"><?php echo esc_html(__('Name', 'victorious'))?></th>
                                    <th style="width: 8%"><?php echo esc_html(__('Entries', 'victorious'))?></th>
                                    <?php if(get_option('victorious_no_cash') == 0):?>
                                        <th style="width: 10%"><?php echo esc_html(__('Entry Fee', 'victorious'))?></th>
                                        <th style="width: 7%"><?php echo esc_html(__('Prizes', 'victorious'))?></th>
                                    <?php endif;?>
                                    <th style="width: 6%"><?php echo esc_html(__('Rank', 'victorious'))?></th>
                                    <th style="width: 16%">&nbsp;</th>
                                </tr>
                            </thead>
                            <tbody id="tableLiveEntriesContent"></tbody>
                        </table>
                    </div>
                </div>
            </article>
        </div>
    </div>
</div>

<div id="entryModal" data-backdrop="static" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <iframe id="iframeEntry" src="" width="99.6%" height="100%" frameborder="0"></iframe>
            </div>

        </div>
    </div>
</div>