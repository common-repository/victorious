<?php
class VIC_TablePlayers extends WP_List_Table
{
    private static $players;
    private static $teams;
    private static $playerposition;
    function __construct()
    {
        self::$players = new VIC_Players();
        self::$teams = new VIC_Teams();
        self::$playerposition = new VIC_PlayerPosition();
        global $status, $page;
        $aResults = null;
        parent::__construct( array(
            'singular'  => esc_html(__( 'book', 'mylisttable' , 'victorious')),     //singular name of the listed records
            'plural'    => esc_html(__( 'books', 'mylisttable' , 'victorious')),   //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
    }

    function column_default( $item, $column_name ) 
    {
        switch( $column_name ) 
        { 
            case 'image':
                return '<img width="35px" height="35px" alt="" src="'.esc_url($item['full_image_path']).'">';
            case 'name':
                return esc_html($item[ $column_name ]);
            case 'salary':
                return '<input type="hidden" name="salary_sport_id['.esc_html($item['id']).']" value="'.sanitize_text_field($_GET['sport_id']).'"/> <input type="text" name="salary['.esc_html($item['id']).']" value="'.number_format($item[ $column_name ]).'" onkeyup="this.value = accounting.formatNumber(this.value)" />';
            case 'sport_name':
                return esc_html($item[ $column_name ]);
            case 'indicator_name':
                return esc_html($item[ $column_name ]);
            case 'team':
                return esc_html($item['team_name']);
            case 'position':
                return esc_html($item['position_name']);
            case 'edit':
                $sport_param = !empty($_GET['sport_id']) ? '&sport_id='.sanitize_text_field($_GET['sport_id']) : '';
                return sprintf('<a href="?page=%s'.$sport_param.'&action=%s&id=%s">Edit</a>', 'add-players','edit',esc_html($item['id']));
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    function get_columns()
    {
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'image' => esc_html(__('Image', 'victorious')),
            'name' => esc_html(__('Name', 'victorious')),
            'salary' => esc_html(__('Salary', 'victorious')),
            'sport_name' => esc_html(__('Sport', 'victorious')),
            'indicator_name' => esc_html(__('Indicator', 'victorious')),
            'team' => esc_html(__('Team', 'victorious')),
            'position' => esc_html(__('Position', 'victorious')),
            'edit'    => '',
        );
        return $columns;
    }
    
    function get_sortable_columns() 
    {
        $sortable_columns = array(
            'name'  => array('name',false),
            'team'  => array('org_id',false),
            'position'  => array('position_id',false),
        );
        return $sortable_columns;
    }
    
    function usort_reorder( $a, $b ) 
    {
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? sanitize_text_field($_GET['orderby']) : 'id';
        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? sanitize_text_field($_GET['order']) : 'DESC';
        // Determine sort order
        $result = strcmp( $a[$orderby], $b[$orderby] );
        // Send final sort direction to usort
        return ( $order === 'asc' ) ? $result : -$result;
    }

    function column_cb($item) 
    {
        if($item['siteID'] > 0)
        {
            return sprintf(
                '<input type="checkbox" name="id[]" value="%s" />', $item['id']
            );    
        }
        return '';
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
            $aCond = array("PLAYERS.name LIKE '%$keyword%'" => "");
        }

        //get data
        $sport_id = !empty($_GET['sport_id']) ? sanitize_text_field($_GET['sport_id']) : '';
        list($total_items, $aResults) = self::$players->getPlayersByFilter($sport_id, $aCond, 'id DESC', ($this->get_pagenum() - 1) * $item_per_page, $item_per_page);
        $aResults = self::$players->parsePlayersData($aResults);
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