<?php

ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once '_classes/db.class.php';
require_once '_classes/mcg-store-model.class.php';
require_once '_classes/mcg-store.class.php';

$objStore = new MCGStore();

if (isset($boolCertificateBuilder)) {
    $objStore->strMode = 'builder';
    $objStore->txtCartTitle = 'Certificate';
    $objStore->txtBtnAdd = 'Add To Certificate';
    $objStore->txtBtnCheckout = 'Checkout';
    $objStore->txtCssClassAdd = 'btn btn-success addItem';
    $objStore->txtCssClassAddAlt = 'btn btn-success addItemAddOn';
    $objStore->txtCssClassCheckout = 'btn btn-danger addCert';

    //echo "<style>#order-form .basket{margin-top:-117px!important}</style>";
}
if (isset($_GET['cancel'])) {
    $objStore->emptyCart();
}
if (isset($_GET['submitCertOrder'])) {
    MCGStoreModel::submitOrder($_POST);
}
if (isset($_POST['ajax'])) {
    $objStore->ajaxRequest();
} else {
    if (isset($_GET['orderCompleted'])) {
        require_once '_views/certorder.view.php';
    } elseif (isset($_GET['redeemOrder'])) {
        if (isset($_GET['confirm'])) {

            require_once '_views/confirmorder.view.php';
        } else {
            require_once '_views/redeemorder.view.php';
        }
    } else {
        require_once '_views/store.view.php';
    }
}
?>