<div class="basket " data-spy="affix affix-bottom" data-offset-top="50">
    <h3><?php echo $objStore->txtCartTitle ?> <span class="cart-min" style="">Minimize</span></h3> 
    <div class="basket-msg"><?php
$total = 0;
$totalW = 0;
//if cart exists in session, fill in the info 

if (isset($_SESSION['updateCart'])) {
    $count = count($_SESSION['updateCart']);
    if ($count > 0 && $objStore->strMode == 'basket') {
        $total = 0;
        $totalW = 0;
       ?> 
        <script>
            jQuery(document).ready(function() {
        <?php
        foreach ($_SESSION['updateCart'] as $item) {
            ?>   
                                MCG.fn.addBasket(<?php echo $item->pid ?>,"<?php echo $item->title ?>","<?php echo $item->variation ?>",<?php echo $item->price ?>,<?php echo $item->weight ?>,<?php echo $item->addon ?>,<?php echo $item->quantity ?>, 1 );
            <?php
        }
        ?>    
                    });
                </script>
                <?php
            }
        } else {
            ?><h4 class="no-items">Basket is Empty</h4><p>You have no items in your basket. Use the <strong><?php echo $objStore->txtBtnAdd ?></strong> buttons on this page to build your order.</p><?php } ?></div>
    <div class="basket-total"> Total $<span class="total"><?php
        if ($total > 0) {
            setlocale(LC_MONETARY, 'en_US');
            echo money_format('%i', $total);
        } else {
            ?>0.00<?php } ?></span></div>
    <label>Create order as Gift Certificate <input type="checkbox" name="makeGC" value="1"/></label> 
    <a class="<?php echo $objStore->txtCssClassCheckout ?>" onClick="MCG.fn.checkout()"><?php echo $objStore->txtBtnCheckout ?> </a>
</div> 
<script>
    jQuery(document).ready(function() {
        jQuery('#order-form .basket .dev-info').bind('click', function() {
            jQuery(this).toggleClass('open');
        });
    });
</script>
 