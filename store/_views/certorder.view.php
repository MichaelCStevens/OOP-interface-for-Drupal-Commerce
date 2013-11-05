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
 
<div id="order-form"> 
 Thank you for your order! 
 
 Your unique redemption code for this order is:<br/>
 <strong><?php echo $_GET['serial'] ?></strong><br/><br/>
 An email will be sent to your email address at <?php //echo $user->email; ?>with the Order Contents and redemption code.
 
 <br/>
 <br/>
 <a class="btn btn-danger" href="/online-store">Continue Shopping</a>
</div>
 
 
