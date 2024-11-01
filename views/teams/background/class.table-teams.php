<?php
class VIC_TableTeams extends WP_List_Table
{
    private static $teams;
    function __construct()
    {
        self::$teams = new VIC_Teams();
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
//            case 'teamID':
//                //return '<img width="35px" height="35px" alt="" src="'.VICTORIOUS_IMAGE_URL.Teams::replaceSuffix($item['image']).'">';
//                return $item[$]
//            case 'name':
//                return $item[ $column_name ];
//            case 'org_name':
//                return sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>', 'add-teams','edit',$item['teamID']);
            case 'background_path':
            if(!empty($item[$column_name])){
                return '<a target="_blank" href="'.esc_html($item[$column_name]).'"><img width="35px" height="35px" alt="" src="'.esc_html($item[$column_name]).'"></a>';
            }
            return '--';
            case 'upload_image':
               $html = "<input type='file' name='upload_image[]' value='".esc_html($item['teamID'])."'><input type='hidden' name=teamID[] value='".esc_html($item['teamID'])."'>";
                return $html;
            case 'delete_image':
                //$url = admin_url() . 'admin.php?page=teams-background&action=delete&teamID='.$item['teamID'];
//                return '<a target="_blank" href="'.$url.'">'.__('Delete image','victorious').'</a>';
                return '<a href="#/" onclick="jQuery.admin.deleteTeamImage('.esc_html($item['teamID']).')">'.__('Delete image','victorious').'</a>';
            default:
                return esc_html($item[$column_name]) ; //Show the whole array for troubleshooting purposes
        }
    }

    function get_columns()
    {
        $columns = array(
            'teamID' => esc_html(__('ID', 'victorious')),
            'name' => esc_html(__('Name', 'victorious')),
            'org_name'  => esc_html(__('Sport name ','victorious')),
            'background_path'=>esc_html(__('Image','victorious')),
            'upload_image'=>esc_html(__('Upload image','victorious')),
            'delete_image'=>esc_html(__('Delete image','victorious'))
        );
        return $columns;
    }
    
    function get_sortable_columns() 
    {
        $sortable_columns = array(
            'teamID'  => array('teamID',false),
        );
        return $sortable_columns;
    }
    
    function usort_reorder( $a, $b ) 
    {
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? sanitize_text_field($_GET['orderby']) : 'teamID';
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
            '<input type="checkbox" name="id[]" value="%s" />', $item['teamID']
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
        

        $aSorts= '';
        if(isset($_GET['orderby'])){
            $aSorts = sanitize_text_field($_GET['orderby']).' '.sanitize_text_field($_GET['order']);
        }
        
        //get data
        list($total_items, $aPools) = self::$teams->getTeamsBackgroundByFilter($keyword,$aSorts, ($this->get_pagenum() - 1) * $item_per_page, $item_per_page);
        $aResults = self::$teams->parseBackgroundTeamsData($aPools);
        
        $columns  = $this->get_columns();
        $hidden   = array();
        
        //sort data
        $sortable = $this->get_sortable_columns();
//        if($aResults != null)
//        {
//            usort( $aResults, array( &$this, 'usort_reorder' ) );
//        }
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