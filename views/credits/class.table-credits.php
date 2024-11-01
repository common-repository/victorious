<?php
class VIC_TableCredits extends WP_List_Table
{
    private static $user;
    function __construct()
    {
        self::$user = new VIC_User();
        global $status, $page;
        $aResults = null;
        parent::__construct( array(
            'singular'  => __( 'book', 'mylisttable' , 'victorious'),     //singular name of the listed records
            'plural'    => __( 'books', 'mylisttable' , 'victorious'),   //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
    }

    function column_default( $item, $column_name ) 
    {
        switch( $column_name ) 
        { 
            case 'ID':
                return esc_html($item['ID']);
            case 'user_login':
                return esc_html($item['user_login']);
            case 'balance':
                return esc_html($item['balance']);
            /*case 'payment_request_pending':
                return $item['payment_request_pending'];*/
            case 'action':
                return '<a onclick="jQuery.admin.userCredits(this, '.$item['ID'].', \'add\', \''.__('Add credit', 'victorious').'\')" href="#">Add credit</a>
                        <a onclick="jQuery.admin.userCredits(this, '.$item['ID'].', \'remove\', \''.__('Remove credit', 'victorious').'\')" href="#">Remove credit</a>';
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    function get_columns()
    {
        $columns = array(
            'ID' => esc_html(__('ID', 'victorious')),
            'name' => esc_html(__('Name', 'victorious')),
            'balance' => esc_html(__('Total Balance', 'victorious')),
            //'payment_request_pending' => __('Payment Request Pending', 'victorious'),
            'action' => esc_html(__('Action', 'victorious')),
        );		
        return $columns;
    }
    
    function get_sortable_columns() 
    {
        $sortable_columns = array(
            'ID'  => array('ID',false),
            'name'  => array('user_login',false),
            //'balance'  => array('balance',false),
            //'payment_request_pending'  => array('payment_request_pending',false),
        );
        return $sortable_columns;
    }
    
    function usort_reorder( $a, $b ) 
    {
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? sanitize_text_field($_GET['orderby']) : 'ID';
        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? sanitize_text_field($_GET['order']) : 'ASC';
        
        if(is_numeric($a[$orderby]))
        {
            $result = $a[$orderby] - $b[$orderby];
        }
        else 
        {
            $result = strcmp( $a[$orderby], $b[$orderby] );
        }
        return ( $order === 'asc' ) ? $result : -$result;
    }
    
    function column_name($item) 
    {
        $actions = array(
        );

        return sprintf('%1$s %2$s', $item['user_login'], $this->row_actions($actions) );
    }
    
    function prepare_items($keyword = null) 
    {
        $user_id = VIC_GetUserId();
        $screen = get_current_screen();
        
        // retrieve the "per_page" option
        $screen_option = $screen->get_option('per_page', 'option');
        
        //add page number to table usermeta
        if(isset($_POST['wp_screen_options']))
        {
            $screen_value = sanitize_text_field($_POST['wp_screen_options']['value']);
            $meta = get_user_meta($user_id, $screen_option);
            if($meta == null)
            {
                add_user_meta($user_id, $screen_option, $screen_value);
            }
            else 
            {
                update_user_meta($user_id, $screen_option, $screen_value);
            }
            header('Location:'.sanitize_url($_SERVER['REQUEST_URI']));
        }
        
        // retrieve the value of the option stored for the current user
        $item_per_page = get_user_meta(VIC_GetUserId(), $screen_option, true);
        
        if ( empty ( $item_per_page) || $item_per_page < 1 ) {
            // get the default value if none is set
            $item_per_page = $screen->get_option( 'per_page', 'default' );
        }
        
        //search
        $aCond = null;
        if($keyword != null)
        {
            $keyword = trim($keyword);
            $aCond = array("user_login LIKE '%%%$keyword%%'");
        }
        
        //get data
        list($total_items, $aPools) = self::$user->getUsers($aCond, 'ID ASC', ($this->get_pagenum() - 1) * $item_per_page, $item_per_page);
        $aResults = self::$user->parseUsersData($aPools);
        
        $columns  = $this->get_columns();
        $hidden   = array();
        
        //sort data
        $sortable = $this->get_sortable_columns();
        if($aResults != null)
        {
            $victorious = new VIC_Victorious();
            $payment = new VIC_Payment();
            $global_setting = $victorious->getGlobalSetting();
            $default_only = empty($global_setting['allow_multiple_balances']) ? true : false;
            foreach($aResults as $k => $item)
            {
                $aResults[$k]['balance'] = $payment->getDisplayUserBalance($item['ID'], $default_only);
            }
            usort( $aResults, array( &$this, 'usort_reorder' ) );
        }
        $this->_column_headers = array( $columns, $hidden, $sortable );
        
        //pagination
        $this->set_pagination_args( array(
            'total_items' => $total_items,                 
            'per_page'    => $item_per_page       
        ) );
        $this->items = $aResults;
    }
    function display_vc_table() {
		$singular = $this->_args['singular'];

		$this->display_tablenav( 'top' );

		$this->screen->render_screen_reader_content( 'heading_list' );
		?>
		<div class="vc-table vc-table-admin">
			<table cellspacing="0" cellpadding="0">
				<thead>
					<tr>
						<?php $this->print_column_headers(); ?>
					</tr>
				</thead>
				<tbody id="the-list"
					<?php
					if ( $singular ) {
						echo " data-wp-lists='list:$singular'";
					}
					?>
					>
					<?php $this->display_rows_or_placeholder(); ?>
				</tbody>			
			</table>
		</div>
		<?php
	}
}
?>