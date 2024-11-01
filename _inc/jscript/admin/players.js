jQuery.players = 
{
    setData: function()
    {
        this.aTeams = jQuery('#teamsData').val();
        this.aPositions = jQuery('#positionsData').val();
    },
    
    loadTeams: function()
    {
        var aTeams = jQuery.parseJSON(this.aTeams);
        var orgID = jQuery('#org').val();
        var selectTeam = jQuery('#selectTeam').val();
        var html = 
            '<select name="val[team_id]">\n\
                <option value="0">None</option>';
        if(aTeams != null)
        {
            var aTeam = '';
            var select = ''
            for(var i = 0; i < aTeams.length; i++)
            {
                aTeam = aTeams[i];
                select = '';
                if(selectTeam == aTeam.teamID)
                {
                    select = 'selected="true"';
                }
                if(aTeam.organization_id == orgID)
                {
                    html += '<option ' + select + ' value="' + aTeam.teamID + '">' + aTeam.name + '</option>';
                }
            }
        }
        html += '</select>';
        jQuery('#htmlTeams').empty().append(html);
    },
    
    loadPositions: function()
    {
        var aPositions = jQuery.parseJSON(this.aPositions);
        var orgID = jQuery('#org').val();
        var selectPosition = jQuery('#selectPosition').val();
        var html = '<select name="val[position_id]">';
        if(aPositions != null)
        {
            var aPosition = '';
            var select = ''
            for(var i = 0; i < aPositions.length; i++)
            {
                aPosition = aPositions[i];
                select = '';
                if(selectPosition == aPosition.id)
                {
                    select = 'selected="true"';
                }
                if(aPosition.org_id == orgID)
                {
                    html += '<option ' + select + ' value="' + aPosition.id + '">' + aPosition.name + '</option>';
                }
            }
        }
        html += '</select>';
        jQuery('#htmlPositions').empty().append(html);
        // filter motocross org
         jQuery('#htmlTeams').closest('tr').show();
         jQuery('#salary').closest('tr').show();
        jQuery('#htmlPositions').closest('tr').show();
        jQuery('#country').closest('tr').hide();
        var is_motocross = this.checkOrgMotocross(orgID);
           jQuery('#is_privateers').prop('disabled',true);
            jQuery('#is_privateers').closest('tr').hide();
        if(is_motocross){
            jQuery('#htmlTeams').closest('tr').hide();
            jQuery('#salary').closest('tr').hide();
            jQuery('#htmlPositions').closest('tr').hide();
            jQuery('#country').closest('tr').show();
            jQuery('#is_privateers').prop('disabled',false);
            jQuery('#is_privateers').closest('tr').show();
            

        }
        
    },
    checkOrgMotocross: function(org_id){
        var motocross_org = jQuery('#listMotocrossOrg').val();
        motocross_org = JSON.parse(motocross_org);
        if(typeof motocross_org !='undefined' && motocross_org.length > 0){
            for(var i in motocross_org){
                if(motocross_org[i] == org_id){
                    return true;
                }
            }
            
        }
        return false;
    }
}