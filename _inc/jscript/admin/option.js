
    function addArray(obj)
    {
        var item = jQuery(obj).prev().clone();
        item.find('input').val('');
        jQuery(obj).before(item);
        initArray();
        return false;
    };
    
    function removeArray(obj)
    {
        if(confirm(wpfs['a_sure']))
        {
            jQuery(obj).parent('div').remove();
        }
        initArray();
        return false;
    };
    
    function initArray()
    {
        jQuery('.array-holder').each(function(){
            var item = jQuery(this).find('.array-item');
            if(item.length < 2)
            {
                item.children('a').hide();
            }
            else
            {
                item.children('a').show();
            }
        })
    };
