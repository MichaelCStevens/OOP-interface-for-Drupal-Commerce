if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function(strNeedle) {
        for(var intCounter = 0; intCounter < this.length; intCounter++) {
            if (this[intCounter] === strNeedle) {
                return intCounter;
            }
        }

        return -1;
    };
}
var  globalTotal=0;
var giftcardGlobal=0;

var MCG = {
    p: {
        basket: {
            positionTop: 0
        }, 
        parents: {
            list:[]
        }, 
        cart: {
            strings: {
                titleBasket: '', 
                titleAdd: '', 
                titleCheckout: '', 
                shippingFee: '', 
                totalPrice:'',
                totalWeight: '',
                cssCheckout: ''
            }, 
            mode: '', 
            runningWeight: 0, 
            list: [], 
            coolers: []
        }
    }, 
    fn: {
        addCoolers: function(objValue) {
            MCG.p.cart.coolers.push(objValue);//push function will insert values in the list array
        }, 
        setGiftCardGlobal: function() {
            giftcardGlobal=1;
        },
        checkQuant: function(pid, x) {
            var returndata=0;
            var  intFound=-1;
            jQuery.getJSON('//checkQuant?pid='+pid, function(data) { 
                //  alert('id returned: '+data);
                var items = [];
 
                jQuery.each(data, function(key, val) {
                    items.push(val);
                });
                    //crapy hacks thanks for changing spec two mins before launch, client
                if(x==1){
                    var arrItemData				= [];
                    arrItemData['pid']=items[0];
                    arrItemData['eid']=items[1];
                    arrItemData['cartindex']= MCG.p.cart.list.length;
                    for (var i2 = 0; i2 < MCG.p.parents.list.length; i2++) {  
                        if (MCG.p.parents.list[i2].eid == items[1]) {
                            intFound			= i2; 
                            break;
                        }
                    }
                    if(parseInt(intFound>-1)){
                        MCG.fn.removeParentList(intFound);
                    }
                    MCG.p.parents.list.push(arrItemData);
                }
                if(x==1){
                    returndata=items[0];
                }
                if(x==2){
                    returndata=items[1];
                }
                
            });
            return returndata;
        }, 
        checkQuantIncrement: function(pid, pid2, x) {
            var returndata=0;
            jQuery.getJSON('//checkQuantIncrement?pid='+pid+'&pid2='+pid2, function(data) { 
                //  alert('id returned: '+data);
                var items = [];
                var  intFound=-1;
                jQuery.each(data, function(key, val) {
                    items.push(val);
                });
                var arrItemData				= [];
                arrItemData['pid']=items[0];
                arrItemData['eid']=items[1];
                arrItemData['cartindex']= MCG.p.cart.list.length;
                for (var i2 = 0; i2 < MCG.p.parents.list.length; i2++) {  
                    if (MCG.p.parents.list[i2].eid == items[1]) {
                        intFound			= i2; 
                        break;
                    }
                }
                if(parseInt(intFound>-1)){
                    MCG.fn.removeParentList(intFound);
                }
                MCG.p.parents.list.push(arrItemData); 
                if(x==1){
                    returndata=items[0];
                }
                if(x==2){
                    returndata=items[1];
                } 
                
            });
            return returndata;
        },	
        removeParentList: function(intIndex) {  
            MCG.p.parents.list.splice(intIndex, 1);  
        }, 	
        addList: function(objValue) {
            MCG.p.cart.list.push(objValue); 
        }, 
		
        removeList: function(intIndex) {
            var intFound=-1;
            jQuery('div[attr-pid="' + MCG.p.cart.list[intIndex].addon + '"]')
            .css('background','none');
				
            MCG.p.cart.list.splice(intIndex, 1);
            for (var i2 = 0; i2 < MCG.p.parents.list.length; i2++) {  
                if (MCG.p.parents.list[i2].cartindex == intIndex) {
                    intFound			= i2; 
                    break;
                }
            }
            if(parseInt(intFound>-1)){
                MCG.fn.removeParentList(intFound);
            }
	
            MCG.fn.updateBasket(0);
        }, 
		
        buildBasket: function() {
            var onclick='';
            var objContainer			= jQuery('#col-left');
			
            if (objContainer.length == 0) {
                var objContainer			= jQuery('#order-form');
            }
			
            if (objContainer.length > 0) {
                if(MCG.p.cart.strings.titleBasket=='Certificate'){
                    var states={
                        "AK": "Alaska",    
                        "AZ": "Arizona",    
                        "AR": "Arkansas",    
                        "CA": "California",    
                        "CO": "Colorado",    
                        "CT": "Connecticut",    
                        "DE": "Delaware", 
                        "DC": "District of Columbia",    
                        "FL": "Florida",    
                        "GA": "Georgia",    
                        "HI": "Hawaii",    
                        "ID": "Idaho",    
                        "IL": "Illinois",    
                        "IN": "Indiana",    
                        "IA": "Iowa", 
                        "KS": "Kansas",    
                        "KY": "Kentucky",    
                        "LA": "Louisiana",    
                        "ME": "Maine",    
                        "MD": "Maryland",    
                        "MA": "Massachusetts",    
                        "MI": "Michigan",    
                        "MN": "Minnesota", 
                        "MS": "Mississippi",    
                        "MO": "Missouri",    
                        "MT": "Montana",    
                        "NE": "Nebraska",    
                        "NV": "Nevada",    
                        "NH": "New Hampshire",    
                        "NJ": "New Jersey",  
                        "NM": "New Mexico",    
                        "NY": "New York",    
                        "NC": "North Carolina",    
                        "ND": "North Dakota",    
                        "OH": "Ohio",    
                        "OK": "Oklahoma",    
                        "OR": "Oregon",  
                        "PA": "Pennsylvania",    
                        "RI": "Rhode Island",    
                        "SC": "South Carolina",    
                        "SD": "South Dakota",    
                        "TN": "Tennessee",    
                        "TX": "Texas",    
                        "UT": "Utah",  
                        "VT": "Vermont",    
                        "VA": "Virginia",    
                        "WA": "Washington",    
                        "WV": "West Virginia",    
                        "WI": "Wisconsin",    
                        "WY": "Wyoming"
                    }
                    //   console.log(states);
                    var stateHTML='<select class="select-state"><option value="0">Choose a shipping state</option>';
                    
             
                    for (var key in states) {
                        if (states.hasOwnProperty(key)) {
                            var attrValue = states[key];
                            var attrName=key;
                            
                            stateHTML+='<option value="'+attrName+'">'+attrValue+'</option>';
                        }
                       
                    }
                
                    stateHTML+='</select>';
                   
                }else{
                    stateHTML='';
                }
                jQuery(objContainer).prepend('<div class="basket" style="display: none;">' + 
                    '<h3>' + MCG.p.cart.strings.titleBasket + '</h3>' + 
                    '<div class="basket-msg"></div>' + 
                    '<div class="basket-total">Total $<span class="total"></span></div>'+// +stateHTML+ 
                    '<a class="' + MCG.p.cart.strings.cssCheckout + '"   id="btnCheckout">' + MCG.p.cart.strings.titleCheckout + '</a>' + 
                    '</div>');
            }
        }, 
		
        addBasket: function(intProductID, strTitle, txtVariation, dblPrice, dblWeight, boolAddon, intQuantity, intMode, boolUpdateCart) {
            var intFoundIndex			= -1
            var intFound2= -1;
            var arrItemData				= [];
			
            arrItemData['pid']			= intProductID;
            arrItemData['title']		= strTitle;
            arrItemData['variation']	= txtVariation;
            arrItemData['price']		= dblPrice;
            arrItemData['weight']		= dblWeight;
            arrItemData['addon']		= boolAddon;
            arrItemData['quantity']		= intQuantity;
			
            for (var intCounter = 0; intCounter < MCG.p.cart.list.length; intCounter++) { 
                if (MCG.p.cart.list[intCounter].pid == intProductID) {
                    intFoundIndex				= intCounter;
                    break;
                }
            }
             
			
            if ((intFoundIndex == -1) || (intProductID == 80)) {
                var varc=MCG.fn.checkQuant(intProductID ,2);
                for (var i2 = 0; i2 < MCG.p.parents.list.length; i2++) { 
                    console.log(MCG.p.parents.list[i2].eid+' = '+varc);
                    if (MCG.p.parents.list[i2].eid == varc) {
                        intFound2			= i2;
                        var cartIndex=  MCG.p.parents.list[i2].cartindex-1;
                        console.log('found: '+intFound2);
                      
                    }
                }
                if(parseInt(intFound2)>-1){
                   
                  
                    var varc2=MCG.fn.checkQuantIncrement( MCG.p.parents.list[intFound2].pid,intProductID, 1);
                    //  MCG.fn.removeParentList(intFound2);
                    console.log("cartindex is:"+cartIndex)
                    console.log(MCG.p.cart.list);
                    console.log(MCG.p.cart.list[cartIndex]);
                    MCG.fn.removeList(cartIndex);
                    var newi= jQuery('option[value="'+varc2+'"]');
                    if(varc2>0){
                        console.log('incrementing found item, varc2:'+varc2);
                        //  MCG.fn.removeList(cartIndex);
                        arrItemData['pid']			= varc2;
                        arrItemData['title']		=  strTitle;
                        arrItemData['variation']	= newi.attr('attr-variation');
                        arrItemData['price']		= newi.attr('attr-price');
                        arrItemData['weight']		= newi.attr('attr-weight');
                        arrItemData['addon']		= boolAddon;
                        arrItemData['quantity']		= 1;
                     
                        MCG.fn.addList(arrItemData);
                       
                    }else{
                        MCG.fn.addList(arrItemData);
                    }
                }else{
                    console.log('nothing found');
                    MCG.fn.addList(arrItemData);

                }
            } else {
                var varx=MCG.fn.checkQuant(intProductID ,1);
                if(parseInt(varx)>0){
                    console.log('removing item and replacing');
                    MCG.fn.removeList(intFoundIndex);
                    var newi= jQuery('option[value="'+varx+'"]');
                    arrItemData['pid']			= varx;
                    arrItemData['title']		= strTitle;
                    arrItemData['variation']	= newi.attr('attr-variation');
                    arrItemData['price']		= newi.attr('attr-price');
                    arrItemData['weight']		= newi.attr('attr-weight');
                    arrItemData['addon']		= boolAddon;
                    arrItemData['quantity']		= 1;
                    MCG.fn.addList(arrItemData);
                }else{
                    console.log('not removing, checkQuant='+varx);
                    // increment product already in array
                    var intCurQuantity			= parseInt(MCG.p.cart.list[intFoundIndex].quantity);
				
                    if (!isNaN(intCurQuantity)) {
                        MCG.p.cart.list[intFoundIndex].quantity		= intCurQuantity + 1;
                    }
                }
            }
			
            if (boolUpdateCart) {
                MCG.fn.updateBasket(intMode);
            }
        }, 
		
        updateBasket: function(intMode) {
            var arrJSONList			= [];
            var txtCartText			= '';
            var dblTotal			= 0;
            var dblTotalWeight		= 0;
            var dblCurWeight		= 0;
            var dblCurPrice			= 0;
            var intAddonIndex		= 0;
            var intRegProdCounter	= 0;
            var arrCoolerData;
            var intCounter;
			
            MCG.p.cart.list.sort(function(a, b) {
                return a.addon - b.addon;
            })
	
            // show all regular products first
            for (intCounter = 0; intCounter < MCG.p.cart.list.length; intCounter++) { 
                // we only want to show regular products in this loop, because we'll output the addon later in the next loop with it's own HTML/table
                if (MCG.p.cart.list[intCounter].addon == 0) {
                    dblCurPrice			= MCG.p.cart.list[intCounter].price * MCG.p.cart.list[intCounter].quantity;
                    dblTotal			= parseFloat(dblTotal) + parseFloat(dblCurPrice);
			
                    if (txtCartText.length == 0) {
                        // open the products table HTML
                        txtCartText	+=	'<h4 class="products">Products</h4>' + 
                    '<table class="products">' + 
                    '<thead>' + 
                    '<tr>' + 
                    '<th class="title">Title</th>' + 
                    '<th class="qty">Qty</th>' + 
                    '<th class="price">Each</th>' + 
                    '<th class="actions">&nbsp;</th>' + 
                    '</tr>' + 
                    '</thead>' + 
                    '<tbody>';
                    }
		
                    arrCoolerData			= MCG.fn.getCoolerSize(MCG.p.cart.list[intCounter].weight * MCG.p.cart.list[intCounter].quantity, false);
					
                    txtCartText	+=	'<tr>' + 
                    '<td class="title"><strong>' + MCG.p.cart.list[intCounter].title + '</strong></td>' + 
                    '<td class="qty">' + MCG.p.cart.list[intCounter].quantity + '</td>' + 
                    '<td class="price">$' + accounting.formatNumber((parseFloat(MCG.p.cart.list[intCounter].price) + parseFloat(arrCoolerData[1])), 2, ',', '.') + '</td>' + 
                    '<td class="actions" style="height:12px; display:block; overflow:visible"><a href="javascript:void(0);" onclick="MCG.fn.removeList('+ intCounter +')">Remove All</a>' + 
                    '</tr>'+
                    '<tr>' + 
                    '<td colspan="4" class="title2" style="padding-top:0px;padding-bottom:5px"><span>' + MCG.p.cart.list[intCounter].variation + '</span></td>' + 
                    '</tr>';

                    dblCurWeight			= MCG.p.cart.list[intCounter].weight * MCG.p.cart.list[intCounter].quantity;
                    dblTotalWeight			= parseFloat(dblTotalWeight) + parseFloat(dblCurWeight);
                    intRegProdCounter		= intRegProdCounter + MCG.p.cart.list[intCounter].quantity;
                }
				
                // we'll build the cart array in this loop even though we're only showing regular products in this loop. no need for unneccessary code duplication or the creation of a method
                arrJSONList[intCounter]					= {};
                arrJSONList[intCounter]['price']		= MCG.p.cart.list[intCounter].price;
                arrJSONList[intCounter]['pid']			= MCG.p.cart.list[intCounter].pid;
                arrJSONList[intCounter]['title']		= MCG.p.cart.list[intCounter].title;
                arrJSONList[intCounter]['weight']		= MCG.p.cart.list[intCounter].weight;
                arrJSONList[intCounter]['addon']		= MCG.p.cart.list[intCounter].addon;
                arrJSONList[intCounter]['quantity']		= MCG.p.cart.list[intCounter].quantity;
                arrJSONList[intCounter]['variation']	= MCG.p.cart.list[intCounter].variation;
                arrJSONList[intCounter]['index']		= intCounter;
                if(arrJSONList[intCounter]['variation']=='G-25GC' || arrJSONList[intCounter]['variation']=='G-50GC' || arrJSONList[intCounter]['variation']=='G-100GC'){
                    MCG.fn.setGiftCardGlobal();
                }
            }

            // close the products table HTML if we've started it, so we can start the addons table/header
            if (txtCartText.length > 0) {
                txtCartText +=		'</tbody>' + 
            '</table>';
            }
            // show addons
            for (intCounter = 0; intCounter < MCG.p.cart.list.length; intCounter++) { 
                if (MCG.p.cart.list[intCounter].addon > 0) {
                    dblCurPrice			= MCG.p.cart.list[intCounter].price * MCG.p.cart.list[intCounter].quantity;
                    console.log(dblCurPrice);
					
                    dblTotal			= parseFloat(dblTotal) + parseFloat(dblCurPrice);
					
                    txtCartText			+=	'<h4 class="add-on">Selected Add-On</h4>' + 
                    '<p>You are allowed to choose only <u>one</u> add-on per order. If you want to choose a different add-on, you will need to remove the add-on in your basket first before choosing another one.</p>' + 
                    '<table class="add-on">' + 
                    '<thead>' + 
                    '<tr>' + 
                    '<th class="title">Title</th>' + 
                    '<th class="qty">Qty</th>' + 
                    '<th class="price">Each</th>' + 
                    '<th class="actions">&nbsp;</th>' + 
                    '</tr>' + 
                    '</thead>' + 
                    '<tbody>' + 
                    '<tr>' + 
                    '<td class="title"><strong>' + MCG.p.cart.list[intCounter].title + '</strong><span>' + MCG.p.cart.list[intCounter].variation + '</span></td>' + 
                    '<td class="qty">' + MCG.p.cart.list[intCounter].quantity + '</td>' + 
                    '<td class="price">$' + accounting.formatNumber(parseFloat(MCG.p.cart.list[intCounter].price), 2, ',', '.') + '</td>' + 
                    '<td class="actions"><a href="javascript:void(0);" onclick="MCG.fn.removeList(' + intCounter + ')">Remove All</a>' + 
                    '</tr>' + 
                    '</tbody>' + 
                    '</table>';
                    intAddonIndex = intCounter;
                }
            }

            if (intRegProdCounter > 1) {
                // we have more than one product going to the same address, so they will be getting a price break on shipping -- we should tell them that
                txtCartText +=		'<p class="notice">The total shown below reflects savings due to ordering multiple packages to one address.</p>';
            }

            // if all products are removed, lets make sure the addons removes too
            if (MCG.p.cart.list.length == 1) {
                if (MCG.p.cart.list[intAddonIndex].addon > 0) {
                    MCG.fn.removeList(intAddonIndex);
                }
            }
   
            var arrCoolerSize		= MCG.fn.getCoolerSize(dblTotalWeight, 1); 
            var dblCoolerCost		= arrCoolerSize[1];
			
            dblTotalWeight			= dblTotalWeight.toFixed(2);
 
			
            dblTotal				= parseFloat(dblTotal) + parseFloat(dblCoolerCost);
            dblTotal				= dblTotal.toFixed(2);
			
            var objBasketJSON		= JSON.stringify(arrJSONList);
            var objCoolerJSON		= JSON.stringify(MCG.p.cart.coolers);

            //console.log('json string: '+basketJSON);
            if ((MCG.p.cart.mode != 'builder') && (intMode == 0)) {
                jQuery.post(	'/store/index.php', 
                {
                    ajax: 1, 
                    updateCart: objBasketJSON,
                    coolerCost: dblCoolerCost,
                    coolers: objCoolerJSON 
                });
            }
			
            if (MCG.p.cart.list.length == 0) {
                txtCartText			= '<p class="notice empty">You have no items in your cart.</p>';
				
                jQuery('#btnCheckout, .basket-total').hide();
            } else {
                jQuery('#btnCheckout, .basket-total').show();
            }

            jQuery('.basket').show();
            jQuery('.basket-msg').html(txtCartText);

            jQuery('.dev-info .weight').text(dblTotalWeight);
            jQuery('.dev-info .cooler').html(arrCoolerSize[0]);

            jQuery('.basket-total .total').text(dblTotal);
            globalTotal=dblTotal;
        }, 
		
        getCoolerSize: function(dblWeight, boolClear) {
            if (boolClear == 1) {
                MCG.p.cart.coolers			= [];
            }
			
            var strCoolerSize		= '';
            var dblCoolerCost		= 0;
            var dblFlag				= 0;
            var arrCoolerID			= [];
			
            switch (true) {
                case ((dblWeight > 0) && (dblWeight < 12)) :
                    strCoolerSize		= '1x Size1: 1-12lbs';
                    dblCoolerCost		= 15;
                    dblFlag				= dblCoolerCost;
                    break;
					
                case ((dblWeight >= 12) && (dblWeight < 18)) :
                    strCoolerSize		= '1x Size2: 13-18 lbs';
                    dblCoolerCost		= 22;
                    dblFlag				= dblCoolerCost;
                    break;

                case ((dblWeight >= 18) && (dblWeight < 22)) :
                    strCoolerSize		= '1x Size3: 19-22 lbs';
                    dblCoolerCost		= 30;
                    dblFlag				= dblCoolerCost;
                    break;

                case ((dblWeight >= 22) && (dblWeight < 40)) :
                    strCoolerSize		= '1x Size4: > 23 lbs';
                    dblCoolerCost		= 40;
                    dblFlag				= dblCoolerCost;
                    break;

                case (dblWeight >= 40) :
                    strCoolerSize			= '1x Size4: > 23 lbs AND<br/>';
                    dblCoolerCost			= 40;
                    dblFlag					= dblCoolerCost;
					
                    var arrAddlCoolerCost	= MCG.fn.getCoolerSize((dblWeight - 40), 0);
                    dblCoolerCost			= dblCoolerCost + arrAddlCoolerCost[1];
                    strCoolerSize			+= arrAddlCoolerCost[0];
                    break;
            }
			
            switch (dblFlag) {
                case 40 :
                    MCG.fn.addCoolers('size4');
                    break;
					
                case 30 :
                    MCG.fn.addCoolers('size3');
                    break;
				
                case 22 :
                    MCG.fn.addCoolers('size2');
                    break;
					
                case 15 :
                    MCG.fn.addCoolers('size1');
                    break;
            }
			
            var intCoolerCount			= Math.ceil(dblWeight / 40);
            var arrCoolerArray			= [strCoolerSize, dblCoolerCost, intCoolerCount];
			
            return arrCoolerArray;
        }, 
		
        checkout: function() {
            if (MCG.p.cart.mode == 'builder') {
              
                MCG.fn.compileCertificate(); 
                console.log('cert');
            } else {
                console.log('regular cart');
                if(parseFloat(globalTotal)>parseFloat('52.99') && parseFloat(globalTotal) <parseFloat('750') || parseFloat(globalTotal) < parseFloat('750')  && giftcardGlobal ==1){
                    jQuery.post('/store/index.php', 
                    {
                        ajax: 1, 
                        submitCart: 1
                    }, function(objData) { 
                        window.location = '/checkout';
                    });
                }else{ 
                    //alert(globalTotal);
                    if(parseFloat(globalTotal) < parseFloat('52.99') ){
                       
                        alert('Please select more products to meet minimum order requirements.');
                    }
                    if(parseFloat(globalTotal)>parseFloat('750')){
                        alert('Your order has exceeded the limits of our on line ordering system.  You may qualify for special quantity discounts.  Please call the store�816-444-4720 or 888-783-2540 to get a quote on your custom package.');
                    }
                }
            }
        }, 
		
        checkAddon: function() {
           
            var boolAddonFound		= false;
            var intCounter;
			
            for (intCounter = 0; intCounter < MCG.p.cart.list.length; intCounter++) { 
                if (MCG.p.cart.list[intCounter].addon != 0) {
                    boolAddonFound			= true;
                    break;
                }
            }
			
            if (boolAddonFound) {
                return false;
            } else {
                return true;
            }
        }, 
        certShippingPrice:function(){
            var state=jQuery('.redemption-address .state').val();
            var weight = MCG.p.cart.strings.totalWeight; 
            jQuery.get('//estimate-shipping?state='+state+'&weight='+weight+'&cert=1', function(data) {  
                if(data>0){
                    alert('Shipping to this state is: $'+data);
                    MCG.p.cart.strings.shippingFee=data;   
                    jQuery('.shipping-subtotal').html('$'+data);
                    var total=parseFloat(MCG.p.cart.strings.totalPrice)+parseFloat(data);
                    jQuery('.total-cart').html('$'+total.toFixed(2));
                    jQuery('.redeem-validate').hide();
                    jQuery('.after-validate').slideDown();
                }else{
                    alert('Please enter at least a state for redemption address');
                } 
            });
            return data;
        },
        certReview:function(){
            var geocoder = new google.maps.Geocoder();
            jQuery(".after-validate input").css({
                'border':'#000'
            })
            var x=0;
            if(jQuery(".after-validate .billing-address .billingbook").val()=='0'){
                var $this = jQuery('.billing-zip');
                if ($this.val().length == 5) {
                    geocoder.geocode({
                        'address': $this.val()
                    }, function (result, status) {
                        var state = "N/A";
                        var currentState=jQuery('.billing-address .select-state').val();
        
                        //start loop to get state from zip
                        for (var component in result[0]['address_components']) {
                            for (var i in result[0]['address_components'][component]['types']) {
                                if (result[0]['address_components'][component]['types'][i] == "administrative_area_level_1") {
                                    state = result[0]['address_components'][component]['short_name'];
                                    // do stuff with the state here!
                                    if(state!=currentState){
                                        alertt=1;
                                        alert('The billing Zip Code you entered does not match the sate entered');
                                        return false;
                                    }
                              
                        
                                }
                            }
                        }
                      
                    });
                }
                jQuery(".after-validate .billing-address input").each(function() {
                    var val= jQuery(this).val();
                    if(val==''){
                        x=1;
                        jQuery(this).css({
                            'border':'solid 1px red'
                        })
                    }
                });
            }
            if(jQuery(".after-validate .shipping-address .shippingbook").val()=='0'){
                var $this = jQuery('.shipping-zip');
                if ($this.val().length == 5) {
                    geocoder.geocode({
                        'address': $this.val()
                    }, function (result, status) {
                        var state = "N/A";
                        var currentState=jQuery('.shipping-address .select-state').val();
        
                        //start loop to get state from zip
                        for (var component in result[0]['address_components']) {
                            for (var i in result[0]['address_components'][component]['types']) {
                                if (result[0]['address_components'][component]['types'][i] == "administrative_area_level_1") {
                                    state = result[0]['address_components'][component]['short_name'];
                                    // do stuff with the state here!
                                    if(state!=currentState){
                                        alertt=1;
                                        alert('The shipping Zip Code you entered does not match the sate entered');
                                        return false;
                                    }
                                    
                                }
                            }
                        }
                                    
                    });
                }
                jQuery(".after-validate .shipping-adress input").each(function() { 
                    var val= jQuery(this).val();
                    if(val==''){
                        x=1;
                        jQuery(this).css({
                            'border':'solid 1px red'
                        })
                    }
                });
            }
             
             
            
       
            if(x==1){
                alert('Please Fill out all the available fields');
            }else{
                jQuery('.after-validate, .redemption-address').slideUp();  
                jQuery('.order-review').slideDown();
            }
        },
        validateCardInfo:function(){
            var x=0;
            jQuery(".order-review input").css({
                'border':'#000'
            })
            jQuery(".order-review input").each(function() { 
                var val= jQuery(this).val();
                if(val==''){
                    x=1;
                    jQuery(this).css({
                        'border':'solid 1px red'
                    })
                }
            });
            if(x==1){
                alert('Please Fill out all the available fields');
            }else{
                jQuery('.modal-body form').submit();
            }
        },
        compileCertificate: function() {
             //crapy hacks thanks for changing spec two mins before launch, client
            var arrJSONList;
            var dblTotal			= 0;
            var dblTotalWeight		= 0;
            var dblCurPrice			= 0;
            var dblCurWeight		= 0;
            var dblCoolerCost		= 0;
            var strCoolerSize		= '';
            var txtPackage			= '';
            var intCounter;
            var cHTML='<form method="post" action="/store/index.php?submitCertOrder=1"><table class="table table-bordered table-striped"><tr><th>Product</th colspan="2"> </tr>';
	
            for (intCounter = 0; intCounter < MCG.p.cart.list.length; intCounter++) { 
                dblCurPrice				= parseFloat(MCG.p.cart.list[intCounter].price) * parseFloat(MCG.p.cart.list[intCounter].quantity);
                dblCurWeight			= parseFloat(MCG.p.cart.list[intCounter].weight) * parseFloat(MCG.p.cart.list[intCounter].quantity);

                // TODO: Refine this a bit so it's nicer to display back to the customer.
                txtPackage				= '<tr><td colspan="2">'+MCG.p.cart.list[intCounter].variation+' '+MCG.p.cart.list[intCounter].title + ' <input type="hidden" value="'+MCG.p.cart.list[intCounter].pid + '" name="products[]"/> </td>';//<td> ' + dblCurPrice + '  </td></tr> ';
                cHTML+=txtPackage;
                dblTotal				= parseFloat(dblTotal) + parseFloat(dblCurPrice);
                dblTotalWeight			= parseFloat(dblTotalWeight) + parseFloat(dblCurWeight);
            }
            var arrCoolerData		= MCG.fn.getCoolerSize(dblTotalWeight, 1); 
            dblCoolerCost		= arrCoolerData[1];
            dblTotal=dblTotal+dblCoolerCost;	
            var states={
                "AK": "Alaska",    
                "AZ": "Arizona",    
                "AR": "Arkansas",    
                "CA": "California",    
                "CO": "Colorado",    
                "CT": "Connecticut",    
                "DE": "Delaware", 
                "DC": "District of Columbia",    
                "FL": "Florida",    
                "GA": "Georgia",    
                "HI": "Hawaii",    
                "ID": "Idaho",    
                "IL": "Illinois",    
                "IN": "Indiana",    
                "IA": "Iowa", 
                "KS": "Kansas",    
                "KY": "Kentucky",    
                "LA": "Louisiana",    
                "ME": "Maine",    
                "MD": "Maryland",    
                "MA": "Massachusetts",    
                "MI": "Michigan",    
                "MN": "Minnesota", 
                "MS": "Mississippi",    
                "MO": "Missouri",    
                "MT": "Montana",    
                "NE": "Nebraska",    
                "NV": "Nevada",    
                "NH": "New Hampshire",    
                "NJ": "New Jersey",  
                "NM": "New Mexico",    
                "NY": "New York",    
                "NC": "North Carolina",    
                "ND": "North Dakota",    
                "OH": "Ohio",    
                "OK": "Oklahoma",    
                "OR": "Oregon",  
                "PA": "Pennsylvania",    
                "RI": "Rhode Island",    
                "SC": "South Carolina",    
                "SD": "South Dakota",    
                "TN": "Tennessee",    
                "TX": "Texas",    
                "UT": "Utah",  
                "VT": "Vermont",    
                "VA": "Virginia",    
                "WA": "Washington",    
                "WV": "West Virginia",    
                "WI": "Wisconsin",    
                "WY": "Wyoming"
            }
            //   console.log(states);
            var stateHTML='<select name="redemption_state" class="state"><option value="0">Choose a shipping state</option>'; 
            for (var key in states) {
                if (states.hasOwnProperty(key)) {
                    var attrValue = states[key];
                    var attrName=key; 
                    stateHTML+='<option value="'+attrName+'">'+attrValue+'</option>';
                }     
            }
                
            stateHTML+='</select>';
            var stateHTML1='<select  name="billing_state"  class="select-state"><option value="0">Choose a shipping state</option>'; 
            for (var key in states) {
                if (states.hasOwnProperty(key)) {
                    var attrValue = states[key];
                    var attrName=key;
                            
                    stateHTML1+='<option value="'+attrName+'">'+attrValue+'</option>';
                } 
            } 
            stateHTML1+='</select>';
            var stateHTML2='<select name="shipping_state"  class="select-state"><option value="0">Choose a shipping state</option>'; 
            for (var key in states) {
                if (states.hasOwnProperty(key)) {
                    var attrValue = states[key];
                    var attrName=key;
                            
                    stateHTML2+='<option value="'+attrName+'">'+attrValue+'</option>';
                }      
            } 
          
            stateHTML2+='</select>';
            cHTML+='<tr><td  style="text-align:right;font-weight:bold;">Shipping Total<input type="hidden" name="coolers[]" value="'+arrCoolerData[1]+'"></td><td class="shipping-subtotal">TBD</td></tr>';
            cHTML+='<tr><td style="text-align:right;font-weight:bold;">Total</td><td class="total-cart">$'+dblTotal.toFixed(2)+'</td></tr></table>';
            cHTML+='<div class="redemption-address"><h3>Address where Certificate will be redeemed:</h3><div class="addbook"></div><div class="row-fluid"><div class="span6">';
            cHTML+='Adressbook:<br/><label>Full Name <br/><input type="text" name="redemption_fullname"></label>';
            cHTML+='<label>Address <br/><input type="text" name="redemption_address"></label>';
            cHTML+='<label>Address 2 <br/><input type="text" name="redemption_address2"></label></div><div class="span6">';
            cHTML+='<label>City <br/><input type="text" name="redemption_city"></label>';
            cHTML+='<label>State <br/>'+stateHTML+'</label>';
            cHTML+='<label>ZIP <br/><input type="text" name="redemption_zip" class="redemption-zip"></label></div></div>';  
            cHTML+='</div>';
              
            cHTML+='<div class="validate-btn"> ';
            cHTML+='<a href="javascript:void(0);" class="btn btn-primary redeem-validate" onclick=" MCG.fn.certShippingPrice()">Next: Calculate shipping to redemption address</a>';
            cHTML+='</div>';
            cHTML+='<div class="after-validate"  style="display:none;margin-top:20px"><div class="billing-address" style="display:"><h3>Billing Address</h3><div class="addbook"></div><div class="row-fluid"><div class="span6">';
            cHTML+='Adressbook:<br/><label>First Name <br/><input type="text" name="billing_first"></label>';
            cHTML+=' <br/><label>Last Name <br/><input type="text" name="billing_last"></label>';
            cHTML+='<label>Address <br/><input type="text" name="billing_address1"></label>';
            cHTML+='<label>Address 2 <br/><input type="text" name="billing_address2"></label></div><div class="span6">';
            cHTML+='<label>City <br/><input type="text" name="billing_city"></label>';
            cHTML+='<label>State <br/>'+stateHTML1+'</label>';
            cHTML+='<label>ZIP <br/><input class="billing-zip" type="text" name="billing_zip"></label> '; 
            cHTML+='<label>Billing Email<br/><input class="billing-email" type="text" name="billing_email"></label></div>'; 
            
            cHTML+='</div></div>';
              
              
            cHTML+='<div class="shipping-address"><h3>Address where Certificate will be shipped to:</h3><div class="addbook"></div><div class="row-fluid"><div class="span6">';
            cHTML+='Adressbook:<br/><label>Full Name <br/><input type="text" name="shipping_fullname"></label>';
            cHTML+='<label>Address <br/><input type="text" name="shipping_address1"></label>';
            cHTML+='<label>Address 2 <br/><input type="text" name="shipping_address2"></label></div><div class="span6">';
            cHTML+='<label>City <br/><input type="text" name="shipping_city"></label>';
            cHTML+='<label>State <br/>'+stateHTML2+'</label>';
            cHTML+='<label>ZIP <br/><input type="text" class="shipping-zip" name="shipping_zip"></label></div>';   
            cHTML+='</div></div>';
            
            
            cHTML+='<div class="enclosure"><h3>Enclosure Card:</h3> ';
            cHTML+='<textarea style="width:100%" name="enclosure"></textarea>';
            cHTML+='</div>';
            
            
            
            cHTML+='<div class="order-comments"><h3>Order Comments:</h3> ';
            cHTML+='<textarea style="width:100%" name="comment"></textarea>';
            cHTML+='<a class="btn btn-success " onclick="MCG.fn.certReview()" href="javascript:void(0)">Next Step</a></div></div>';
            
            
            
            cHTML+='<div class="order-review"  style="display:none"><h3>Review the order above</h3> <br/>If everything is correct, enter your payment details below and confirm purchase ';
            cHTML+='<div class=" ">';
            cHTML+=' <h2>Credit/Debit Info</h2>';

            cHTML+='<select name="cc_type" class="required" autocomplete="off">';
            cHTML+=' <option onclick="creditcard();" value="Visa">Visa</option>';
            cHTML+='  <option onclick="creditcard();" value="MasterCard">MasterCard</option>';
            cHTML+='   <option onclick="creditcard();" value="Discover">Discover</option>';
            cHTML+='    <option onclick="creditcard();" value="Amex">AMEX</option>';
            cHTML+='    <option onclick="bank();" value="Check">Check</option>';
            cHTML+='     <option onclick="bank();" value="Wire Transfer">Wire Transfer</option>';
            cHTML+='      <option onclick="bank();" value="Certified Funds">Certified Funds</option>';
            cHTML+='    </select>';


            cHTML+='    <table width="100%">';
            cHTML+='    <tbody><tr>';
            cHTML+='<td width="70%"><label>Card # (No Dashes)</label></td>';
            cHTML+='<td width="30%"><label>Security Code</label></td>';
            cHTML+='</tr>';
            cHTML+=' <tr>';
            cHTML+=' <td><input type="text" class="cardnum"   name="card_no" maxlength="19"></td><td><input type="text" name="security" class="seccode" maxlength="4" style="width:75px!important"></td>';
            cHTML+=' </tr>';
            cHTML+='</tbody></table>';

            cHTML+=' <label>Exp Date</label>';
            cHTML+=' <table width="100%">';
            cHTML+=' <tbody><tr>';
            cHTML+=' <td>  <select name="monthExpires" class="exp1" style="width:85%">';
            cHTML+='<option value="" selected="">--Exp Month--</option>';
            cHTML+='<option value="01">(01) January</option> ';
            cHTML+='  <option value="02">(02) February</option> ';
            cHTML+=' <option value="03">(03) March </option>';
            cHTML+='  <option value="04">(04) April </option>';
            cHTML+='   <option value="05">(05) May </option>';
            cHTML+='   <option value="06">(06) June </option>';
            cHTML+='   <option value="07">(07) July </option>';
            cHTML+='   <option value="08">(08) August </option>';
            cHTML+='   <option value="09">(09) September</option>';
            cHTML+='   <option value="10">(10) October</option>';
            cHTML+='   <option value="11">(11) November</option>';
            cHTML+='   <option value="12">(12) December</option> ';
            cHTML+='  </select></td>';
            cHTML+='  <td>  <select name="yearExpires" class="exp2" style="width:85%">';
            cHTML+='     <option value="" selected="">--Exp Year--</option> ';
            cHTML+='      <option value="13">2013</option>';
            cHTML+='       <option value="14">2014</option>';
            cHTML+='  <option value="15">2015</option>';
            cHTML+=' <option value="16">2016</option>';
            cHTML+='  <option value="17">2017</option>';
            cHTML+='   <option value="18">2018</option>';
            cHTML+='   <option value="19">2019</option>';
            cHTML+='    <option value="20">2020</option>';
            cHTML+='    </select></td>';
            cHTML+='   </tr>';
            cHTML+='  </tbody></table> ';
            cHTML+='    </div>';
            cHTML+='';
            cHTML+='';
            cHTML+='<br/><br/><a class="btn btn-primary" href="javascript:void(0)" onClick="MCG.fn.validateCardInfo()">Confirm and Complete Purchase</a></form>';
            arrJSONList				= {};
           
            var weight =dblTotalWeight; 
            MCG.p.cart.strings.totalWeight=dblTotalWeight;
             
            //alert(shippingPrice);
          
			
   
            MCG.p.cart.strings.totalPrice	= dblTotal;
            arrJSONList['price']		= dblTotal;
            arrJSONList['pid']			= 80;
            arrJSONList['title']		= 'Gift Certificate Package';
            arrJSONList['weight']		= 0;
            arrJSONList['addon']		= 0;
            arrJSONList['quantity']		= 1;
            arrJSONList['variation']	= txtPackage;
            arrJSONList['index']		= intCounter;
			
            var basketJSON				= JSON.stringify(arrJSONList);
            console.log('json string: '+basketJSON);
            jQuery('#checkout-modal').modal('show');
            jQuery('#checkout-modal .modal-body').html(cHTML);
            addBooks();
            jQuery(".redemption-address .billingbook").change(function() {
                if(jQuery(this).val()=='0'){
                    jQuery(".redemption-address .row-fluid").slideDown();
                }else{
                    jQuery('.redemption-address .state').val(jQuery('option:selected', this).attr('adminarea'));
                    jQuery(".redemption-address .row-fluid").slideUp();
                    MCG.fn.certShippingPrice();
                } 
            });
            jQuery(".billing-address .billingbook").change(function() {
                if(jQuery(this).val()=='0'){
                    jQuery(".billing-address .row-fluid").slideDown();
                }else{
                    jQuery(".billing-address .row-fluid").slideUp();
                } 
            });
            jQuery(".shipping-address .shippingbook").change(function() {
                if(jQuery(this).val()=='0'){
                    jQuery(".shipping-address .row-fluid").slideDown();
                }else{
                    jQuery(".shipping-address .row-fluid").slideUp();
                } 
            }); 
     
        }, 
		
        redeemCertificate: function() {
            var strCertCode		= jQuery('.r-code').val();
		
            jQuery.post(	'/store/index.php', 
            {
                ajax: 1, 
                redeem: strCertCode
            }, 
            function(objData) {
                if (objData === 'true') {
                    window.location = '/certificate-builder?redeemOrder='+strCertCode;
                } else{
                    alert('Certificate ID invalid or already used');
                }
            });
        }
    }
}

jQuery(document).ready(function() { 

    jQuery.ajaxSetup({
        async: false
    });
	
    MCG.fn.buildBasket();
    jQuery('#btnCheckout').bind('click', function() {    
        MCG.fn.checkout();
 
    });
    // Handler for .ready() called.
    jQuery('.addItem').click(function() {
        var dblWeight		= jQuery(this).parent().find('.selected-item option:selected').attr('attr-weight');
        var strTitle		= jQuery(this).parent().find('h2').text();
        var intProductID	= jQuery(this).parent().find('.selected-item option:selected').val();
        var dblPrice		= jQuery(this).parent().find('.selected-item option:selected').attr('attr-price');
        var txtVariation	= jQuery(this).parent().find('.selected-item option:selected').attr('attr-variation');
		
        // console.log('the pid is:'+pid);
        var boolAddon		= 0;
		
        MCG.fn.addBasket(intProductID, strTitle, txtVariation, dblPrice, dblWeight, boolAddon, 1, 0, true);
    });
    jQuery('.addItemAddOn').click(function() {
          
        if (MCG.fn.checkAddon() == true && MCG.p.cart.list.length > 0) {
            var boolAddon		= jQuery(this).parent().parent().parent().attr('attr-pid');
            var dblWeight		= jQuery(this).parent().find('.selected-item option:selected').attr('attr-weight');
            var strTitle		= jQuery(this).parent().find('h2').text();
            var intProductID	= jQuery(this).parent().find('.selected-item option:selected').val();
            var dblPrice		= jQuery(this).parent().find('.selected-item option:selected').attr('attr-price');
            var txtVariation	= jQuery(this).parent().find('.selected-item option:selected').attr('attr-variation');

            MCG.fn.addBasket(intProductID, strTitle, txtVariation, dblPrice, dblWeight, boolAddon, 1, 0, true);
        } else {
            if (MCG.p.cart.list.length == 0) {
                alert('Addon products are available in combination with a product above, please select an item above before selecting am add on');
            } else {
                alert('You can only pick one Add on per order. If you would like to change your choice, remove the add on from your basket before selecting the correct addon');
            }
        }
    });

    // switch basket to fixed on scroll
    var objBasket			= jQuery('.basket');
	
    if (objBasket.length) {
        jQuery(window).scroll(function() {
            if (MCG.p.basket.positionTop == 0) {
                MCG.p.basket.positionTop	= parseFloat(jQuery('.basket').position().top);
            }
			
            if (jQuery(window).scrollTop() < MCG.p.basket.positionTop) { 
                if (jQuery(objBasket).hasClass('fixed')) {
                    jQuery(objBasket).removeClass('fixed');
                }
            } else {
                if (!jQuery(objBasket).hasClass('fixed')) {
                    jQuery(objBasket).addClass('fixed');
                }
            }
        });
    }
	
// checkout actions
 
});