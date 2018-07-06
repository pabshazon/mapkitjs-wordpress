<?php

defined ('ABSPATH') or die('Permission denied');

class AssetsLoader {

    private $urlCss;
    private $urlScripts;

    public function __construct(){
        $this->urlCss = plugin_dir_url(PLEDGES_PATH . 'assets/css/bootstrap-custom-forms.css');
        $this->urlScripts = plugin_dir_url(PLEDGES_PATH . 'assets/js/thehumsum-3djs.js');
    }

    public function loadCssBootstrapCustom(){
        //@todo divide the css file for what is needed for the map and for the forms
        wp_register_style('thehumsum-pledges', $this->urlCss . 'bootstrap-custom-forms.css', false, 1.0 );
        wp_enqueue_style('thehumsum-pledges');

    }

    public function loadCssBootstrapFormHelper(){

        wp_register_style('thehumsum-bootstrap-formhelper', $this->urlCss . 'bootstrap-formhelper.css', false, 1.0 );
        wp_enqueue_style('thehumsum-bootstrap-formhelper');

    }

    public function loadJsMapkit($mapData, $mapType = null){
        wp_enqueue_script( 'apple-mapkitjs', 'https://cdn.apple-mapkit.com/mk/5.x.x/mapkit.js' );
        if($mapType == 'apple'){
            wp_register_script( 'thehumsum-mapkitjs', $this->urlScripts . 'thehumsum-mapkitjs-std.js', null, null, true);

        }else{
            wp_register_script( 'thehumsum-mapkitjs', $this->urlScripts . 'thehumsum-mapkitjs-std2.js', null, null, true);

        }
        wp_localize_script( 'thehumsum-mapkitjs', 'mapData', $mapData );
        wp_enqueue_script('thehumsum-mapkitjs');

    }

    public function loadJsD3js($vizData, $vizType = 'apple'){
        wp_enqueue_script( '3djs', 'https://d3js.org/d3.v3.min.js' );
        if($vizType == 'apple'){
            wp_register_script( 'thehumsum-3djs', $this->urlScripts . 'thehumsum-3djs.js', null, null, true);

        }else{
            wp_register_script( 'thehumsum-3djs', $this->urlScripts . 'thehumsum-3djs-vanilla.js', null, null, true);

        }

        wp_localize_script( 'thehumsum-3djs', 'mapData3d', $vizData );
        wp_enqueue_script('thehumsum-3djs');
    }

    public function loadJsPledgesForm(){
        wp_enqueue_script('thehumsum-bootstrap', $this->urlScripts . 'bootstrap-formhelper.js');
        wp_enqueue_script('thehumsum-jquery-validate', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.js' );

    }

}