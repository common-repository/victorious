jQuery.fight =
{
    setData : function(aSports, aPositions, lineup)
    {
        this.aSports = aSports;
        this.aPositions = aPositions;
        this.lineup = lineup;
        this.only_playerdraft = 0;
    },
    
    loadSport: function(sel)
    {
        var aSports = jQuery.parseJSON(this.aSports);
        var result = '<select id="poolSport" class="sport" name="val[type]" onchange="jQuery.fight.loadOrgsBySport();jQuery.fight.displayType();">';
        for(var i = 0; i < aSports.length; i++)
        {
            var aSport = aSports[i];
            var select = '';
            if(aSport.name == sel)
            {
                select = 'selected="true"';
            }
            result += '<option ' + select + ' value="' + aSport.name + '">' + aSport.name + '</option>';
        }
        result += '</select>';
        jQuery('#sportResult').empty().append(result);
    },
    
    loadPosition: function()
    {
        var aPositions =  typeof(this.aPositions) !== "undefined" ? jQuery.parseJSON(this.aPositions) : [];
        var data = '';
        if(this.lineup != '')
        {
            data = typeof(this.lineup) !== "undefined" ? jQuery.parseJSON(this.lineup) : [];
        }
        var org_id = jQuery('#poolOrgs').val();
        var result = '<table>';
        var hasPosition = false;
        var aSoccer = jQuery("#gameTypeSoccer").val();
        if(aSoccer != null){
            aSoccer = jQuery.parseJSON(aSoccer);
            for(var i in aSoccer){
                if(aSoccer[i].sport_id == org_id){
                    var str = aSoccer[i].game_type;
                    if(str.indexOf('playerdraft') != -1){
                        org_id=42;
                    }
                }
            }
        }
        for(var i = 0; i < aPositions.length; i++)
        {
            var aPosition = aPositions[i];
            if(aPosition.org_id == org_id)
            {
                hasPosition = true;
                var total = 0;
                var checked = 'checked="true"';
                if(data != '')
                {
                    for(var j = 0; j < data.length; j++)
                    {
                        if(data[j].id == aPosition.id)
                        {
                            total = data[j].total;
                            if(data[j].enable == 1)
                            {
                                checked = 'checked="true"';
                            }
                            else 
                            {
                                checked = '';
                            }
                            break;
                        }
                    }
                }
                result +=   '<tr>\n\
                                <td><span class="me-2">' + aPosition.name + '</span></td>\n\
                                <td><input type="text" name="val[lineup][' + aPosition.id + '][total]" value="' + total + '" /></td>\n\
                                <td><label class="checkbox-control ml-2"><input type="checkbox" name="val[lineup][' + aPosition.id + '][enable]" ' + checked + ' value="1" /><span class="checkmark"></span></label></td>\n\
                            </tr>';
            }
        }
        result += '</table>';
        if(!hasPosition)
        {
            jQuery('.for_playerdraft').hide();
        }
        else 
        {
            jQuery('.for_playerdraft').show();
        }
        if(jQuery('option:selected', "#poolOrgs").attr('only_playerdraft') == 1)
        {
                    var motocross_orgs = jQuery('#motocross_orgs').val();
                    motocross_orgs = JSON.parse(motocross_orgs);
                    var org_id = jQuery('#poolOrgs').val();
                    jQuery('.salary_cap').show();
                    if(jQuery.fight.checkValueExist(motocross_orgs,org_id)){
                         jQuery('.salary_cap').hide();
                    }
                    
        }
        jQuery('#lineupResult').empty().append(result);
    },
    
    addFight : function(oObj)
    {
        var fightItem = jQuery(oObj).parents('.fight_container');
        var cloneItem = fightItem.clone();
        cloneItem.find('select option').removeAttr('selected');
        cloneItem.find('input[type=text]').val('');
        cloneItem.find('input[type=checkbox]').removeAttr('checked');
        cloneItem.find('input[data-name=fightID]').val('');
        fightItem.after(cloneItem);
        cloneItem.find('.fightDatePicker').removeClass('hasDatepicker').removeAttr('id');
        cloneItem.find(".fightDatePicker").datepicker({
            dateFormat: 'yy-mm-dd'
        });
        this.fixFightIndexs();
        
        return false;
    },
    
    removeFight : function(oObj)
    {
        if(confirm(wpfs['a_sure']))
        {
            jQuery(oObj).parents('.fight_container').remove();
            this.fixFightIndexs();
        }
        return false;
    },
    
    fixFightIndexs: function(){
        var index = 0;
        jQuery('.fight_container').each(function(){
            index++;
            jQuery(this).find('.fight_number_title').empty().append('*Fixture ' + index);
            jQuery(this).find('.fight').val(index);
            
            //parse index for fight data
            jQuery(this).find('select').each(function(){
                jQuery(this).attr('name', 'val[' + jQuery(this).attr('data-name') + '][' + index + ']')
            })
            jQuery(this).find('input:not(.fight)').each(function(){
                jQuery(this).attr('name', 'val[' + jQuery(this).attr('data-name') + '][' + index + ']')
            })
        })
        if(jQuery('.fight_container').length == 1)
        {
            jQuery('.fight_container .fight_remove').hide();
        }
        else 
        {
            jQuery('.fight_container .fight_remove').show();
        }
    },
    
    displayType: function(){
        var upload_photo = jQuery('option:selected', "#poolOrgs").attr('upload_photo');
        if(upload_photo == 1){
            jQuery('.for_normal_fight').hide();
            jQuery('.for_fighter').hide();
            jQuery('.for_sportbook').hide();
            jQuery('.for_playerdraft').hide();
            jQuery('.for_round').hide();
            jQuery('.for_motocross').hide();
        }
        else{
            jQuery('.for_normal_fight').show();
            jQuery('.for_fighter').show();
            jQuery('.for_sportbook').show();

            //check only playerdraft
            this.only_playerdraft = jQuery('option:selected', "#poolOrgs").attr('only_playerdraft');
            var allow_motocross = jQuery('#allow_motocross').val();
            var motocross_orgs = jQuery('#motocross_orgs').val();
            motocross_orgs = typeof(motocross_orgs) !== "undefined" ? JSON.parse(motocross_orgs) : [];
            var org_id =  jQuery('#poolOrgs').val();
            if(this.only_playerdraft == 1)
            {
                jQuery('.exclude_fixture').hide();
                jQuery('.fight_container input').attr('disabled', 'true');
                jQuery('.fight_container select').attr('disabled', 'true');
            }
            else 
            {
                jQuery('.exclude_fixture').show();
                jQuery('.fight_container input').removeAttr('disabled');
                jQuery('.fight_container select').removeAttr('disabled');
            }

            var is_team = jQuery('option:selected', "#poolOrgs").attr('is_team');
            if(is_team == 0)
            {
                jQuery('.for_fighter').show();
                jQuery('.for_team').hide();
                jQuery('select.for_fighter').removeAttr('disabled');
                jQuery('select.for_team').attr('disabled', 'true');
            }
            else
            {
                jQuery('.for_fighter').hide();
                jQuery('.for_team').show();
                jQuery('select.for_team').removeAttr('disabled');
                jQuery('select.for_fighter').attr('disabled', 'true');
            }

            var is_round = jQuery('option:selected', "#poolOrgs").attr('is_round');
            if(is_round == 1)
            {
                jQuery('.for_round').show();
            }
            else
            {
                jQuery('.for_round').hide();
            }

            if(allow_motocross == 1 && motocross_orgs.length > 0 && jQuery.fight.checkValueExist(motocross_orgs,org_id)){
                jQuery('.exclude_fixture').hide();

                jQuery('.for_team').hide();
                jQuery('select.for_team').attr('disabled', 'true');

                jQuery('.for_fighter').hide();
                jQuery('select.for_fighter').attr('disabled', 'true');
                jQuery('.fight_container input').attr('disabled', 'true');
                jQuery('.fight_container select').attr('disabled', 'true');

                jQuery('.for_round').hide();

                jQuery('.for_motocross').show();
                jQuery('.motocross_container input').attr('disabled',false);  
                jQuery('.for_playerdraft.salary_cap').hide();
                jQuery('.motocross_container').closest('tr').hide();


            }else{
                   jQuery('.for_motocross').hide();
                   jQuery('.motocross_container input').attr('disabled',true);
                   jQuery('.salary_cap').show();
            }
        }
        return false;
    },
    checkValueExist :function(lists,value){
        for( var i in lists){
            if(lists[i] == value){
                return true;
            }
        }
        return false;
    },
    addMotocross: function(obj){
        var mtIteam = jQuery(obj).closest('.motocross_container');
        var newMtIteam = mtIteam.clone();
        newMtIteam.find('input').val('');
        mtIteam.after(newMtIteam);
        jQuery.fight.fixMotocrossIndex();
    },
    removeMotocross: function(obj){
         if(confirm(wpfs['a_sure']))
        {
            jQuery(obj).parents('.motocross_container').remove();
            this.fixMotocrossIndex();
        }
        return false;
    },
    fixMotocrossIndex: function(){
        var index = 1;
        jQuery('.motocross_container').each(function(){
            jQuery(this).children('input[type=text]').attr('name','val[val_moto]['+index+']');
            jQuery(this).children('label').text('Lap '+index);
            index++;
        });
        if(jQuery('.motocross_container').length == 1)
        {
            jQuery('.motocross_container .mtc_remove').hide();
        }
        else 
        {
            jQuery('.motocross_container .mtc_remove').show();
        }
    },
    loadOrgsBySport: function(sel){
        var sport = jQuery('#poolSport').val();
        var sel = jQuery('#selOrgs').val();
        
        var aSports = jQuery.parseJSON(this.aSports);
        var result = '<select id="poolOrgs" onchange="jQuery.fight.loadFightersOrTeams()" name="val[organization]">';
        for(var i = 0; i < aSports.length; i++)
        {
            var aSport = aSports[i];
            if(aSport.name == sport)
            {
                for(var j = 0; j < aSport.child.length; j++)
                {
                    var org = aSport.child[j];
                    var select = '';
                    if(org.id == sel)
                    {
                        select = 'selected="true"';
                    }
                    result += '<option ' + select + ' value="' + org.organizationID + '">' + org.description + '</option>';
                }
            }
        }
        result += '</select>';
        jQuery('#orgResult').empty().append(result);
        this.loadFightersOrTeams();
        this.loadPosition();
    },
    
    loadFightersOrTeams: function(){
        if(this.only_playerdraft == 0)
        {
            var orgs = jQuery('#poolOrgs').val();
            var is_team = jQuery('option:selected', "#poolOrgs").attr('is_team');
            if(is_team == 0)
            {
                var data = {
                    action: 'loadCbFighters',
                    orgsID: orgs,
                };
                jQuery.post(ajaxurl, data, function(result){
                    jQuery('.cbfighter').empty().append(result);
                    jQuery('.cbfighter').each(function(){
                        jQuery(this).val(jQuery(this).attr('data-sel'));
                    });
                })
            }
            else
            {
                var data = {
                    action: 'loadCbTeams',
                    orgsID: orgs,
                };
                jQuery.post(ajaxurl, data, function(result){
                    jQuery('.cbteam').empty().append(result);
                    jQuery('.cbteam').each(function(){
                        jQuery(this).val(jQuery(this).attr('data-sel'));
                    });
                })
            }
        }
    },
    
    viewResult: function(iPoolID, sTitle){
        jQuery("#resultDialog").empty().append("<center>Loading...Please wait!</center>");
        var dialog = jQuery("#resultDialog").dialog({
            maxHeight: 500,
            width:800,
            minWidth:600,
            modal:true,
            title:sTitle,
            open: function() {
                jQuery('.ui-widget-overlay').addClass('custom-overlay');
            }
        });
        
        var data = {
            action: 'viewResult',
            iPoolID: iPoolID,
        };
        jQuery.post(ajaxurl, data, function(result){
            jQuery("#resultDialog").empty().append(result);
            jQuery("#resultDialog").dialog({
                buttons: {
                    "Update": function() {
                        jQuery.fight.updateResult();
                    },
                    "Close": function() {
                        dialog.dialog( "close" );
                    }
                }
            });
        })
    },
    
    updateResult: function(){
        var data = 'action=updateResult&' + jQuery('#formResult').serialize();
        jQuery.post(ajaxurl, data, function(result){
            alert(result);
            jQuery("#resultDialog").dialog('close');
        })
    },
    
    updatePoolStatus: function(iPoolID, oObj, curValue){
        if(confirm(wpfs['a_sure']))
        {
            var data = {
                action: 'updatePoolComplete',
                iPoolID: iPoolID,
                status: jQuery(oObj).val(),
            };
            jQuery.post(ajaxurl, data, function(result){
                var data = JSON.parse(result);
                if(data.notice)
                {
                    alert(data.notice);
                    jQuery(oObj).val(curValue);
                }
                else
                {
                    alert(data.result);
                    if(jQuery(oObj).val().toLowerCase() == 'complete')
                    {
                        jQuery(oObj).attr('disabled', true);
                        jQuery(oObj).closest('tr').find('.column-result a').hide();
                        jQuery(oObj).closest('tr').find('.column-playerdraft_result a').hide();
                        jQuery(oObj).closest('tr').find('.column-edit a').hide();
                        jQuery(oObj).closest('tr').find('.btn-reverse').show();
                    }
                }
            })
        }
        else 
        {
            jQuery(oObj).val(curValue);
        }
    },
    
    ////////////////////////v2////////////////////////
    viewPlayerDraftResult: function(iPoolID, sTitle){
        jQuery("#resultDialog").empty().append("<center>Loading...Please wait!</center>");
        var dialog = jQuery("#resultDialog").dialog({
            maxHeight: 500,
            width:800,
            minWidth:600,
            modal:true,
            title:sTitle,
            open: function() {
                jQuery('.ui-widget-overlay').addClass('custom-overlay');
            }
        });
        
        var type = jQuery('#results_' + iPoolID).attr('data-file');
        var select_file = 'selected="selected"';
        var select_result = 'selected="selected"';
        if(type == 0)
        {
            select_file = "";
        }
        else
        {
            select_result = "";
        }
        var sbHtml = '<select class="select_type_load_'+iPoolID+'" style="margin-bottom:10px;" onchange="jQuery.fight.selectTypeLoad('+iPoolID+')"><option value="0" ' + select_file + '>Upload File</option><option value="1" ' + select_result + '>Load Result</option></select>';
        jQuery("#resultDialog").empty().append(sbHtml);
        jQuery("#resultDialog").dialog({
                buttons: {
                    "Add": function() {
                        jQuery.fight.updatePlayerDraftResult();
                    },
                    "Close": function() {
                        dialog.dialog( "close" );
                    }
                }
            });
            jQuery.fight.selectTypeLoad(iPoolID);
        return false;
    },
    selectTypeLoad: function(poolID){
        var iLimit = 10;
        var Ipage = 1;
        if(jQuery('.select_type_load_'+poolID).val() == '0'){ // poolID
            jQuery('#formResult').remove();
            jQuery('#resultMessage').remove();
            jQuery.fight.loadUploadFileStats(poolID);
            jQuery('.ui-dialog-buttonset').hide();
            // show stats for upload file + pagination
            jQuery.fight.loadStatsUploadedFile(poolID,iLimit,Ipage);   
        }else{
            jQuery('.ui-dialog-buttonset').show();
            jQuery('.place_upload_file').remove();
            jQuery.fight.loadDataResult(poolID);
        }
    },
    loadDataResult: function(poolID){
        var data = {
            action: 'viewPlayerDraftResult',
            iPoolID: poolID
        };
        jQuery.post(ajaxurl, data, function(result){
            result = jQuery.parseJSON(result);
            jQuery.fight.loadPlayerDraftResult(result.pool, result.fights, result.rounds);
            jQuery.fight.loadPlayerPoints(result.scoring_cat, 1);

        });
    }
    ,
    loadUploadFileStats: function(poolID){

        var info = jQuery('#info_upload_'+poolID).val();
        info = jQuery.parseJSON(info);
        var html = '<div class="place_upload_file"><input type="file" name="file_content_'+poolID+'" id="file_content_'+poolID+'"><button onclick="jQuery.fight.uploadFileStats('+poolID+')">Upload</button><a style="margin-left:10px;"  href="'+linkFileSample+'"> File Sample</a>';
            html+= '<div class="message_upload_'+poolID+'"></div>';
            html+='<div class="file_upload_name">'+info.uploaded_file+'</div>';
            html+='<div class="content_stats_file"></div>';
            html+='</div>';

             jQuery('#resultDialog').append(html);

    },
    uploadFileStats: function(poolID){
        var info_upload = JSON.parse(jQuery('#info_upload_'+poolID).val());
        var file_data = jQuery("#file_content_"+poolID).prop("files")[0];
        var form_data = new FormData(); 
        form_data.append("file", file_data); 
        form_data.append("org_id", info_upload.org_id); 
        form_data.append("poolID", poolID); 
        form_data.append('action','sendUploadedFileStats');
        jQuery.ajax({
                url: ajaxurl,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form_data,                         // Setting the data attribute of ajax with file_data
                type: 'post',
                success: function(data){
                    jQuery('#results_' + poolID).attr('data-file', 1);
                    switch(data.result){
                        case "0":
                            jQuery('.message_upload_'+poolID).html('Please select file upload');
                            break;
                        case "1":
                            jQuery('.message_upload_'+poolID).html('Upload failed');
                            break;    
                        case "2":
                            jQuery('.message_upload_'+poolID).html('File type must be csv');
                            break;    
                        case "3":
                            jQuery('.message_upload_'+poolID).html('Upload successfully');

                                      info_upload.uploaded_file = file_data.name;
                                      jQuery('#info_upload_'+poolID).val(JSON.stringify(info_upload));
                                      jQuery.fight.loadStatsUploadedFile(poolID,10,1);
                                      
                            break;
                    }
                }
       });
    }
    ,
    loadStatsUploadedFile: function(poolID,limit,page){
       var info = jQuery('#info_upload_'+poolID).val();
            info = jQuery.parseJSON(info);
            if(info.uploaded_file == ''){
                return;
            }
       var data = 'action=loadStatsUploadedFile&'+'poolID='+poolID+'&org_id='+info.org_id+'&iLimit='+limit +'&iPage='+page;
       jQuery.post(ajaxurl, data, function(result){
            result = jQuery.parseJSON(result);
           if(result != null && result.data.length > 0 && Object.keys(result.titles).length > 0){
               var contentTh = '';
               var aTitles = result.titles;
               var content = '';
               for(var i in aTitles){
                   contentTh+='<th>'+aTitles[i]+'</th>';
               }
               var aStats = result.data;
               for(var i in aStats){
                   var aStatsPlayer = aStats[i];
                   content+='<tr>';
                   for(var j in aStatsPlayer){
                       content+='<td>'+aStatsPlayer[j]+'</td>';
                   }
                   content+='</tr>';
               }
               var htmlContent = '<table class="tbl_show_stats" style="min-width:500px;" cellspacing="10" cellpadding="2">'+contentTh+content+'</table>';
               jQuery(".content_stats_file").empty().append(htmlContent);
               // pagination
               if(result.pages > 1){
                   var prev_page = 1;
                   if(page > 1){
                       prev_page = page - 1;
                   }
                   var htmlContent = '<div class="tablenav bottom">';
                        htmlContent += '<div class="alignleft actions bulkactions"></div>';
                        htmlContent += '<div class="tablenav-pages">';
                            htmlContent += '<span class="pagination-links">';
                                htmlContent +='<a class="first-page" href="#/prototype" onclick="jQuery.fight.loadStatsUploadedFile('+poolID+','+limit+','+1+');"><span class="screen-reader-text">First page</span><span aria-hidden="true">«</span></a>';
                                htmlContent +='<a class="prev-page" href="#/" onclick="jQuery.fight.loadStatsUploadedFile('+poolID+','+limit+','+prev_page+');"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>';
                                htmlContent += '<span class="screen-reader-text">Current Page</span>';
                                htmlContent += '<span id="table-paging" class="paging-input">'+page+' of <span class="total-pages">'+result.pages+'</span></span>';
                                htmlContent += '<a class="next-page" href="#/" onclick="jQuery.fight.loadStatsUploadedFile('+poolID+','+limit+','+(page+1)+');"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>';
                                htmlContent += '<a class="next-page" href="#/" onclick="jQuery.fight.loadStatsUploadedFile('+poolID+','+limit+','+result.pages+');"><span class="screen-reader-text">Next page</span><span aria-hidden="true">»</span></a>';
                            htmlContent += '</span>';
                        htmlContent += '</div>';
                   htmlContent += '</div>';
                   jQuery(".content_stats_file").append(htmlContent);
               }
               
               
           }else{
               
           }
        });
    
    }
    ,
    loadPlayerDraftResult: function(aPool, aFights, aRounds)
    {
        this.aFights = aFights;
        this.aRounds = aRounds;
        this.aPool = aPool;
        var html = '';
        
        //fight
        var htmlCbFight = '<select name="fightID" id="cbFight" onchange="jQuery.fight.loadPlayerPoints(null, 1)">';
        if(aFights != null && aFights.length > 0)
        {
            for(var i in aFights)
            {
                var aFight = aFights[i];
                htmlCbFight += '<option value="' + aFight.fightID + '">' + aFight.name + '</option>';
            }
        }
        htmlCbFight += '</select>';
        var htmlFight = '';
        if(aPool.only_playerdraft == 0)
        {
            htmlFight  = 
                '<div class="table">\n\
                    <div class="table_left">Fight: </div>\n\
                    <div class="table_right">\n\
                        ' + htmlCbFight + '\n\
                    </div>\n\
                    <div class="clear"></div>\n\
                </div>';
        }
        html = '<div id="resultMessage"></div>\n\
                    <form id="formResult">\n\
                    <input type="hidden" name="poolID" value="' + aPool.poolID + '" />\n\
                    ' + htmlFight + '\n\
                    <div class="table">\n\
                        <div class="table_left" style="width:100px;">Scoring: </div>\n\
                        <div class="table_right" id="resultScoring" style="margin-left:100px;">\n\
                        </div>\n\
                        <div class="clear"></div>\n\
                    </div>\n\
                </div>\n\
                <div id="load_result_paging_content"></div>';
        
        
        //round
        var htmlCbRound = '<select name="roundID" id="cbRound" onchange="jQuery.fight.loadPlayerPoints(null, 1)">';
        if(aRounds != null && aRounds.length > 0)
        {
            for(var i in aRounds)
            {
                var aRound = aRounds[i];
                htmlCbRound += '<option value="' + aRound.id + '">' + aRound.name + '</option>';
            }
        }
        htmlCbRound += '</select>';
        var htmlRound = '';
        if(aPool.is_round == 1)
        {
            htmlRound  = 
                '<div class="table">\n\
                    <div class="table_left round-title">Round: </div>\n\
                    <div class="table_right holeshot">\n\
                        ' + htmlCbRound + '\n\
                    </div>\n\
                    <div class="clear"></div>\n\
                </div>';
            html = '<div id="resultMessage"></div>\n\
                        <form id="formResult">\n\
                        <input type="hidden" name="poolID" value="' + aPool.poolID + '" />\n\
                        ' + htmlRound + '\n\
                        <div class="table">\n\
                            <div class="table_left" style="width:100px;">Scoring: </div>\n\
                            <div class="table_right" id="resultScoring" style="margin-left:100px;">\n\
                            </div>\n\
                            <div class="clear"></div>\n\
                        </div>\n\
                    </div>';
        }
       
       
        jQuery("#resultDialog").append(html);
    },
    
    loadPlayersResult: function()
    {
        var aPlayers = this.aPlayers;
        var aFights = this.aFights;
        var aPool = this.aPool;
        var fightID = jQuery('#cbFight').val();
        var teamID1 = ''; 
        var teamID2 = '';
        if(aFights != null && aFights.length > 0)
        {
            for(var i in aFights)
            {
                if(aFights[i].fightID == fightID)
                {
                    teamID1 = aFights[i].fighterID1;
                    teamID2 = aFights[i].fighterID2;
                }
            }
        }
        
        var result = '<select name="playerID" id="cbPlayers" onchange="jQuery.fight.loadPlayerPoints()">';
        for(var i in aPlayers)
        {
            var aPlayer = aPlayers[i];
            if(aPool.only_playerdraft == 0 && (aPlayer.team_id == teamID1 || aPlayer.team_id == teamID2))
            {
                result += '<option value="' + aPlayer.id + '">' + aPlayer.name + '</option>';
            }
            else if(aPool.only_playerdraft == 1)
            {
                result += '<option value="' + aPlayer.id + '">' + aPlayer.name + '</option>';
            }
        }
        result += '</select>';
        jQuery('#resultPlayer').empty().append(result);
    },
    
    loadPlayerPoints: function(resultScoringCat, page)
    {
        var iLimit = 10;
        if(typeof resultScoringCat != 'undefined' && resultScoringCat != null)
        {
            this.resultScoringCat = resultScoringCat;
        }
        var fightID = jQuery('#cbFight').val();
        var roundID = jQuery('#cbRound').val();
        var playerID = jQuery('#cbPlayers').val();
        var aPool = this.aPool;
        var aScoringCats = this.resultScoringCat;
        var data = 'action=loadPlayerPoints&poolID=' + aPool.poolID + '&fightID=' + fightID + '&roundID=' + roundID + '&playerID=' + playerID + '&page=' + page;
        jQuery.post(ajaxurl, data, function(result){
            result = jQuery.parseJSON(result);
            var aPlayers = result.players;
            var paging = result.paging;
            var i, j, aPlayer, aScoringCat, aPlayerScoring, point;
            var total_players = result.total;
            var html = '<table>\n\
                <tr>\n\
                    <td style="width:200px"></td>';
            if(aScoringCats != null)
            {
                for(i in aScoringCats)
                {
                    aScoringCat = aScoringCats[i];
                    html += '<td style="width:35px;text-align:center">' + aScoringCat.name + '</td>';
                }
            }
            html +=  '<tr>';
            
            if(aPlayers != null)
            {
                for(i in aPlayers)
                {
                    aPlayer = aPlayers[i];
                    aPlayerScoring = aPlayer.scorings;
                    html += 
                        '<tr>\n\
                            <td>\n\
                                ' + aPlayer.name + '\n\
                                <input type="hidden" name="playerID[]" value="' + aPlayer.id + '" />\n\
                            </td>';
                    for(j in aScoringCats)
                    {
                        aScoringCat = aScoringCats[j];
                        point = jQuery.fight.parsePlayerScoring(aScoringCat.id, aPlayerScoring);
                        html += '<td><input type="text" style="width:100%" name="scoring_category_id[' + aPlayer.id + '][' + aScoringCat.id + ']" value="' + point + '" /></td>';
                    }
                    html += '</tr>';
                }
            }
            html += '</table>';
            if(paging != null)
            {
                html += '<div class="tablenav bottom">\n\
                            <div class="tablenav-pages">\n\
                                <span class="pagination-links">' + paging + '</span>\n\
                            </div>\n\
                        </div>';
            }
            jQuery('#resultScoring').empty().append(html);
            
            var total_pages = Math.ceil(total_players/iLimit);
            if(total_pages > 1){
                var prev_page = 1;
                if(page > 1){
                    prev_page = page - 1; 
                }
//                var htmlContent = '<div class="tablenav bottom">';
//                        htmlContent += '<div class="alignleft actions bulkactions"></div>';
//                        htmlContent += '<div class="tablenav-pages">';
//                            htmlContent += '<span class="pagination-links">';
//                                htmlContent +='<a class="first-page" href="#/prototype" onclick="jQuery.fight.loadPlayerPoints('+null+','+1+');"><span class="screen-reader-text">First page</span><span aria-hidden="true">«</span></a>';
//                                htmlContent +='<a class="prev-page" href="#/" onclick="jQuery.fight.loadPlayerPoints('+null+','+prev_page+');"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">‹</span></a>';
//                                htmlContent += '<span class="screen-reader-text">Current Page</span>';
//                                htmlContent += '<span id="table-paging" class="paging-input">'+page+' of <span class="total-pages">'+total_pages+'</span></span>';
//                                htmlContent += '<a class="next-page" href="#/" onclick="jQuery.fight.loadPlayerPoints('+null+','+(page+1)+');"><span class="screen-reader-text">Next page</span><span aria-hidden="true">›</span></a>';
//                                htmlContent += '<a class="next-page" href="#/" onclick="jQuery.fight.loadPlayerPoints('+null+','+total_pages+');"><span class="screen-reader-text">Next page</span><span aria-hidden="true">»</span></a>';
//                            htmlContent += '</span>';
//                        htmlContent += '</div>';
//                   htmlContent += '</div>';
    
                var htmlContent = '<div class="tablenav bottom">';
                        htmlContent += '<div class="alignleft actions bulkactions"></div>';
                        htmlContent += '<div class="tablenav-pages">';
                            htmlContent += '<span class="pagination-links">';
                            if(page != 1){
                                htmlContent +='<a class="prev-page" href="#/" onclick="jQuery.fight.loadPlayerPoints('+null+','+prev_page+');"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">prev</span></a>';
                            }

                               for(var i = 1; i <= total_pages;i++){
                                   if(i == page){
                                        htmlContent +='<a style="border-color: #5b9dd9;color: #fff;background: #00a0d2;box-shadow: none;outline: 0;" class="prev-page" href="#/"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">'+i+'</span></a>';
                                   }else{
                                        htmlContent +='<a class="prev-page" href="#/" onclick="jQuery.fight.loadPlayerPoints('+null+','+i+');"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">'+i+'</span></a>';
                                   }
                               }
                                        if(total_pages != page){
                                            htmlContent +='<a class="prev-page" href="#/" onclick="jQuery.fight.loadPlayerPoints('+null+','+(page+1)+');"><span class="screen-reader-text">Previous page</span><span aria-hidden="true">next</span></a>';
                                        }

                            htmlContent += '</span>';
                        htmlContent += '</div>';
                   htmlContent += '</div>';
            jQuery("#load_result_paging_content").empty().append(htmlContent);
            }

            
        });
    },
    
    parsePlayerScoring: function(scoring_category_id, aPlayerScorings)
    {
        if(aPlayerScorings != null)
        {
            var i;
            for(i in aPlayerScorings)
            {
                if(aPlayerScorings[i].scoring_category_id == scoring_category_id)
                {
                    return aPlayerScorings[i].points;
                }
            }
        }
        return 0;
    },
    
    updatePlayerDraftResult: function(is_motocross)
    {
        var url = 'updatePlayerDraftResult';
        if(typeof is_motocross != 'undefined' && is_motocross != null && is_motocross==true){
            url = 'updateMotocrossPlayerResult';
        }
        var data = 'action='+url+'&' + jQuery('#formResult').serialize();
        jQuery.post(ajaxurl, data, function(result){
            alert(result);
            var poolID = jQuery('#formResult input[name="poolID"]').val();
            jQuery('#results_' + poolID).attr('data-file', 0);
        });
    },
    reverseResult: function(poolID, oObj)
    {
        if(confirm(wpfs['a_sure']))
        {
            jQuery(oObj).closest('tr').find('.btn-reverse').attr('disabled', 'true');
            var data = 'action=reverseResult&poolID=' + poolID;
            jQuery.post(ajaxurl, data, function(result){
                var data = JSON.parse(result);
                if(data.notice)
                {
                    alert(data.notice);
                }
                else
                {
                    alert(data.result);
                    jQuery(oObj).closest('tr').find('.btn-reverse').removeAttr('disabled').hide();
                    jQuery(oObj).closest('tr').find('select').removeAttr('disabled').val('NEW');
                    jQuery(oObj).closest('tr').find('.column-result a').show();
                    jQuery(oObj).closest('tr').find('.column-playerdraft_result a').show();
                    jQuery(oObj).closest('tr').find('.column-edit a').show();
                }
            });
        }
    },
    viewMotocrossPlayerDraftResult: function(iPoolID,sTitle){
         jQuery("#resultDialog").empty().append("<center>Loading...Please wait!</center>");
        var dialog = jQuery("#resultDialog").dialog({
            maxHeight: 500,
            width:800,
            minWidth:600,
            modal:true,
            title:sTitle,
            open: function() {
                jQuery('.ui-widget-overlay').addClass('custom-overlay');
            }
        });

        jQuery("#resultDialog").dialog({
                buttons: {
                    "Add": function() {
                        jQuery.fight.updatePlayerDraftResult(true);
                    },
                    "Close": function() {
                        dialog.dialog( "close" );
                    }
                }
            });
           jQuery.fight.loadMotocrossResult(iPoolID);
        return false;
    }
    ,
    loadMotocrossResult: function(poolID){
        var data = {
            action: 'viewPlayerDraftResult',
            iPoolID: poolID
        };
        jQuery.post(ajaxurl, data, function(result){
            result = jQuery.parseJSON(result);
            jQuery.fight.loadPlayerDraftResult(result.pool,null,result.rounds);
            jQuery('#resultDialog center').hide();
            jQuery('.round-title').html('Laps: ');
            jQuery('.round-title').html('Laps: ');
            jQuery('#resultDialog').append("<input type='hidden' id='pool-detail' value='"+JSON.stringify(result.pool)+"'>");
            jQuery('#resultDialog #formResult select#cbRound').attr('onchange','jQuery.fight.loadMotocrossPlayerResult()');
            jQuery('#resultDialog #formResult select#cbRound').after('<div class="holeshot" style="display:none;float:right">HoleShot<select id="holeshot" name="holeshot"></select></div>');
            jQuery.fight.loadMotocrossPlayerResult();
        });
    },
    loadMotocrossPlayerResult: function(){
       var roundID = jQuery('#resultDialog #formResult select#cbRound').val();
       var pool = JSON.parse(jQuery('#pool-detail').val());
        jQuery('#resultScoring').html('Loading...Please wait!');
       page = 0;
       var data = 'action=loadMotocrossPlayerPoints&poolID=' + pool.poolID + '&roundID=' + roundID +'&org_id='+pool.organization+ '&page=' + page;
       jQuery.post(ajaxurl,data,function(result){
          
           result = jQuery.parseJSON(result);
           var players = result.players;
           var holeshot = result.holeshot;
      
           var num = 1;
           var html = '<table cellpadding="2" cellspacing="10">';
           html+= '<input type="hidden" name="orgID" value="'+pool.organization+'">'
           var input = '';
           for(var i in players){
               var player = players[i];
               if(num == 1 || (num-1)%4 == 0){
                   html+='<tr>';
               }
               input = '';
               input = '<input type="text" name="val['+player.id+']" style="width:50px" value="'+player.pos+'">'
               html+='<td><div class="mt-player-name">'+player.name+'</div>'+input+'</td>';
               
               if(num % 4 == 0){
                   html+='</tr>';
               }
               
               num++;
           }
           html+='</table>';
           jQuery('#resultScoring').html(html);
           // add option for holeshot
           if(jQuery('#holeshot option').length == 0){
              jQuery('#holeshot').closest('.holeshot').show().css({'float':'right','width':'360px'});
              jQuery('#holeshot').css('margin-left','10px');
              var htmlOptions = '';
              htmlOptions +='<option value="'+0+'">Choose holeshot</option>';
              for(var i in players){
                  var player = players[i];
                  htmlOptions+='<option value="'+player.id+'">'+player.name+'</option>';
              }
              jQuery('#holeshot').html(htmlOptions);
           }
              jQuery('#holeshot').val(holeshot);
       });
     
    },
}


jQuery(document).on('click', '#resultScoring .page-numbers:not(current)', function(e){
    e.preventDefault();
    var href = jQuery(this).attr('href');
    var page = href.split('?paged=');
    jQuery.fight.loadPlayerPoints(null, page[1]);
});