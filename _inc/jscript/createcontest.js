var lastPID = "";
var activeID = new Array();

function validateContest()
{
    // Need to ensure at least one fixture was checked
    // Need to uncheck other fixutures from other ID
    var oneChecked = false;
    var fixtureList = fixtures.games[lastPID];
    for (a = 0; a < fixtureList.length; a++)
    {
        var fixture = fixtureList[a];
        if (jQuery('#fixture_' + lastPID + "_" + fixture.fightID).is(':checked'))
        {
            oneChecked = true;
            break;
        }
    }
    if (oneChecked)
    {
        for (var i = 0; i < activeID.length; i++)
        {
            if (lastPID == activeID[i])
            {
                continue;
            } else
            {
                var fixtureList = fixtures.games[activeID[i]];
                for (b = 0; b < fixtureList.length; b++)
                {
                    var fixture = fixtureList[b];
                    jQuery('#fixture_' + activeID[i] + "_" + fixture.fightID).iCheck('uncheck');
                }
            }
        }
    } else
    {
        alert("Please select at least one fixture to be part of your contest");
    }
    return  oneChecked;
}

function setOptions(matchWith)
{
    if (!isNaN(matchWith))
    {
        return true;
    }
    switch (matchWith)
    {
        case "head2head":
            jQuery('.leagueDiv').hide();
            jQuery('#addPayouts').hide();
            jQuery('#payoutExample').hide();
            jQuery('#payouts').empty();
            jQuery('#top3Percent').hide();
            break;
        case "league":
            console.log();
            jQuery('.leagueDiv').show();
            jQuery('#addPayouts').hide();
            jQuery('#payoutExample').hide();
            jQuery('#payouts').empty();
            jQuery('#top3Percent').hide();
            break;
        case "multi_payout":
            jQuery('#addPayouts').show();
            jQuery('#payoutExample').show();
            jQuery('#payouts').show();
            jQuery('#top3Percent').hide();
            break;
        case "top3":
            jQuery('#addPayouts').hide();
            jQuery('#payoutExample').hide();
            jQuery('#payouts').hide();
            jQuery('#top3Percent').show();
            break;
        case "winnertakeall":
            jQuery('#addPayouts').hide();
            jQuery('#payouts').hide();
            jQuery('#payoutExample').hide();
            jQuery('#top3Percent').hide();
            break;
        case "public":
            jQuery('#password_content').hide();
            jQuery('#password').attr('disabled', 'disabled');
            break;
        case "private":
            jQuery('#password_content').show();
            jQuery('#password').removeAttr('disabled');
            break;
        case "winnertakeall":
        case "top3":
        case "on":
            break;
    }
    
    jQuery.createcontest.calculatePrizes();
    jQuery.createcontest.loadLiveDraft();
    jQuery.createcontest.loadPickTieBreaker();
    jQuery.createcontest.loadSpecifyDatesForSeasonLong();
    jQuery.createcontest.loadRoundPickem();
    jQuery.createcontest.loadBracket();
    jQuery.createcontest.loadGoliath();
    jQuery.createcontest.loadMiniGoliath();
    jQuery.createcontest.loadSurvival();
}

jQuery.createcontest =
{
    setData: function (aSports, aPools, aFights, aRounds, aPositions, lineup, lineup_no_position, aMixingPools)
    {
        this.aSports = aSports;
        this.aPools = aPools;
        this.aFights = aFights;
        this.aRounds = aRounds;
        this.aPositions = aPositions;
        this.lineup = lineup;
        this.lineup_no_position = lineup_no_position;
        this.aMixingPools = aMixingPools;
        jQuery.parseJSON(this.aPools);
    },
    init: function ()
    {
        var aPools = jQuery.parseJSON(this.aPools);
        if (aPools != null)
        {
            for (var i = 0; i < aPools.length; i++)
            {
                jQuery('#sportRadios' + aPools[i].organization).removeAttr('disabled');
            }
        }
    },
    selectSportType: function ()
    {
        var sport_type = jQuery('#sportType select').val();
        jQuery('.single_sport_group').hide();
        jQuery('.mixing_sport_group').hide();
        if (sport_type == 'mixing')
        {
            jQuery('.mixing_sport_group').show();
            jQuery.createcontest.loadSports();
            jQuery.createcontest.mixingLoadFixtures();
        } else if(sport_type == 'motocross'){
            jQuery('.single_sport_group').show();
            jQuery.createcontest.loadSportMotocross();
            jQuery.createcontest.loadPools();
            jQuery('#game_type option:not(#playerdraftType)').hide();
            jQuery('#wrapRounds .widget-title').remove();
            jQuery('#roundDiv').hide();
        }
        else
        {
            jQuery('.single_sport_group').show();
            jQuery.createcontest.loadSports();
            jQuery.createcontest.loadPools();
        }
        if(sport_type != 'single'){
            jQuery('.allow_select_tie').hide();
            jQuery('.allow_new_tie_breaker').hide();
        }
    },
    loadSports: function(){

        if(jQuery('#is-single-game').val()== false){
            return false;
        }
        var aSports = JSON.parse(this.aSports);
        var motocross_id = jQuery('#motocross_id').val();
        jQuery('#sports').empty();
        var html = '';
        jQuery('#sports').empty();
        for(var i in aSports){
            if(aSports[i].id != motocross_id){
               if(typeof aSports[i].child != 'undefined' && aSports[i].child != null && (aSports[i].child).length > 0){
                   html += '<option disabled="true">'+aSports[i].name+'</option>';
                  var sports = aSports[i].child;
                  for(var j in sports){
                      var sport = sports[j];
                      if(sport.is_active == 1){

                          html+= '<option is_team="'+sport.is_team+'" value="'+sport.id+'" playerdraft="'+sport.is_playerdraft+'" only_playerdraft="'+sport.only_playerdraft+'" is_round="'+sport.is_round+'" is_picktie="'+sport.is_picktie+'" upload_photo="'+sport.upload_photo+'" style="padding-left: 20px">';
                          html+=sport.name;
                          html+='</option>';
                      }
                  }
                    jQuery('#sports').html(html);
               }
            }
        }
    },
    loadSportMotocross: function(){
        var html = '';
        jQuery('#sports').empty();
        var aSports = JSON.parse(this.aSports);
        var motocross_id = jQuery('#motocross_id').val();
        for(var i in aSports){
            if(aSports[i].id == motocross_id){
               if(typeof aSports[i].child != 'undefined' && aSports[i].child != null && (aSports[i].child).length > 0){
                   html = '<option disabled="true">'+aSports[i].name+'</option>';
                  var sports = aSports[i].child;
                  for(var j in sports){
                      var sport = sports[j];
                      if(sport.is_active == 1){

                          html+= '<option value="'+sport.id+'" playerdraft="'+sport.is_playerdraft+'" only_playerdraft="0" is_round="'+sport.is_round+'" is_picktie="'+sport.is_picktie+'" style="padding-left: 20px">';
                          html+=sport.name;
                          html+='</option>';
                      }
                  }
                    jQuery('#sports').html(html);
               }
            }
        }

    },
    loadPools: function ()
    {
        var org_id = jQuery('#sports').val();
        var is_playerdraft = jQuery('option:selected', '#sports').attr('playerdraft');
        var is_playerunit = jQuery('option:selected', '#sports').attr('playerunit');
        var is_pick = jQuery('option:selected', '#sports').attr('pick');
        var is_round = jQuery('option:selected', '#sports').attr('is_round');
        var is_team = jQuery('option:selected', '#sports').attr('is_team');
        var is_picktie = jQuery('option:selected', '#sports').attr('is_picktie');
        var only_playerdraft = jQuery('option:selected', '#sports').attr('only_playerdraft');
        var upload_photo = jQuery('option:selected', '#sports').attr('upload_photo');
        var aPools = jQuery.parseJSON(this.aPools);
        var selectPool = jQuery('#selectPool').val();
        if (aPools != null)
        {
            var html = '<select class="form-control" name="poolID" onchange="jQuery.createcontest.loadFights(jQuery(this).val());jQuery.createcontest.loadRounds(jQuery(this).val());">';
            for (var i in aPools)
            {
                var aPool = aPools[i];
                var selected = '';
                if (selectPool == aPool.poolID)
                {
                    selected = 'selected="true"';
                }
                if (aPool.organization == org_id)
                {
                    html += '<option value="' + aPool.poolID + '" ' + selected + ' data-weekly="' + aPool.weekly + '" data-yearly="' + aPool.yearly + '" data-group_stage="' + aPool.group_stage + '" data-mini_group_stage="' + aPool.mini_group_stage + '" data-playoff="' + aPool.playoff + '">' + aPool.poolName + '</option>';
                    if (aPool.type == 'MMA')
                    {
                        jQuery('.minutes').show();
                    } else
                    {
                        jQuery('.minutes').hide();
                    }
                }
            }
            html += '</select>';
            jQuery('#poolDates').empty().append(html);
        }
        if(upload_photo == 1){
            jQuery('.for_team').show();
            jQuery('#wrapFixtures').hide();
            jQuery('#wrapRounds').hide();
            jQuery('.for_playerdraft').hide();
            jQuery('.specify_dates_for_season_long').hide();
            this.loadGameType();
            this.loadFights(jQuery('#poolDates select').val());
        }
        else{
            if (only_playerdraft == 0)
            {
                jQuery('#game_type option').show();
                jQuery('#wrapFixtures').show();
                if (is_playerdraft == 1)
                {
                    jQuery('#playerdraftType').show();
                } else
                {
                    jQuery('#playerdraftType').hide();
                    if (jQuery("#game_type").val() == "playerdraft")
                    {
                        jQuery('#game_type>option:selected').next().attr('selected', 'true');
                    }
                }
            } else
            {
                jQuery('#wrapFixtures').hide();
                jQuery('#game_type #playerdraftType').show();
                jQuery('#game_type option:not(#playerdraftType)').hide();
            }
            jQuery('#game_type option:first:visible').attr("selected", "true");
            if (is_round == 0)
            {
                jQuery('#wrapRounds').hide();
            } else
            {
                jQuery('#wrapRounds').show();
            }
            if (is_team == 1 || (is_team == 0 && is_playerdraft == 0))
            {
                jQuery('.for_team').show();
            } else
            {
                jQuery('.for_team').hide();

            }
            if (org_id == 44) //golf
            {
                jQuery('#wrapOptionType').show();
                jQuery('#optionType').removeAttr('disabled');
            } else
            {
                jQuery('#wrapOptionType').hide();
                jQuery('#optionType').attr('disabled', true);
            }
            jQuery('#selectPool').val('');

            //load fights or load rounds
            this.loadFights(jQuery('#poolDates select').val());
            this.loadRounds(jQuery('#poolDates select').val());
            this.loadGameType();
            this.loadPosition();
        }
    },
    loadFights: function (poolID)
    {
        //load game type
        var game_type = jQuery('#game_type').val();
        if (game_type == null) {
            if (jQuery('#gameTypeData').val() != 'null') {
                game_type = jQuery('#gameTypeData').val();
            }
        }
        var aFights = jQuery.parseJSON(this.aFights);
        var selectFight = '';
        if (jQuery('#selectFight').length > 0 && jQuery('#selectFight').val() != '')
        {
            selectFight = jQuery.parseJSON(jQuery('#selectFight').val());
        }

        var result = '';
        if (aFights != null)
        {
            for (var i = 0; i < aFights.length; i++)
            {
                var aFight = aFights[i];
                var selected = '';
                if ((selectFight != null && selectFight.indexOf(aFight.fightID) > -1) || selectFight == '' || selectFight == null)
                {
                    selected = 'checked="true"';
                }
                if (aFight.poolID == poolID)
                {
                    var date = aFight.startDate;
                    date = date.split(" ");
                    var time = date[1];
                    time = time.split(':');
                    var hours = parseInt(time[0]);
                    var ampm = (hours > 12 )?' PM':' AM';
                    hours = ((hours+ 11) % 12 + 1);

                    var str_date = hours+':'+time[1] + ampm;
                    if (game_type == 'picksquares') {
                        result += '<label class="radio-control" for="fixture_' + poolID + '_' + aFight.fightID + '">' +
                            aFight.name + " - " + str_date +
                            '<input type="radio" ' + selected + ' id="fixture_' + poolID + '_' + aFight.fightID + '" name="fightID[]" value="' + aFight.fightID + '">' +
                            '<span class="checkmark"></span>' +
                            '</label><br/>';
                    } else {
                        result += '<label class="checkbox-control d-inline-block mt-0 mb-2" for="fixture_' + poolID + '_' + aFight.fightID + '">' +
                            aFight.name + " - " + str_date +
                            '<input type="checkbox" ' + selected + ' id="fixture_' + poolID + '_' + aFight.fightID + '" name="fightID[]" value="' + aFight.fightID + '">' +
                            '<span class="checkmark"></span>' +
                            '</label><br/>';
                    }
                    //result += '<label for="fixture_' + poolID + '_' + aFight.fightID + '">' + aFight.name + " - " + str_date + '</label><br/>';
                }
            }
        }
        //jQuery('#selectFight').val('');
        jQuery('#fixtureDiv').empty().append(result);
        this.loadSpreadPoint();
        this.loadUltimatePickPoint();

        //only show pictie for weekly
        var is_picktie = jQuery('option:selected', '#sports').attr('is_picktie');
        //var weekly_event = jQuery('option:selected', '#poolDates select').attr('data-weekly');
        if (is_picktie == 1 /*&& weekly_event == 1*/)
        {
            jQuery('#game_type #picktieType').show();
        } else
        {
            jQuery('#game_type #picktieType').hide();
        }

        //live draft
        this.loadLiveDraft();
        this.loadPickTieBreaker();
        this.loadSpecifyDatesForSeasonLong();
        this.loadRoundPickem();
        this.loadBracket();
        this.loadGoliath();
        this.loadMiniGoliath();
        this.loadSurvival();
    },
    loadRounds: function (poolID)
    {
        //load game type
        var game_type = jQuery('#game_type').val();
//                if (game_type != 'picksquares' && game_type != 'pickem' && game_type != 'picktie') {
//                    this.loadGameType();
//                }
        var aRounds = jQuery.parseJSON(this.aRounds);
        var selectRound = '';
        if (jQuery('#selectRound').length > 0 && jQuery('#selectRound').val() != '')
        {
            selectRound = jQuery.parseJSON(jQuery('#selectRound').val());
        }
        var result = '';
        if (aRounds != null)
        {
            for (var i in aRounds)
            {
                var aRound = aRounds[i];
                var selected = '';
                if ((selectRound != null && selectRound.indexOf(aRound.id) > -1) || selectRound == '' || selectRound == null)
                {
                    selected = 'checked="true"';
                }
                if (aRound.poolID == poolID)
                {
                    result += '<input type="checkbox" ' + selected + ' id="round_' + poolID + '_' + aRound.id + '" name="roundID[]" value="' + aRound.id + '">';
                    result += '<label for="round_' + poolID + '_' + aRound.id + '">' + aRound.name + '</label><br/>';
                }
            }
        }

        var pool = this.checkPoolMotocross(poolID);
        if(pool != false){
            jQuery('#wrapRounds').show();
            jQuery('#wrapFixtures').hide();

        }
        jQuery('#selectRound').val('');
        jQuery('#roundDiv').empty().append(result);
        if(pool != false){
            jQuery('#wrapRounds input[type=checkbox]').attr({onClick:'return false',onChange:'return false'});
        }
    },
    checkPoolMotocross: function (poolID){
      var pools = JSON.parse(this.aPools);
      for(var i in pools){
          if(pools[i].poolID == poolID && pools[i].is_motocross == true)
              return true;
      }
      return false;
    },
    loadGameType: function ()
    {
        var html = '';
        var gametypes = '';
        var gametype = '';
        var sports = jQuery.parseJSON(this.aSports);
        var selectPool = jQuery('#sports').val();

        for (var i in sports)
        {
            if (sports[i].child)
            {
                for (var j in sports[i].child)
                {
                    if (selectPool == sports[i].child[j].id)
                    {
                        gametypes = sports[i].child[j].game_type;
                    }
                }
            }

        }
        for (var i in gametypes)
        {
            gametype = gametypes[i];
            html += '<option value="' + gametype.value.toLowerCase() + '" id="' + gametype.value + 'Type">' + VIC_ParseGameTypeName(gametype.value) + '</option>';
        }
        jQuery('#game_type').empty().append(html);
        jQuery.createcontest.gameTypeAttr();
    },
    loadSpreadPoint: function ()
    {
        if (jQuery('#game_type').val() == 'pickspread')
        {
            jQuery('#spreadpoint').show();
            var poolID = jQuery('#poolDates select').val();
            var aFights = jQuery.parseJSON(this.aFights);
            var html = '';
            if (aFights != null)
            {
                for (var i in aFights)
                {
                    var aFight = aFights[i];
                    if (aFight.poolID == poolID)
                    {
                        html +=
                                '<tr>\n\
                        <td>' + aFight.name + '</td>\n\
                        <td><input type="text" name="team1_spread_points[' + aFight.fightID + ']" value="' + aFight.team1_spread_points + '" /></td>\n\
                        <td><input type="text" name="team2_spread_points[' + aFight.fightID + ']" value="' + aFight.team2_spread_points + '" /></td>\n\
                    </tr>';
                    }
                }
            }
            jQuery('#spreadpoint table tbody').empty().append(html);
        } else
        {
            jQuery('#spreadpoint').hide();
            jQuery('#spreadpoint table tbody').empty();
        }
    },
    loadUltimatePickPoint: function ()
    {
        if (jQuery('#game_type').val() == 'pickultimate')
        {
            jQuery('#ultimate_pick_point').show();
            var poolID = jQuery('#poolDates select').val();
            var aFights = jQuery.parseJSON(this.aFights);
            var html = '';
            if (aFights != null)
            {
                for (var i in aFights)
                {
                    var aFight = aFights[i];
                    if (aFight.poolID == poolID)
                    {
                        html +=
                                '<tr>\n\
                        <td>' + aFight.name + '</td>\n\
                        <td><input type="text" name="over_under[' + aFight.fightID + ']" value="' + aFight.over_under + '" /></td>\n\
                        <td><input type="text" name="team1_spread_points[' + aFight.fightID + ']" value="' + aFight.team1_spread_points + '" /></td>\n\
                        <td><input type="text" name="team2_spread_points[' + aFight.fightID + ']" value="' + aFight.team2_spread_points + '" /></td>\n\
                    </tr>';
                    }
                }
            }
            jQuery('#ultimate_pick_point table tbody').empty().append(html);
        } else
        {
            jQuery('#ultimate_pick_point').hide();
            jQuery('#ultimate_pick_point table tbody').empty();
        }
    },
    gameTypeAttr: function (value)
    {

        var gametype = jQuery('#game_type').val();
        if (typeof value != 'undefined')
        {
            jQuery('#game_type').val(value);
            gametype = value;
        }

        jQuery('.prize_structure_title').show();
        jQuery('.group_prize_structure td').show();
        jQuery('.allow_select_tie').hide();
        jQuery('#allow_tie').attr('checked',false);
        jQuery('.allow_new_tie_breaker').hide();
        jQuery('#allow_new_tie_breaker').attr('checked',false);
        jQuery('#wrapFixtures').show();
        jQuery('#wrapEvent').show();
        //jQuery('.sport_type_group').show();
        jQuery('.event_group').show();
        jQuery('.for_playerdraft').hide();
        jQuery('.salary_remaining').show();
		jQuery('.salary_remaining').find('input').removeAttr('disabled');
        jQuery('.for_portfolio').hide();
        jQuery('.for_portfolio').find('input').attr('disabled', 'disabled');
        jQuery('.for_olddraft').hide();
        jQuery('.for_olddraft').find('input').attr('disabled', 'disabled');
        jQuery('#wrapContestType').show();
        jQuery('#wrapPlayerRestriction').show();
        jQuery('#wrapLineup').show();
        jQuery('.leagueDiv').find('select').removeAttr('disabled');
        //jQuery('.leagueDiv').show();
        jQuery('.for_playoff').hide();
        jQuery('.for_playoff').find('select').attr('disabled', 'disabled');

        switch (gametype)
        {
            case 'playerdraft':
                if (typeof value == 'undefined') {
                    this.loadFights(jQuery("#poolDates select").val());
                }
                jQuery('.for_playerdraft').show();
                jQuery('.for_playerdraft').find('input').removeAttr('disabled');
                this.loadPosition();
                break;
            case 'picksquares':
                if (typeof value == 'undefined') {
                    jQuery.createcontest.loadPickSquareGameType();

                } else {
                    // for edit contest in admin
                    jQuery('#typeRadios9').trigger('click');
                    jQuery('.prize_structure_title').hide();
                    jQuery('#typeRadios9').closest('.radio').hide();
                    jQuery('#typeRadios10').closest('.radio').hide();
                    jQuery('#typeRadios11').closest('.radio').hide();

                }
                jQuery(".admin_contest_type").hide();
                jQuery('.for_playerdraft').hide();
                break;
            case 'teamdraft':
                this.loadTeamLineup();
                jQuery('.salary_remaining').show();
				jQuery('.salary_remaining').find('input').removeAttr('disabled');
                break;
            case 'sportbook':
                jQuery('.for_sportbook').show();
                jQuery('.salary_remaining').hide();
				jQuery('.salary_remaining').find('input').attr('disabled', 'disabled');
                break;
            case 'golfskin':
                jQuery('.prize_structure_title').hide();
                jQuery('.group_prize_structure td').hide();
                break;
            case 'pickem':
                jQuery('.allow_select_tie').show();
                jQuery('.allow_new_tie_breaker').show();
                jQuery('.salary_remaining').hide();
				jQuery('.salary_remaining').find('input').attr('disabled', 'disabled');
                break;
            case 'howmanygoals':
            case 'bothteamstoscore':
                jQuery('.allow_new_tie_breaker').show();
                break;
            case 'uploadphoto':
                jQuery('#wrapFixtures').hide();
                break;
            case 'portfolio':
                jQuery('#wrapFixtures').hide();
                jQuery('#wrapEvent').hide();
                //jQuery('.sport_type_group').hide();
                jQuery('.event_group').hide();
                jQuery('.for_playerdraft').hide();
                jQuery('.salary_remaining').hide();
				jQuery('.salary_remaining').find('input').attr('disabled', 'disabled');
                jQuery('.for_portfolio').show();
                jQuery('.for_portfolio').find('input').removeAttr('disabled');

                jQuery("#contest_cut_date").datepicker({
                    minDate: 0,
                    changeMonth: false,
                    onClose: function( selectedDate ) {
                        jQuery("#contest_end_date").datepicker("option", "minDate", selectedDate);
                    }
                });
                jQuery("#contest_end_date").datepicker({
                    changeMonth: false,
                    onClose: function( selectedDate ) {
                        jQuery("#contest_cut_date").datepicker("option", "maxDate", selectedDate);
                    }
                });
                break;
            case 'olddraft':
                jQuery('#wrapFixtures').hide();
                jQuery('#wrapEvent').hide();
                //jQuery('.sport_type_group').hide();
                jQuery('.event_group').hide();
                jQuery('.for_playerdraft').hide();
                jQuery('.salary_remaining').hide();
				jQuery('.salary_remaining').find('input').attr('disabled', 'disabled');
                jQuery('.for_olddraft').show();
                jQuery('.for_olddraft').find('input').removeAttr('disabled');

                jQuery("#contest_cut_date").datepicker({
                    minDate: 0,
                    changeMonth: false,
                    onClose: function( selectedDate ) {
                        jQuery("#contest_end_date").datepicker("option", "minDate", selectedDate);
                    }
                });
                jQuery("#contest_end_date").datepicker({
                    changeMonth: false,
                    onClose: function( selectedDate ) {
                        jQuery("#contest_cut_date").datepicker("option", "maxDate", selectedDate);
                    }
                });
                break;
            case 'nfl_playoff':
                jQuery('.salary_remaining').hide();
                jQuery('.salary_remaining').find('input').attr('disabled', 'disabled');
                jQuery('#wrapContestType').hide();
                jQuery('#wrapPlayerRestriction').hide();
                jQuery('#wrapLineup').hide();
                jQuery('.leagueDiv').find('select').attr('disabled', 'disabled');
                jQuery('.leagueDiv').hide();
                jQuery('.for_playoff').show();
                jQuery('.for_playoff').find('select').removeAttr('disabled');
                break;
            default :
                if (typeof value == 'undefined') {
                    this.loadFights(jQuery("#poolDates select").val());
                }
                jQuery('#game_type').val(gametype);
                jQuery('.for_playerdraft').hide();
        }
        if (gametype != 'picksquares') {
            jQuery('.prize_structure_title').show();
            jQuery('#typeRadios9').closest('.radio').show();
            jQuery('#typeRadios10').closest('.radio').show();
            jQuery('#typeRadios11').closest('.radio').show();

            jQuery('.admin_contest_type').show();
            /*jQuery('#typeRadios8').closest('.radio').show();
            if (typeof value == 'undefined') { // load edit
                jQuery('#typeRadios7').trigger('click').closest('.radio').show();
            }*/
            jQuery(".picksquare_payout").hide();
        }
        this.loadSpreadPoint();
        this.loadUltimatePickPoint();
    },
    calculatePrizes: function ()
    {
        var winnerPercent = jQuery('#winnerPercent').val();
        var firstPercent = jQuery('#firstPercent').val();
        var secondPercent = jQuery('#secondPercent').val();
        var thirdPercent = jQuery('#thirdPercent').val();
        var size = jQuery('#leagueSize').val();
        var entryFee = jQuery('#entry_fee').val();
        var structure = jQuery('input:radio[name=structure]:checked').val();
        var type = jQuery('input:radio[name=type]:checked').val();
        var game_type = jQuery('#game_type').val();
        //calculate
        var prizes = [];
        var poss = [];
        var poss_ranges = [];
        if (type == 'head2head')
        {
            size = 2;
            structure = "winnertakeall";
        }
        jQuery('.payout_percentage').hide();
        jQuery('.balance_type_group').hide();
        jQuery('.prize_structure_group').hide();
        if (parseInt(entryFee) > 0)
        {
            jQuery('.payout_percentage').show();
            jQuery('.balance_type_group').show();
            jQuery('.prize_structure_group').show();
            prize = size * entryFee * winnerPercent / 100;
            switch (structure)
            {
                case "winnertakeall":
                    poss.push("1st");
                    prizes.push(prize.toFixed(2));
                    break;
                case "top3":
                    prizes.push((prize * firstPercent / 100).toFixed(2));//1st
                    prizes.push((prize * secondPercent / 100).toFixed(2));//2nd
                    prizes.push((prize * thirdPercent / 100).toFixed(2));//3th
                    poss.push("1st");
                    poss.push("2nd");
                    poss.push("3rd");
                    break;
                case 'multi_payout':
                    if (jQuery("#payouts input[name='percentage[]']").length > 0)
                    {
                        var index = -1;
                        jQuery("#payouts input[name='payouts_from[]']").each(function () {
                            index++;
                            var from = parseInt(jQuery(this).val());
                            var to = parseInt(jQuery("#payouts input[name='payouts_to[]']:eq(" + index + ")").val());
                            if (from > 0 && to > 0)
                            {
                                poss_ranges.push(parseInt(to) - parseInt(from) + 1);
                                from = jQuery.createcontest.parsePosition(from);
                                to = jQuery.createcontest.parsePosition(to);
                                pos = from + " - " + to;
                                if (from == to)
                                {
                                    pos = from;
                                }
                                poss.push(pos);
                            }
                        });
                        jQuery("#payouts input[name='percentage[]']").each(function () {
                            var percentage = jQuery(this).val();
                            if (percentage != '')
                            {
                                percentage = parseInt(jQuery(this).val());
                            } else
                            {
                                percentage = 0;
                            }
                            prizes.push((prize * percentage / 100).toFixed(2));//1st
                        });
                    }
                    break;
                    /*default :
                     break;*/
            }
        }
        
        //multiple balance
        var currency_symbol = '';
        var currency_position = '';
        if(jQuery('#balance_type').length > 0){
            var sign = jQuery('#balance_type option:selected').data('sign');
            sign = sign.split('|');
            currency_symbol = sign[1];
            currency_position = jQuery('#balance_type option:selected').data('currency_position');
        }

        //view result
        var html =
                '<table style="width:100%">\n\
        <tr><td class="text-left">Pos</td><td class="text-right">Prize</td></tr>';
        var count = 0;
        for (var i in poss)
        {
            var prize = prizes[i];
            var pos = poss[i];
            if (typeof poss_ranges[i] != 'undefined')
            {
                prize = (prize / poss_ranges[i]).toFixed(2);
            }
            count++;
            /*place = null;
             switch (count)
             {
             case 1:
             place = '1st';
             break;
             case 2:
             place = '2nd';
             break;
             case 3:
             place = '3rd';
             break;
             }*/
            html += '<tr><td class="text-left">' + pos + '</td><td class="text-right">' + VIC_FormatMoney(prize, currency_symbol, currency_position) + '</td></tr>';
        }
        if (game_type == 'picksquares' && prize != undefined) {
            var html =
                    '<table style="width:100%">\n\
        <tr><td class="text-left">' + VIC_FormatMoney(prize, currency_symbol, currency_position) + '/square </td></tr>';
        }
        if (game_type == 'golfskin') {
            var html =
                    '<table style="width:100%">\n\
        <tr><td class="text-left">' + VIC_FormatMoney(entryFee, currency_symbol, currency_position) + '/player </td></tr>';
        }
        html += '</table>';
        jQuery("#prizesum").empty().append(html);
        
        //guarantee prize
        jQuery.createcontest.loadGuaranteedPrizeStructure();
    },
    parsePosition: function (num)
    {
        switch (num)
        {
            case 1:
                num = num + "st";
                break;
            case 2:
                num = num + "nd";
                break;
            case 3:
                num = num + "rd";
                break;
            default :
                num = num + "th";
                break;
        }
        return num;
    },
    addInsufficientZeroToMoneyFormat: function (str)
    {
        str = str.toFixed(2);
        if (str.substring(-2, 1) == '.')
        {
            str += '0';
        }
        return str;
    },
    loadPosition: function ()
    {
        var aPositions = jQuery.parseJSON(this.aPositions);
        var data = '';
        if (this.lineup != '')
        {
            data = jQuery.parseJSON(this.lineup);
        }
        var org_id = jQuery('#sports').val();
        var result = '<table>';
        var hasPosition = false;
        var sportData = this.aSports;
        if (aPositions != null)
        {
            var game_type = jQuery('#game_type').val();
            if (sportData != null) {
                sportData = jQuery.parseJSON(sportData);
                for (var i in sportData) {
                    if (sportData[i].id == 42) {
                        var sportSoccer = sportData[i].child;
                        if (sportSoccer != null) {
                            for (var j in sportSoccer) {
                                if (sportSoccer[j].id == org_id && game_type == 'playerdraft') {
                                    org_id = 42;
                                }
                            }
                        }
                    }
                }
            }
            for (var i = 0; i < aPositions.length; i++)
            {
                var aPosition = aPositions[i];
                if (aPosition.org_id == org_id)
                {
                    hasPosition = true;
                    var total = 0;
                    var checked = 'checked="true"';
                    if (data != '')
                    {
                        for (var j = 0; j < data.length; j++)
                        {
                            if (data[j].id == aPosition.id)
                            {
                                total = data[j].total;
                                if (data[j].enable == 1)
                                {
                                    checked = 'checked="true"';
                                } else
                                {
                                    checked = '';
                                }
                                break;
                            }
                        }
                    }
                    result += '<tr>\n\
                            <td><span class="me-2">' + aPosition.name + '</span></td>\n\
                            <td><input type="text" class="form-control" name="lineup[' + aPosition.id + '][total]" value="' + total + '" /></td>\n\
                            <td><label class="checkbox-control ml-2"><input type="checkbox" name="lineup[' + aPosition.id + '][enable]" ' + checked + ' value="1" /><span class="checkmark"></span></label></td>\n\
                        </tr>';
                }
            }
        }
        result += '</table>';
        if (!hasPosition)
        {
            jQuery('.for_playerdraft').hide();
        } else
        {
            jQuery('.for_playerdraft').show();
        }
        if (jQuery('option:selected', "#poolOrgs").attr('only_playerdraft') == 1)
        {
            jQuery('.salary_cap').show();
        }
        jQuery('#lineupResult').empty().append(result);
    },
    
    loadTeamLineup: function()
    {
        if(jQuery('#teamLineupData').length == 0)
        {
            return;
        }
        var team_lineups = jQuery.parseJSON(jQuery('#teamLineupData').html().trim());
        var sport_id = jQuery('#sports').val();
        var result = '<table>';
        for (var i = 0; i < team_lineups.length; i++)
        {
            var lineup = team_lineups[i];
            if (lineup.sport_id == sport_id)
            {
                var checked = 'checked="true"';
                if(lineup.enable == 0)
                {
                    checked = '';
                }
                result += '<tr>\n\
                        <td>' + lineup.name + '</td>\n\
                        <td><input type="text" name="team_lineup[' + lineup.id + '][total]" value="' + lineup.total + '" /></td>\n\
                        <td><input type="checkbox" name="team_lineup[' + lineup.id + '][enable]" ' + checked + ' value="1" /></td>\n\
                    </tr>';
            }
        }
        result += '</table>';
        jQuery('#lineupResult').empty().append(result);
    },
    
    optionType: function ()
    {
        var is_round = jQuery('#sports option:selected').attr('is_round');
        if (is_round == 1)
        {
            var type = jQuery('#optionType').val();
            if (type == 'salary')
            {
                //jQuery('.for_group').hide();
                var html = '<input type="text" value="' + this.lineup_no_position + '" name="lineup_no_position">';
                jQuery('#lineupResult').empty().append(html);
            } else
            {
                //jQuery('.for_group').show();
                this.loadPosition();
            }
        } else
        {
            jQuery('.for_playerdraft.for_group').show();
        }
        this.gameTypeAttr();
    },
    addPayouts: function ()
    {
        var plugin_url_image = jQuery("#plugin_url_image").val();
        var html =
                '<div class="form-group mb-2">\n\
        <label>' + wpfs['from'] + '</label>\n\
        <input type="text" class="form-control" name="payouts_from[]" value="" onkeyup="jQuery.createcontest.calculatePrizes()">\n\
        <label>' + wpfs['to'] + '</label>\n\
        <input type="text" class="form-control" name="payouts_to[]" value="" onkeyup="jQuery.createcontest.calculatePrizes()">\n\
        <label>:</label>\n\
        <input type="text" class="form-control" name="percentage[]" value="" onkeyup="jQuery.createcontest.calculatePrizes()">\n\
        <label>%</label>\n\
        <a onclick="return jQuery.createcontest.removePayouts(jQuery(this).parent());" href="#">\n\
            <span class="material-icons material-icons-outlined">remove_circle</span>\n\
        </a>\n\
    </div>';
        jQuery("#payouts").append(html);
        return false;
    },
    
    addPayoutsGuaranteed: function ()
    {
        var plugin_url_image = jQuery("#plugin_url_image").val();
        var html =
            '<div>\n\
                <label style="display: inline-block;width: auto">' + wpfs['from'] + '</label>\n\
                <input type="text" name="guaranteed_payouts_from[]" value="" style="display: inline-block;width: 50px;padding: 2px 5px;text-align:center" onkeyup="jQuery.createcontest.calculatePrizes()">\n\
                <label style="display: inline-block;width: auto">' + wpfs['to'] + '</label>\n\
                <input type="text" name="guaranteed_payouts_to[]" value="" style="display: inline-block;width: 50px;padding: 2px 5px;text-align:center" onkeyup="jQuery.createcontest.calculatePrizes()">\n\
                <label style="display: inline-block;width: auto">:</label>\n\
                <input type="text" name="guaranteed_percentage[]" value="" style="display: inline-block;width: 50px;padding: 2px 5px;text-align:center" onkeyup="jQuery.createcontest.calculatePrizes()">\n\
                <label style="display: inline-block;width: auto">%</label>\n\
                <a onclick="return jQuery.createcontest.removePayouts(jQuery(this).parent());" href="#">\n\
                    <img title="' + wpfs['delete'] + '" alt="' + wpfs['delete'] + '" src="' + plugin_url_image + 'delete.png"\>\n\
                </a>\n\
            </div>';
        jQuery("#payoutsGuaranteed").append(html);
        return false;
    },
    
    loadGuaranteedPrizeStructure : function()
    {
        jQuery('#guaranteed_prizeDiv').hide();
        jQuery('#guaranteed_structDiv').hide();
        jQuery('#guaranteed_top3').hide();
        jQuery('#guaranteed_multi_payout').hide();
        /*if(parseFloat(jQuery("#entry_fee").val()) == 0)
        {
            jQuery('#guaranteed').attr('disabled', 'disabled');
        }
        else
        {
            jQuery('#guaranteed').removeAttr('disabled');
        }*/
        if(jQuery('#guaranteed').prop('checked')/* && parseFloat(jQuery("#entry_fee").val()) > 0*/){
            jQuery('#guaranteed_prizeDiv').show();

            if(jQuery('input[name="structure"]:radio:checked').val() == 'top3' || jQuery('input[name="structure"]:radio:checked').val() == 'multi_payout'){
                jQuery('#guaranteed_structDiv').show();
            }

            if(jQuery('input[name="structure"]:radio:checked').val() == 'top3'){
                jQuery('#guaranteed_top3').show();
            }

            if(jQuery('input[name="structure"]:radio:checked').val() == 'multi_payout'){
                jQuery('#guaranteed_multi_payout').show();
            }
        }
    },
    
    addPayoutsPickSquare: function () {
        var plugin_url_image = jQuery("#plugin_url_image").val();
        var html =
                '<div>\n\
        <input type="text" name="payouts_name[]" value="" style="display: inline-block;width: 150px;padding: 5px 5px;text-align:center" onkeyup="jQuery.createcontest.calculatePrizes()">\n\
        <input type="text" name="payouts_price[]" value="" style="display: inline-block;width: 150px;padding: 5px 5px;text-align:center" onkeyup="jQuery.createcontest.calculatePrizes()">\n\
        <label style="display: inline-block;width: auto">' + currency_symbol + '</label>\n\
        <a onclick="return jQuery.createcontest.removePayouts(jQuery(this).parent());" href="#">\n\
            <img title="' + wpfs['delete'] + '" alt="' + wpfs['delete'] + '" src="' + plugin_url_image + 'delete.png"\>\n\
        </a>\n\
    </div>';
        jQuery("#picksquare_payouts").append(html);
        return false;
    },
    removePayouts: function (item)
    {
        item.remove();
        this.calculatePrizes();
        return false;
    },
    create: function ()
    {

        if(jQuery('.group_prize_structure input[type="radio"]:checked').val() != 'multi_payout'){
            jQuery('#payouts').empty();
        }
        jQuery('#btn_create_contest').attr('disabled', 'true').text(wpfs['working'] + '...');
        jQuery.post(ajaxurl, 'action=createContest&' + jQuery('#formCreateContest').serialize(), function (result) {
            result = jQuery.parseJSON(result);
            if (result.result == 0)
            {
                jQuery('.public_message').empty().append(result.msg).show();
                jQuery('#btn_create_contest').removeAttr('disabled').text(wpfs['create_contest']);
            } else
            {
                window.location = result.url;
            }
        });
    },
    mixCreate: function ()
    {
        if(jQuery('.group_prize_structure input[type="radio"]:checked').val() != 'multi_payout'){
            jQuery('#payouts').empty();
        }
        jQuery.post(ajaxurl, 'action=mixCreateContest&' + jQuery('#mixFormCreateContest').serialize(), function (result) {
            result = jQuery.parseJSON(result);
            if (result.result == 0)
            {
                jQuery('.public_message').empty().append(result.msg).show();
                jQuery('#btn_create_contest').removeAttr('disabled').text(wpfs['create_contest']);
            } else
            {
                window.location = result.url;
            }
        });
    },
    mixingLoadFixtures: function (is_admin)
    {
        var aDates = jQuery.parseJSON(this.aMixingPools);
        var select = jQuery('#listDate').val();
        if (aDates[select])
        {
            var result = '';
            var listOrg = {};
            for (var orgID in aDates[select])
            {

                  var nameSport = jQuery.createcontest.getNameSportById(orgID);
                  var aFights = aDates[select][orgID];

                    var selectFight = '';
                    if (jQuery('#selectFight').length > 0 && jQuery('#selectFight').val() != '')
                    {
                        selectFight = jQuery.parseJSON(jQuery('#selectFight').val());
                    }
                    result += '<h3>' + nameSport + '</h3>';
                    for (var i in aFights)
                    {
                        var aFight = aFights[i];
                        var poolID = aFights[i].poolID;
                        var selected = '';
                        if ((selectFight != null && selectFight.indexOf(aFight.fightID) > -1) || selectFight == '' || selectFight == null)
                        {
                            selected = 'checked="true"';
                        }

                            var date = aFight.startDate;
                            date = date.split(" ");
                            result += '<input type="checkbox" ' + selected + ' id="fixture_' + poolID + '_' + aFight.fightID + '" name="mixingPools[' + orgID + '][' + poolID + '][]" value="' + aFight.fightID + '">';
                            result += '<label for="fixture_' + poolID + '_' + aFight.fightID + '">' + aFight.name + " - " + date[1] + '</label><br/>';
                    }
                    listOrg[orgID] = orgID;
            }

            if( typeof is_admin != 'undefined' && is_admin === true){
                this.loadMixingPosition(listOrg);
            }
                                      console.log(result);

            jQuery('#selectFight').val('');
            jQuery('#fixtureDiv').empty().append(result).show();
        }
//                if (aDates[select])
//                {
//                    var result = '';
//                    var listOrg = {};
//                    for (var pool_index in aDates[select])
//                    {
//                        var poolID = aDates[select][pool_index].poolID;
//                        var orgID = aDates[select][pool_index].organization;
//                        var nameSport = jQuery.createcontest.getNameSportById(orgID);
//
//                        if (orgID == 13 || orgID == 14 || orgID == 15 || orgID == 16)
//                        {
//                            var selectFight = '';
//                            if (jQuery('#selectFight').length > 0 && jQuery('#selectFight').val() != '')
//                            {
//                                selectFight = jQuery.parseJSON(jQuery('#selectFight').val());
//                            }
//                            result += '<h3>' + nameSport + '</h3>';
//                            for (var i in aFights)
//                            {
//                                var aFight = aFights[i];
//                                var selected = '';
//                                if ((selectFight != null && selectFight.indexOf(aFight.fightID) > -1) || selectFight == '' || selectFight == null)
//                                {
//                                    selected = 'checked="true"';
//                                }
//                                if (aFight.poolID == poolID)
//                                {
//                                    var date = aFight.startDate;
//                                    date = date.split(" ");
//                                    result += '<input type="checkbox" ' + selected + ' id="fixture_' + poolID + '_' + aFight.fightID + '" name="mixingPools[' + orgID + '][' + poolID + '][]" value="' + aFight.fightID + '">';
//                                    result += '<label for="fixture_' + poolID + '_' + aFight.fightID + '">' + aFight.name + " - " + date[1] + '</label><br/>';
//                                }
//                            }
//                            listOrg[orgID] = orgID;
//                        }
//                    }
//
//                    if( typeof is_admin != 'undefined' && is_admin === true){
//                        this.loadMixingPosition(listOrg);
//                    }
//                    
//                    jQuery('#selectFight').val('');
//                    jQuery('#fixtureDiv').empty().append(result).show();
//                }
    },
    loadMixingPosition: function (listOrg) {
        if (Object.keys(listOrg).length < 1) {
            return;
        }
        jQuery('#lineupResult').empty();
        var aPositions = jQuery.parseJSON(this.aPositions);
        for (var orgID in listOrg) {

            var data = '';
            if (this.lineup != '')
            {
                data = jQuery.parseJSON(this.lineup);
            }
            var org_id = orgID;
            var result = '<table>';
            var nameSport = jQuery.createcontest.getNameSportById(orgID);

            if (aPositions != null)
            {
                for (var i = 0; i < aPositions.length; i++)
                {
                    var aPosition = aPositions[i];
                    if (aPosition.org_id == org_id)
                    {
                        hasPosition = true;
                        var total = 0;
                        var checked = 'checked="true"';
                        if (data != '')
                        {
                            for (var j = 0; j < data.length; j++)
                            {
                                if (data[j].id == aPosition.id)
                                {
                                    total = data[j].total;
                                    if (data[j].enable == 1)
                                    {
                                        checked = 'checked="true"';
                                    } else
                                    {
                                        checked = '';
                                    }
                                    break;
                                }
                            }
                        }
                        result += '<tr>\n\
                            <td><span class="me-2">' + aPosition.name + '</span></td>\n\
                            <td><input type="text" class="form-control" name="lineup[' + aPosition.id + '][total]" value="' + total + '" /></td>\n\
                            <td><label class="checkbox-control ml-2><input type="checkbox" name="lineup[' + aPosition.id + '][enable]" ' + checked + ' value="1" /><span class="checkmark"></span></label></td>\n\
                        </tr>';
                    }
                }
            }
            result += '</table>';
            if (!hasPosition)
            {
                jQuery('.for_playerdraft').hide();
            } else
            {
                jQuery('.for_playerdraft').show();
            }
            if (jQuery('option:selected', "#poolOrgs").attr('only_playerdraft') == 1)
            {
                jQuery('.salary_cap').show();
            }
            jQuery('#lineupResult').append('<h3>'+nameSport+'</h3>');
            jQuery('#lineupResult').append(result);
        }
    }
    ,
    getNameSportById: function (org_id) {
        var sports = jQuery.parseJSON(this.aSports);

        for (var i in sports)
        {
            if (sports[i].child)
            {
                for (var j in sports[i].child)
                {
                    if (org_id == sports[i].child[j].id)
                    {
                        return  sports[i].child[j].name;
                    }
                }
            } else {
                if (sports[i].id == org_id) {
                    return sports[i].name;
                }
            }
        }
        return false;
    },
    checkPoolBelongToPlayDraft: function (org_id) {
        var sports = jQuery.parseJSON(this.aSports);
        for (var i in sports)
        {
            if (sports[i].child)
            {
                for (var j in sports[i].child)
                {
                    if (org_id == sports[i].child[j].id)
                    {
                        gametypes = sports[i].child[j].game_type;
                        for (var i in gametypes) {
                            if (gametypes[i].value == 'playerdraft') {
                                return true;
                            }
                        }
                        return false;
                    }
                }
            } else {
                if (sports[i].id == org_id) {
                    if (sports[i].is_playerdraft == 1) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        }
        return false;
    },
    loadPickSquareGameType: function () {
        var game_type = jQuery('#game_type').val();
        var poolID = jQuery('#poolDates select').val();
        jQuery.createcontest.loadFights(poolID);
        jQuery('#game_type').val(game_type);
        if (game_type == 'picksquares') {
            jQuery(".group_prize_structure").show();
            jQuery('#typeRadios9').trigger('click');
            jQuery('.prize_structure_title').hide();
            jQuery('#typeRadios9').closest('.radio').hide();
            jQuery('#typeRadios10').closest('.radio').hide();
            jQuery('#typeRadios11').closest('.radio').hide();

            jQuery('#typeRadios7').closest('.radio').hide();
            jQuery('#typeRadios8').trigger("click").closest('.radio').hide();

            jQuery(".picksquare_payout").show();
        } else if (game_type == 'golfskin') {
            jQuery(".group_prize_structure").hide();
        } else {
            jQuery(".group_prize_structure").show();
            jQuery('.prize_structure_title').show();
            jQuery('#typeRadios9').closest('.radio').show();
            jQuery('#typeRadios10').closest('.radio').show();
            jQuery('#typeRadios11').closest('.radio').show();

            jQuery('#typeRadios8').closest('.radio').show();
            jQuery('#typeRadios7').trigger('click').closest('.radio').show();

            jQuery(".picksquare_payout").hide();

            jQuery.createcontest.calculatePrizes();
        }
        if (game_type == 'pickem') {
            jQuery('.allow_select_tie').show();
        } else {
            jQuery('#allow_tie').attr('checked', false);
            jQuery('.allow_select_tie').hide();
        }

        if(game_type == 'bothteamstoscore' || game_type == 'howmanygoals' || game_type =='pickem'){
            jQuery('.allow_new_tie_breaker').show();
        }else{
            jQuery('#allow_new_tie_breaker').attr('checked', false);
            jQuery('.allow_new_tie_breaker').hide();
        }
    },
    
        rugbyInitStartDraftDatetime: function(){
        if(jQuery('#live_draft_start').length > 0)
        {
            var plugin_url_image = jQuery("#plugin_url_image").val();
            jQuery('#live_draft_start').datepicker({
                minDate: 0,
                //maxDate: maxDate,
                showOn: "button",
                buttonImage: plugin_url_image + "calendar.png",
                buttonImageOnly: true,
                buttonText: "Select date"
            });
        }
    },
    
    loadLiveDraft: function()
    {
		return;
        //check to hide daily and weekly events
        if(jQuery('#game_type').val() == 'livedraft')
        {
            var first_valid_id = 0;
            jQuery('#poolDates select option').each(function(){
                if(jQuery(this).data('yearly') != 1)
                {
                    jQuery(this).hide();
                }
                else if(first_valid_id == 0)
                {
                    first_valid_id = jQuery(this).attr('value');
                    jQuery(this).attr('selected', 'selected');
                }
            })
            if(first_valid_id > 0)
            {
                //jQuery('#poolDates select').val(first_valid_id);
            }
            else
            {
                jQuery('#poolDates select').attr('disabled', 'disabled').hide();
                if(jQuery('#poolDates #no_live_draft_event').length == 0)
                {
                    jQuery('#poolDates').append('<div id="no_live_draft_event">' + wpfs['no_event_for_live_draft'] + '</div>');
                }
            }
            
            //only show even number for league size
            var first_league_size_value = 0;
            jQuery('#leagueSize option').each(function(){
                if(jQuery(this).attr('value') % 2 != 0)
                {
                    jQuery(this).hide();
                }
                else if(first_league_size_value == 0)
                {
                    first_league_size_value = jQuery(this).attr('value');
                }
            })
            if(first_league_size_value > 0)
            {
                jQuery('#leagueSize').val(first_league_size_value);
            }
            
            //hide multi entry
            jQuery(".for_multi_entry").hide();
        }
        else
        {
            jQuery('#poolDates select option').each(function(){
                jQuery(this).show();
            })
            jQuery('#poolDates select').removeAttr('disabled').show();
            jQuery('#poolDates #no_live_draft_event').remove();
            
            //show even number for league size 
            jQuery('#leagueSize option').each(function(){
                jQuery(this).show();
            })
            
            //show multi entry
            jQuery(".for_multi_entry").show();
        }
        
        if(jQuery('#game_type').val() == 'livedraft' && jQuery('#poolDates select option:selected').data('yearly') == 1)
        {
            jQuery('.for_live_draft').show();
            jQuery('.for_live_draft input').removeAttr('disabled');
        }
        else
        {
            jQuery('.for_live_draft').hide();
            jQuery('.for_live_draft input').attr('disabled', 'disabled');
        }
        
        if(jQuery('#poolDates select option:selected').data('yearly') == 1 || jQuery('select#pools option:selected').data('yearly') == 1 ||
           jQuery('#poolDates select').is(':disabled') || jQuery('select#pools').is(':disabled'))
        {
            jQuery('#wrapFixtures').hide();
            jQuery('#fixtureDiv').closest('tr').hide();
        }
        /*else if(jQuery('#game_type').val() != 'uploadphoto')
        {
            jQuery('#wrapFixtures').show();
            jQuery('#fixtureDiv').closest('tr').show();
        }*/
    },
    
    loadRoundPickem: function()
    {
        //check to hide daily and weekly events
        if(jQuery('#game_type').val() == 'roundpickem')
        {
            jQuery('#poolDates select option').each(function(){
                if(jQuery(this).data('yearly') != 1)
                {
                    jQuery(this).hide();
                }
                else
                {
                    jQuery(this).attr('selected', 'selected');
                }
            })
        }
        else if(jQuery('#game_type').val() != 'livedraft' && 
                jQuery('#game_type').val() != 'bracket' &&
                jQuery('#game_type').val() != 'goliath' &&
                jQuery('#game_type').val() != 'minigoliath')
        {
            jQuery('#poolDates select option').each(function(){
                jQuery(this).show();
            })
        }
    },
    
    loadBracket: function()
    {
        //check to hide daily and weekly events
        if(jQuery('#game_type').val() == 'bracket')
        {
            jQuery('#poolDates select option').each(function(){
                if(jQuery(this).data('yearly') != 1)
                {
                    jQuery(this).hide();
                }
                else
                {
                    jQuery(this).attr('selected', 'selected');
                }
            })
            jQuery('.specify_dates_for_season_long').hide();
            jQuery('.specify_dates_for_season_long').find('input').attr('disabled', 'disabled');
        }
        else if(jQuery('#game_type').val() != 'livedraft' && 
                jQuery('#game_type').val() != 'roundpickem' &&
                jQuery('#game_type').val() != 'goliath' &&
                jQuery('#game_type').val() != 'minigoliath')
        {
            jQuery('#poolDates select option').each(function(){
                jQuery(this).show();
            })
            jQuery.createcontest.loadSpecifyDatesForSeasonLong()
        }
    },
    
    loadGoliath: function()
    {
        //check to hide daily and weekly events
        if(jQuery('#game_type').val() == 'goliath')
        {
            jQuery('#poolDates select option').each(function(){
                if(jQuery(this).data('group_stage') != 1)
                {
                    jQuery(this).hide();
                }
                else
                {
                    jQuery(this).attr('selected', 'selected');
                }
            })
            jQuery('#wrapFixtures').hide();
            jQuery('#fixtureDiv').empty();
            jQuery('.specify_dates_for_season_long').hide();
            jQuery('.specify_dates_for_season_long').find('input').attr('disabled', 'disabled');
        }
        else if(jQuery('#game_type').val() != 'livedraft' && 
                jQuery('#game_type').val() != 'roundpickem' && 
                jQuery('#game_type').val() != 'bracket' &&
                jQuery('#game_type').val() != 'minigoliath')
        {
            jQuery('#poolDates select option').each(function(){
                jQuery(this).show();
            })
            jQuery.createcontest.loadSpecifyDatesForSeasonLong()
        }
    },
    
    loadMiniGoliath: function()
    {
        //check to hide daily and weekly events
        if(jQuery('#game_type').val() == 'minigoliath')
        {
            var mini_group_stage = jQuery('#poolDates select option:selected').data('mini_group_stage');
            if(mini_group_stage != 1)
            {
                jQuery('#poolDates select option').each(function(){
                    if(jQuery(this).data('mini_group_stage') != 1)
                    {
                        jQuery(this).hide();
                    }
                    else
                    {
                        jQuery(this).attr('selected', 'selected');
                    }
                })
            }
            else
            {
                jQuery('#poolDates select option').each(function(){
                    if(jQuery(this).data('mini_group_stage') != 1)
                    {
                        jQuery(this).hide();
                    }
                })
            }
            jQuery('#wrapFixtures').hide();
            jQuery('#fixtureDiv').empty();
            jQuery('.specify_dates_for_season_long').hide();
            jQuery('.specify_dates_for_season_long').find('input').attr('disabled', 'disabled');
        }
        else if(jQuery('#game_type').val() != 'livedraft' && 
                jQuery('#game_type').val() != 'roundpickem' && 
                jQuery('#game_type').val() != 'bracket' &&
                jQuery('#game_type').val() != 'goliath')
        {
            jQuery('#poolDates select option').each(function(){
                jQuery(this).show();
            })
            jQuery.createcontest.loadSpecifyDatesForSeasonLong()
        }
    },
    
    loadSurvival: function()
    {
        jQuery('.for_survival').hide();
        jQuery('.for_survival').find('input').attr('disabled', 'disabled');
            
        //check to hide daily and weekly events
        if(jQuery('#game_type').val() == 'survival')
        {
            jQuery('#poolDates select option').each(function(){
                if(jQuery(this).data('yearly') != 1)
                {
                    jQuery(this).hide();
                }
                else
                {
                    jQuery(this).attr('selected', 'selected');
                }
            })
            jQuery('#wrapFixtures').hide();
            jQuery('#fixtureDiv').empty();
            jQuery('.for_survival').show();
            jQuery('.for_survival').find('input').removeAttr('disabled', 'disabled');
        }
        else if(jQuery('#game_type').val() != 'livedraft' && 
                jQuery('#game_type').val() != 'roundpickem' && 
                jQuery('#game_type').val() != 'bracket' &&
                jQuery('#game_type').val() != 'goliath' &&
                jQuery('#game_type').val() != 'minigoliath')
        {
            jQuery('#poolDates select option').each(function(){
                jQuery(this).show();
            })
            jQuery.createcontest.loadSpecifyDatesForSeasonLong()
        }
    },
    
    loadPickTieBreaker: function(){
        if(jQuery('#game_type').val() == 'picktie' && jQuery('#poolDates select option:selected').data('yearly') == 1){
            jQuery('.show_weekly_pick').show();
        }else{
            jQuery('.show_weekly_pick').hide();
        }
    },
    
    loadSpecifyDatesForSeasonLong: function()
    {
        if((jQuery('#poolDates select option:selected').data('yearly') == 1 || 
           jQuery('select#pools option:selected').data('yearly') == 1)/* && jQuery('#game_type').val() != 'livedraft'*/)
        {
            jQuery('.specify_dates_for_season_long').find('input').removeAttr('disabled');
            if(jQuery('#yearly_contest_start').length > 0)
            {
                var plugin_url_image = jQuery("#plugin_url_image").val();
                jQuery('#yearly_contest_start').datepicker({
                    minDate: 0,
                    //maxDate: maxDate,
                    showOn: "button",
                    buttonImage: plugin_url_image + "calendar.png",
                    buttonImageOnly: true,
                    buttonText: "Select date",
                    onClose: function( selectedDate ) {
                        jQuery("#yearly_contest_end").datepicker("option", "minDate", selectedDate);
                    }
                });
            }
            if(jQuery('#yearly_contest_end').length > 0)
            {
                var plugin_url_image = jQuery("#plugin_url_image").val();
                jQuery('#yearly_contest_end').datepicker({
                    minDate: 0,
                    //maxDate: maxDate,
                    showOn: "button",
                    buttonImage: plugin_url_image + "calendar.png",
                    buttonImageOnly: true,
                    buttonText: "Select date",
                    onClose: function( selectedDate ) {
                        jQuery("#yearly_contest_start").datepicker("option", "maxDate", selectedDate);
                    }
                });
            }
            jQuery('.specify_dates_for_season_long').show();
        }
        else
        {
            jQuery('.specify_dates_for_season_long').hide();
            jQuery('.specify_dates_for_season_long').find('input').attr('disabled', 'disabled');
        }
    },
    
    loadSpecifyNumberOfMultiEntries: function()
    {
        if(jQuery('#multi_entry').is(':checked'))
        {
            jQuery('.number_of_multi_entries input').removeAttr('disabled');
            jQuery('.number_of_multi_entries').show();
        }
        else
        {
            jQuery('.number_of_multi_entries input').attr('disabled', 'disabled');
            jQuery('.number_of_multi_entries').hide();
        }
    },

    loadPlayoff: function(){
        if(jQuery('#poolDates select option:selected').data('playoff') == 1){
            jQuery('#fixtureDiv').empty();
            jQuery('#wrapFixtures').hide();
            jQuery('#game_type').find('option').removeAttr('selected').hide();
            jQuery('#game_type').find('option[value="nfl_playoff"]').show();
            jQuery('#game_type').val('nfl_playoff');

            jQuery('.salary_remaining').hide();
            jQuery('.salary_remaining').find('input').attr('disabled', 'disabled');
            jQuery('#wrapContestType').hide();
            jQuery('#wrapPlayerRestriction').hide();
            jQuery('#wrapLineup').hide();
            jQuery('.leagueDiv').find('select').attr('disabled', 'disabled');
            jQuery('.leagueDiv').hide();
            jQuery('.for_playoff').show();
            jQuery('.for_playoff').find('select').removeAttr('disabled');

            if(jQuery('#playoff_wildcard_draft_start').length > 0){
                var plugin_url_image = jQuery("#plugin_url_image").val();
                jQuery('#playoff_wildcard_draft_start').datepicker({
                    minDate: 0,
                    //maxDate: maxDate,
                    showOn: "button",
                    buttonImage: plugin_url_image + "calendar.png",
                    buttonImageOnly: true,
                    buttonText: "Select date",
                    dateFormat: 'yy-mm-dd'
                });
            }
            if(jQuery('#playoff_divisional_draft_start').length > 0){
                var plugin_url_image = jQuery("#plugin_url_image").val();
                jQuery('#playoff_divisional_draft_start').datepicker({
                    minDate: 0,
                    //maxDate: maxDate,
                    showOn: "button",
                    buttonImage: plugin_url_image + "calendar.png",
                    buttonImageOnly: true,
                    buttonText: "Select date",
                    dateFormat: 'yy-mm-dd'
                });
            }
            if(jQuery('#playoff_conference_draft_start').length > 0){
                var plugin_url_image = jQuery("#plugin_url_image").val();
                jQuery('#playoff_conference_draft_start').datepicker({
                    minDate: 0,
                    //maxDate: maxDate,
                    showOn: "button",
                    buttonImage: plugin_url_image + "calendar.png",
                    buttonImageOnly: true,
                    buttonText: "Select date",
                    dateFormat: 'yy-mm-dd'
                });
            }
            if(jQuery('#playoff_super_bowl_draft_start').length > 0){
                var plugin_url_image = jQuery("#plugin_url_image").val();
                jQuery('#playoff_super_bowl_draft_start').datepicker({
                    minDate: 0,
                    //maxDate: maxDate,
                    showOn: "button",
                    buttonImage: plugin_url_image + "calendar.png",
                    buttonImageOnly: true,
                    buttonText: "Select date",
                    dateFormat: 'yy-mm-dd'
                });
            }

            return
        }
        else{
            jQuery('#game_type').find('option').show();
            jQuery('#game_type').find('option[value="nfl_playoff"]').removeAttr('selected').hide();
            jQuery('#game_type').trigger('change');
        }
    }
};
jQuery(window).load(function () {
    jQuery.createcontest.setData(
        jQuery("#sportData").length > 0 ? jQuery("#sportData").html().trim() : "[]",
        jQuery("#poolData").length > 0 ? jQuery("#poolData").html().trim() : "[]",
        jQuery("#fightData").length > 0 ?  jQuery("#fightData").html().trim() : "[]",
        jQuery("#roundData").length > 0 ? jQuery("#roundData").html().trim() : "[]",
        jQuery("#positionData").length > 0 ? jQuery("#positionData").html().trim() : "[]",
        jQuery("#lineupData").length > 0 ? jQuery("#lineupData").html().trim() : "[]",
        jQuery("#lineupNoPositionData").length > 0 ? jQuery("#lineupNoPositionData").html().trim() : "[]",
        jQuery("#mixingPoolData").length > 0 ? jQuery("#mixingPoolData").html().trim() : "[]"
    );

    if (jQuery("#type_create_contest").val() == 'mixing')
    {
           jQuery('.single_sport_group').hide();
           jQuery('.mixing_sport_group').hide();
           jQuery('.mixing_sport_group').show();
           jQuery.createcontest.loadPools();
         jQuery.createcontest.mixingLoadFixtures(true);
    } else
    {
        jQuery.createcontest.loadPools();
    }

    if (typeof jQuery("#leagueIDData").val() != 'undefined' && jQuery("#leagueIDData").val() != '')
    { 
        jQuery.createcontest.calculatePrizes();
        jQuery.createcontest.gameTypeAttr(jQuery('#gameTypeData').val());

        //jQuery.createcontest.loadPosition();
        // jQuery.createcontest.optionType();
        //jQuery.createcontest.selectSportType();
    } else
    {
        jQuery.createcontest.gameTypeAttr();
        jQuery.createcontest.loadPosition();
        jQuery.createcontest.optionType();
        jQuery.createcontest.selectSportType();
    }
    if(jQuery('#rugby_long_season').length > 0 && jQuery('#rugby_long_season').val() == 1){
        jQuery.createcontest.loadFights(jQuery('select[name=poolID]').val());
    }
    
    //for rugby
    jQuery.createcontest.rugbyInitStartDraftDatetime();
    jQuery.createcontest.loadLiveDraft();
    jQuery.createcontest.loadPickTieBreaker();
    jQuery.createcontest.loadSpecifyDatesForSeasonLong();
    jQuery.createcontest.loadRoundPickem();
    jQuery.createcontest.loadBracket();
    jQuery.createcontest.loadGoliath();
    jQuery.createcontest.loadMiniGoliath();
    jQuery.createcontest.loadSurvival();

    //playoff game type
    jQuery.createcontest.loadPlayoff();
    jQuery('#poolDates').change(function(){
        jQuery.createcontest.loadPlayoff();
    })
});

jQuery(document).on('change', '#pools', function () {
    jQuery.createcontest.rugbyInitStartDraftDatetime();
});

jQuery(document).on('click', '.radio input', function (event) {
    setOptions(this.value);
});

jQuery(document).on('submit', '#formCreateContest', function (e) {
    e.preventDefault();
    jQuery.createcontest.create();
});
jQuery(document).on('submit', '#mixFormCreateContest', function (e) {
    e.preventDefault();
    jQuery.createcontest.mixCreate();
});

jQuery('#formadmin_createcontest input[type="submit"] ').click(function(){
    if(jQuery('.group_prize_structure input[type="radio"]:checked').val() != 'multi_payout'){
                    jQuery('#payouts').empty();
       }
});
jQuery(document).ready(function(){
    if(jQuery('#sportType select option').length == 1 && jQuery('#sportType select option').val() == 'single'){
        jQuery('#sportType').hide();
    }
    else{
        //jQuery('#sportType').show();
        jQuery('#sportType').hide();
    }
    jQuery.createcontest.loadGuaranteedPrizeStructure();
    jQuery.createcontest.loadSpecifyNumberOfMultiEntries();
});

jQuery(document).on('change', '#game_type', function (e) {
    jQuery.createcontest.loadLiveDraft();
    jQuery.createcontest.loadPickTieBreaker();
    jQuery.createcontest.loadSpecifyDatesForSeasonLong();
    jQuery.createcontest.loadRoundPickem();
    jQuery.createcontest.loadBracket();
    jQuery.createcontest.loadGoliath();
    jQuery.createcontest.loadMiniGoliath();
    jQuery.createcontest.loadSurvival();
});