<div id="dlgUserCredits" style="display: none;">
    <div id="msgUserCredits" class="public_message"></div>
    <form id="formUserCredits">
        <table>
            <tr>
                <td style="width: 170px"><?php echo esc_html(__("Name", 'victorious'));?></td>
                <td class="full_name">User</td>
            </tr>
        </table>
        <hr>
        <table>
            <?php if(!empty($balance_types)):?>
            <tr>
                <td>
                    <?php echo esc_html(__("Balance Type", 'victorious'));?>
                </td>
            </tr>
            <tr>
                <td>
                    <select class="form-control" name="balance_type_id" id="balance_type">
                        <?php foreach($balance_types as $balance_type):?>
                            <option value="<?php echo esc_html($balance_type['id']);?>">
                                <?php echo esc_html($balance_type['name']).' - '.esc_html($balance_type['symbol']);?>
                            </option>
                        <?php endforeach;?>
                    </select>
                </td>
            </tr>
            <?php endif;?>
            <tr>
                <td>
                    <?php echo esc_html(__("Amount", 'victorious'));?>
                    <input type="hidden" name="user_id" class="user_id" />
                </td>
            </tr>
            <tr>
                <td>
                    <input type="text" class="form-control" name="credits" size="50" />
                </td>
            </tr>
            <tr>
                <td><?php echo esc_html(__("Reason", 'victorious'));?></td>
            </tr>
            <tr>
                <td>
                    <textarea rows="5" cols="50" name="reason"></textarea>
                </td>
            </tr>
        </table>
    </form>
</div>