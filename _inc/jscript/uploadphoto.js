var result_select_id = "";
var result_select_page = "";
jQuery.uploadphoto =
{
    initResult: function()
    {
        var is_live = jQuery('#is_live').val();

        //check live
        if(is_live == 1)
        {
            jQuery.uploadphoto.liveEntriesResult();
            setInterval(function(){ 
                jQuery.uploadphoto.liveEntriesResult();
            }, 60000);
        }
        else
        {
            jQuery.uploadphoto.loadResult();
        }

        //upload result modal
        jQuery('#btn-update-result').click(function(){
            var dialog = jQuery("#dlgUploadPhoto").dialog({
                height: 'auto',
                width:'700',
                modal:true,
                title:wpfs['update_result'],
                open:function(){
                    new qq.FineUploader({
                        element: document.getElementById("uploadPhotoImage"),
                        request: {
                            endpoint: ajaxurl + '?resize=0'
                        },
                        multiple: false,
                        validation: {
                            allowedExtensions: ['jpeg', 'jpg', 'png', 'gif'],
                            //itemLimit: 1,
                            sizeLimit: 52428800 // 50 kB = 50 * 1024 bytes
                        },
                        callbacks: {
                            onComplete: function(id, name, result, xhr) {
                                jQuery('#formUploadPhoto input[name="image"]').val(result.filepath);
                            }
                        }
                    });
                },
                close:function(){
                    jQuery('#formUploadPhoto input[name="total_kill"]').val('');
                    jQuery('#formUploadPhoto input[name="finish"]').val('');
                    jQuery('#formUploadPhoto input[name="image"]').val('');
                    jQuery('#uploadPhotoImage').empty();
                },
                buttons: {
                    "Update": function() {
                        jQuery.uploadphoto.saveUploadPhotoResult()
                    },
                    Cancel: function() {
                        dialog.dialog( "close" );
                    }
                },
            });
        })

        //init standing
        this.initStanding();
    },

    initStanding: function(admin_page){
        //result detail
        jQuery(document).off('click', '.view_standing_detail');
        jQuery(document).on('click', '.view_standing_detail', function(){
            result_select_id = jQuery(this).attr('id');
            jQuery('#table_standing tr').removeClass('active');
            jQuery(this).addClass('active');
            var league_id = jQuery(this).data('leagueId');
            var user_id = jQuery(this).data('userId');
            var entry_number = jQuery(this).data('entryNumber');
            jQuery.uploadphoto.loadResultDetail(league_id, user_id, entry_number, admin_page);
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
            action: 'uploadPhotoLoadResult',
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
                jQuery('.view_standing_detail:first').trigger('click');
            }
        });
    },

    loadResultDetail: function (league_id, user_id, entry_number, admin_page)
    {
        jQuery.global.showLoading('#result');
        var data = {
            action: 'uploadPhotoLoadResultDetail',
            league_id: league_id,
            user_id: user_id,
            entry_number: entry_number,
            admin_page: admin_page
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
            jQuery.uploadphoto.loadResult();
        });
    },
    
    saveUploadPhotoResult: function(){
        jQuery('#msgUploadPhoto').hide();
        jQuery.post(ajaxurl, jQuery('#formUploadPhoto').serialize(), function (result) {
            result = JSON.parse(result);
            if(result.success == 1){
                jQuery('#dlgUploadPhoto').dialog('close');
                jQuery.uploadphoto.loadResult();
                alert(result.message);
            }
            else{
                jQuery('#msgUploadPhoto').html(result.message).show();
            }
        });
    }
};