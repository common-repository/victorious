var nonRoundValues = new Array(2,4,6,7,8,9,15,17,18,19,20);
function importPicks(picks, methods, rounds, minutes, spread, over_under)
{
    jQuery("input[type='radio']").removeAttr('checked');
    picks = picks.split(",");
    methods =  methods.split(",");
    rounds =  rounds.split(",");
    minutes =  minutes.split(",");
    spread =  spread.split(",");
    over_under = over_under.split(",");
    jQuery(".fightID").each(function(){
        var index = picks.indexOf(jQuery(this).val());
        if(index > -1)
        {
            var fightID = jQuery(this).attr("data-fightid");
            jQuery(this).attr("checked", "checked");
            jQuery("#method" + fightID).val(methods[index]);
            jQuery("#round" + fightID).val(rounds[index]);
            jQuery("#minute" + fightID).val(minutes[index]);
            jQuery("input[name='spread"  + fightID + "']").each(function(){
                if(jQuery(this).val() == spread[index])
                {
                    jQuery(this).attr("checked", "checked");
                }
            })
            jQuery("input[name='over_under_value"  + fightID + "']").each(function(){
                if(jQuery(this).val() == over_under[index])
                {
                    jQuery(this).attr("checked", "checked");
                }
            })
            checkMethod(methods[index], fightID);
        }
    });
}
function pickSelected(leagueID)
{
    var ifAnyBoutChecked = true;
    jQuery('#submitPicksForm table:first tr').each(function(){
        if(jQuery(this).find('input[type=radio]').length > 0 && !jQuery(this).find('input[type=radio]').is(':checked'))
        {
            ifAnyBoutChecked = false;
            return;
        }
    })

    if ( leagueID )
    {
            jQuery('#submitPicksForm input[name="is_league"]').val(1);
    }

    if ( !ifAnyBoutChecked )
    {
            alert(wpfs['input_picks']);
            return false;
    }
    return true;
}
function checkMethod(value,fightID)
{
        var roundSelectName = "#round" + fightID;
        var minuteSelectName = "#minute" + fightID;
        for (i=0; i < nonRoundValues.length; i++)
        {
                if ( nonRoundValues[i] == value )
                {
                        jQuery(roundSelectName).val(-1);
                        jQuery(minuteSelectName).val(-1);
                        jQuery(roundSelectName).attr('disabled', 'disabled');
                        jQuery(minuteSelectName).attr('disabled', 'disabled');
                        return true;
                }
        }
        jQuery(roundSelectName).removeAttr('disabled');
        jQuery(minuteSelectName).removeAttr('disabled');
        return true;
}

jQuery(window).load(function(){
    jQuery(".method").each(function(){
        checkMethod(jQuery(this).val(), jQuery(this).attr("data-id"));
    })
})