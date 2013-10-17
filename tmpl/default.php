<?php // no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//include the Stripe PHP library
require_once(JPATH_ROOT . '/libraries/stripe/lib/Stripe.php');
require('includes/config.inc.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$errmsg = '';
	$success = '';
	if (isset($_POST['stripeToken'])) {
		$token = $_POST['stripeToken'];
		try {
			Stripe::setApiKey(STRIPE_PRIVATE_KEY);
			// Create a Customer
			$customer = Stripe_Customer::create(array(
			  "card" => $token,
			  "description" => "payinguser@example.com")
			);
			// Charge the Customer
			$temp_amount = (int)$amount;
			$charge = Stripe_Charge::create(array(
			  "amount" => $temp_amount, // product price in cents
			  "currency" => $currency,
			  "customer" => $customer->id)
			);
			
			// Save the customer ID in your database so you can use it later
			//saveStripeCustomerId($user, $customer->id);
			
			// Check that the charge was made:
			if ($charge->paid == true) {
				$success = "<div class='alert alert-success'>
								<button type='button' class='close' data-dismiss='alert'>&times;</button>
								Thank you. Your payment has been processed.
							</div>";
			} else { // 	
				echo '<div alert alert-error>The payment system rejected the transaction.</div>';
			}
			
		} catch(Stripe_CardError $e) {
		  // The card has been declined
		  $body = $e->getJsonBody();
		  $err  = $body['error'];

		  print('Status:' . $e->getHttpStatus() . "\n");
		  print('Type:' . $err['type'] . "\n");
		  print('Code:' . $err['code'] . "\n");
		  print('Param:' . $err['param'] . "\n");
		  print('Message:' . $err['message'] . "\n");
		  $errmsg ="<div class='alert alert-error'>
						<button type='button' class='close' data-dismiss='alert'>&times;</button>
						Your payment could not be processed and you have not been charged. The payment system rejected the transaction. You can try again or use another card. Error code x001.
					</div>";
		  
		} catch (Stripe_InvalidRequestError $e) {
		  // Invalid parameters were supplied to Stripe's API
		  $errmsg ="<div class='alert alert-error'>
						<button type='button' class='close' data-dismiss='alert'>&times;</button>
						Your payment could not be processed and you have not been charged. The payment system rejected the transaction. Error code x002.
					</div>";
		} catch (Stripe_AuthenticationError $e) {
		  // Authentication with Stripe's API failed
		  // (maybe you changed API keys recently)
		  $errmsg ="<div class='alert alert-error'>
						<button type='button' class='close' data-dismiss='alert'>&times;</button>
						Sorry, something went wrong. Please contact the website administrator. Error code x003.
					</div>";
		} catch (Stripe_ApiConnectionError $e) {
		  // Network communication with Stripe failed
		  $errmsg ="<div class='alert alert-error'>
						<button type='button' class='close' data-dismiss='alert'>&times;</button>
						Your payment could not be processed and you have not been charged. Please contact the website administrator. Error code x004.
					</div>";
		} catch (Stripe_Error $e) {
		  // Display a very generic error to the user, and maybe send
		  // yourself an email
		  $errmsg ="<div class='alert alert-error'>
						<button type='button' class='close' data-dismiss='alert'>&times;</button>
						Sorry, something went wrong. Please contact the website administrator. Error code x005.
					</div>";
		} catch (Exception $e) {
		  // Something else happened, completely unrelated to Stripe
		  $errmsg ="<div class='alert alert-error'>
						<button type='button' class='close' data-dismiss='alert'>&times;</button>
						Sorry, something went wrong. Please contact the website administrator. Error code x006.
					</div>";
		}
	}
}
?>
<form action="" method="POST" id="payment-form">  
<script
  src="https://checkout.stripe.com/v2/checkout.js" class="stripe-button"
  data-key="<?php echo STRIPE_PUBLIC_KEY ?>"
  data-amount="<?php echo $amount ?>"
  data-name="<?php echo $itemname ?>"
  data-description="<?php echo $itemdesc ?>"
  data-currency="<?php echo $currency ?>"
  >
  </script>
</form>
<span class="payment-errors"><?php echo $errmsg ?></span>
<span class="payment-success"><?php echo $success ?></span>
