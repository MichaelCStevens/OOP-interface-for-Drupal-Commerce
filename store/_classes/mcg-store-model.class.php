<?php

class MCGStoreModel {

    // properties
    public $id;
    public $intAddonCategoryID = 6;
    public $intGiftCategoryID = 8;
    public $intCoolerCategoryID = 9;
    public static $response;
    // paths
    public static $strPhysPath = '/path/to/root';
    public static $strURLPath = '/store';

    function __construct() {
        date_default_timezone_set('America/New_York');

        ini_set('display_errors', 'on');
        error_reporting(E_ALL);
    }

    function __destruct() {
        
    }

    public function checkLoggedIn() {
        global $user;

        if ($user->uid) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Returns all products that are not in one of the reserved product categories (add-ons, coolers, gift certificates)
     *
     * @return PDO result set, or false if no results found
     */

    public function getProducts() {
        $objQuery = DB::prepare("	SELECT		a.*, ab.*, d.filename, e.tid 
													FROM		field_data_field_product AS a 
													LEFT JOIN 	commerce_product AS ab ON a.field_product_product_id = ab.product_id 
													LEFT JOIN	field_data_field_images AS c ON a.field_product_product_id = c.entity_id
													LEFT JOIN	file_managed AS d ON c.field_images_fid = d.fid
													LEFT JOIN	taxonomy_index AS e ON a.entity_id = e.nid
													WHERE		e.tid NOT IN (:addon_category, :gift_category, :cooler_category) 
													GROUP BY	a.entity_id 
													ORDER BY	a.field_product_product_id ASC");
        $objQuery->bindParam(':addon_category', intval($this->intAddonCategoryID), PDO::PARAM_INT);
        $objQuery->bindParam(':gift_category', intval($this->intGiftCategoryID), PDO::PARAM_INT);
        $objQuery->bindParam(':cooler_category', intval($this->intCoolerCategoryID), PDO::PARAM_INT);
        $objQuery->execute();

        $intNumRows = $objQuery->rowCount();

        if ($intNumRows > 0) {
            return $objQuery->fetchAll();
        } else {
            return false;
        }
    }

    /*
     * Returns all variations of products (usually the various combinations of meat sizes (2-pack, 4-pack, etc).
     *
     * @param $intProductID		int		The parent product ID to retreive variations for
     *
     * @return PDO result set, or false if no results found
     */

    public function getSubProducts($intProductID) {
        $objQuery = DB::prepare("	SELECT		a.*, b.*, c.*, w.* 
													FROM		field_data_field_product AS a 
													LEFT JOIN	commerce_product AS b ON a.field_product_product_id = b.product_id 
													LEFT JOIN	field_data_field_base_weight AS w ON a.field_product_product_id = w.entity_id 
													LEFT JOIN	field_data_commerce_price AS c ON a.field_product_product_id = c.entity_id 
													WHERE		a.entity_id = :product");
        $objQuery->bindParam(':product', intval($intProductID), PDO::PARAM_INT);
        $objQuery->execute();

        $intNumRows = intval($objQuery->rowCount());

        if ($intNumRows > 0) {
            return $objQuery->fetchAll();
        } else {
            return false;
        }
    }

    /*
     * Returns all products within the gift certificate category
     *
     * @return PDO result set, or false if no results found
     */

    public function getGiftProducts() {
        $objQuery = DB::prepare("	SELECT 		a.*, ab.*, d.filename, e.tid FROM field_data_field_product AS a
													LEFT JOIN 	commerce_product AS ab ON a.field_product_product_id = ab.product_id 
													LEFT JOIN 	field_data_field_images AS c ON a.field_product_product_id = c.entity_id
													LEFT JOIN 	file_managed AS d ON c.field_images_fid = d.fid
													LEFT JOIN 	taxonomy_index AS e ON a.entity_id = e.nid
													WHERE		e.tid = :gift_category 
													GROUP BY	a.entity_id");
        $objQuery->bindParam(':gift_category', intval($this->intGiftCategoryID), PDO::PARAM_INT);
        $objQuery->execute();

        $intNumRows = intval($objQuery->rowCount());

        if ($intNumRows > 0) {
            return $objQuery->fetchAll();
        } else {
            return false;
        }
    }

    /*
     * Returns all products within the add-ons category, all of which can be purchased only after a regular product has been added to the cart
     *
     * @return PDO result set, or false if no results found
     */

    public function getAddonProducts() {
        $objQuery = DB::prepare("	SELECT		a.*, ab.*, d.filename, e.tid FROM field_data_field_product AS a 
													LEFT JOIN	commerce_product AS ab ON a.field_product_product_id = ab.product_id 
													LEFT JOIN	field_data_field_images AS c ON a.field_product_product_id = c.entity_id
													LEFT JOIN	file_managed AS d ON c.field_images_fid = d.fid
													LEFT JOIN	taxonomy_index AS e ON a.entity_id = e.nid
													WHERE		e.tid = :addon_category 
													GROUP BY	a.entity_id");
        $objQuery->bindParam(':addon_category', intval($this->intAddonCategoryID), PDO::PARAM_INT);
        $objQuery->execute();

        $intNumRows = $objQuery->rowCount();

        if ($intNumRows > 0) {
            return $objQuery->fetchAll();
        } else {
            return false;
        }
    }

    public static function getAddressbooks() {
        global $user;

        $strCurDir = getcwd();
        $strDrupalDir = self::$strPhysPath;

        // change current folder to drupal's folder
        chdir($strDrupalDir);

        // bootstrap drupal
        define('DRUPAL_ROOT', self::$strPhysPath);

        require_once './includes/errors.inc';
        require_once './includes/bootstrap.inc';

        // set the session cookie domain
        $cookie_domain = $_SERVER['HTTP_HOST'];
        drupal_bootstrap(DRUPAL_BOOTSTRAP_SESSION);
        drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

        // switch back to our working folder
        chdir($strCurDir);

        // start session
        drupal_session_start();
//        $resultA = db_query("SELECT a.profile_id,  b.* FROM `commerce_customer_profile` AS a
//                     LEFT JOIN `field_data_commerce_customer_address` AS b on a.profile_id=b.entity_id
//                     WHERE a.status='1' AND a.uid = '$user->uid' AND a.type='shipping' AND b.commerce_customer_address_thoroughfare !=''");



        $orderBilling = db_query("SELECT *,  f.field_phone_number_value as phone, x.profile_id as bpid FROM `field_data_commerce_customer_billing` as a
                                  LEFT JOIN  field_data_commerce_customer_address as b on a.commerce_customer_billing_profile_id=b.entity_id
                                   LEFT JOIN field_data_field_phone_number as f on a.commerce_customer_billing_profile_id=f.entity_id
                                   LEFT JOIN `commerce_customer_profile` AS  x on  a.commerce_customer_billing_profile_id=x.profile_id
                                WHERE x.status='1' AND x.uid = '$user->uid' GROUP BY x.profile_id");

        $addBook = array();
        $c = 0;
        foreach ($orderBilling as $ob) {
            $addBook[$c]['billing_first'] = $ob->commerce_customer_address_first_name;
            $addBook[$c]['billing_last'] = $ob->commerce_customer_address_last_name;
            $addBook[$c]['billing_full_name'] = $ob->commerce_customer_address_name_line;
            $addBook[$c]['billing_company'] = $ob->commerce_customer_organization_name;
            $addBook[$c]['billing_address'] = $ob->commerce_customer_address_thoroughfare;
            $addBook[$c]['billing_locality'] = $ob->commerce_customer_address_locality;
            $addBook[$c]['billing_admin_area'] = $ob->commerce_customer_address_administrative_area;
            $addBook[$c]['billing_zip'] = $ob->commerce_customer_address_postal_code;
            $addBook[$c]['billing_zip4'] = $ob->commerce_customer_address_postal_code_ext;
            $addBook[$c]['billing_email'] = $ob->commerce_customer_address_data;
            $addBook[$c]['billing_phone'] = $ob->phone;
            $addBook[$c]['id'] = $ob->bpid;

            $c++;
        }
//        $resultA = db_query("SELECT a.profile_id,  b.* FROM `commerce_customer_profile` AS a
//                     LEFT JOIN `field_data_commerce_customer_address` AS b on a.profile_id=b.entity_id
//                     WHERE a.status='1' AND a.uid = '$user->uid' AND a.type='billing' AND b.commerce_customer_address_thoroughfare !='' ");

        $sql = "SELECT *, a.entity_id AS orderid,x.profile_id as bpid,  f.field_phone_number_value as phone FROM `field_data_commerce_customer_shipping` AS a
               LEFT JOIN field_data_commerce_customer_address AS b ON a.commerce_customer_shipping_profile_id=b.entity_id 
               LEFT JOIN field_data_field_phone_number as f on a.commerce_customer_shipping_profile_id=f.entity_id
               LEFT JOIN `commerce_customer_profile` AS  x on  a.commerce_customer_shipping_profile_id=x.profile_id
                WHERE x.status='1' AND x.uid = '$user->uid' GROUP BY x.profile_id";
// echo $sql;
        $resultA = db_query($sql);
        $addBookB = array();
        $c = 0;
        foreach ($resultA as $row) {
            $addBookB[$c]['billing_first'] = $row->commerce_customer_address_first_name;
            $addBookB[$c]['billing_last'] = $row->commerce_customer_address_last_name;
            $addBookB[$c]['billing_full_name'] = $row->commerce_customer_address_name_line;
            $addBookB[$c]['billing_company'] = $row->commerce_customer_organization_name;
            $addBookB[$c]['billing_address'] = $row->commerce_customer_address_thoroughfare;
            $addBookB[$c]['billing_locality'] = $row->commerce_customer_address_locality;
            $addBookB[$c]['billing_admin_area'] = $row->commerce_customer_address_administrative_area;
            $addBookB[$c]['billing_zip'] = $row->commerce_customer_address_postal_code;
            $addBookB[$c]['billing_zip4'] = $row->commerce_customer_address_postal_code_ext;
            $addBookB[$c]['billing_email'] = $row->commerce_customer_address_data;
            $addBookB[$c]['billing_phone'] = $row->phone;
            $addBookB[$c]['id'] = $row->bpid;

            $c++;
        }
        return array($addBook, $addBookB, $sql);
    }

    public function emptyCart() {
        global $user;
        //this request takes the data from the session and adds to cart through the drupal/commerce api
        //empty the drupal cart and fill with basket items 
        $objOrder = commerce_cart_order_load($user->uid);

        if ($objOrder) {
            commerce_cart_order_empty($objOrder);
            unset($_SESSION);
        }
    }

    /*
     * Handles all incoming AJAX requests from the front-end
     */

    public function processAjax($arrData) {
        // import the drupal environment so we can utilize the framework and all of its exposed methods
        global $user;

        $strCurDir = getcwd();
        $strDrupalDir = self::$strPhysPath;

        // change current folder to drupal's folder
        chdir($strDrupalDir);

        // bootstrap drupal
        define('DRUPAL_ROOT', self::$strPhysPath);

        require_once './includes/errors.inc';
        require_once './includes/bootstrap.inc';

        // set the session cookie domain
        $cookie_domain = $_SERVER['HTTP_HOST'];
        drupal_bootstrap(DRUPAL_BOOTSTRAP_SESSION);
        drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

        // switch back to our working folder
        chdir($strCurDir);

        // start session
        drupal_session_start();

        // if we're redeeming a gift certificate...
        if (isset($arrData['redeem'])) {
            $txtFail = sprintf('Sorry, the code: %s is not valid, or has been used already.', $arrData['redeem']);
            $objQuery = DB::prepare("	SELECT		* 
													FROM		certificate 
													WHERE		redemption_code = :code 
													AND			date_used = '0'");
            $objQuery->bindParam(':code', $arrData['redeem'], PDO::PARAM_STR);
            $objQuery->execute();

            $intNumRows = $objQuery->rowCount();

            if ($intNumRows > 0) {

                echo 'true';
                return;
            } else {
                echo json_encode(array('success' => false));
            }
        }

        if (isset($arrData['updateCart'])) {
            // ajax request to update the basket
            // stores the cart items in the session so we can add to cart
            $arrData['updateCart'] = json_decode($arrData['updateCart']);

            if (isset($arrData['coolers'])) {
                $arrData['coolers'] = json_decode($arrData['coolers']);
            }

            if ($arrData['coolerCost'] != '') {
                // not a certificate
                $_SESSION['coolerCost'] = $arrData['coolerCost'];
                $_SESSION['updateCart'] = $arrData['updateCart'];
                $_SESSION['coolers'] = $arrData['coolers'];
            } else {
                $_SESSION['updateCart'][] = $arrData['updateCart'];

                echo 'true';
            }
        }

        if (isset($arrData['submitCart'])) {
            if ($arrData['submitCart'] == 1) {
                //this request takes the data from the session and adds to cart through the drupal/commerce api
                //empty the drupal cart and fill with basket items 
                $objOrder = commerce_cart_order_load($user->uid);

                if ($objOrder) {
                    commerce_cart_order_empty($objOrder);
                }

                foreach ($_SESSION['certificateIDs'] as $arrData) {
                    //lets remove the prodfuct from db and recreate it to the latest options below
                    commerce_product_delete($arrData['id']);

                    // $message = 'The certificate id DELETED was: ' . $arrData['id'];
                    // drupal_set_message($message, $type = 'status');
                }

                $_SESSION['certificateIDs'] = '';

                //ajax request to finsh and add items in basket to checkout 
                $intCartCounter = 0;

                foreach ($_SESSION['updateCart'] as $objItem) {
                    $intCartCounter++;

                    $objLineItem = NULL;

                    if ($objItem->pid == '80') {
                        // item is giftcard, so lets build a new product with the specific items and combined price
                        if ($objProduct = commerce_product_load('80')) {
                            $dblAmount = $objItem->price * 100;
                            $strItemSKU = $intCartCounter . date('smdm') . rand(1, 3);

                            // save sku in session so we can remove and recreate a new one on checkout
                            $strItemName = 'Gift Certificate #' . $strItemSKU . ' Includes: ' . $objItem->variation;

                            $arrValues = array();
                            $arrExtras = array();

                            $arrValues['original_order'] = $objOrder->order_id;
                            $arrValues['original_line_item'] = $objProduct->line_item_id;
                            $arrValues['original_product'] = '80';
                            $arrValues['price'] = $dblAmount;
                            $arrExtras['status'] = '1';
                            $arrExtras['uid'] = '1';
                            $arrExtras['sku'] = $strItemSKU;
                            $arrExtras['title'] = $strItemName;
                            $arrExtras['body'] = 'No description.';

                            $intNewItemID = $this->commerce_installments_create_product('product', $arrValues, $arrExtras);

                            $_SESSION['certificateIDs'][$strItemSKU]['id'] = $intNewItemID;
                            $_SESSION['certificateIDs'][$strItemSKU]['items'] = $objItem->variation;

                            //  $message = 'The certificate id generated was: ' . $intNewItemID;
                            //  drupal_set_message($message, $type = 'status');

                            $objProduct = commerce_product_load($intNewItemID);
                            $objLineItem = commerce_product_line_item_new($objProduct, 1);
                            $objLineItem = commerce_cart_product_add($user->uid, $objLineItem);
                            $resultA = db_query("UPDATE field_data_commerce_stock SET commerce_stock_value = :stock
                        WHERE entity_id= :id", array(':id' => $intNewItemID, ':stock' => '1'));
                        }
                    } else {
                        // not a giftcard, so simple add to cart
                        // echo 'not gc';

                        if ($objProduct = commerce_product_load($objItem->pid)) {
                            $objProduct = commerce_product_load($objItem->pid);
//                            if ($objItem->quantity > 1) {
//                                $sku= $objProduct->sku; 
//                            $sql = " SELECT * FROM commerce_product WHERE sku=''";
//                            $resultA = db_query($sql);
//                        }
                            $objLineItem = commerce_product_line_item_new($objProduct, $objItem->quantity);
                            $objLineItem = commerce_cart_product_add($user->uid, $objLineItem);
                        }
                    }
                }
            }
            //now we add the coolers to cart
            foreach ($_SESSION['coolers'] as $strCoolerType) {
                switch (strtolower($strCoolerType)) {
                    case 'size4':
                        $intCoolerID = 79;
                        break;

                    case 'size3':
                        $intCoolerID = 78;
                        break;

                    case 'size2':
                        $intCoolerID = 77;
                        break;

                    case 'size1':
                    default:
                        $intCoolerID = 76;
                        break;
                }

                $objLineItem = NULL;

                if ($objProduct = commerce_product_load($intCoolerID)) {
                    $objProduct = commerce_product_load($intCoolerID);
                    $objLineItem = commerce_product_line_item_new($objProduct, 1);
                    $objLineItem = commerce_cart_product_add($user->uid, $objLineItem);
                }

                echo json_encode(array('success' => true));
            }

            // erase existing "xxxx added to cart" messages
            $arrMessages = drupal_get_messages();
        }
    }

    /*
     * Creates a product (primarily used when creating gift certificates)
     *
     * @return unique ID (int) of newly created product
     */

    public function commerce_installments_create_product($strProductType, $arrValues, $arrExtras) {
        $arrFormState = array();
        $arrForm = array();

        $arrFormState['values'] = $arrValues;
        $arrForm['#parents'] = array();

        $objNewProduct = commerce_product_new($strProductType);

        $objNewProduct->status = $arrExtras['status'];
        $objNewProduct->uid = $arrExtras['uid'];
        $objNewProduct->sku = $arrExtras['sku'];
        $objNewProduct->title = $arrExtras['title'];
        $objNewProduct->created = $objNewProduct->changed = time();

        $arrOrderData = array(LANGUAGE_NONE => array(0 => array('target_id' => $arrValues['original_order'])));
        $arrFormState['values']['field_original_order'] = $arrOrderData;

        $arrLineItemData = array(LANGUAGE_NONE => array(0 => array('target_id' => $arrValues['original_line_item'])));
        $arrFormState['values']['field_original_line_item'] = $arrLineItemData;

        $arrProductData = array(LANGUAGE_NONE => array(0 => array('target_id' => $arrValues['original_product'])));
        $arrFormState['values']['field_original_product'] = $arrProductData;

        $arrPriceData = array(LANGUAGE_NONE => array(
                0 => array(
                    'amount' => $arrValues['price'],
                    'currency_code' => commerce_default_currency()
                )
                ));

        $arrFormState['values']['commerce_price'] = $arrPriceData;
        $arrFormState['values']['body'] = $arrExtras['body'];

        field_attach_submit('commerce_product', $objNewProduct, $arrForm, $arrFormState);

        commerce_product_save($objNewProduct);

        return $objNewProduct->product_id;
    }

    /*
     * Returns the cooler shipping cost for the given weight
     */

    public function getCoolerCost($dblWeight) {
        $dblCoolerCost = 0;

        switch (true) {
            case (($dblWeight > 0) && ($dblWeight < 12)) :
                $dblCoolerCost = 15;  // 15 USD
                break;

            case (($dblWeight >= 12) && ($dblWeight < 18)) :
                $dblCoolerCost = 22;  // 22 USD
                break;

            case (($dblWeight >= 18) && ($dblWeight < 22)) :
                $dblCoolerCost = 30;  // 30 USD
                break;

            case (($dblWeight >= 22) && ($dblWeight < 40)) :
                $dblCoolerCost = 40;  // 40 USD
                break;

            case ($dblWeight >= 40) :
                $dblCoolerCost = 40 + ($this->getCoolerCost($dblWeight - 40));
                break;
        }

        return $dblCoolerCost;
    }

    public function submitOrder($post) {
        print_r($post);
        $amt = 0;
        $weight = 0;
        global $user;
        $action = 'redeemable';

        $strCurDir = getcwd();
        $strDrupalDir = self::$strPhysPath;

        // change current folder to drupal's folder
        chdir($strDrupalDir);

        // bootstrap drupal
        define('DRUPAL_ROOT', self::$strPhysPath);

        require_once './includes/errors.inc';
        require_once './includes/bootstrap.inc';

        // set the session cookie domain
        $cookie_domain = $_SERVER['HTTP_HOST'];
        drupal_bootstrap(DRUPAL_BOOTSTRAP_SESSION);
        drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

        // switch back to our working folder
        chdir($strCurDir);

        // start session
        drupal_session_start();

        $order = commerce_order_new($user->uid, 'redeemable');
        commerce_order_save($order);
        $order_wrapper = entity_metadata_wrapper('commerce_order', $order);
        $shippingID = $post['addbook'][1];
        $billingID = $post['addbook'][2];
        $redemptionID = $post['addbook'][0];
        $params = $post;
        if ($redemptionID == 0) {

            $profile = commerce_customer_profile_new('shipping', $user->uid);
            $profile->commerce_customer_address = array(LANGUAGE_NONE => array(0 => addressfield_default_values()));


            $profile->commerce_customer_address['und'][0]['name_line'] = $params["redemption_fullname"];
            $profile->commerce_customer_address['und'][0]['organisation_name'] = '';
            $profile->commerce_customer_address['und'][0]['thoroughfare'] = $params["redemption_address1"] . ' ' . $params["redemption_address2"];
            $profile->commerce_customer_address['und'][0]["locality"] = $params["redemption_city"];
            $profile->commerce_customer_address['und'][0]["administrative_area"] = $params["redemption_state"];
            $profile->commerce_customer_address['und'][0]["postal_code"] = $params["redemption_zip"];
            $profile->commerce_customer_address['und'][0]["postal_code_ext"] = '';
            $profile->commerce_customer_address['und'][0]["data"] = $params["redemption_email"];
            $profile->commerce_customer_address['und'][0]["phone"] = $params["redemption_phone"];
            commerce_customer_profile_save($profile);
            $redemptionID = $profile->profile_id;
        }
        if ($shippingID == 0) {

            $profile = commerce_customer_profile_new('shipping', $user->uid);
            $profile->commerce_customer_address = array(LANGUAGE_NONE => array(0 => addressfield_default_values()));


            $profile->commerce_customer_address['und'][0]['name_line'] = $params["shipping_fullname"];
            $profile->commerce_customer_address['und'][0]['organisation_name'] = '';
            $profile->commerce_customer_address['und'][0]['thoroughfare'] = $params["shipping_address1"] . ' ' . $params["shipping_address2"];
            $profile->commerce_customer_address['und'][0]["locality"] = $params["shipping_city"];
            $profile->commerce_customer_address['und'][0]["administrative_area"] = $params["shipping_state"];
            $profile->commerce_customer_address['und'][0]["postal_code"] = $params["shipping_zip"];
            $profile->commerce_customer_address['und'][0]["postal_code_ext"] = '';
            $profile->commerce_customer_address['und'][0]["data"] = $params["shipping_email"];
            $profile->commerce_customer_address['und'][0]["phone"] = $params["shipping_phone"];
            commerce_customer_profile_save($profile);
            $shippingID = $profile->profile_id;
        }
        if ($billingID == 0) {
            $profile = commerce_customer_profile_new('billing', $user->uid);
            $profile->commerce_customer_address = array(LANGUAGE_NONE => array(0 => addressfield_default_values()));


            $profile->commerce_customer_address['und'][0]['name_line'] = $params["billing_fullname"];
            $profile->commerce_customer_address['und'][0]['first_name'] = $params["billing_first"];
            $profile->commerce_customer_address['und'][0]['last_name'] = $params["billing_last"];
            $profile->commerce_customer_address['und'][0]['organisation_name'] = '';
            $profile->commerce_customer_address['und'][0]['thoroughfare'] = $params["billing_address1"] . ' ' . $params["billing_address2"];
            $profile->commerce_customer_address['und'][0]["locality"] = $params["billing_city"];
            $profile->commerce_customer_address['und'][0]["administrative_area"] = $params["billing_state"];
            $profile->commerce_customer_address['und'][0]["postal_code"] = $params["billing_zip"];
            $profile->commerce_customer_address['und'][0]["postal_code_ext"] = '';
            $profile->commerce_customer_address['und'][0]["data"] = $params["billing_email"];
            $profile->commerce_customer_address['und'][0]["phone"] = $params["billing_phone"];
            commerce_customer_profile_save($profile);
            $billingID = $profile->profile_id;
        }


        $profile_shipping = array(
            'und' => array(array('profile_id' => $shippingID,),),);
        $profile_billing = array(
            'und' => array(array('profile_id' => $billingID,),),);
        $order->commerce_customer_billing = $profile_billing;
        $order->commerce_customer_shipping = $profile_shipping;
        $commerce_line_items = array();
        $oid = $order->order_id;
        //print_r($objOrder);

        echo"<br/>";
        foreach ($post['products'] as $objItem) {
            echo"Item ID: $objItem<br/><br/>";
            $objLineItem = NULL;
            if ($product = commerce_product_load($objItem)) {
                //   print_r($product);
                echo"product loaded, orderid is $order->order_id<br/><br/>";
                $product = commerce_product_load($objItem);
                $line_item = commerce_product_line_item_new($product, '1', $order->order_id);
                commerce_line_item_save($line_item);
                //   print_r($line_item);
                $commerce_line_items[] = $line_item;
                $order_wrapper->commerce_line_items[] = $line_item;
                $amt = $amt + ($line_item->commerce_unit_price['und']['0']['amount'] / 100);
                $weight = $weight + $product->field_base_weight['und']['0']['weight'];
            }
        }
        // print_r($objOrder);
        //now we add the coolers to order
        foreach ($post['coolers'] as $strCoolerType) {
            switch (strtolower($strCoolerType)) {
                case 'size4':
                    $intCoolerID = 79;
                    break;

                case 'size3':
                    $intCoolerID = 78;
                    break;

                case 'size2':
                    $intCoolerID = 77;
                    break;

                case 'size1':
                default:
                    $intCoolerID = 76;
                    break;
            }
            //   print_r($objOrder);
            $objLineItem = NULL;

            if ($objProduct = commerce_product_load($intCoolerID)) {
                $objProduct = commerce_product_load($intCoolerID);
                $objLineItem = commerce_product_line_item_new($objProduct, 1, $order->order_id);
                commerce_line_item_save($objLineItem);
                $commerce_line_items[] = $objLineItem;
                $order_wrapper->commerce_line_items[] = $objLineItem;
                $amt = $amt + ($objLineItem->commerce_unit_price['und']['0']['amount'] / 100);
                $weight = $weight + $objProduct->field_base_weight['und']['0']['weight'];
            }
        }

        $shippingID = $post['addbook'][1];
        $billingID = $post['addbook'][2];
        $params = $post;

        echo"amt is $amt<br/>";
        if ($post['addbook'][0] != 0) {
            //redemption address was from addressbook, get state
            $orderBilling = db_query("SELECT *,  f.field_phone_number_value as phone, x.profile_id as bpid FROM `field_data_commerce_customer_billing` as a
                                  LEFT JOIN  field_data_commerce_customer_address as b on a.commerce_customer_billing_profile_id=b.entity_id
                                   LEFT JOIN field_data_field_phone_number as f on a.commerce_customer_billing_profile_id=f.entity_id
                                   LEFT JOIN `commerce_customer_profile` AS  x on  a.commerce_customer_billing_profile_id=x.profile_id
                                WHERE x.status='1' AND x.profile_id = '" . $redemptionID . "'");

            $addBook = array();
            $c = 0;
            //   print_r($orderBilling);
            foreach ($orderBilling as $ob) {
                $redeemState = $ob->commerce_customer_address_administrative_area;
                $c++;
            }
        } else {
            $redeemState = $post['redemption_state'];
        }
        //add state shipping fee as line item
        $result = db_query("SELECT *, a.entity_id as zoneid FROM field_data_shipping_matrix_locality as a  
                        LEFT JOIN field_data_shipping_matrix_price as b ON a.entity_id=b.entity_id
                        WHERE a.shipping_matrix_locality_administrative_area= :state", array(':state' => $redeemState));


        foreach ($result as $row) {

            $zone = $row->zoneid;
            $fp = 0;
            if ($zone < 4 && $weight > 15) {
                $fp = ($weight - 15) * 1;
            }
            if ($zone >= 4 && $weight > 15) {
                $fp = ($weight - 15) * 2;
            }

            $response = number_format($row->shipping_matrix_price_amount / 100 + $fp, 2);
        }
        $amount = $response * 100;
        $product = commerce_product_load('80');
        $sku = $redeemState . ' State Shipping Fee ID#' . date('dmyis');
        $desc = $redeemState . ' State Shipping Fee ID#' . date('dmyis');
        $values['original_order'] = $order->order_id;
        $values['original_line_item'] = '1';
        $values['original_product'] = '80';
        $values['price'] = $amount;
        $extras['status'] = '1';
        $extras['uid'] = '1';
        $extras['sku'] = $sku;
        $extras['title'] = $desc;
        $extras['body'] = $desc;
        $newid = commerce_installments_create_product('product', $values, $extras);

        $product = commerce_product_load($newid);

        $line_item = commerce_product_line_item_new($product, 1, $order->order_id);

        commerce_line_item_save($line_item);

        $order_wrapper->commerce_line_items[] = $line_item;
        //$amt = $amt + $response;

        $order = commerce_order_status_update($order, $action, TRUE);
        commerce_order_save($order);
        echo"total:";
        print_r($order->commerce_order_total['und'][0]['amount']);
        $amt = ($order->commerce_order_total['und'][0]['amount']) / 100;
        echo"amt is $amt";
        //exit(0);
        if (MCGStoreModel::charge($amt, $post) == true) {
            MCGStoreModel::createRedemptionRecord($oid, $commerce_line_items, $redemptionID);
        } else {
            $order = commerce_order_status_update($order, 'canceled', TRUE);
            commerce_order_save($order);
            header('Location: /certificate-builder?chargeFailed=1');
        }
    }

    public function createRedemptionRecord($order_id, $line_items, $redemptionID) {
        $line_items = json_encode($line_items);
        $line_items = base64_encode($line_items);
        //insert redmeption address and order info to db
        $chars = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $serial = '';
        for ($i = 0; $i < 20; $i++) {
            $serial .= (!($i % 5) && $i ? '-' : '') . $chars[rand(0, (count($chars) - 1))];
        }
        //  $line_items = $sku['id'] . '^' . $sku['items']; //base64_encode(serialize($line_item));
        $result = db_query("INSERT INTO certificate SET `redemption_code`='$serial', `orig_order_id`='$order_id', `serialized_order`='$line_items', ship_profile_id='$redemptionID'");
        $message = 'The certificate code generated was: ' . $serial;
        MCGStoreModel::emailReceipt($serial, $user->uid);
        header('Location: /certificate-builder?orderCompleted=1&serial=' . $serial);
        return true;
    }

    public function emailReceipt($serial, $uid) {
        global $user;
        $to = $user->mail;

        $subject = 'Your  Gift certificate is ready';
        $message = 'Hello, <br/><br/>
    Your gift Certificate Redemption code is: ' . $serial . ' 
        <br/>Gift Certificates can be redeemed at http://www..com/online-store<br/><br/>Than you for your order!';
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: webmaster@.com' . "\r\n" .
                'Reply-To: webmaster@.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
        // exit(0);
    }

    private function charge($amount, $post) {
        return true;
        $amt2 = number_format($amount, 2, '.', ' ');
        require_once 'anet_php_sdk/AuthorizeNet.php';
        define("AUTHORIZENET_API_LOGIN_ID", "loginid");
        define("AUTHORIZENET_TRANSACTION_KEY", "key");
        define("AUTHORIZENET_SANDBOX", false);
        $sale = new AuthorizeNetAIM;

        if ($post['addbook'][1] > 0) {
            $orderBilling = db_query("SELECT *,  f.field_phone_number_value as phone, x.profile_id as bpid FROM `field_data_commerce_customer_billing` as a
                                  LEFT JOIN  field_data_commerce_customer_address as b on a.commerce_customer_billing_profile_id=b.entity_id
                                   LEFT JOIN field_data_field_phone_number as f on a.commerce_customer_billing_profile_id=f.entity_id
                                   LEFT JOIN `commerce_customer_profile` AS  x on  a.commerce_customer_billing_profile_id=x.profile_id
                                WHERE x.status='1' AND x.profile_id = '" . $post['addbook'][1] . "'");



            foreach ($orderBilling as $ob) {

                $sale->first_name = $ob->commerce_customer_address_first_name;
                $sale->last_name = $ob->commerce_customer_address_last_name;
                $sale->address = $ob->commerce_customer_address_thoroughfare;
                $sale->city = $ob->commerce_customer_address_locality;
                $sale->state = $ob->commerce_customer_address_administrative_area;
                $sale->zip = $ob->commerce_customer_address_postal_code;
                $sale->email = $ob->commerce_customer_address_data;
                $sale->ship_to_first_name = $ob->commerce_customer_address_first_name;
                $sale->ship_to_last_name = $ob->commerce_customer_address_last_name;
                $sale->ship_to_address = $ob->commerce_customer_address_thoroughfare;
                $sale->ship_to_city = $ob->commerce_customer_address_locality;
                $sale->ship_to_state = $ob->commerce_customer_address_administrative_area;
                $sale->ship_to_zip = $ob->commerce_customer_address_postal_code;
                //  $sale->ship_to_email = $ob->commerce_customer_address_data;
//                $params['shipping_phone'] = $ob->phone;
//                $params['id'] = $ob->bpid;
            }
        } else {
            $sale->first_name = $post["billing_first"];
            $sale->last_name = $post["billing_last"];
            $sale->address = $post["billing_address1"] . ' ' . $post["billing_address2"];
            $sale->city = $post["shipping_city"];
            $sale->state = $post["billing_state"];
            $sale->country = 'US';
            $sale->zip = $post["billing_zip"];
            $sale->email = $post["billing_email"];
            $sale->ship_to_first_name = $post["billing_first"];
            $sale->ship_to_last_name = $post["billing_last"];
            $sale->ship_to_address = $post["billing_address1"] . ' ' . $post["billing_address2"];
            $sale->ship_to_city = $post["shipping_city"];
            $sale->ship_to_state = $post["billing_state"];
            $sale->ship_to_country = 'US';
            $sale->ship_to_zip = $post["billing_zip"];
        }
        $sale->amount = $amt2;
        $sale->card_num = $post['card_no'];
        $sale->exp_date = $post['monthExpires'] . '/' . $post['yearExpires'];
        print_r($sale);
//        $sale->first_name = $binfo->first;
//        $sale->last_name = $binfo->last;
//        $sale->address = $binfo->street . ' ' . $binfo->street2;
//        $sale->city = $binfo->city;
//        $sale->state = $binfo->state;
//        $sale->country = $binfo->country;
//        $sale->zip = $binfo->postal_code;
//        $sale->email = $binfo->email;
        // $sale->addLineItem('item1', ' Market', '', '1', $amt2, 'N');
        $response = $sale->authorizeAndCapture();

        MCGStoreModel::$response = $response;
        //   print_r($response);
        // exit(0);
        if ($response->approved) {
            return true;
        } else {
            return false;
        }
    }

    public function getRdemptionOrder($code) {
        $sql = "	SELECT		* 
													FROM		certificate 
													WHERE		redemption_code = :code 
													AND			date_used = '0'";
        $objQuery = DB::prepare($sql);
        $objQuery->bindParam(':code', $code, PDO::PARAM_STR);
        $objQuery->execute();
//echo/ $sql;
//echo"code is $code";
        $intNumRows = $objQuery->rowCount();
        //  echo "introws is $intNumRows";
        if ($intNumRows > 0) {
            $rows = $objQuery->fetchAll();
            //  print_r($rows);
            $orderShipping = db_query("SELECT *,  f.field_phone_number_value as phone, x.profile_id as bpid FROM `field_data_commerce_customer_shipping` as a
                                  LEFT JOIN  field_data_commerce_customer_address as b on a.commerce_customer_shipping_profile_id=b.entity_id
                                   LEFT JOIN field_data_field_phone_number as f on a.commerce_customer_shipping_profile_id=f.entity_id
                                   LEFT JOIN `commerce_customer_profile` AS  x on  a.commerce_customer_shipping_profile_id=x.profile_id
                                WHERE x.status='1' AND x.profile_id = '" . $rows[0][6] . "'");


            return array($orderShipping, $rows[0][5], $rows[0][6]);
        } else {
            return false;
        }
    }

    public function getProductInfo($id) {
        $sql = "SELECT * FROM commerce_product WHERE product_id='$id'";
        $objQuery = DB::prepare($sql);
        $objQuery->bindParam(':id', $id, PDO::PARAM_STR);
        $objQuery->execute();
//echo $sql;
//echo"code is $code";
        $intNumRows = $objQuery->rowCount();
        //  echo "introws is $intNumRows";
        if ($intNumRows > 0) {
            $rows = $objQuery->fetchAll();
            //   print_r($rows);
            return $rows[0]['title'];
        }
    }

    function checkRedeemed($code) {
        $sql = "	SELECT		* 
													FROM		certificate 
													WHERE		redemption_code = :code 
													AND			date_used = '0'";
        $objQuery = DB::prepare($sql);
        $objQuery->bindParam(':code', $code, PDO::PARAM_STR);
        $objQuery->execute();
//echo/ $sql;
//echo"code is $code";
        $intNumRows = $objQuery->rowCount();
        //  echo "introws is $intNumRows";
        if ($intNumRows > 0) {
            return true;
        } else {
            return false;
        }
    }

    function confirmRedemption($code, $address) {
        if (MCGStoreModel::checkRedeemed($code) == false) {
            return false;
        }
        $orderItems = MCGStoreModel::getRdemptionOrder($code);
        $shipid = $orderItems[2];
        MCGStoreModel::updateAddress($shipid, $address);
        global $user;
        $strCurDir = getcwd();
        $strDrupalDir = self::$strPhysPath;

        // change current folder to drupal's folder
        chdir($strDrupalDir);

        // bootstrap drupal
        define('DRUPAL_ROOT', self::$strPhysPath);

        require_once './includes/errors.inc';
        require_once './includes/bootstrap.inc';

        // set the session cookie domain
        $cookie_domain = $_SERVER['HTTP_HOST'];
        drupal_bootstrap(DRUPAL_BOOTSTRAP_SESSION);
        drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

        // switch back to our working folder
        chdir($strCurDir);


        $orderItems = base64_decode($orderItems[1]);
        $orderItems = json_decode($orderItems);

        $order = commerce_order_new($user->uid, 'pending');
        commerce_order_save($order);
        $order_wrapper = entity_metadata_wrapper('commerce_order', $order);
        $shippingID = $shipid;
        $billingID = $shipid;
        $redemptionID = $shipid;
        $params = $post;
        //   print_r($order);
        $profile_shipping = array(
            'und' => array(array('profile_id' => $shippingID,),),);
        $profile_billing = array(
            'und' => array(array('profile_id' => $billingID,),),);
        $order->commerce_customer_billing = $profile_billing;
        $order->commerce_customer_shipping = $profile_shipping;

        $commerce_line_items = array();
        $oid = $order->order_id;
        foreach ($orderItems as $o) {
            //   echo"Item ID: " . $o->commerce_product->und[0]->product_id . "<br/><br/>";
            $objLineItem = NULL;
            if ($product = commerce_product_load($o->commerce_product->und[0]->product_id)) {
                //   print_r($product);
                // echo"product loaded, orderid is $order->order_id<br/><br/>";
                $product = commerce_product_load($o->commerce_product->und[0]->product_id);
                $line_item = commerce_product_line_item_new($product, '1', $order->order_id);
                commerce_line_item_save($line_item);
                //    print_r($line_item);
                $commerce_line_items[] = $line_item;
                $order_wrapper->commerce_line_items[] = $line_item;
            }
        }
        //print_r($order);
        //now we add the coolers to order
        foreach ($post['coolers'] as $strCoolerType) {
            switch (strtolower($strCoolerType)) {
                case 'size4':
                    $intCoolerID = 79;
                    break;

                case 'size3':
                    $intCoolerID = 78;
                    break;

                case 'size2':
                    $intCoolerID = 77;
                    break;

                case 'size1':
                default:
                    $intCoolerID = 76;
                    break;
            }
            //   print_r($objOrder);
            $objLineItem = NULL;

            if ($objProduct = commerce_product_load($intCoolerID)) {
                $objProduct = commerce_product_load($intCoolerID);
                $objLineItem = commerce_product_line_item_new($objProduct, 1, $order->order_id);
                commerce_line_item_save($objLineItem);
                $commerce_line_items[] = $objLineItem;
                $order_wrapper->commerce_line_items[] = $objLineItem;
            }
        }

        $order = commerce_order_status_update($order, 'pending', TRUE);
        commerce_order_save($order);
//           print_r($order);
//        exit(0);
        db_query("UPDATE certificate SET date_used='" . date('Y-m-d H:i:s') . "', redeemed_by='" . $user->uid . "' WHERE redemption_code='$code' ");
        // print_r($user);
        $to = $user->mail;

        $subject = 'Your  Gift certificate is ready';
        $message = 'Hello, <br/><br/>' .
                'Your gift Certificate #: ' . $code . ' has been successfully redeemed. Please login to your account to view the order details. <br/><br/>Thank You!<br/>' .
                '<br/> http://www..com/<br/>';
        $headers = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
        $headers .= 'From: webmaster@.com' . "\r\n" .
                'Reply-To: webmaster@.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
        //  echo"To $to";
        // exit(0);
        return true;
    } 

    function updateAddress($shipid, $address) {
        $sql = "UPDATE field_data_commerce_customer_address SET commerce_customer_address_thoroughfare='" . $address['address'] . "',
                commerce_customer_address_locality='" . $address['city'] . "',  commerce_customer_address_postal_code='" . $address['zip'] . "' WHERE entity_id='$shipid' ";
        db_query($sql);

        $sql = "UPDATE field_revision_commerce_customer_address SET commerce_customer_address_thoroughfare='" . $address['address'] . "',
                commerce_customer_address_locality='" . $address['city'] . "',  commerce_customer_address_postal_code='" . $address['zip'] . "' WHERE entity_id='$shipid' ";
        db_query($sql);




        $sql = "UPDATE field_data_field_target_ship_date SET field_target_ship_date_value='" . $address['targetdate'] . "' WHERE entity_id='$shipid' AND bundle='shipping' ";
        db_query($sql);

        $sql = "UPDATE field_revision_field_target_ship_date SET field_target_ship_date_value='" . $address['targetdate'] . "' WHERE entity_id='$shipid' AND bundle='shipping' ";
        db_query($sql);
        //echo $sql;
        return true;
    }

}

?>