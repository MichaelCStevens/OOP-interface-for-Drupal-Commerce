<link href="<?php echo $objStore->strURLPath(); ?>/_assets/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
<link href="<?php echo $objStore->strURLPath(); ?>/_assets/css/style.css" type="text/css" rel="stylesheet"/>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script> 
<script>jQuery.noConflict();</script>
<script src="<?php echo $objStore->strURLPath(); ?>/_assets/bootstrap/js/bootstrap.js"></script>
<script src="<?php echo $objStore->strURLPath(); ?>/_assets/js/accounting.min.js"></script>
<script src="<?php echo $objStore->strURLPath(); ?>/_assets/js/script.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/jquery-ui.min.js"></script>
<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/themes/redmond/jquery-ui.css"/>
<?php
$sql = "SELECT * FROM blackout_dates WHERE `type`='0'";
// ORDER BY b.commerce_customer_address_address";
$resultA = db_query("$sql");
$days = array();
foreach ($resultA as $result) {
    // print_r($result);
    $days[$result->date] = $result->date;
}
?>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script>
    function reedemOrder(){
        var x=0;
        jQuery('input').css({'border':'solid 1px #000'});
        if(jQuery('#targetdate').val()==''){
            jQuery('#targetdate').css({'border':'solid 1px red'});
            alert('Please Enter a Target Shipping Date'); 
            return false;
        }
        jQuery('#redeemform input').each(function() {
            if($(this).val()==''){
                x=1;
            $(this).css({'border':'solid 1px red'});
        }
        });
      
        if(x=='1'){
            alert('Please Fill out all of the address fields');
            return false;
           
        }
        jQuery('#redeemform').submit();
       
    }
    jQuery( document ).ready(function( $ ) {
        setDate();
    });
    
   
    function setDate(){
     
        var today = new Date();
        var dd = today.getDate();
        var datebox=jQuery("#targetdate");
        datebox.datepicker({
            beforeShowDay: disableSpecificWeekDays
        });
        datebox.datepicker( "option", "dateFormat", 'yy-mm-dd' );
        //    jQuery("#edit-customer-profile-shipping-field-target-ship-date-und-0-value-date").datepicker();

        datebox.datepicker("option", "minDate", today );
    }
    
    function disableSpecificWeekDays(date) {
        var daysToDisable = [<?php
if (!isset($days['Monday'])) {
    echo'1,';
}
?><?php
if (!isset($days['Tuesday'])) {
    echo'2,';
}
?><?php
if (!isset($days['Wednesday'])) {
    echo'3,';
}
?> <?php
if (!isset($days['Thursday'])) {
    echo'4,';
}
?> <?php
if (!isset($days['Friday'])) {
    echo'5,';
}
?> <?php
if (!isset($days['Saturday'])) {
    echo'6,';
}
?> <?php
if (!isset($days['Sunday'])) {
    echo'0,';
}
?>];
            var day = date.getDay();
            for (i = 0; i < daysToDisable.length; i++) {
                if ($.inArray(day, daysToDisable) != -1) {
                    return [false];
                }
            }
            return [true];
        }
        
        function trim1 (str) {
            return str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
  
        }
 
        MCG.p.cart.mode = '<?php echo $objStore->strMode; ?>';
        
        if(window.location.hash) {
            // console.log(window.location.hash);
            if(window.location.hash=='#cert') {
        
                MCG.fn.compileCertificate();
            }
        }
    
</script>
<?php
$orderInfo = MCGStoreModel::getRdemptionOrder($_GET['redeemOrder']);
//print_r($orderInfo);
if($orderInfo!=false){
foreach ($orderInfo[0] as $oi) {
    //print_r($oi);
    $state = $oi->commerce_customer_address_administrative_area;
    $address = $oi->commerce_customer_address_thoroughfare;
    $city = $oi->commerce_customer_address_locality;
    $zip = $oi->commerce_customer_address_postal_code;
}
?> 
<form id="redeemform" method="post" action="/certificate-builder?redeemOrder=<?php echo $_GET['redeemOrder'] ?>&confirm=1">
    <div id="order-form" class="row-fluid">  
        <div class="span6">
            <h3>Please Confirm the address this order will be sent to:</h3>
            <br/>
            <br/>   <label>Address<br/> <input type="text" name="address" value="<?php echo $address ?>"/></label><br/>
            <label>City <br/> <input type="text" name="city" value="<?php echo $city ?>"/></label><br/>
            <label>State:<br/> <?php echo $state ?> (cannot be changed)</label><br/>
            <label>Zip <br/><input type="text" name="zip" value="<?php echo $zip ?>"/></label><br/>
            <br/><br/>
            <label>Target Shipping Date:<input class=" " type="text" id="targetdate" name="targetdate" value="" size="60" maxlength="128"/></label>

        </div> 
        <div class="span6">

            <h3>Order Summary:</h3>
            <table class="table table-bordered table-striped">
                <tr>
                    <th>Item</th>
                </tr>
                <?php
                $lineitems = $orderInfo[1];
                $lineitems = base64_decode($lineitems);
                $lineitems = json_decode($lineitems);

                foreach ($lineitems as $li) {
                    // print_r();
                    $title = MCGStoreModel::getProductInfo($li->commerce_product->und[0]->product_id);
                    ?>
                    <tr>
                        <td><?php echo $title; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
    </div> <a class="btn btn-danger" href="javascript:void(0)" onclick="reedemOrder();">Redeem My Order</a>  
</form>
<?php }else{ ?>
Code Already redeemed.
<?php } ?>


