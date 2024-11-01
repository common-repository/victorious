<?php VIC_GetMessage(); ?>
<div id="msgAddCredits" class="public_message"></div>

    <form id="formAddCredits">

        <input type="hidden" name="gateway" value="<?php echo VICTORIOUS_GATEWAY_BILLMAP;?>" />
        <div class="row">
            <div class="col-md-12">
                <p>
                    <?php echo esc_html(__('How many credits do you want to add', 'victorious'));?>
                </p>
            </div>    
            <div class="col-md-6">
                <input type="text" name="credits"/>
            </div>
        </div>

        <h3><?php echo esc_html(__('Information transaction', 'victorious'));?></h3>
        <!-- <div class="row">
            <div class="col-md-6">
                <p>
                    <?php echo esc_html(__('Username', 'victorious'));?>:<br/>
                    <input type="text" name="username"/>
                </p>
            </div>
            <div class="col-md-6">
                <p>
                    <?php echo esc_html(__('Password', 'victorious'));?>:<br/>
                    <input type="password" name="password"/>
                </p>
            </div>
        </div> -->
        <div class="row">
            <!-- <div class="col-md-6">
                <p>
                    <?php echo esc_html(__('Service Code', 'victorious'));?>:<br/>
                    <input type="text" name="service_code"/>
                </p>
            </div> -->
            <div class="col-md-12">
            	<p>
                    <?php echo esc_html(__('SubscriberID', 'victorious'));?>:<br/>
                    <input type="text" name="subscriber_id"/>
                </p>
            </div>
        </div>
        <!-- <div class="row">
            <div class="col-md-12">
                <p>
                    <?php echo esc_html(__('Reference', 'victorious'));?>:<br/>
                    <textarea name="reference" style="width: 100%;" rows="3"></textarea>
                </p>
            </div>
        </div> -->
        <div class="row">
            <div class="col-lg-12">
                <br>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <input id="btnAddCredits" type="submit" class="button" value="<?php echo esc_html(__('Add', 'victorious'));?>" onclick="jQuery.payment.sendCredits()" />
                <span class="waiting" style="display: none"><?php echo esc_html(__('Please wait...', 'victorious'));?></span>
            </div>
        </div>

    </form>
