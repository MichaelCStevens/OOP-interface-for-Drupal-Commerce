<?php

class MCGStore {

    // control properties
    public $strMode = 'basket';
    public $objModel;
    // display strings
    public $txtCartTitle = 'Your Basket';
    // button labels
    public $txtBtnCheckout = 'Finish and Checkout';
    public $txtBtnAdd = 'Add To Basket';
    // css classes
    public $txtCssClassAdd = 'success2 addItem';
    public $txtCssClassAddAlt = 'success2 addItemAddOn';
    public $txtCssClassCheckout = 'btn btn-danger';

    function __construct() {
        $this->objModel = new MCGStoreModel();
    }

    /*
     * Retrieves the physical path of the drupal folder from the model
     *
     * @return string containing the physical path for the drupal folder
     */

    public static function strPhysPath() {
        return MCGStoreModel::$strPhysPath;
    }

    /*
     * Retrieves the URL path of the store folder from the model
     *
     * @return string containing the URL path for the store folder
     */

    public static function strURLPath() {
        return MCGStoreModel::$strURLPath;
    }

    /*
     * Forwards AJAX requests to the AJAX handler method within the model
     */

    public function emptyCart() {
        $this->objModel->emptyCart();
    }

    public function ajaxRequest() {
        $boolAJAX = intval($_POST['ajax']);

        if ($boolAJAX == 1) {
            $this->objModel->processAjax($_POST);
        }
    }

}

?>