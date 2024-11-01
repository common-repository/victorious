<?php
class VIC_TableStatistic extends WP_List_Table
{
    private static $leagues;
    private static $statistic;
    function __construct()
    {
        self::$leagues = new VIC_Leagues();
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
                return esc_html($item['leagueID']);
            case 'contest':
                return esc_html($item[ 'name' ]);
            case 'gameType':
                return esc_html($item[ $column_name ]);
            case 'poolName':
                return esc_html($item[ $column_name ]);
            case 'startDate':
                return esc_html($item[ $column_name ]);
            case 'creator':
                return esc_html($item[ $column_name ]);
            case 'status':
                return esc_html($item[ $column_name ]);
            case 'detail':
                return '<a onclick="return jQuery.statistic.showPoolStatisticDetail('.esc_html($item['leagueID']).', \''.esc_html($item['name']).'\')" href="#">'.__('View', 'victorious').'</a>';
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    function get_columns()
    {
        $columns = array(
            'ID' => esc_html(__('ID', 'victorious')),
            'contest' => esc_html(__('Name', 'victorious')),
            'gameType' => esc_html(__('Game Type', 'victorious')),
            'poolName' => esc_html(__('Event', 'victorious')),
            'startDate' => esc_html(__('Start Date', 'victorious')),
            'creator' => esc_html(__('Creator', 'victorious')),
            'status' => esc_html(__('Status', 'victorious')),
            'detail' => esc_html(__('Detail', 'victorious')),
        );		
        return $columns;
    }
    
    function get_sortable_columns() 
    {
        $sortable_columns = array(
            'name'  => array('name',false),
        );
        return $sortable_columns;
    }
    
    function usort_reorder( $a, $b ) 
    {
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? sanitize_text_field($_GET['orderby']) : 'leagueID';
        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? sanitize_text_field($_GET['order']) : 'ASC';
        // Determine sort order
        $result = strcmp( $a[$orderby], $b[$orderby] );
        // Send final sort direction to usort
        return ( $order === 'asc' ) ? $result : -$result;
    }
    
    function column_name($item) 
    {
        $actions = array(
        );

        return sprintf('%1$s %2$s', esc_html($item['poolName']), $this->row_actions($actions) );
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
            $aCond = array("poolName LIKE '%%%$keyword%%'");
        }
        
        //get data
        list($total_items, $aResults) = self::$leagues->getLeaguesByFilter($aCond, 'leagueID DESC', ($this->get_pagenum() - 1) * $item_per_page, $item_per_page);
        $aResults = self::$leagues->parseLeagueData($aResults);
                
        $columns  = $this->get_columns();
        $hidden   = array();
        
        //sort data
        $sortable = $this->get_sortable_columns();
        if($aResults != null)
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
    
    function getData()
    {
        return self::$statistic->getProfit();
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