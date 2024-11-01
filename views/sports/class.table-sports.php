<?php
class VIC_TableSports extends WP_List_Table
{
    private static $sports;
    function __construct()
    {
        $this->item_per_page = 10;
        self::$sports = new VIC_Sports();
        global $status, $page;
        $aResults = null;
        parent::__construct( array(
            'singular'  => __( 'book', 'mylisttable' , 'victorious'),     //singular name of the listed records
            'plural'    => __( 'books', 'mylisttable' , 'victorious'),   //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );
    }

    function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'name':
                if($item['parent_id'] > 0){
                    return '<div style="padding-left: 30px"> |--'.esc_html($item['name']).' </div>';
                }
                return esc_html( $item['name'] );
            case 'active':
                if($item['parent_id'] > 0){
                    $active_display = $unactive_display = 'style="display:none"';
                    if($item['is_active'] == 1)
                    {
                        $active_display = '';
                    }
                    else
                    {
                        $unactive_display = '';
                    }
                    return '<span id="setting'.$item['id'].'">
                            <a class="active" '.$active_display.' title="Unactive" onclick="jQuery.admin.activeOrgsSetting('.$item['id'].', 0)">
                                <img class="active" src="'.VICTORIOUS__PLUGIN_URL_IMAGE.'bullet_green.png" alt="Unactive" style="cursor:pointer" />
                            </a>
                            <a class="unactive" '.$unactive_display.' title="Activate" onclick="jQuery.admin.activeOrgsSetting('.$item['id'].', 1)">
                                <img src="'.VICTORIOUS__PLUGIN_URL_IMAGE.'bullet_red.png" alt="Active" style="cursor:pointer" />
                            </a>
                        </span>';
                }
                return '';
            case 'reverse_points':
                if($item['parent_id'] > 0){
                    $rv_active_display = $rv_unactive_display = 'style="display:none"';
                    if($item['reverse_points'] == 1)
                    {
                        $rv_active_display = '';
                    }
                    else
                    {
                        $rv_unactive_display = '';
                    }
                    return '<span id="rv_setting'.$item['id'].'">
                            <a class="active" '.$rv_active_display.' title="Unactive" onclick="jQuery.admin.reversePointOrgsSetting('.esc_html($item['id']).', 0)">
                                <img class="active" src="'.VICTORIOUS__PLUGIN_URL_IMAGE.'bullet_green.png" alt="Unactive" style="cursor:pointer" />
                            </a>
                            <a class="unactive" '.$rv_unactive_display.' title="Activate" onclick="jQuery.admin.reversePointOrgsSetting('.esc_html($item['id']).', 1)">
                                <img src="'.VICTORIOUS__PLUGIN_URL_IMAGE.'bullet_red.png" alt="Active" style="cursor:pointer" />
                            </a>
                        </span>';
                }
                break;
            case 'edit':
                if($item['siteID'] > 0){
                    return '<a href="?page=add-sports&amp;action=edit&amp;id='.esc_html($item['id']).'">Edit</a>';
                }
                return '';
                break;
        }
        return '';
    }

    function get_columns()
    {
        $columns = array(
            'cb' => '<input type="checkbox" />',
            'name' => esc_html(__('Name', 'victorious')),
            'active' => esc_html(__('Active', 'victorious')),
			'reverse_points' => esc_html(__('Reverse Point', 'victorious')),
            'edit'    => '',
        );
        return $columns;
    }
    
    function column_cb($item) 
    {
        if(!isset($item['child']) &&  $item['siteID'] > 0)
        {
            return '<input type="checkbox" value="'.esc_html($item['id']).'" name="id[]">';
        }
        return '';
    }

    /*function column_name($itemSport)
    {
        $delHtml = "";
        if(!isset($itemSport['child']) && $itemSport['siteID'] > 0)
        {
            $delHtml = '<input type="checkbox" value="'.$itemSport['id'].'" name="id[]">';
        }
        $editHtml = "";
        if($itemSport['siteID'] > 0)
        {
            $editHtml = '<a href="?page=add-sports&amp;action=edit&amp;id='.$itemSport['id'].'">Edit</a>';
        }
        $result =   '<tr class="alternate">
                        <th class="check-column" scope="row">
                            '.$delHtml.'
                        </th>
                        <td class="name column-name">
                            '.$itemSport['name'].'
                        </td>
                        <td></td>
						<td></td>
                        <td class="edit column-edit">
                            '.$editHtml.'
                        </td>
                    </tr>';
        if(isset($itemSport['child']) && $itemSport['child'] != null)
        {
            foreach($itemSport['child'] as $item)
            {
                $delHtml = "";
                if($item['siteID'] > 0)
                {
                    $delHtml = '<input type="checkbox" value="'.$item['id'].'" name="id[]">';
                }
                $editHtml = "";
                if($item['siteID'] > 0)
                {
                    $editHtml = '<a href="?page=add-sports&amp;action=edit&amp;id='.$item['id'].'">Edit</a>';
                }

				//active
                $active_display = $unactive_display = 'style="display:none"';
                if($item['is_active'] == 1)
                {
                    $active_display = '';
                }
                else
                {
                    $unactive_display = '';
                }

				//reverse point
                $rv_active_display = $rv_unactive_display = 'style="display:none"';
                if($item['reverse_points'] == 1)
                {
                    $rv_active_display = '';
                }
                else
                {
                    $rv_unactive_display = '';
                }

                $result .= '<tr class="alternate">
                                <th class="check-column" scope="row">
                                    '.$delHtml.'
                                </th>
                                <td class="name column-name">
                                    <div style="padding-left: 30px"> |--'.$item['name'].' </div>
                                </td>
                                <td class="active column-active" id="setting'.$item['id'].'">
                                    <a class="active" '.$active_display.' title="Unactive" onclick="jQuery.admin.activeOrgsSetting('.$item['id'].', 0)">
                                        <img class="active" src="'.VICTORIOUS__PLUGIN_URL_IMAGE.'bullet_green.png" alt="Unactive" style="cursor:pointer" />
                                    </a>
                                    <a class="unactive" '.$unactive_display.' title="Activate" onclick="jQuery.admin.activeOrgsSetting('.$item['id'].', 1)">
                                        <img src="'.VICTORIOUS__PLUGIN_URL_IMAGE.'bullet_red.png" alt="Active" style="cursor:pointer" />
                                    </a>
                                </td>
								<td class="rv_active column-active" id="rv_setting'.$item['id'].'">
                                    <a class="active" '.$rv_active_display.' title="Unactive" onclick="jQuery.admin.reversePointOrgsSetting('.$item['id'].', 0)">
                                        <img class="active" src="'.VICTORIOUS__PLUGIN_URL_IMAGE.'bullet_green.png" alt="Unactive" style="cursor:pointer" />
                                    </a>
                                    <a class="unactive" '.$rv_unactive_display.' title="Activate" onclick="jQuery.admin.reversePointOrgsSetting('.$item['id'].', 1)">
                                        <img src="'.VICTORIOUS__PLUGIN_URL_IMAGE.'bullet_red.png" alt="Active" style="cursor:pointer" />
                                    </a>
                                </td>
                                <td class="edit column-edit">
                                    '.$editHtml.'
                                </td>
                            </tr>';
            }
        }
        return $result;
    }*/

    function prepare_items() 
    {
        //get data
        $originalResults = self::$sports->getSports();
        $aResults = $originalResults;
        $sports = array();
        if($aResults != null){
            if(!empty($_GET['id'])){
                foreach($aResults as $k => $aResult){
                    foreach($aResult['child'] as $k2 => $child){
                        if($_GET['id'] != $child['id']){
                            unset($aResults[$k]['child'][$k2]);
                        }
                    }
                    if(empty($aResults[$k]['child'])){
                        unset($aResults[$k]);
                    }
                }
                array_values($aResults);
            }
            foreach($aResults as $k => $aResult){
                $sports[] = $aResult;
                if(empty($aResult['child'])){
                    continue;
                }
                foreach($aResult['child'] as $child){
                    $sports[] = $child;
                }
            }
        }
        $columns  = $this->get_columns();
        $hidden   = array();
        
        //sort data
        $sortable = array();
        $this->_column_headers = array( $columns, $hidden, $sortable );

        $this->items = $sports;

        return $originalResults;
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