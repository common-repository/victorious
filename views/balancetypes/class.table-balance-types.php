<?php
class VIC_TableBalanceTypes extends WP_List_Table
{
    private static $balanceType;
    function __construct()
    {
        self::$balanceType = new VIC_BalanceType();
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
                if(empty($item['image'])){
                    return '';
                }
                return '<img width="35px" height="35px" alt="" src="'.$item['image_url'].'">';
            case 'enabled':
                if($item['enabled']){
                    return '<a class="active">
                        <img class="active" src="'.VICTORIOUS__PLUGIN_URL_IMAGE.'bullet_green.png" alt="Unactive" />
                    </a>';
                }
                else{
                    return '<a class="unactive">
                        <img src="'.VICTORIOUS__PLUGIN_URL_IMAGE.'bullet_red.png" alt="Active" />
                    </a>';
                }
            case 'currency_position';
                return VIC_CurrencyPositionList($item['currency_position']);
            case 'edit':
                /*if($item['is_core'] == 1){
                    return '';
                }*/
                return sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>', 'add-balance-type','edit',$item['id']);
            default:
                return esc_html($item[$column_name]) ; //Show the whole array for troubleshooting purposes
        }
    }

    function get_columns()
    {
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'image' => esc_html(__('Image', 'victorious')),
            'name' => esc_html(__('Name', 'victorious')),
            'currency_code' => esc_html(__('Currency Code', 'victorious')),
            'currency_position' => esc_html(__('Currency Position', 'victorious')),
            'symbol' => esc_html(__('Currency Symbol', 'victorious')),
            'enabled' => esc_html(__('Enable', 'victorious')),
            'edit' => esc_html(__('Action', 'victorious')),
        );
        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = array(
            'name'  => array('name',false)
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

    function column_cb($item)
    {
        if($item['is_core'] == 1){
            return '';
        }
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
        $aCond = null;
        if($keyword != null)
        {
            $keyword = trim($keyword);
            $aCond = array("name LIKE '%%%$keyword%%'");
        }

        //get data
        list($total_items, $aResults) = self::$balanceType->getBalanceTypes($aCond, 'ID ASC', ($this->get_pagenum() - 1) * $item_per_page, $item_per_page);

        $columns  = $this->get_columns();
        $hidden   = array();

        //sort data
        $sortable = $this->get_sortable_columns();
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
			<table>
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