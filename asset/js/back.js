/**
 * Created by Gaun on 9/4/2015.
 */

jQuery(document).ready(function(){

    // Process to output contacts into CSV
    jQuery('#export_contacts').click(function() {
        var data = {
            action: 'itrr_csv_action'
        }
        jQuery.ajax({
            url: itrr_admin_ajax_url,
            datatype: 'html',
            type: 'POST',
            data: data,
            success: function(respond) {

            }
        });

        return true;
    });
    if(jQuery("#itrr_date_picker").length) {
        jQuery("#itrr_date_picker").daterangepicker({
            initialText : 'Today...',
            presetRanges: [{
                text: 'Today',
                dateStart: function () {
                    return moment()
                },
                dateEnd: function () {
                    return moment()
                }
            }, {
                text: 'Yesterday',
                dateStart: function () {
                    return moment().subtract(1, 'days')
                },
                dateEnd: function () {
                    return moment().subtract(1, 'days')
                }
            }, {
                text: 'Current Week',
                dateStart: function () {
                    return moment().startOf('week')
                },
                dateEnd: function () {
                    return moment().endOf('week')
                }
            }, {
                text: 'Last Week',
                dateStart: function () {
                    return moment().add('weeks', -1).startOf('week')
                },
                dateEnd: function () {
                    return moment().add('weeks', -1).endOf('week')
                }
            }, {
                text: 'Last Month',
                dateStart: function () {
                    return moment().add('months', -1).startOf('month')
                },
                dateEnd: function () {
                    return moment().add('months', -1).endOf('month')
                }
            }],

            datepickerOptions: {
                numberOfMonths: 2
                //initialText: 'Select period...'
            },
            onChange: function () {
                var date_range = JSON.stringify(jQuery("#itrr_date_picker").daterangepicker("getRange"));
                var security = jQuery('#itrr_homepage_ajax_nonce').val();
                jQuery('.itrr_date_picker button').addClass('ui-selected');
                var data = {
                    action: 'itrr_date_action',
                    security: security,
                    begin: JSON.parse(date_range).start,
                    end: JSON.parse(date_range).end
                };
                jQuery.ajax({
                    url: itrr_admin_ajax_url,
                    datatype: 'html',
                    type: 'POST',
                    data: data,
                    success: function(respond) {                      
                        //location.reload();
                        respond = JSON.parse(respond);

                        jQuery('#itrr_stats_impression').text(respond['total_impression']);
                        jQuery('#itrr_stats_conversion').text(respond['total_conversion']);
                        jQuery('#itrr_stats_rate').text(respond['total_rate'] + '%');
                        jQuery.each(respond['triggers'], function(key, val){
                            jQuery('.itrr_active_triggers').eq(key).find('.itrr_impression_val').text(val.impression);
                            jQuery('.itrr_active_triggers').eq(key).find('.itrr_conversion_val').text(val.conversion);
                            jQuery('.itrr_active_triggers').eq(key).find('.itrr_rate_val').text(val.rate + '%');
                        });
                    }
                });
            }
        });
    }
    // send contact email
    jQuery('#itrr_contact_send').click(function(){
        var valid = true;
        if(jQuery('.itrr_contact_msg').val() == ''){
            jQuery('#failure-alert').show();
            jQuery('.itrr_contact_msg').addClass('error');
            valid = false;
        }
        var email = jQuery('#itrr_contact_email').val();
        if(email == '' || isValidEmailAddress(email) != true) {
            jQuery('#itrr_contact_email').addClass('error');
            jQuery('#failure-alert').show();
            return false;
        }

        if(valid != true) return false;

        jQuery(this).attr('disabled', 'true');
        var security = jQuery('#itrr_support_ajax_nonce').val();
        var data = {
            action:'itrr_send_email_action',
            email: email,
            content: jQuery('.itrr_contact_msg').val(),
            security: security
        }
        jQuery.ajax({
            url: itrr_admin_ajax_url,
            datatype: 'html',
            type: 'POST',
            data: data,
            success: function(respond) {
                jQuery('#itrr_contact_send').removeAttr('disabled');
                respond = jQuery.parseJSON(respond);
                if (respond.code != 'success') {
                    jQuery('#curl_failure-alert').show();
                } else {
                    jQuery('#success-alert').show();
                }
            }
        });
    });
    jQuery('.itrr_contact_msg').bind('input propertychange', function(){
        jQuery('#failure-alert').hide();
        jQuery('#curl_failure-alert').hide();
        jQuery('#success-alert').hide();
    });
    jQuery('#itrr_contact_email').change(function(){
        jQuery('#itrr_contact_email').removeClass('error');
    });
    // Sendinblue
    jQuery('#itrr_sib_login_button').click(function(){
        var api_key = jQuery('.itrr_sendinblue_access_key').val();
        if(api_key == ''){
            jQuery('.itrr_sendinblue_access_key').addClass('error');
        }
        jQuery('.itrr_sendinblue_access_key').removeClass('error');
        var security = jQuery('#itrr_setting_ajax_nonce').val();
        var data = {
            action:'itrr_login_sendinblue',
            key: api_key,
            security: security
        }
        jQuery('.itrr_loading_bar').show();
        jQuery.ajax({
            url: itrr_admin_ajax_url,
            datatype: 'html',
            type: 'POST',
            data: data,
            success: function(respond) {
                jQuery('.itrr_loading_bar').hide();
                respond = jQuery.parseJSON(respond);
                if(respond.code == 'success'){
                    location.reload();
                }else{
                    jQuery('.itrr_sendinblue_access_key').addClass('error');
                }
            }
        });
    });
    jQuery('.itrr_sendinblue_access_key').change(function(){
        jQuery('.itrr_sendinblue_access_key').removeClass('error');
    });

    // Mailchimp itrr_mcp_login_button
    jQuery('#itrr_mcp_login_button').click(function(){
        var api_key = jQuery('.itrr_mailchimp_access_key').val();
        if(api_key == ''){
            jQuery('.itrr_mailchimp_access_key').addClass('error');
        }
        jQuery('.itrr_mailchimp_access_key').removeClass('error');
        var security = jQuery('#itrr_setting_ajax_nonce').val();
        var data = {
            action:'itrr_login_mailchimp',
            key: api_key,
            security: security
        };
        jQuery('.itrr_loading_bar').show();
        jQuery.ajax({
            url: itrr_admin_ajax_url,
            datatype: 'html',
            type: 'POST',
            data: data,
            success: function(respond) {
                jQuery('.itrr_loading_bar').hide();
                respond = jQuery.parseJSON(respond);
                if(respond.code == 'success'){
                    location.reload();
                }else{
                    jQuery('.itrr_mailchimp_access_key').addClass('error');
                }
            }
        });
    });
    jQuery('.itrr_mailchimp_access_key').change(function(){
        jQuery('.itrr_mailchimp_access_key').removeClass('error');
    });
    // for searching posts and pages by keyword
    jQuery(document).on('click', '#itrr_search_posts', function(){
        // hide confirm message;
        jQuery('#itrr_search_posts_results_message').hide(800);
        var keyword = jQuery('#itrr_search_posts_key').val();
        var data = {
            action:'itrr_search_page_link',
            keyword: keyword
        }
        jQuery.ajax({
            url: itrr_admin_ajax_url,
            datatype: 'json',
            type: 'POST',
            data: data,
            success: function(respond) {
                respond = jQuery.parseJSON(respond);
                var result_string;
                var count_posts = respond.length;
                if(typeof(count_posts) == 'undefined')
                {
                    result_string = 'There is not selected pages with "' + keyword + '"in URL or title';
                }
                else
                {
                    if ( keyword == '')
                    {
                        result_string = 'There are all ' + count_posts + ' pages';
                    }
                    else
                    {
                        result_string = 'We found ' + count_posts + ' pages with the keyword "' + keyword + '"';
                    }

                }
                jQuery('#itrr_search_result_message').html('<p>' + result_string + '</p>');
                var html='';
                var grid_class = '';
                for ( var i = 0 ; i < count_posts ; i++)
                {
                    if(i%2 == 0)
                    {
                        grid_class = 'even';
                    }
                    else
                    {
                        grid_class = 'odd';
                    }
                    html += '<tr class = "itrr_result_list ' + grid_class + '"><td class="itrr_srt_first"> <input type="checkbox" class="itrr_search_result_list" value="' + respond[i].permalink+ '" checked/></td><td class="itrr_srt_second">' + respond[i].title + '</td><td class="itrr_srt_third">' + respond[i].info + '</td></tr>';
                   }
                if ( count_posts === undefined )
                {
                    count_posts = 0;
                }
                jQuery('#itrr_search_result_list_all').prop('checked' , true);
                jQuery('#itrr_search_result_table tbody').html(html);
                jQuery('#itrr_search_select_message'). html('<p>' + count_posts + ' pages selected </p>');
                jQuery("#itrr_search_posts_results").show();
                // for table title alignment
                var cur_width = jQuery(".itrr_search_result_table_head .itrr_srt_third").width();
                var th_width =  jQuery(".itrr_search_result_table_head .itrr_srt_second").width();
                var tbody_width = jQuery(".itrr_result_list .itrr_srt_second").width();
                var delta_width = th_width - tbody_width ;
                jQuery(".itrr_search_result_table_head .itrr_srt_third").css('width', cur_width + delta_width + 'px');
            }
        });
    });
    // for select all
    jQuery('#itrr_search_result_list_all').live('change',function(){
       if ( jQuery(this).is(':checked'))
       {
           jQuery('#itrr_search_result_table tbody').find("input").prop('checked' ,true);
           var counts_selected = jQuery('#itrr_search_result_table > tbody > tr').length;
           jQuery('#itrr_search_select_message'). html('<p>' + counts_selected + ' pages selected </p>');
       }
        else
       {
           jQuery('#itrr_search_result_table tbody').find("input").prop('checked' ,false);
           jQuery('#itrr_search_select_message'). html('<p> There are not any selected pages yet </p>');
       }
    });
    //for select individual list
    jQuery('.itrr_search_result_list').live("change" , function(){
       var len = jQuery('#itrr_search_result_table tbody input:checked').length;
        jQuery('#itrr_search_select_message'). html('<p>' + len + ' pages selected </p>');
        if(!jQuery(this).is(':checked'))
        {
            jQuery('#itrr_search_result_list_all').prop('checked' , false);
        }
    });
    // confirm selected lists.
    jQuery(document).on('click', '#itrr_search_confirm_button', function(event){
        event.preventDefault();
        var reg_url = '';
        var counts_rsult = 0;
        jQuery('.itrr_search_result_list').each(function(){
            if(jQuery(this).is(':checked'))
            {
                reg_url += jQuery(this).val().replace(itrr_site_url , '') + "|";
                counts_rsult++ ;
            }
        });
        var result_url = reg_url.substring(0 , reg_url.length - 1);
        if(reg_url.length > 0) {
            jQuery('#sc_rule_where_specific').prop('checked', true);
            var specific_count = parseInt(jQuery('#specific_url_account').val()) + 1;
            jQuery('#specific_url_account').val(specific_count);
            if (jQuery('#sc_rule_where_first_url').val() == "") {
                jQuery('#sc_rule_where_first_url').val(result_url);
            }
            else {
                var added_html = '<div class="scenario_form_ele specific_url_ele" id="sc_rule_specific_url_' + specific_count + '" >' +
                    '<div class="scenario_title_area"> &nbsp; </div>' + '<div class="scenario_field_area">' +
                    '&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="sc_rule[where][specific_urls][]" class="specific_url" value="'
                    + result_url + '">&nbsp;&nbsp;<span class="remove_specfic_url_btn" onclick="removeSpecificURLElement(' + specific_count + ');">' +
                    '<i class="fa fa-trash-o fa-lg"></i></span>' + '</div></div>';
                jQuery('div.specific_url_group').append(added_html);
            }
        }
        jQuery('#itrr_search_posts_results').hide();
        var confirm_message = '';
        if(jQuery('#itrr_search_posts_key').val() == '')
        {
            confirm_message += '<p>' + counts_rsult + 'pages are selected.';
        }
        else
        {
            confirm_message = '<p> The' + counts_rsult + ' pages selected for the keyword "' + jQuery('#itrr_search_posts_key').val() + '" are confirmed.</p>';
        }
        confirm_message += '<p>The URLs of these pages have been added to the field Regex in the tab By pages.</p>';
        jQuery('#itrr_search_posts_results_message').html(confirm_message);
        jQuery('#itrr_search_posts_results_message').show(800);

    });
    // click cancel
    jQuery(document).on('click', '#itrr_search_cancel_button', function(event){
        event.preventDefault();
        jQuery('#itrr_search_result_list_all').prop('checked' , false);
        jQuery('#itrr_search_result_table tbody').find("input").prop('checked' ,false);
        jQuery('#itrr_search_posts_results').hide();
        jQuery('#itrr_search_posts_key').val('');
    });

    jQuery(document).on('click', '#itrr_clear_setting', function(event){
        event.preventDefault();
        jQuery('#itrr_clear_setting_panel').show(800);

    });
    jQuery(document).on('click', '#itrr_setting_confirm_yes', function(event){
        event.preventDefault();
        var security = jQuery('#itrr_setting_ajax_nonce').val();
        var data = {
            action:'clear_plugin_settings',
            clear: 'yes',
            security : security
        };
        jQuery(this).prop("disabled", true);
        jQuery('#itrr_clear_setting_panel').css('opacity','0.6');
        jQuery.ajax({
            url : itrr_admin_ajax_url,
            type : "POST",
            data : data,
            success : function(respond)
            {
                if ( respond == 'success')
                {
                    jQuery('#itrr_setting_confirm_yes').prop('disabled', false);
                    jQuery('#itrr_clear_setting_panel').css('opacity','1');
                    jQuery('#itrr_clear_setting_panel').hide();
                    jQuery('#itrr_setting_clear_success_message').show(400);
                    setInterval(function () {jQuery('#itrr_setting_clear_success_message').hide(400); }, 3000);
                    jQuery('input[name = "itrr_setting_seo_continue"]').prop('checked' , true);
                    jQuery('input[name = "itrr_setting_contacts"]').prop('checked' , true);
                    jQuery('input[name = "itrr_setting_branding"]').prop('checked' , true);
                    // for sendinblue default view
                    var html_sib = "<h3><?php _e('Create a free SendinBlue account','itrr_lang'); ?></h3><div class='itrr_setting_text'>" +
                        " Sign up for free and send up to 9,000 emails/month. <br> SendinBlue is one of the best all-in-one marketing platform." +
                        "<br><br> <p><span class='dashicons dashicons-yes'></span>Send email newsletters, transactional messages and even text messages" +
                        "</p> <p><span class='dashicons dashicons-yes'></span>Collect and manage your contacts with powerful segmentation</p> <p>" +
                        "<span class='dashicons dashicons-yes'></span>Configure scenarios targeting specific contact behaviors" +
                        "</p><p><span class='dashicons dashicons-yes'></span>Build your emails easily with the responsive design builder" +
                        "</p><p><span class='dashicons dashicons-arrow-right-alt2'></span><a href='https://www.sendinblue.com/features/?ae=206' target='_blank'>" +
                        "See all features</a></p> <p><span class='dashicons dashicons-arrow-right-alt2'></span>" +
                        "<a href='https://www.sendinblue.com/pricing/?ae=206' target='_blank'>See pricing for more than 9,000 emails/month" +
                        "</a></p></div> <h3>Synchronization</h3> <div class='itrr_setting_text'> " +
                        "<p>To active contact\'s synchronization with SendinBlue, please enter your API Access key:" +
                        "</p> <input type='text' placeholder='API key' name='itrr_sib_access_key' class='itrr_input itrr_sendinblue_access_key' value='' /> " +
                        "<i id='' class='fa fa-cog fa-spin fa-2x itrr_loading_bar' style='display:none; vertical-align: top;color: #808080;'></i>" +
                        " <a href='javascript:void(0)' id='itrr_sib_login_button' class='itrr_button' style='margin-left: 12px' >Validate</a>" +
                        " <br> <a href='https://my.sendinblue.com/advanced/apikey/?utm_source=wordpress_plugin&amp;utm_medium=plugin&amp;utm_campaign=module_link' target='_blank'>" +
                        "<span class='dashicons dashicons-arrow-right-alt2'></span>Get the API key from my SendinBlue account</a></div>";
                    jQuery('.itrr_setting_content .itrr_sendinblue_content').css('display' , 'block');
                    jQuery('.itrr_setting_content .itrr_sendinblue_content .itrr_setting_left').html(html_sib);
                    if( !jQuery('.itrr_sib_tab .itrr_arrow').hasClass('open'))
                    {
                        jQuery('.itrr_sib_tab .itrr_arrow').addClass('open');
                        jQuery('.itrr_sib_tab .itrr_arrow').removeClass('close');
                    }
                    // for mailchimp default view
                    var html_mch = "<div class='itrr_setting_text'> " +
                        "<p>To active contact\'s synchronization with Mailchimp, please enter your API Access key:</p> " +
                        "<input type='text' placeholder='API key' name='itrr_mcp_access_key' class='itrr_input itrr_mailchimp_access_key' value='' /> " +
                        "<i id='' class='fa fa-cog fa-spin fa-2x itrr_loading_bar' style='display:none; vertical-align: top;color: #808080;'></i> " +
                        "<a href='javascript:void(0)' id='itrr_mcp_login_button' class='itrr_button' style='margin-left: 12px' >Validate</a><br> " +
                        "<a href='https://us3.admin.mailchimp.com/account/api/' target='_blank'><span class='dashicons dashicons-arrow-right-alt2'> " +
                        "</span>Get the API key from my Mailchimp account</a> </div>";
                    jQuery('.itrr_setting_content .itrr_mcp_content .itrr_setting_left').html(html_mch);
                    jQuery('.itrr_setting_content .itrr_mcp_content').css('display' , 'none');
                    if( !jQuery('.itrr_mcp_tab .itrr_arrow').hasClass('close'))
                    {
                        jQuery('.itrr_mcp_tab .itrr_arrow').addClass('close');
                        jQuery('.itrr_mcp_tab .itrr_arrow').removeClass('open');
                    }
                }
                else
                {
                    jQuery('#itrr_clear_setting_panel').hide();
                    jQuery('#itrr_setting_clear_fail_message').show(400);
                    setInterval(function () {jQuery('#itrr_setting_clear_fail_message').hide(400); }, 3000);
                }
            }
        })


    });
    jQuery(document).on('click', '#itrr_setting_confirm_cancel', function(event) {
        event.preventDefault();
        jQuery('#itrr_clear_setting_panel').hide(800);
    });
});

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/);
    return pattern.test(emailAddress);
}