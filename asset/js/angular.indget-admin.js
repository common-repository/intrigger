
(function() {
    'use strict';
    var indget_preview_url = '';
    var typeApp = angular.module('itrrIndgetApp', []);

    var itrrIndgetAdminTypeCtrl = typeApp.controller('itrrIndgetAdminTypeCtrl', function($scope) {

            $scope.indget_types = JSON.parse(ITRR_INDGET_TYPES);
            $scope.indget_subtypes = JSON.parse(ITRR_INDGET_SUBTYPES);
            $scope.indget_type = jQuery('#intrigger_indget_type').val();
            $scope.indget_subtype = jQuery('#intrigger_indget_subtype').val();

            indget_preview_url = 'indget_' + $scope.indget_type.split('_')[0] + '_' + $scope.indget_subtype.split('_')[0] + '_preview';


            $scope.onChangeIndgetType = function(type_id) {
                jQuery('div.indget_type_ele').removeClass('selected');
                jQuery('div.indget_type_ele#' + type_id).addClass('selected');

                jQuery('div.indget_ele_preview').removeClass('selected');
                jQuery('div.indget_ele_preview#' + type_id + '_img').addClass('selected');

                jQuery('#intrigger_indget_type').val(type_id);
                $scope.indget_type = type_id;

                angular.element('[ng-controller=itrrIndgetAdminCtrl]').scope().indget_type = type_id;
                jQuery('.ind-type-area').addClass('hide');
                jQuery('#ind-' + $scope.indget_type + '-' + $scope.indget_subtype).removeClass('hide');

                indget_preview_url = 'indget_' + $scope.indget_type.split('_')[0] + '_' + $scope.indget_subtype.split('_')[0] + '_preview';
                get_preview_image();
            };
            $scope.onChangeIndgetSubType = function(subtype_id) {
                jQuery('div.indget_subtype_ele').removeClass('selected');
                jQuery('div.indget_subtype_ele#' + subtype_id).addClass('selected');

                jQuery('div.indget_subtype_ele_preview').removeClass('selected');
                jQuery('div.indget_subtype_ele_preview#' + subtype_id + '_img').addClass('selected');

                jQuery('#intrigger_indget_subtype').val(subtype_id);

                $scope.indget_subtype = subtype_id;
                angular.element('[ng-controller=itrrIndgetAdminCtrl]').scope().indget_subtype = subtype_id;
                jQuery('.ind-type-area').addClass('hide');
                jQuery('#ind-' + $scope.indget_type + '-' + $scope.indget_subtype).removeClass('hide');

                indget_preview_url = 'indget_' + $scope.indget_type.split('_')[0] + '_' + $scope.indget_subtype.split('_')[0] + '_preview';
                get_preview_image();
            };

        });

    var itrrIndgetAdminCtrl = typeApp.controller('itrrIndgetAdminCtrl', function($scope) {
        $scope.indget_type = jQuery('#intrigger_indget_type').val();
        $scope.indget_subtype = jQuery('#intrigger_indget_subtype').val();
        jQuery('.ind-type-area').addClass('hide');
        jQuery('#ind-' + $scope.indget_type + '-' + $scope.indget_subtype).removeClass('hide');
    });

    /**
     * Get the content of the tinyMCE editor.
     * @link http://wordpress.stackexchange.com/questions/42652/how-to-get-the-input-of-a-tinymce-editor-when-using-on-the-front-end
     * @return {string} Returns the content
     */
    function get_tinymce_content(editorID){
        //change to name of editor set in wp_editor()
        if (jQuery('#wp-'+editorID+'-wrap').hasClass("tmce-active"))
            var content = tinyMCE.get(editorID).getContent({format : 'raw'});
        else
            var content = jQuery('#'+editorID).val();
        return content;
    }

    jQuery(document).ready(function () {

        jQuery('li#toplevel_page_itrr_page_home').removeClass('wp-not-current-submenu');
        jQuery('li#toplevel_page_itrr_page_home').addClass('wp-has-current-submenu');
        jQuery('li#toplevel_page_itrr_page_home').addClass('wp-menu-open');
        jQuery('li#toplevel_page_itrr_page_home > a').removeClass('wp-not-current-submenu');
        jQuery('li#toplevel_page_itrr_page_home > a').addClass('wp-has-current-submenu');
        jQuery('li#toplevel_page_itrr_page_home > a').addClass('wp-menu-open');

        get_preview_image();

        jQuery('#con_drive_theme').change(function() {
            var selected_theme = jQuery(this).val();
            var theme_base_url = jQuery('#con_drive_theme_preview').attr('base-src');
            jQuery('#con_drive_theme_preview').attr('src', theme_base_url + selected_theme + '.png');
            jQuery('#con_drive_theme_name').text(selected_theme);
        });

        jQuery('.my-color-picker').spectrum({
            preferredFormat: "hex",
            showInput: true,
            showButtons: false
        });


        /**
         * Preview process.
         */
        function preview_collect(int_type){
            var headline = jQuery('#indget_'+int_type+'_collect_headline').val();
            var headline_fontsize = jQuery('#indget_'+int_type+'_collect_headline_fontsize').val();
            var input_preview = jQuery('#indget_'+int_type+'_collect_input_preview').val();
            var button_label = jQuery('#indget_'+int_type+'_collect_button_label').val();
            var background_color = jQuery('#indget_'+int_type+'_collect_background_color').val();
            var headline_font_color = jQuery('#indget_'+int_type+'_collect_headline_font_color').val();
            var button_background_color = jQuery('#indget_'+int_type+'_collect_button_background_color').val();
            var button_font_color = jQuery('#indget_'+int_type+'_collect_button_font_color').val();

            jQuery('div#indget_'+int_type+'_collect_preview div.int_indget_'+int_type+'_collect_default_title').text(headline);
            jQuery('div#indget_'+int_type+'_collect_preview input.itrr_collect_email').attr('placeholder', input_preview);
            jQuery('div#indget_'+int_type+'_collect_preview a[itrr-btn="'+int_type+'_collect"]').text(button_label);
            jQuery('div#indget_'+int_type+'_collect_preview div.int_indget_'+int_type+'_collect_default_title').css('font-size', headline_fontsize + 'px');
            jQuery('div#indget_'+int_type+'_collect_preview div.int_indget_'+int_type+'_collect_default_title').css('color', headline_font_color);
            jQuery('div#indget_'+int_type+'_collect_preview div.int_indget_'+int_type+'_collect_default').css('background-color', background_color);
            jQuery('div#indget_'+int_type+'_collect_preview a[itrr-btn="'+int_type+'_collect"]').css('background-color', button_background_color);
            jQuery('div#indget_'+int_type+'_collect_preview a[itrr-btn="'+int_type+'_collect"]').css('color', button_font_color);

            get_preview_image();
        }
        function preview_drive(int_type){
            var headline = jQuery('#indget_'+int_type+'_drive_headline').val();
            var headline_fontsize = jQuery('#indget_'+int_type+'_drive_headline_fontsize').val();
            var button_label = jQuery('#indget_'+int_type+'_drive_button_label').val();
            var button_link = jQuery('#indget_'+int_type+'_drive_button_link').val();
            var message_body = get_tinymce_content('indget_'+int_type+'_drive_message_body');
            var background_color = jQuery('#indget_'+int_type+'_drive_background_color').val();
            var headline_font_color = jQuery('#indget_'+int_type+'_drive_headline_font_color').val();
            var button_background_color = jQuery('#indget_'+int_type+'_drive_button_background_color').val();
            var button_font_color = jQuery('#indget_'+int_type+'_drive_button_font_color').val();

            jQuery('div#indget_'+int_type+'_drive_preview div.int_indget_'+int_type+'_drive_default_title').text(headline);
            jQuery('div#indget_'+int_type+'_drive_preview a[itrr-btn="'+int_type+'"]').text(button_label);
            jQuery('div#indget_'+int_type+'_drive_preview div.int_indget_'+int_type+'_drive_default_title').css('font-size', headline_fontsize + 'px');
            jQuery('div#indget_'+int_type+'_drive_preview div.int_indget_'+int_type+'_drive_default_title').css('color', headline_font_color);
            jQuery('div#indget_'+int_type+'_drive_preview div.int_indget_'+int_type+'_drive_default').css('background-color', background_color);
            jQuery('div#indget_'+int_type+'_drive_preview a[itrr-btn="'+int_type+'"]').css('background-color', button_background_color);
            jQuery('div#indget_'+int_type+'_drive_preview a[itrr-btn="'+int_type+'"]').css('color', button_font_color);
            jQuery('div#indget_'+int_type+'_drive_preview a[itrr-btn="'+int_type+'"]').attr('href', button_link);
            jQuery('div#indget_'+int_type+'_drive_preview div.int_indget_'+int_type+'_drive_default_message').html(message_body);

            get_preview_image();

        }
        jQuery('a#indget_continue_collect_preview_refresh').click(function() {
            preview_collect('continue');
        });
        jQuery('a#indget_continue_drive_preview_refresh').click(function() {
            preview_drive('continue');
        });
        //
        jQuery('a#indget_inline_collect_preview_refresh').click(function() {
            preview_collect('inline');
        });
        jQuery('a#indget_inline_drive_preview_refresh').click(function() {
            preview_drive('inline');
        });
        //
        jQuery('a#indget_float_collect_preview_refresh').click(function() {
            preview_collect('float');
        });
        jQuery('a#indget_float_drive_preview_refresh').click(function() {
            preview_drive('float');
        });
    });
    function get_preview_image(){
        if(jQuery('#'+indget_preview_url).length>0) {
            html2canvas(jQuery('#' + indget_preview_url), {
                onrendered: function (canvas) {
                    var dataURL = canvas.toDataURL();
                    jQuery('#indget_preview_img_base').val(dataURL);
                },
                width: 800,
                height: 140
            });
        }else{
            jQuery('#indget_preview_img_base').val("");
        }
    }
}());

