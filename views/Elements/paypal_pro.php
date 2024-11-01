<input type="hidden" name="gateway" value="<?php echo VICTORIOUS_GATEWAY_PAYPAL_PRO;?>" /><h3><?php echo esc_html(__('Card information', 'victorious'));?></h3><div class="row">    <div class="col-md-6">        <p>            <?php echo esc_html(__('First name on card', 'victorious'));?>:<br/>            <input type="text" name="first_name"/>        </p>    </div>    <div class="col-md-6">        <p>            <?php echo esc_html(__('Address', 'victorious'));?>:<br/>            <input type="text" name="street"/>        </p>    </div></div><div class="row">    <div class="col-md-6">        <p>            <?php echo esc_html(__('Last name on card', 'victorious'));?>:<br/>            <input type="text" name="last_name"/>        </p>    </div>    <div class="col-md-6">    </div></div><div class="row">    <div class="col-md-6">        <p>            <?php echo esc_html(__('Credit card number', 'victorious'));?>:<br/>            <input type="text" name="credit_card_number" />        </p>    </div>    <div class="col-md-6">        <p>            <?php echo esc_html(__('City', 'victorious'));?>:<br/>            <input type="text" name="city"/>        </p>    </div></div><div class="row">    <div class="col-md-6">        <p>            <?php echo esc_html(__('Credit card type', 'victorious'));?>:<br/>            <select name="credit_card_type">                <option value="">                    <?php echo esc_html(__('Select', 'victorious'));?>                </option>                <option value="visa">                    <?php echo esc_html(__('Visa', 'victorious'));?>                </option>                <option value="mastercard">                    <?php echo esc_html(__('Master card', 'victorious'));?>                </option>                <option value="americanexpress">                    <?php echo esc_html(__('American Express', 'victorious'));?>                </option>                <option value="discover">                    <?php echo esc_html(__('Discover', 'victorious'));?>                </option>            </select>        </p>    </div>    <div class="col-md-6">        <p>            <?php echo esc_html(__('Country', 'victorious'));?>:<br/>            <select name="countrycode">                <option value=""><?php echo esc_html(__('Select', 'victorious'));?></option>                <?php foreach($country_list as $code => $country):?>                    <option value="<?php echo esc_html($code);?>"><?php echo esc_html($country);?></option>                <?php endforeach;?>            </select>        </p>    </div></div><div class="row">    <div class="col-md-6">        <p class="pull-left">            <?php echo esc_html(__('Expire date', 'victorious'));?>:<br/>            <select name="expire_month">                <?php for($i = 1; $i <= 12; $i++):?>                    <option value="<?php echo esc_attr($i < 10 ? '0' : '');?><?php echo esc_html($i);?>"><?php echo esc_attr($i < 10 ? '0' : '');?><?php echo esc_html($i);?></option>                <?php endfor;?>            </select>            <select name="expire_year">                <?php for($i = date('Y'); $i <= date('Y') + 10; $i++):?>                    <option value="<?php echo esc_html($i);?>"><?php echo esc_html($i);?></option>                <?php endfor;?>            </select>        </p>        <p class="pull-right">            <?php echo esc_html(__('Security code', 'victorious'));?>:<br/>            <input type="text" name="cvv"/>        </p>    </div>    <div class="col-md-6">        <p>            <?php echo esc_html(__('State', 'victorious'));?>:<br/>            <input type="text" name="state"/>        </p>    </div></div><div class="row">    <div class="col-md-6">        <p>            <?php echo esc_html(__('Phone', 'victorious'));?>:<br/>            <input type="text" name="phone"/>        </p>    </div>    <div class="col-md-6">        <p>            <?php echo esc_html(__('Zip code', 'victorious'));?>:<br/>            <input type="text" name="zipcode"/>        </p>    </div></div>