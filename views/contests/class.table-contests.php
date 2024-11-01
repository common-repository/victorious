<?php
class VIC_TableContests extends WP_List_Table
{
    private static $leagues, $allow_export_pick;
    function __construct()
    {
        self::$leagues = new VIC_Leagues();
        self::$allow_export_pick = false;
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
                return $item['leagueID'];
            case 'name':
                return $item[ $column_name ];
            case 'gameType':
                return VIC_ParseGameTypeName($item[ $column_name ]);
            case 'poolName':
                return $item[ $column_name ];
            case 'startDate':
                return $item[ $column_name ];
            case 'creator':
                return $item[ $column_name ];
            case 'is_feature':
                $active_display = $unactive_display = 'style="display:none"';
                if($item['is_feature'] == 1)
                {
                    $active_display = '';
                }
                else 
                {
                    $unactive_display = '';
                }
                echo 
                    '<a class="active feature_contest_'.$item['leagueID'].'" '.$active_display.' title="Unactive" onclick="jQuery.admin.showSetFeatureContestDlg('.$item['leagueID'].', 0)">
                        <img class="active" src="'.VICTORIOUS__PLUGIN_URL_IMAGE.'bullet_green.png" alt="Unactive" style="cursor:pointer" />
                    </a>
                    <a class="unactive feature_contest_'.$item['leagueID'].'" '.$unactive_display.' title="Activate" onclick="jQuery.admin.showSetFeatureContestDlg('.$item['leagueID'].', 1)">
                        <img src="'.VICTORIOUS__PLUGIN_URL_IMAGE.'bullet_red.png" alt="Active" style="cursor:pointer" />
                    </a>';
                return;
            case 'opponent':
                echo esc_html(strtolower($item[ $column_name ]));
                echo " | ";
                echo '<a onclick="return jQuery.admin.showInviteFriendDialog(\''.__('Invite friend', 'victorious').'\', '.esc_html($item['leagueID']).')" href="javascript:void(0)">'.esc_html(__('Invite', 'victorious')).'</a>';
                return;
            case 'status':
                return $item[ $column_name ];
            case 'action2':
                if(empty($item['contest_status']))
                {
                    if($item['gameType'] == VICTORIOUS_GAME_TYPE_UPLOADPHOTO){
                        echo '<a href="javascript:void(0)" onclick="jQuery.admin.loadUploadPhotoResult('.esc_html($item['leagueID']).', \''.esc_html($item['status']).'\')">'.esc_html(__('Result', 'victorious')).'</a>';
                        echo " &nbsp ";
                    }
                    if(self::$allow_export_pick)
                    {
                        echo '<a target="_blank" href="'.admin_url().'admin.php?page=manage-contests&leagueID='.esc_html($item['leagueID']).'">'.esc_html(__('Export', 'victorious')).'</a>';
                        if($item['gameType'] != VICTORIOUS_GAME_TYPE_UPLOADPHOTO){
                            echo " &nbsp ".'<a onclick="return jQuery.admin.showUserPicks('.esc_html($item['leagueID']).')" href="#">'.esc_html(__('Picks', 'victorious')).'</a>';
                        }
                        
                    }
                    if(strtolower($item['status']) == "complete" && $item['entry_fee'] > 0)
                    {
                        echo " &nbsp ";
                        echo '<a href="javascript:void(0)" onclick="jQuery.admin.cancelContest(this, '.esc_html($item['leagueID']).')">'.esc_html(__('Cancel', 'victorious')).'</a>';
                    }
                    if($item['status'] == "NEW")
                    {
                        echo " &nbsp ";
                        return sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>', 'add-contests','edit',esc_html($item['leagueID']));
                    }
                    else 
                    {
                        return '';
                    }
                }
                else
                {
                    return esc_html(__('Cancelled', 'victorious'));
                }
            default:
                return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes
        }
    }

    function get_columns()
    {
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'ID' => esc_html(__('ID', 'victorious')),
            'name' => esc_html(__('Name', 'victorious')),
            'gameType' => esc_html(__('Game Type', 'victorious')),
            'poolName' => esc_html(__('Event', 'victorious')),
            'startDate' => esc_html(__('Start Date', 'victorious')),
            'creator' => esc_html(__('Creator', 'victorious')),
            'is_feature' => esc_html(__('Feature', 'victorious')),
            'opponent' => esc_html(__('Opponent', 'victorious')),
            'status' => esc_html(__('Event Status', 'victorious')),
            'action2'    => '',
        );
        return $columns;
    }
    
    function get_sortable_columns() 
    {
        $sortable_columns = array(
            'name'  => array('name',false),
            'gameType'  => array('gameType',false),
            'poolName'  => array('poolName',false),
            'startDate'  => array('startDate',false),
            'status'  => array('status',false),
        );
        return $sortable_columns;
    }
    
    function usort_reorder( $a, $b ) 
    {
        // If no sort, default to title
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? sanitize_text_field($_GET['orderby']) : 'leagueID';
        // If no order, default to asc
        $order = ( ! empty($_GET['order'] ) ) ? sanitize_text_field($_GET['order']) : 'DESC';
        // Determine sort order
        $result = strcmp( $a[$orderby], $b[$orderby] );
        // Send final sort direction to usort
        return ( $order === 'asc' ) ? $result : -$result;
    }

    function column_cb($item) 
    {
        if(strtolower($item['status']) == "new")
        {
            return sprintf(
                '<input type="checkbox" name="id[]" value="%s" />', $item['leagueID']
            );   
        }
        else 
        {
            return '';
        }
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
            $aCond = array("name LIKE '%$keyword%'" => "");
        }

        //get data
        list($total_items, $aResults, self::$allow_export_pick) = self::$leagues->getLeaguesByFilter($aCond, 'leagueID DESC', ($this->get_pagenum() - 1) * $item_per_page, $item_per_page);
        $aResults = self::$leagues->parseLeagueData($aResults);

        $columns  = $this->get_columns();
        $hidden   = array();
        
        //sort data
        $sortable = $this->get_sortable_columns();
        if($aResults != null)
        {
            //usort( $aResults, array( &$this, 'usort_reorder' ) );
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