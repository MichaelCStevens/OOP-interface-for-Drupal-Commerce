<div class="product-addons row-fluid">
    <div class="span12"><br/>
        <h3>Select an Add On</h3> 
        Limit of one add on per order

        <?php
        $c = 0;
        $cc = 0;
        foreach ($objStore->objModel->getAddonProducts() as $product) {
            $c++;
            $cc++;
            //print_r($product);
            $subs = $objStore->objModel->getSubProducts($product['entity_id']);
            // print_r($subs);
            $formattedTitle = explode('(', $product['title']);
            $formattedTitle = $formattedTitle[0];
            ?>
            <?php if ($c == 1) { ?> <div class="product-list row-fluid"> <?php } ?>
                <div class="product span6" attr-pid="<?php print($product['entity_id']); ?>">
                    <div class="row-fluid">

                        <div class="img span4"><img src="/sites/default/files/<?php print($product['filename']); ?>"/></div>
                        <div class="p-info span8">
                            <h2><?php print($formattedTitle); ?></h2>
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
                            <a class="<?php echo $objStore->txtCssClassAddAlt; ?>" href="javascript:void(0);" ><?php echo $objStore->txtBtnAdd ?></a>
                        </div>
                    </div>
                </div>
                <?php if ($c == 2) { ?> </div> <?php $c = 0;
        } ?>
        <?php }
        ?>


    </div>
</div>