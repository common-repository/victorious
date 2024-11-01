jQuery.summary = {
    calculateLayout: function(){
        var total_width = frozen_width = 0;
        jQuery('.tbl-wrapper table th').each(function(){
            total_width += jQuery(this).width() + + parseFloat(jQuery(this).css('padding-left')) + parseFloat(jQuery(this).css('padding-right'));
        });
        jQuery('.tbl-wrapper table th.frozen').each(function(){
            frozen_width += jQuery(this).width() + parseFloat(jQuery(this).css('padding-left')) + parseFloat(jQuery(this).css('padding-right'));
        });
        jQuery('.outerdiv').css('right', frozen_width);
        jQuery('.innerdiv').css('margin-left', frozen_width);
        jQuery('.tbl-wrapper table').width(total_width - frozen_width + 5);
    },
    
    loadSummary: function(page){
        jQuery('#statistic_loading').show();
        jQuery('#wrap_outerdiv .outerdiv').hide();
        if(typeof page == 'undefined')
        {
            page = 1;
        }
        var sort_by = jQuery('#sort_by').val();
        var sort_type = jQuery('#sort_type').val();
        jQuery.post(ajaxurl, 'action=loadSummary&page=' + page + '&sort_by=' + sort_by + '&sort_type=' + sort_type, function(result) {
            jQuery("#tableSummary tbody").empty().append(jQuery(result).filter('#result-template').html());
            jQuery("#pg").remove();
            jQuery("#tableSummary").after(jQuery(result).filter('#paging-template').html());
            jQuery('#statistic_loading').hide();
	});
    },
    
    doSort: function(sort_by_value)
    {
        var sort_by = jQuery('#sort_by').val();
        var sort_type = jQuery('#sort_type').val();
        var sort_type_value = '';
        if(sort_by == '' || sort_by != sort_by_value)
        {
            jQuery('#sort_by').val(sort_by_value);
            jQuery('#sort_type').val('ASC');
            sort_type_value = 'ASC';
        }
        else 
        {
            if(sort_type == 'ASC')
            {
                jQuery('#sort_type').val('DESC');
                sort_type_value = 'DESC';
            }
            else 
            {
                jQuery('#sort_type').val('ASC');
                sort_type_value = 'ASC';
            }
        }
        jQuery('#tableSummary .fa-sort-up').hide();
        jQuery('#tableSummary .fa-sort-down').hide();
        switch(sort_type_value)
        {
            case 'DESC':
                jQuery('#sort_by_' + sort_by_value + ' .fa-sort-up').show();
                break;
            case 'ASC':
                jQuery('#sort_by_' + sort_by_value + ' .fa-sort-down').show();
                break;
        }
        this.loadSummary();
    }
}