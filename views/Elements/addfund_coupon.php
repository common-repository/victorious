<?php if (!empty($isHasCoupon)): ?>
    <div class="vc-dashboard-item border-white pb-0">
        <div class="row">
            <div class="col-md-6">
                <h3 class="vc-tabpane-title"><?php echo esc_html(__('Coupon code', 'victorious'));?>:</h3>
                <input type="hidden" name="coupon_code" id="f-add-funds-coupon-code"/>
                <div class="form-inline">
                    <div class="form-group mb-2"><input type="text" id="f-add-funds-coupon-code-input" class="form-control"/></div>
                    <input type="button" class="vc-button btn-green btn-size-sm btn-radius5 font-weight-normal mx-sm-3 mb-2" onclick="jQuery.payment.applyCoupon()" id="btn_apply_coupon" value="<?php echo esc_html(__('Apply', 'victorious'));?>" />
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>