<?php
class VIC_TableTransactions extends WP_List_Table
{
    private static $user;
    private static $statistic;
    function __construct()
    {
        self::$user = new VIC_User();
        self::$statistic = new VIC_Statistic();
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
                return $item['fundshistoryID'];
            case 'uID':
                return $item['userID'];
            case 'user_login':
                return $item['user_login'];
            case 'type':
                return $item['type'];
            case 'amount':
                return $item['amount'];
            case 'new_balance':
                return $item['new_balance'];
            case 'date':
                return $item['date'];
            case 'status':
                return $item['status'];
            case 'contest':
                if($item['leagueID'] > 0)
                {
                    return '<a href="#" onclick="return jQuery.statistic.loadLeagueDetail('.$item['leagueID'].', \'Transaction\')">'.__('View', 'victorious').'</a>';
                }
                return '';
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    function get_columns()
    {
        $columns = array(
            'ID' => __('ID', 'victorious'),
            'uID' => __('uID', 'victorious'),
            'name' => __('Name', 'victorious'),
            'type' => __('Type', 'victorious'),
            'amount' => __('Amount', 'victorious'),
            'new_balance' => __('Balance', 'victorious'),
            'date' => __('Date', 'victorious'),
            'status' => __('Status', 'victorious'),
            'contest' => __('Contest', 'victorious'),
        );		
        return $columns;
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
        list($total_items, $aResults) = self::$statistic->getFundhistory($aCond, 'fundshistoryID DESC', ($this->get_pagenum() - 1) * $item_per_page, $item_per_page);
        $columns  = $this->get_columns();
        $hidden   = array();
        
        //sort data
        $this->_column_headers = array( $columns, $hidden, null );
        
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