      <?php
      // Checks template is loading via system, so do not move..
      if (!defined('PARENT') || !isset($this->STRIPE_PARAMS['perishable-key'])) {
        exit;
      }

      // STRIPE API
      ?>
      <script>
      //<![CDATA[
      function mc_Stripe() {
        jQuery(document).ready(function() {
          var stripe = Stripe('<?php echo $this->STRIPE_PARAMS['perishable-key']; ?>');
          jQuery.ajax({
            type     : 'POST',
            url      : '<?php echo $this->BASE_PATH; ?>/?cart-ops=checkout-ops&nav=stripe',
            data     : jQuery('#pform').serialize(),
            cache    : false,
            dataType : 'json',
            success  : function (data) {
              switch(data['msg']) {
                case 'ok':
                  stripe.redirectToCheckout({
                    sessionId : data['s_id']
                  }).then(function (result) {
                    mc_CloseSpinner();
                    mc_showDialog((data['html'] ? data['html'] : data['text'][1]), result.error.message, 'err');
                  });
                  break;
                default:
                  mc_CloseSpinner();
                  mc_showDialog((data['html'] ? data['html'] : data['text'][1]), data['text'][0], 'err');
                  break;
              }
            }
          });
        });
      }
      //]]>
      </script>




