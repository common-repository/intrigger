<div id="intrigger_float" style="">
    <div id="float_bar_wrap" class="int_indget_float_collect_default {{float_bar_pos}}" data-pos="{{pos}}" style="padding: 5px 0px; background-color: {{background_color}}; {{pos}}: -80px;">
        <div class="int_float_bar_content_warp">
            <a href="{{branding_url}}" class="int_indget_float_brand" target="_blank" style="display: {{show_brand}}">
                <div class="logo_image" title="Powered by InTrigger" style="background-image: url({{branding_image_url}})"></div>
            </a>
            <div class="int_indget_float_collect_default_title" style="font-size: {{headline_fontsize}}px; color: {{headline_font_color}}; ">
                {{headline}}
            </div>
            <div class="int_indget_float_collect_default_content">
                <input type="email" class="itrr_collect_email" id="itrr_collect_email_{{indget_id}}_{{post_id}}" placeholder="{{input_preview}}">
                <a href="javascript:void(0);" itrr-btn="float_collect" itrr-indget-id="{{indget_id}}" itrr-post-id="{{post_id}}" itrr_scenario_id="{{scenario_id}}" target="_blank" style="color:{{button_font_color}}; background-color: {{button_background_color}};">{{button_label}}</a>
            </div>
        </div>
        <div class="clearfix"></div>
        <span id="{{float_close_pos}}" class="preview_hidden">
                {{float_bar_close}}
            </span>
        <span id="{{float_open_pos}}" class="preview_hidden" style="background-color: {{background_color}};">
            {{float_bar_open}}
        </span>
    </div>
</div>
