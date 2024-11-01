<?php echo esc_html(__('Your bank:','victorious'));?><br>
<?php if (count($banks)): ?>
<select name="val[bankcode]">
    <?php foreach ($banks as $code => $name): ?>
    <option value="<?php echo esc_attr($code); ?>" <?php if ($userBankCode === $code):?><?php echo 'selected'; ?><?php endif; ?>><?php echo esc_html($name); ?></option>
    <?php endforeach; ?>
</select>
<?php endif; ?>

