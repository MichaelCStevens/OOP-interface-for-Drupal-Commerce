<link href="<?php echo $objStore->strURLPath(); ?>/_assets/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
<link href="<?php echo $objStore->strURLPath(); ?>/_assets/css/style.css" type="text/css" rel="stylesheet"/>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script>jQuery.noConflict();</script>
<script src="<?php echo $objStore->strURLPath(); ?>/_assets/bootstrap/js/bootstrap.js"></script>
<script src="<?php echo $objStore->strURLPath(); ?>/_assets/js/accounting.min.js"></script>
<script src="<?php echo $objStore->strURLPath(); ?>/_assets/js/script.js"></script>
<script>
    MCG.p.cart.mode = '<?php echo $objStore->strMode; ?>';
        
    if(window.location.hash) {
        // console.log(window.location.hash);
        if(window.location.hash=='#cert') {
        
            MCG.fn.compileCertificate();
        }
    }
</script>
<?php
if ($objStore->strMode == 'builder') {
    if (MCGStoreModel::checkLoggedIn() == false) {
        $message = 'You must be logged in to create a certificate';
        drupal_set_message($message, $type = 'status');
        header('Location: /index.php?q=user');
    }
    require_once 'checkout.view.php';
}
?> 
<div id="order-form"> 
    <?php
    if (isset($_GET['chargeFailed'])) {
        echo"<h1>Your order failed to charge! Please try again<br/><br/> <a class='btn btn-danger btn-large' href='/certificate-builder'>Back to certificate builder</a></h1>";
    } else {
        ?>
        Use the Certificate Builder below to create a custom certificate. When you are finished, click Add Certificate to Cart<br/><br/>
        <?php
        if ($objStore->strMode == 'basket') {
            require_once 'redeem.view.php';
        }
        if ($objStore->strMode == 'basket') {
            require_once 'giftcards.view.php';
        }

        //require_once 'basket.view.php';
        require_once 'mainproducts.view.php';
        require_once 'productaddons.view.php';

        if ($objStore->strMode == 'basket') {
            require_once 'giftcards.view.php';
        }
    }
    ?>
</div>
<script type="text/javascript">
    MCG.p.cart.strings.titleBasket		= '<?php echo $objStore->txtCartTitle; ?>';
    MCG.p.cart.strings.titleAdd			= '<?php echo $objStore->txtBtnAdd; ?>';
    MCG.p.cart.strings.titleCheckout	= '<?php echo $objStore->txtBtnCheckout; ?>';
    MCG.p.cart.strings.cssCheckout		= '<?php echo $objStore->txtCssClassCheckout; ?>';
    function addBooks(){
        var html1='';
        var html2='';
  
<?php
if (isset($boolCertificateBuilder)) {
    $addressbook = MCGStoreModel::getAddressbooks();

    // foreach ($addBook as $add) {
    //    echo "<option attr-name='" . $add['full_name'] . "'  attr-address='" . $add['address'] . "'  attr-city='" . $add['locality'] . "'  attr-state='" . $add['admin_area'] . "'  attr-zip='" . $add['zip'] . "'  attr-phone='" . $add['phone'] . "'  value='" . $add['id'] . "'>" . $add['full_name'] . " " . $add['address'] . " " . $add['locality'] . " " . $add['admin_area'] . " " . $add['zip'] . "</option>";
    //}
    echo "html1=\"<select name='addbook[]' class='billingbook'><option value='0'>Enter Manually</option>\";";
    foreach ($addressbook[0]as$billingbook) {
        echo "html1+=\"<option value='" . $billingbook['id'] . "' first='" . $billingbook['billing_first'] . "' last='" . $billingbook['billing_last'] . "' address='" . $billingbook['billing_address'] . "' locality='" . $billingbook['billing_locality'] . "' adminarea='" . $billingbook['billing_admin_area'] . "' zip='" . $billingbook['billing_zip'] . "' email='" . $billingbook['billing_email'] . "'> " . $billingbook['billing_full_name'] . " " . $billingbook['billing_company'] . "" . $billingbook['billing_address'] . " " . $billingbook['billing_locality'] . " " . $billingbook['billing_admin_area'] . " " . $billingbook['billing_zip'] . "</option>\";";
    }
    echo"html1+=\"</select>\";";


    echo "html2=\"<select name='addbook[]' class='shippingbook'><option value='0'>Enter Manually</option>\";";
    foreach ($addressbook[1]as$shippingbook) {
        echo "html2+=\"<option value='" . $shippingbook['id'] . "' first='" . $shippingbook['billing_first'] . "' last='" . $shippingbook['billing_last'] . "' address='" . $shippingbook['billing_address'] . "' locality='" . $shippingbook['billing_locality'] . "' adminarea='" . $shippingbook['billing_admin_area'] . "' zip='" . $shippingbook['billing_zip'] . "' email='" . $shippingbook['billing_email'] . "'> " . $shippingbook['billing_full_name'] . " " . $shippingbook['billing_company'] . "" . $shippingbook['billing_address'] . " " . $shippingbook['billing_locality'] . " " . $shippingbook['billing_admin_area'] . " " . $shippingbook['billing_zip'] . "'</option>\";";
    }
    echo"html2+=\"</select>\";";
    // echo  $addressbook[2];
    ?>
<?php } ?>
    
        jQuery('.redemption-address .addbook, .billing-address .addbook').prepend(html1);
        jQuery('.shipping-address .addbook').prepend(html2);
    }
    
    jQuery(document).ready(function() {
 
<?php
if ((isset($_SESSION['updateCart'])) && (is_array($_SESSION['updateCart']))) {
    if ((count($_SESSION['updateCart'])) > 0 && ($objStore->strMode == 'basket')) {
        foreach ($_SESSION['updateCart'] as $objItem) {
            ?>
                                MCG.fn.addBasket(<?php echo $objItem->pid ?>, "<?php echo trim($objItem->title) ?>", "<?php echo $objItem->variation ?>", <?php echo $objItem->price ?>, <?php echo $objItem->weight ?>, <?php echo $objItem->addon ?>, <?php echo $objItem->quantity ?>, 1, false);
            <?php
        }
    }
}
?>
		
        MCG.fn.updateBasket(0);
    });
</script>

