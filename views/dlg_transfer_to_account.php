<div id="dlgTransferToAccount" style="display: none">
    <form id="formTransferToAccount">
        <p>
            <?php echo esc_html(__('Amount', 'victorious'));?>:<br/>
            <input type="text" name="amount" />
        </p>
        <p>
            <?php echo esc_html(__('User', 'victorious'));?>:<br/>
            <input type="text" name="username" id="transfer_username" />
        </p>
        <?php if(!empty($global_setting['allow_multiple_balances']) && !empty($balance_types)):?>
            <p>
                <?php echo esc_html(__('Balance Type', 'victorious'));?>
                <select class="form-control" name="balance_type_id" id="balance_type">
                    <?php foreach($balance_types as $balance_type):?>
                        <option value="<?php echo esc_attr($balance_type['id']);?>">
                            <?php echo esc_html($balance_type['name']);?>
                        </option>
                    <?php endforeach;?>
                </select>
            </p>
        <?php endif;?>
    </form>
	<div id="msgTransferToAccount" class="public_message"></div>
</div>