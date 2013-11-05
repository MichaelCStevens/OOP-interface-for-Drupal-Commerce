<div class="row-fluid">
    <div class="span12"><br/>
        <h3>Gift Cards And Steak Certificates</h3> 

        <?php
        $c = 0;
        $cc = 0;
        foreach ($objStore->objModel->getGiftProducts() as $product) {

            $c++;
            $cc++;
            $subs = $objStore->objModel->getSubProducts($product['entity_id']);
            $formattedTitle = explode('(', $product['title']);
            $formattedTitle = $formattedTitle[0];
            ?>
            <?php if ($c == 1) { ?> <div class="product-list row-fluid"> <?php } ?>
                <div class="product span6">
                    <div class="row-fluid">

                        <div class="img span4"><img src="/sites/default/files/<?php print($product['filename']); ?>"/></div>
                        <div class="p-info span8">
                            <h2><?php print($formattedTitle); ?></h2>
                            <span style="display: block;margin-top: 12px;font-style: italic;">Dollar value cards to be used in the store</span>
                            <select class="selected-item">
                                <?php
                                if (count($subs) > 0) {
                                    foreach ($subs as $sub) {
                                        //format price
                                        $l2d = substr($sub['commerce_price_amount'], -2, 2);
                                        $price = substr_replace($sub['commerce_price_amount'], '.', -2, 2) . $l2d;
                                        ?>
                                        <option value="<?php echo $sub['product_id']; ?>" attr-variation="<?php echo $sub['sku']; ?>"  attr-weight="<?php echo $sub['field_base_weight_weight']; ?>" attr-price="<?php echo $price; ?>" ><?php echo $sub['title']; ?> - $<?php echo $price; ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                            <a class="success2 addItem" href="javascript:void(0);" onclick="MCG.fn.setGiftCardGlobal()" >Add To Basket</a>
                        </div>
                    </div>
                </div>
                <?php if ($c == 2) { ?> </div> <?php $c = 0;
        } ?>
        <?php } ?>
        <div class="product span6">
            <div class="row-fluid">

                <div class="img span4"><img src="/sites/default/files/Tulips.jpg"/></div>
                <div class="p-info span8">
                    <h2>Steak Certificates</h2>
                    <span style="display: block;margin-top: 12px;font-style: italic;">Choose specific steak package to be redeemed later</span>
                    <div style="height:40px;overflow: auto;display: none">

                    </div>
                    <a href="/certificate-builder" class="btn btn-danger certBuilder">Build a Gift Certificate</a>
                </div>
            </div>
        </div>
    </div>
</div></div>