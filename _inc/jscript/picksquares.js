var aIndex = [];
jQuery(document).ready(function(){
    if(jQuery('#pick_squares').length > 0){
        aIndex = jQuery('#pick_squares').val();
        if(aIndex != ''){
            aIndex = jQuery.parseJSON(aIndex);
        }else{
            aIndex = [];
        }
        jQuery('.picksquare_table tr td').css('cursor','pointer');
    }
});
jQuery(document).on('click', '.picksquare_table tr td',function(){
   // var tdIndex =jQuery(this).closest('td').index();
  //  var trIndex = jQuery(this).closest('tr').index();
    var index = jQuery(this).closest('td').html();


   if(!inArray(index,aIndex)){
           aIndex.push(index);
           jQuery(this).css('background','yellow');
   }else{
       aIndex.splice( aIndex.indexOf(index), 1 );
        jQuery(this).css('background','white');
    }
   jQuery("#pick_squares").val(JSON.stringify(aIndex));
});

function inArray(needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}
function checkFormPickSquare(){
    if(aIndex.length < 1){
        alert('please select at least one square');
        return false;
    }
    return true;
}

var result_select_id = "";
var result_select_page = "";
jQuery.picksquares =
{
    initPickSquares: function()
    {
        //submit data
        jQuery(document).on('click', '#btnSubmit', function(){
            if(checkFormPickSquare())
            {
                jQuery.global.disableButton('btnSubmit', wpfs['working'] + '...');

                //submit data
                jQuery.post(ajaxurl, 'action=submitPickSquares&' + jQuery('#formData').serialize(), function(result) {
                    var json = jQuery.parseJSON(result);
                    if(json.success == 0 && json.redirect)
                    {
                        document.location = json.redirect;
                    }
                    else if(json.success == 0)
                    {
                        jQuery.global.enableButton('btnSubmit', wpfs['enter']);
                        alert(json.message);
                    }
                    else
                    {
                        document.location = json.redirect;
                    }
                })
            }
        })
    },
    
    initPickSquaresResult: function()
    {
        var is_live = jQuery('#is_live').val();

        //check live
        if(is_live == 1)
        {
            jQuery.picksquares.liveEntriesResult();
            setInterval(function(){ 
                jQuery.picksquares.liveEntriesResult();
            }, 60000);
        }
        else
        {
            jQuery.picksquares.loadResult();
        }
        
        //result detail
        jQuery(document).on('click', '#table_standing tr', function(){
            result_select_id = jQuery(this).attr('id');
            jQuery('#table_standing tr').removeClass('active');
            jQuery(this).addClass('active');
            var opponent_id = jQuery(this).data('user_id');
            var opponent_entry_number = jQuery(this).data('entry_number');
            jQuery.picksquares.loadResultDetail(opponent_id, opponent_entry_number);
        })
    },
    
    loadResult: function (page)
    {
        var league_id = jQuery('#league_id').val();
        var entry_number = jQuery('#entry_number').val();
        if(typeof page != "undefined")
        {
            result_select_id = "";
            result_select_page = page;
        }
        if(result_select_page != "")
        {
            page = result_select_page;
        }
        jQuery.global.showLoading('#wrapper_standing');
        var data = {
            action: 'pickSquaresLoadResult',
            page: typeof page != 'undefined' ? page : 1,
            league_id: league_id,
            user_id: jQuery('#user_id').val(),
            entry_number: entry_number,
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#result').html("");
            jQuery('#standing').html(result);
            if(result_select_id != "")
            {
                jQuery('#' + result_select_id).trigger('click');
            }
            else
            {
                jQuery('#table_standing tbody tr:first').trigger('click');
            }
        });
    },
    
    loadResultDetail: function (opponent_id, opponent_entry_number)
    {
        var league_id = jQuery('#league_id').val();
        var user_id = jQuery('#user_id').val();
        var entry_number = jQuery('#entry_number').val();
        
        jQuery.global.showLoading('#result');
        var data = {
            action: 'pickSquaresLoadResultDetail',
            league_id: league_id,
            user_id: user_id,
            entry_number: entry_number,
            opponent_id: opponent_id,
            opponent_entry_number: opponent_entry_number,
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#result').html(result);
        });
    },
    
    liveEntriesResult: function ()
    {
        var league_id = jQuery('#league_id').val();
        var data = {
            action: 'liveEntriesResult',
            leagueID: league_id
        };
        jQuery.post(ajaxurl, data, function () {
            jQuery.picksquares.loadResult();
        });
    },
}