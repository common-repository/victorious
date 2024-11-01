<?php
class VIC_TableScoringCategory extends WP_List_Table
{
    private static $scoringcategory;
    private static $sports;
    function __construct()
    {
        self::$scoringcategory = new VIC_ScoringCategory();
        self::$sports = new VIC_Sports();
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
            case 'name':
                return esc_html($item[ $column_name ]);
            case 'points':
                return esc_html($item[ $column_name ]);
            case 'type':
                return esc_html($item['scoring_type']);
            case 'org_id':
                return esc_html($item['sport_name']);
            case 'edit':
                return sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>', 'add-scoringcategory','edit',esc_html($item['id']));
            case 'is_active':
                $active_display = $unactive_display = 'style="display:none"';
                if($item['is_active'] == 1)
                {
                    $active_display = '';
                }
                else 
                {
                    $unactive_display = '';
                }
                return '<a id="active'.esc_html($item['id']).'" '.$active_display.' title="Unactive" onclick="jQuery.admin.activeScoringCategorySetting('.esc_html($item['id']).', 0)">
                            <img class="active" src="'.VICTORIOUS__PLUGIN_URL_IMAGE.'bullet_green.png" alt="Unactive" style="cursor:pointer" />
                        </a>
                        <a id="unactive'.esc_html($item['id']).'" '.$unactive_display.' title="Activate" onclick="jQuery.admin.activeScoringCategorySetting('.esc_html($item['id']).', 1)">
                            <img src="'.VICTORIOUS__PLUGIN_URL_IMAGE.'bullet_red.png" alt="Active" style="cursor:pointer" />
                        </a>';
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    function get_columns()
    {
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'name' => esc_html(__('Name', 'victorious')),
            'points' => esc_html(__('Point', 'victorious')),
            'org_id' => esc_html(__('Sport', 'victorious')),
            'type' => esc_html(__('Type', 'victorious')),
            'is_active' => esc_html(__('Active', 'victorious')),
            'edit'    => '',
        );
        return $columns;
    }
    
    function get_sortable_columns() 
    {
        $sortable_columns = array(
            'name'  => array('name',false),
            'organization'  => array('org_id',false),
            'type'  => array('scoring_type',false),
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
        return null;
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
            $aCond = array("SCORING_CATEGORY.name LIKE '%$keyword%'" => "");
        }
        
        //get data
        $sport_id = !empty($_GET['sport_id']) ? sanitize_text_field($_GET['sport_id']) : '';
        list($total_items, $aResults) = self::$scoringcategory->getScoringCategoryByFilter($sport_id, $aCond, 'id DESC', ($this->get_pagenum() - 1) * $item_per_page, $item_per_page);
        
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