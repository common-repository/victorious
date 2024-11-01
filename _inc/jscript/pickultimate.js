var result_select_id = "";
var result_select_page = "";
jQuery.pickultimate =
{
    initPickUltimate: function()
    {
        jQuery('.vc-select-winner').click(function(){
            var parent = jQuery(this).closest('td');
            parent.find('input[type=radio]').removeAttr('checked');
            parent.find('.vc-select-winner').removeClass('active');
            jQuery(this).find('input[type=radio]').prop('checked', true);
            jQuery(this).addClass('active');
        });

        //submit data
        jQuery(document).on('click', '#btnSubmit', function(){
            if(!jQuery.pickultimate.validatePick()) {
                return;
            }
            jQuery.global.disableButton('btnSubmit', wpfs['working'] + '...');

            //submit data
            jQuery.post(ajaxurl, 'action=submitPickUltimate&' + jQuery('#formData').serialize(), function(result) {
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
        })
    },

    validatePick: function() {
        var ifAnyBoutChecked = true;
        jQuery('#formData tr td').each(function(){
            if(jQuery(this).find('input[type=radio]').length > 0 && !jQuery(this).find('input[type=radio]').is(':checked'))
            {
                ifAnyBoutChecked = false;
                return;
            }
        })

        if(!ifAnyBoutChecked )
        {
            alert(wpfs['input_picks']);
            return false;
        }
        return true;
    },

    initPickUltimateResult: function()
    {
        var is_live = jQuery('#is_live').val();

        //check live
        jQuery.pickultimate.loadResult();
        if(is_live == 1)
        {
            //jQuery.pickultimate.liveEntriesResult();
            setInterval(function(){
                jQuery.pickultimate.liveEntriesResult();
            }, 60000);
        }

        //result detail
        jQuery(document).on('click', '#table_standing tbody tr', function(){
            result_select_id = jQuery(this).attr('id');
            jQuery('#table_standing tr').removeClass('active');
            jQuery(this).addClass('active');
            var opponent_id = jQuery(this).data('user_id');
            var opponent_entry_number = jQuery(this).data('entry_number');
            jQuery.pickultimate.loadResultDetail(opponent_id, opponent_entry_number);
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
        jQuery.global.showLoading('#vc-leaderboard');
        var data = {
            action: 'pickUltimateLoadResult',
            page: typeof page != 'undefined' ? page : 1,
            league_id: league_id,
            user_id: jQuery('#user_id').val(),
            entry_number: entry_number,
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery('#result').html("");
            jQuery('#vc-leaderboard').html(result);
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

        jQuery.global.showLoading('#vc-leaderboard');
        jQuery.global.showLoading('#vc-leaderboard-detail');
        var data = {
            action: 'pickUltimateLoadResultDetail',
            league_id: league_id,
            user_id: user_id,
            entry_number: entry_number,
            opponent_id: opponent_id,
            opponent_entry_number: opponent_entry_number,
            week: jQuery('#result_week').val()
        };
        jQuery.post(ajaxurl, data, function (result) {
            jQuery.global.hideLoading('#vc-leaderboard');
            jQuery('#vc-leaderboard-detail').html(result);
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
            jQuery.pickultimate.loadResult();
        });
    },
};