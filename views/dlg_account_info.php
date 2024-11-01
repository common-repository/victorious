<div id="dlgAccountInfo" style="display: none">
    <div id="msgAccountInfo" class="public_message"></div>
    <form id="formAccountInfo">
        <?php if(get_option('victorious_payout_method') == 'paypal'):?>
            <p>
                <?php echo esc_html(__('Gateway', 'victorious'));?>:<br/>
                <select name="val[gateway]">
                    <?php foreach($aGateways as $aGateway):?>
                    <option value="<?php echo esc_html($aGateway);?>" <?php if(isset($aUserPayment['gateway']) && $aUserPayment['gateway'] == $aGateway):?>selected=true"<?php endif;?>><?php echo esc_html($aGateway);?></option>
                    <?php endforeach;?>
                </select>
            </p>
            <p>
                <?php echo esc_html(__('Email', 'victorious'));?>:<br/>
                <input type="text" name="val[email]" size="60" value="<?php if(isset($aUserPayment['email'])):?><?php echo esc_html($aUserPayment['email']);?><?php endif;?>" />
            </p>
        <?php else:?>
            <p>
                <?php echo esc_html(__('Name', 'victorious'));?> (<?php echo esc_html(__('required', 'victorious'));?>):<br/>
                <input type="text" name="val[name]" size="60" value="<?php if(isset($aUserPayment['name'])):?><?php echo esc_html($aUserPayment['name']);?><?php endif;?>" />
            </p>
            <p>
                <?php echo esc_html(__('House/Deparment', 'victorious'));?>:<br/>
                <input type="text" name="val[house]" size="60" value="<?php if(isset($aUserPayment['house'])):?><?php echo esc_html($aUserPayment['house']);?><?php endif;?>" />
            </p>
            <p>
                <?php echo esc_html(__('Street', 'victorious'));?> (<?php echo esc_html(__('required', 'victorious'));?>):<br/>
                <input type="text" name="val[street]" size="60" value="<?php if(isset($aUserPayment['street'])):?><?php echo esc_html($aUserPayment['street']);?><?php endif;?>" />
            </p>
            <p>
                <?php echo esc_html(__('Unit number', 'victorious'));?>:<br/>
                <input type="text" name="val[unit_number]" size="60" value="<?php if(isset($aUserPayment['unit_number'])):?><?php echo esc_html($aUserPayment['unit_number']);?><?php endif;?>" />
            </p>
            <p>
                <?php echo esc_html(__('City', 'victorious'));?> (<?php echo esc_html(__('required', 'victorious'));?>):<br/>
                <input type="text" name="val[city]" size="60" value="<?php if(isset($aUserPayment['city'])):?><?php echo esc_html($aUserPayment['city']);?><?php endif;?>" />
            </p>
            <p>
                <?php echo esc_html(__('State/Provine', 'victorious'));?> (<?php echo esc_html(__('required', 'victorious'));?>):<br/>
                <input type="text" name="val[state]" size="60" value="<?php if(isset($aUserPayment['state'])):?><?php echo esc_html($aUserPayment['state']);?><?php endif;?>" />
            </p>
            <p>
                <?php echo esc_html(__('Country', 'victorious'));?> (<?php echo esc_html(__('required', 'victorious'));?>):<br/>
                <input type="text" name="val[country]" size="60" value="<?php if(isset($aUserPayment['country'])):?><?php echo esc_html($aUserPayment['country']);?><?php endif;?>" />
            </p>
        <?php endif;?>
    </form>
</div>