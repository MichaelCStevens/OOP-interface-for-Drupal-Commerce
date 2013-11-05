<link href="<?php echo $objStore->strURLPath(); ?>/_assets/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
<link href="<?php echo $objStore->strURLPath(); ?>/_assets/css/style.css" type="text/css" rel="stylesheet"/>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script>jQuery.noConflict();</script>
<script src="<?php echo $objStore->strURLPath(); ?>/_assets/bootstrap/js/bootstrap.js"></script>
<script src="<?php echo $objStore->strURLPath(); ?>/_assets/js/accounting.min.js"></script>
<script src="<?php echo $objStore->strURLPath(); ?>/_assets/js/script.js"></script>
 
<?php
 if(MCGStoreModel::confirmRedemption($_GET['redeemOrder'],$_POST)==true){
    ?> 
     <div id="order-form"> 
   Your order has been redeemed successfully!
</div>
     <?php
 }else{
     ?>
This certificate has been redeemed already. Please check your order history.
     <?php 
     
 }
?> 

