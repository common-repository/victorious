<?php
class VIC_TableWithdrawls extends WP_List_Table
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
            case 'uID':
                return esc_html($item['userID']);
            case 'user_login':
                return esc_html($item['user_login']);
            case 'amount':
                return VIC_FormatMoney($item['amount'], $item['balance_type']['currency_code_symbol']);
            case 'real_amount':
                return VIC_FormatMoney($item['real_amount'], $item['balance_type']['currency_code_symbol']);
            case 'new_balance':
                return VIC_FormatMoney($item['new_balance'], $item['balance_type']['currency_code_symbol']);
            case 'requestDate':
                return esc_html($item['requestDate']);
            case 'status':
                return '<span class="withdraw_status_'.esc_html($item['withdrawlID']).'">'.VIC_WithdrawalStatus($item['status']).'</span>';
            case 'gateway':
                return esc_html($item['gateway']);
            case 'action':
                return '<a id="withdrawal_'.esc_html($item['withdrawlID']).'" onclick="return jQuery.admin.userWithdrawls('.esc_html($item['withdrawlID']).')" href="javascript:void(0)">'.__('View', 'victorious').'</a>
                        <input class="withdrawlID" type="hidden" value="'.esc_html($item['withdrawlID']).'">
                        <input class="reason" type="hidden" value="'.esc_html($item['reason']).'">
                        <input class="response_message" type="hidden" value="'.esc_html($item['response_message']).'">';
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    function get_columns()
    {
        $columns = array(
            'uID' => esc_html(__('user ID', 'victorious')),
            'name' => esc_html(__('Name', 'victorious')),
            'gateway'=>esc_html(__('Gateway','victorious')),
            'amount' => esc_html(__('Amount', 'victorious')),
            'real_amount' => esc_html(__('Real Amount', 'victorious')),
            'new_balance' => esc_html(__('Balance', 'victorious')),
            'requestDate' => esc_html(__('Request Date', 'victorious')),
            'status' => esc_html(__('Status', 'victorious')),
            'action' => esc_html(__('Action', 'victorious')),
        );		
        return $columns;
    }
    
    function get_sortable_columns() 
    {
        $sortable_columns = array(
            'uID'  => array('userID',false),
            'name'  => array('user_login',false),
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
        list($total_items, $aResults) = self::$user->getUsersWithdrawls($aCond, 'w.withdrawlID DESC', ($this->get_pagenum() - 1) * $item_per_page, $item_per_page);
        $columns  = $this->get_columns();
        $hidden   = array();
        //sort data
        $sortable = $this->get_sortable_columns();
        if($aResults != null && !empty($_GET['orderby']))
        {
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