function removeSpecificURLElement(url_index) {
    var url_element = jQuery('#sc_rule_specific_url_' + url_index);
    url_element.remove();
}
function removeSpecificExcludedURLElement(url_index) {
    var url_element = jQuery('#sc_rule_specific_url_excluded_' + url_index);
    url_element.remove();
}
(function() {
    'use strict';

    var scenarioApp = angular.module('itrrScenarioApp', []);
    var itrrScenarioTypeCtrl = scenarioApp.controller('itrrScenarioTypeCtrl', function($scope) {

        $scope.float_bar_open = "";
        $scope.indget_types = JSON.parse(ITRR_INDGET_TYPES);
        $scope.scenario_type = jQuery('#intrigger_scenario_type').val();
        $scope.onChangeType = function(type_id) {
            if ($scope.scenario_type == type_id) {
                return;
            }
            // pages tooltip show for float_bar only...
            jQuery('#itrr_scenario_page_tip').show();
            if(type_id == 'float_bar')
                jQuery('#itrr_scenario_page_tip').hide();

            jQuery('div.scenario_type_ele').removeClass('selected');
            jQuery('div.scenario_type_ele#' + type_id).addClass('selected');

            jQuery('div.scenario_ele_preview').removeClass('selected');
            jQuery('div.scenario_ele_preview#' + type_id + '_img').addClass('selected');

            jQuery('#intrigger_scenario_type').val(type_id);
            $scope.scenario_type = type_id;

            if(typeof ITRR_HOME_LISTS == 'undefined') {
                var indgets = JSON.parse(ITRR_INDGET_LISTS);
                var display_indgets = [];
                angular.forEach(indgets, function (item, key) {
                    if (item.type == $scope.scenario_type) {
                        display_indgets.push({
                            id: key,
                            name: item.name
                        });
                    }
                });
                angular.element('[ng-controller=itrrScenarioIndgetCtrl]').scope().scenario_type = type_id;
                angular.element('[ng-controller=itrrScenarioIndgetCtrl]').scope().indgets = display_indgets;
                angular.element('[ng-controller=itrrScenarioIndgetCtrl]').scope().indget_id = '';
            }else{
                var href = jQuery('#itrr_home_continue_btn').attr('href');
                var param = jQuery('#intrigger_scenario_type').val();
                jQuery('#itrr_home_continue_btn').attr('href',href + '&sel_type=' + param);

            }
        }


    });

    var itrrScenarioIndgetCtrl = scenarioApp.controller('itrrScenarioIndgetCtrl', function($scope, $http) {
        $scope.scenario_type = jQuery('#intrigger_scenario_type').val();
        var indgets = JSON.parse(ITRR_INDGET_LISTS);
        var display_indgets = [];
        var scenario_indget_id = jQuery('#scenario_indget_id').val();
        var selected_num = -1;
        angular.forEach(indgets, function(item, key) {
           if (item.type == $scope.scenario_type) {
               if (key == scenario_indget_id) {
                   selected_num = display_indgets.length;
               }
               display_indgets.push({
                   id : key,
                   name : item.name
               });
           }
        });
        if (selected_num >= 0) {
            $scope.indget_id = display_indgets[selected_num];
        }
        $scope.indgets = display_indgets;
        $scope.onChangeIndget = function(indget_id) {
            var data = {
                action: 'get_preview_indget',
                indget_id: indget_id
            }
            jQuery.ajax({
                url: itrr_admin_ajax_url,
                datatype: 'html',
                type: 'POST',
                data: data,
                success: function(respond) {
                    jQuery('#scenario_indget_preview').html(respond);
                }
            });
        }
    });
    scenarioApp.directive('chosenSelect', function() {
        return {
            // Restrict it to be an attribute in this case
            restrict: 'A',
            // responsible for registering DOM listeners as well as updating the DOM
            link: function(scope, element, attrs) {
                jQuery(element).chosen();
            }
        };
    });
    var itrrScenarioRulesCtrl = scenarioApp.controller('itrrScenarioRulesCtrl', function($scope) {
       // $scope.rule_when_opt = 'already_visited';
        $scope.addSpecificURL = function() {
            var specific_count = parseInt(jQuery('#specific_url_account').val()) + 1;
            jQuery('#specific_url_account').val(specific_count);
            var added_html = '<div class="scenario_form_ele specific_url_ele" id="sc_rule_specific_url_' + specific_count + '" >' +
                '<div class="scenario_title_area">&nbsp;</div>' +
                '<div class="scenario_field_area">' +
                '&nbsp;&nbsp;&nbsp;&nbsp;<input type="url" name="sc_rule[where][specific_urls][]" class="specific_url">&nbsp;&nbsp;<span class="remove_specfic_url_btn" onclick="removeSpecificURLElement(' + specific_count + ');"><i class="fa fa-trash-o fa-lg"></i></span>' +
                '</div></div>';
            jQuery('div.specific_url_group').append(added_html);
        }
        $scope.addSpecificURLExcluded = function() {
            var specific_count = parseInt(jQuery('#specific_url_account_excluded').val()) + 1;
            jQuery('#specific_url_account_excluded').val(specific_count);
            var added_html = '<div class="scenario_form_ele specific_url_ele" id="sc_rule_specific_url_excluded_' + specific_count + '" >' +
                '<div class="scenario_title_area">&nbsp;</div>' +
                '<div class="scenario_field_area">' +
                '&nbsp;&nbsp;&nbsp;&nbsp;<input type="url" name="sc_rule[where][specific_urls_excluded][]" class="specific_url">&nbsp;&nbsp;<span class="remove_specfic_url_btn" onclick="removeSpecificExcludedURLElement(' + specific_count + ');"><i class="fa fa-trash-o fa-lg"></i></span>' +
                '</div></div>';
            jQuery('div.specific_url_group_excluded').append(added_html);
        }
        /* categories */
        $scope.updatePost = function(){
            if($scope.rule_where_post){
                // if the post is checked, ...
                if(jQuery( ".itrr_cats:checked" ).length == 0) jQuery('.itrr_cats').prop('checked', true);
            }
        }
        $scope.updateSubPost = function(){
            // if the post is checked, ...
            if(jQuery( ".itrr_cats:checked" ).length == 0) jQuery('.itrr_post').prop('checked', false);

        }

        /* terms */
        $scope.cpts = angular.fromJson(ITRR_CPTS);
        /*$scope.cpts = {
            "Articles": {
                "data": [{"name": "Version1", "select": true}, {"name": "Version2", "select": false}],
                "select": true
            },
            "Machines":[]};*/
        $scope.updateCPT = function(cpt){
            if(jQuery( "."+cpt+":checked" ).length == 0)
                jQuery('.'+cpt).prop('checked', true);
        }
        $scope.updateTerm = function(cpt){
            jQuery('.cpt_'+cpt).prop('checked', true);
            // if the terms are checked, ...
            if(jQuery( "."+cpt+":checked" ).length == 0) jQuery('.cpt_'+cpt).prop('checked', false);

        }

    });
    // for priority score comment show/hide
    jQuery(document).ready(function () {
        jQuery('li#toplevel_page_itrr_page_home').removeClass('wp-not-current-submenu');
        jQuery('li#toplevel_page_itrr_page_home').addClass('wp-has-current-submenu');
        jQuery('li#toplevel_page_itrr_page_home').addClass('wp-menu-open');
        jQuery('li#toplevel_page_itrr_page_home > a').removeClass('wp-not-current-submenu');
        jQuery('li#toplevel_page_itrr_page_home > a').addClass('wp-has-current-submenu');
        jQuery('li#toplevel_page_itrr_page_home > a').addClass('wp-menu-open');
        jQuery('#itrr_priority_comment').mouseenter(function(){
            jQuery('.senario_priority_comment_area').addClass('show_comment');
        });
        jQuery('#itrr_priority_comment').mouseleave(function(){
            jQuery('.senario_priority_comment_area').removeClass('show_comment');
        });
        jQuery('.itrr-tooltip').tooltipster({
            theme: 'tooltipster-light',
            maxWidth: 320});
    });
	  var itrrScenarioTabCtrl = scenarioApp.controller('TabsCtrl', ['$scope', function ($scope) {
        $scope.tabs = [{
            title: 'By pages / posts',
            url: 'one.tpl.html'
        }, {
            title: 'By keyword',
            url: 'two.tpl.html'
        }];

        $scope.currentTab = 'one.tpl.html';

        $scope.onClickTab = function (tab) {
            $scope.currentTab = tab.url;
            if($scope.currentTab == 'two.tpl.html')
            {
                jQuery('#itrr_scenario_form_group').hide();
            }
            else
            {
                jQuery('#itrr_scenario_form_group').show();
            }
        }
        $scope.isActiveTab = function(tabUrl) {
            return tabUrl == $scope.currentTab;
        }
    }]);
    // for wp_lightbox customizing.

}());
