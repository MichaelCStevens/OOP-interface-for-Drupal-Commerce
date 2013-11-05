<h3>Meat Selections</h3> 
<?php
$c = 0;
$cc = 0;
foreach ($objStore->objModel->getProducts() as $product) {

    $c++;
    $cc++;
    $subs = $objStore->objModel->getSubProducts($product['entity_id']);
    $formattedTitle = explode('(', $product['title']);
    $formattedTitle = $formattedTitle[0];
    //crapy hacks thanks for changing spec two mins before launch, client
    $stringVar = $formattedTitle;
    if($product[3] == 40){
        $marketPhrase = 'The First Choice for Kansas City';
        $altPhrase = 'The First Choice for Kansas City';
        $titlePhrase = 'The First Choice for Kansas City';
        $productTitle = 'Kansas City Strip Steaks';
    }
    elseif($product[3] == 42){
        $marketPhrase = 'The Tender Best!';
        $altPhrase = 'The Tender Best!';
        $titlePhrase = 'The Tender Best!';
        $productTitle = 'Filet Mignon';
    }
    elseif($product[3] == 43){
        $marketPhrase = 'Choose Racks, Chops or Roasts';
        $altPhrase = 'Choose Racks, Chops or Roasts';
        $titlePhrase = 'Choose Racks, Chops or Roasts';
        $productTitle = 'Lamb';
    }
    elseif($product[3] == 46){
        $marketPhrase = 'Combo Packs of your favorite Steaks';
        $altPhrase = 'Combo Packs of your favorite Steaks';
        $titlePhrase = 'Combo Packs of your favorite Steaks';
        $productTitle = 'Mix and Match';
    }
    elseif($product[3] == 49){
        $marketPhrase = 'Best of Both Worlds';
        $altPhrase = 'Best of Both Worlds';
        $titlePhrase = 'Best of Both Worlds';
        $productTitle = 'Porterhouse';
    }
    elseif($product[3] == 60){
        $marketPhrase = "The Butcher's Choice";
        $altPhrase = "The Butcher's Choice";
        $titlePhrase = "The Butcher's Choice";
        $productTitle = 'Ribeye';
    }
    elseif($product[3] == 69){
        $marketPhrase = 'Choose Chops or Racks';
        $altPhrase = 'Choose Chops or Racks';
        $titlePhrase = 'Choose Chops or Racks';
        $productTitle = 'Berkshire Pork';
    }
    elseif($product[3] == 70){
        $marketPhrase = 'Roast with the Most!';
        $altPhrase = 'Roast with the Most!';
        $titlePhrase = 'Roast with the Most!';
        $productTitle = 'Roasts';
    }
    elseif($product[3] == 71){
        $marketPhrase = 'Classic Goodness!';
        $altPhrase = 'Classic Goodness!';
        $titlePhrase = 'Classic Goodness!';
        $productTitle = 'T-Bone Steaks';
    }
    else{
        $marketPhrase = '';
        $altPhrase = '';
        $titlePhrase = '';
        $productTitle = $formattedTitle;
    }
    
    ?>
    
    
    
    <?php if ($c == 1) { ?> <div class="product-list row-fluid"> <?php } ?>
    
    
    
        <div class="product span6">
            <div class="row-fluid">
                <div class="img span4"><img alt="<?php echo $altPhrase; ?>" title="<?php echo $titlePhrase; ?>" src="/sites/default/files/<?php print($product['filename']); ?>"/></div>
                <div class="p-info span8">
                    <h2>
                    
                    
                    <?php echo $productTitle; ?>
                    
                    
                    </h2>
                    <span style="display: block;margin-top: 12px;font-style: italic;"><?php echo $marketPhrase; ?></span>
                    <select class="selected-item" attr-parent-id="<?php echo $product['entity_id']?>">
                        <?php
                        if (count($subs) > 0) {
                            foreach ($subs as $sub) {
                                //format price
                                $dblWeight = floatval($sub['field_base_weight_weight']);
                                $dblPrice = floatval(substr_replace($sub['commerce_price_amount'], '.', -2, 2) . substr($sub['commerce_price_amount'], -2, 2));
                                $dblDisplayPrice = number_format($dblPrice + intval($objStore->objModel->getCoolerCost($dblWeight)), 2, '.', ',');
//                                print_r( dblWeight);
//                                print_r(intval($objStore->objModel->getCoolerCost($dblWeight)));
                                if($formattedTitle!='Mix and Match'){
                                ?>
                                <option value="<?php echo $sub['product_id']; ?>" attr-variation="<?php echo $sub['sku']; ?>"  attr-weight="<?php echo $dblWeight; ?>" attr-price="<?php echo $dblPrice; ?>" ><?php echo $sub['title']; ?> - $<?php echo $dblDisplayPrice; ?></option>
                                <?php
                            }else{
                                ?>
                                <option value="<?php echo $sub['product_id']; ?>" attr-variation="<?php echo $sub['sku']; ?>"  attr-weight="<?php echo $dblWeight; ?>" attr-price="<?php echo $dblPrice; ?>" >$<?php echo $dblDisplayPrice; ?></option>
                                <?php
                            }}
                        }
                        ?>
                    </select>
                    <a class="<?php echo $objStore->txtCssClassAdd; ?>" href="javascript:void(0);" ><?php echo $objStore->txtBtnAdd ?></a>
                </div>
            </div>
        </div>
        <?php if ($c == 2) { ?> </div> <?php $c = 0;
    } ?>
<?php } ?>