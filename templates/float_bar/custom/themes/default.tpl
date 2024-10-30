<div id="intrigger_float" style="">
    <div id="float_bar_wrap" class="int_indget_float_custom_default {{float_bar_pos}}" data-pos="{{pos}}" style="padding: 5px 0px;">
        <div class="int_indget_float_custom_default_content">
            {{intrigger_float_custom}}
        </div>
        <div class="clearfix"></div>
        <span id="{{float_close_pos}}" class="preview_hidden">
                {{float_bar_close}}
            </span>
        <span id="{{float_open_pos}}" class="preview_hidden" style="background-color: rgb(235, 89, 60);">
            {{float_bar_open}}
        </span>
    </div>
</div>
<style>
    #float_bar_wrap{
        display:none;
    }
    .int_indget_float_custom_default {

        display: block;
        width: 100%;
        background-color: rgb(235, 89, 60);

        box-shadow: 0 0 5px rgba(0,0,0,0.35);
        -moz-box-shadow: 0 0 5px rgba(0,0,0,0.35);
        -webkit-box-shadow: 0 0 5px rgba(0,0,0,0.35);
        -ms-filter: \"progid:DXImageTransform.Microsoft.Shadow(Color=#e5e5e5,direction=120,strength=3)\";
        filter: progid:DXImageTransform.Microsoft.Shadow(Color=#e5e5e5,direction=120,strength=3);
    }
    .int_indget_float_top {
        top: 0px;
        border-bottom: 2px solid #ffffff;
        position: relative;
        position: fixed;
        z-index: 3000;
    }
    .int_indget_float_bottom {
        bottom: 0px;
        border-top: 2px solid #ffffff;
        position: absolute;
        position: fixed;
        z-index: 3000;
    }

    .int_indget_float_custom_default_title {
        font-weight: bold;
        text-align: right;
        width: 70%;
        display: inline-block;
        padding: 8px;
    }
    .int_indget_float_custom_default_content {
        box-sizing: border-box;
        text-align: center;
        color: #fff;
        /*display: inline-block;*/
    }
    .int_indget_float_custom_default_content a {
        text-decoration: none;
        border-style: none;
        font-size: 14px;
        padding: 3px 20px;
        border-radius: 5px;
        color: #fff;
        /*display: inline-block;*/
    }
    #int_indget_float_open_top {

        overflow: hidden;
        position: absolute;
        right: 10px;
        top: -96px;
        z-index: 100;
        box-shadow: 0 0 5px rgba(0,0,0,0.35);
        -moz-box-shadow: 0 0 5px rgba(0,0,0,0.35);
        -webkit-box-shadow: 0 0 5px rgba(0,0,0,0.35);
        -ms-filter: \"progid:DXImageTransform.Microsoft.Shadow(Color=#e5e5e5,direction=120,strength=3)\";
        filter: progid:DXImageTransform.Microsoft.Shadow(Color=#e5e5e5,direction=120,strength=3);
        -webkit-border-bottom-right-radius: 5px;
        -webkit-border-bottom-left-radius: 5px;
        -moz-border-radius-bottomright: 5px;
        -moz-border-radius-bottomleft: 5px;
        border-bottom-right-radius: 5px;
        border-bottom-left-radius: 5px;
        background-color: #eb593c;
        border-color: #fff;
        border-style: solid;
        border-width: 2px;
        padding: 20px 10px 5px 10px;
        cursor: pointer;

    }
    #int_indget_float_close_top {
        display: block;
        overflow: hidden;
        position: absolute;
        right: 20px;
        top: 4px;
        z-index: 10;
        border: none;
        padding: 0px;
        cursor: pointer;
    }
    #int_indget_float_close_bottom {

        display: block;
        overflow: hidden;
        position: absolute;
        right: 20px;
        top: 4px;
        z-index: 10;
        border: none;
        padding: 0px;
        cursor: pointer;
    }
    #int_indget_float_open_bottom {
        overflow: hidden;
        position: absolute;
        right: 10px;
        bottom: -96px;
        z-index: 100;
        box-shadow: 0 0 5px rgba(0,0,0,0.35);
        -moz-box-shadow: 0 0 5px rgba(0,0,0,0.35);
        -webkit-box-shadow: 0 0 5px rgba(0,0,0,0.35);
        -ms-filter: \"progid:DXImageTransform.Microsoft.Shadow(Color=#e5e5e5,direction=120,strength=3)\";
        filter: progid:DXImageTransform.Microsoft.Shadow(Color=#e5e5e5,direction=120,strength=3);
        -webkit-border-bottom-right-radius: 5px;
        -webkit-border-bottom-left-radius: 5px;
        -moz-border-radius-bottomright: 5px;
        -moz-border-radius-bottomleft: 5px;
        border-radius: 5px;
        background-color: #eb593c;
        border-color: #fff;
        border-style: solid;
        border-width: 3px;
        padding: 5px 10px 20px 10px;
        cursor: pointer;
    }

    @media screen and (max-width: 768px) {
        .int_indget_float_custom_default_content{

            width: 100% !important;
            margin-bottom: 10px;

        }
        .int_indget_float_custom_default_title{

            width: 100% !important;
            margin-bottom: 10px;
            text-align: center;

        }
    }
    @media screen and (max-width: 480px) {

        .int_indget_float_custom_default_title{

            width: 100% !important;
            margin-bottom: 10px;
            padding: 0px 40px 0px 10px;
            text-align: center;
        }
    }
    body{
        margin: 0;
    }
</style>
<script>
</script>