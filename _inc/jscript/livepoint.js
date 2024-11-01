var gl_sort = "points DESC";
jQuery(document).on('change','#gameType',function(){
    loadContestByGameType();
});

function loadContestByGameType()
{
    var gameType = jQuery("#gameType").val();
    jQuery.post(ajaxurl, "action=getLeagueByGameType&gameType=" + gameType, function(result) {
        jQuery('#league').html(result);
    });
}

jQuery(document).on('click','#player_stats_paging .pa',function(){
   var btn = jQuery(this);
   jQuery("#player_stats_paging .pa").removeClass("disabled");
   jQuery("#player_stats_paging .pa").removeClass("disable-paging");
   jQuery(btn).addClass(" disabled"); 
   jQuery(btn).addClass(" disable-paging");
   var page = jQuery("#player_stats_paging .disabled").data("page");
   loadPage(page,gl_sort);
});

jQuery(document).on("change","#city",function(){
    loadPage(1);
})

jQuery(document).on("click","#header .header",function(){
    var sort = jQuery(this).data("sorttype");
    if(sort.search("DESC") != -1){
        var sort_after = sort.split(" ");
        jQuery(this).removeAttr("data-sorttype",true);
        jQuery(this).attr("data-sorttype",sort_after[0]);
    }else{
        jQuery(this).removeAttr("data-sorttype",true);
        jQuery(this).attr("data-sorttype",sort + " DESC");
    }
    gl_sort = sort;
    loadPage(1);
})

jQuery(document).on("change","#league",function(){
    loadPage(1,gl_sort);
})

function loadPage(page){
    showLoading("#team_detail");
    var gameType = jQuery("#gameType").val();
    var city = jQuery("#city").val();
    var leagueid = jQuery("#league").val();
    jQuery.post(ajaxurl, "action=getLivePoint&gameType=" + gameType + "&page="+page+"&city="+city+"&sort="+gl_sort+"&leagueid="+leagueid, function(result) {
        jQuery("#team_detail").html(result);
    });

}
function showLoading(wrapper){
    jQuery(wrapper).append('<div class="f-loading"></div>');
}


