<!-- Javascript Import -->
<script src="https://www.paypal.com/sdk/js?client-id=sb&commit=false"></script>

<!-- PayPal In-Context Checkout script -->
<script type="text/javascript">

    paypal.Buttons({

        // Set your environment
        env: '<?php PAYPAL_ENVIRONMENT ?>',

        // Set style of buttons
        style: {
            layout: 'horizontal',   // horizontal | vertical
            size:   'responsive',   // medium | large | responsive
            shape:  'pill',         // pill | rect
            color:  'gold',         // gold | blue | silver | black,
            fundingicons: false,    // true | false,
            tagline: false          // true | false,
        },

        // Wait for the PayPal button to be clicked
        createOrder: function() {

            let currencySelect = document.getElementById("currency_Code");
            let formData = new FormData();
            formData.append('item_amt', $(".total_price").value);
            formData.append('tax_amt', document.getElementById("tax_amt").value);
            formData.append('handling_fee', document.getElementById("handling_fee").value);
            formData.append('insurance_fee', document.getElementById("insurance_fee").value);
            formData.append('shipping_amt', document.getElementById("shipping_amt").value);
            formData.append('shipping_discount', document.getElementById("shipping_discount").value);
            formData.append('total_amt', $("#total").value);
            formData.append('currency', currencySelect.options[currencySelect.selectedIndex].value);
            formData.append('return_url',  '<?php PAYPAL_CALLBACK?> ?commit=false');
            formData.append('cancel_url', '<?php PAYPAL_CANCEL_URL ?>');

            return fetch(
                '<?php  URL['services']['orderCreate']?>',
                {
                    method: 'POST',
                    body: formData
                }
            ).then(function(response) {
                return response.json();
            }).then(function(resJson) {
                console.log('Order ID: '+ resJson.data.id);
                return resJson.data.id;
            });
        },

        // Wait for the payment to be authorized by the customer
        onApprove: function(data, actions) {
            return fetch(
                '<?php  URL['services']['orderGet'] ?>',
                {
                    method: 'GET'
                }
            ).then(function(res) {
                return res.json();
            }).then(function(res) {
                window.location.href = 'success.php';
            });
        }

    }).render('#paypalCheckoutContainer');

</script>