<?php
Class VIC_SuccesswithdrawlsController
{
	public function process()
	{
        if(isset($_SESSION['withdrawlID']))
        {
            VIC_Redirect(admin_url().'admin.php?page=withdrawls', __('Successfully updated', 'victorious'));
        }
        else
        {
            VIC_Redirect(admin_url().'admin.php?page=withdrawls');
        }
    }
}
?>