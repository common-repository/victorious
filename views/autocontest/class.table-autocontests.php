<?php
class VIC_TableAutoContests extends WP_List_Table
{
    private static $autocontest;
    function __construct()
    {
        $this->item_per_page = 10;
        self::$autocontest = new VIC_AutoContest();
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
            case 'action2':
                return sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a>', 'add-auto-contest','edit',$item['id']);
            default:
                return print_r( $item, true ) ;
        }
        return null;
    }

    function get_columns()
    {
        $columns = array(
            'cb'        => '<input type="checkbox" />',
            'name'      => esc_html(__('Name', 'victorious')),
            'game_type' => esc_html(__('Game Type', 'victorious')),
            'league_size' => esc_html(__('League Size', 'victorious')),
            'entry_fee' => esc_html(__('Entry Fee', 'victorious')),
			'status'     => esc_html(__('Action', 'victorious')),
            'action2'    => '',
        );
        return $columns;
    }
    
    function column_cb($item) 
    {
        return '<input name="id[]" type="checkbox" value="'.$item['id'].'" />';
    }
    
    function column_name($item) 
    {
        return esc_html($item['name']);
    }

    /*function column_sports($item) 
    {
        $aSport = self::$autocontest->getSport();

        $tmp_sports = explode(',', $item['sports']);
        $tmp = array();

        foreach ($aSport as $k => $val) {
            if(count($val['child']) > 0){
                foreach ($val['child'] as $key => $value) {
                    if(in_array($value['id'], $tmp_sports)) $tmp[] = $value['name'];
                }
            }            
        }
        return implode(' - ', $tmp);
    }*/

    function column_game_type($item) 
    {
        $tmp = explode(',', $item['game_type']);

        $tmp = array_map(
            function($val){ 
                $game_type = array(
                    'playerdraft'   => 'Player Draft',
                    'livedraft'     => 'Live Draft',
                    'playerunit'    => 'Share Unit',
                    'pickem'        => 'Pick \'Em',
                    'pickspread'    => 'Pick \'Em Against Spread',
                    'pickmoney'     => 'Pick \'Em Against Money Line',
                    'picktie'       => 'Pick \'Em / Tie breaker',
                    'pickultimate'  => 'Ultimate Pickem',
                    'picksquares'   => 'Pick Squares',
                    'best5'         => 'Best 5',
                    'golfskin'      => 'Skin',
                    'howmanygoals'  => 'How many goals?',
                    'bothteamstoscore' => 'Both teams to score?'
                );
                return $game_type[$val]; 
            }, 
            $tmp
        );

        $tmp = implode(' - ', $tmp);

        return $tmp;
    }

    function column_league_size($item) 
    {
        return $item['league_size'];
    }

    function column_entry_fee($item) 
    {
        return $item['entry_fee'];
    }

    function column_status($item) 
    {
        //($item['status'] == 'active') ? esc_html(__('Active', 'victorious') : esc_html(__('InActive', 'victorious')
        $active_display = '';
        if($item['status'] == 'inactive') $active_display = "style='display: none;'";
        if($item['status'] == 'active') $unactive_display = "style='display: none;'";

        $xhtml = '<span id="setting'.$item['id'].'">
                    <a class="active" '.$active_display.' title="Active" onclick="jQuery.admin.activeAutoContest('.$item['id'].', \'inactive\')">
                        <img class="active" src="'.VICTORIOUS__PLUGIN_URL_IMAGE.'bullet_green.png" alt="Unactive" style="cursor:pointer" />
                    </a>
                    <a class="unactive" '.$unactive_display.' title="UnActivate" onclick="jQuery.admin.activeAutoContest('.$item['id'].', \'active\')">
                        <img src="'.VICTORIOUS__PLUGIN_URL_IMAGE.'bullet_red.png" alt="Active" style="cursor:pointer" />
                    </a>
                </span>';
        return $xhtml;
    }
    
    function prepare_items() 
    {
        //get data
        $aResults = self::$autocontest->getAutoContests();
        $columns  = $this->get_columns();
        $hidden   = array();
        
        //sort data
        $sortable = array();
        $this->_column_headers = array( $columns, $hidden, null );
        
        //pagination
        $this->set_pagination_args( array(
            'total_items' => !empty($aResults) ? count($aResults) : 0,
            'per_page'    => 100       
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