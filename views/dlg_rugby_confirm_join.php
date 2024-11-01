<div class="f-lightbox f-legacy-lightbox" id="dlgRugbyConfirmJoin" style="display: none;">
    <div class="f-modal f-background"></div>
    <div class="f-outer">
        <div class="f-inner">
            <header style="display: none;">
                <h2><?php echo esc_html(__('Join contest', 'victorious'));?></h2>
            </header>
            <div class="f-body" style="">
                <p>
                    <?php echo esc_html(__('Are you sure you would like to join this contest. This contest will allow draft player on <span id="live_draft_time"></span>. We will send you an email when draft time starts.', 'victorious'));?>
                </p>
                <a class="f-button f-primary f-left btn_ok" href="javascript:void(0)" style="margin-right: 10px;">
                    <?php echo esc_html(__('Ok', 'victorious'));?>
                </a>
                <a class="f-button f-primary f-left" onclick="return jQuery.playerdraft.closeDialog('#dlgRugbyConfirmJoin')" href="javascript:void(0)">
                    <?php echo esc_html(__('Cancel', 'victorious'));?>
                </a>
                <div class="clear"></div>
            </div>
        </div>
        <a class="f-close" onclick="return jQuery.playerdraft.closeDialog('#dlgRugbyConfirmJoin')" href="javascript:void(0)" style="margin-left: -501px;"></a>
    </div>
</div>