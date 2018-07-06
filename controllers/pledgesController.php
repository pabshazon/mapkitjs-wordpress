<?php
/**
 * Created by IntelliJ IDEA.
 * User: pablo
 * Date: 7/2/18
 * Time: 5:32 PM
 */
defined ('ABSPATH') or die('Permission denied');


include(PLEDGES_PATH . 'models/pledgeModel.php');
include(PLEDGES_PATH . 'models/mapkitJsModel.php');
include(PLEDGES_PATH . 'models/3djsVizModel.php');
include(PLEDGES_PATH . 'controllers/helpers/assetsLoader.php');

class PledgesController {

    private $assetsLoader;

    /**
     * Constructor and Hooks initialization - this section acts as the action dispatcher.
     * This is how we route/trigger the different actions into the controller methods because of WP.
     * @todo: move this section into an ActionDispatcher or ActionRouter class to manage multiple Controllers.
     */
    public function __construct(){
        $this->loadWpActions();
        $this->loadFilters();
        $this->loadShortCodes();
        $this->assetsLoaderHelper = new AssetsLoader();
    }

    private function loadShortCodes(){
        //Shortcodes used in the pages to show the pledges input form and visualizations
        add_shortcode('thehumsum_pledges', array($this, 'generatePledgesInputFormAction'));
        add_shortcode('thehumsum_pledges_visualization', array($this, 'generatePledgesVisualizationAction'));

    }

    private function loadWpActions(){
        //Pledges form submitted by logged out and in users
        //wp-note: the form needs the html form action pointing to admin-post.php and a hidden form field called 'action'
        //with 'pledge_form' value to use as router. Yes, it sucks, they use the word action for too many things with
        //different semantics.
        add_action( 'admin_post_nopriv_pledge_form', array($this, 'submitPledgesFormAction'));
        add_action( 'admin_post_pledge_form', array($this, 'submitPledgesFormAction'));

    }

    private function loadFilters(){
        //Now wp filters needed yet - needed if we want to modify an existing plugin variable value.

    }



    /**
     * Actions - this section contains the Controllers Actions themselves.
     */
    public function generatePledgesInputFormAction($atts = [], $content = null, $tag = ''){
        //load css and js assets
        $this->assetsLoaderHelper->loadCssBootstrapCustom();
        $this->assetsLoaderHelper->loadCssBootstrapFormHelper();
        $this->assetsLoaderHelper->loadJsPledgesForm();
        //render view
        include(PLEDGES_PATH . 'views/pledgesInputView.php');
        showPledgesInputView(admin_url( 'admin-post.php' ));

    }

    public function generatePledgesVisualizationAction($atts = [], $content = null, $tag = ''){
        //retrieve data and reformat for mapkitjs needs
        $pledgeModel = new PledgeModel();
        $pledgesData = $pledgeModel->getAllData();
        $mapModel = new MapKitJsModel($pledgesData);
        $mapAnnotationData = $mapModel->getAnnotationData();

        //retrieve data and reformat for 3djsViz needs
        $threeDjsModel = new ThreeDJsModel($pledgesData);
        $vizData = $threeDjsModel->getVisualizationData();

        //load css and js assets, sending the mapkitjs data needs to the js
        $this->assetsLoaderHelper->loadCssBootstrapCustom();
        $this->assetsLoaderHelper->loadJsMapkit($mapAnnotationData, $atts['type']);
        $this->assetsLoaderHelper->loadJsD3js($vizData,  $atts['type']);

        //render view
        $savedMessage = isset($_GET['saved']);
        include(PLEDGES_PATH . 'views/pledgesVisualizationView.php');
        showPledgesVisualizationView($savedMessage);

    }

    public function submitPledgesFormAction(){
        $pledgeModel = new PledgeModel($_POST);
        $pledgeModel->validate();
        $result = $pledgeModel->save();
        if($result != 1){
            //@todo improve the error handling and messaging, no need to kill the application here
            //@todo think and implement a logs strategy
            write_log('ERROR from mysql - Pledges model - saving a new pledge');
            write_log(print_r($result, true));
            write_log(print_r($_POST,true));
            die('There was an error saving the pledge in the database.');

        }

        wp_redirect(esc_url(get_permalink(get_page_by_title(('pledges-stats')))).'?saved=true');
        exit();

    }

}
