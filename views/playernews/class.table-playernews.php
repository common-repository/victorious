<?php
class VIC_TablePlayerNews extends WP_List_Table
{
    private static $players;
    private static $playernews;
    private static $sports;
    function __construct()
    {
        self::$players = new VIC_Players();
        self::$playernews = new VIC_PlayerNews();
        self::$sports = new VIC_Sports();
        global $status, $page;
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
            case 'title':
                return esc_html($item[ $column_name ]);
            case 'player_name':
                return esc_html($item['player_name']);
            case 'update':
                return esc_html($item['updated']);
            case 'edit':
                return sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>', 'add-playernews','edit',$item['id']);
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    function get_columns()
    {
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'title' => esc_html(__('Title', 'victorious')),
            'player_name' => esc_html(__('Player', 'victorious')),
            'update' => esc_html(__('Update', 'victorious')),
            'edit'    => '',
        );
        return $columns;
    }
    
    function get_sortable_columns() 
    {
        $sortable_columns = array(
            'title'  => array('title',false),
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
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />', $item['id']
        );    
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
        if($keyword != null)
        {
            $keyword = trim($keyword);
        }
        
        //get data
        list($total_items, $aResults) = self::$playernews->getPlayerNewsByFilter(null, 'id DESC', ($this->get_pagenum() - 1) * $item_per_page, $item_per_page, $keyword);
        
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