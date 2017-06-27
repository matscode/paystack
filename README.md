# matscode/paystack
###### This package is for communicating with PAYSTACK RESTful API. 
Having other resource point available on PAYSTACK API, Resources like; 
- Transaction
- Customers
- Plans
- Subscription
- Transfers
- Charges
- and many more

Just to name a few, it is only the Transaction Resource that is made available currently in this package. Development is ongoing while releases are Stable. Incase you find a BUG, Please, do be kind to open an issue

## Requirements
- Curl 

## Install

### Via Composer

``` bash
    $ composer require matscode/paystack
```

## Making Transactions/Recieving Payment

### Initialize Transaction


``` php
	use Matscode\Paystack\Transaction;
	use Matscode\Paystack\Utility\Debug; // for Debugging purpose
	use Matscode\Paystack\Utility\Http;
	
	$secretKey = 'sk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

	// creating the transaction object
	$Transaction = new Transaction( $secretKey );
```

Set data/payload/requestBody to post with initialize request. Minimum required data are email and amount.

``` php
	// Set data to post using array
	$data = 
	[
	    'email'  => 'customer@email.com',
	    'amount' => 500000 // amount must be in kobo using this method
    ];
    $response = $Transaction->initialize($data);
```
OR 
``` php
	// Set data to post using this method
	$response =
    		$Transaction
    		    ->setCallbackUrl('http://michaelakanji.com') // to override/set callback_url, it can also be set on your dashboard 
    			->setEmail( 'matscode@gmail.com' )
    			->setAmount( 75000 ) // amount must be in Naira while using this method
    			->initialize();
```

Now do a redirect to payment page (using authorization_url)
<br>
NOTE: Recommended to Debug `$response` or check if authorizationUrl is set, and save your Transaction reference code. useful to verify Transaction status

``` php
	// recommend to save Transaction reference in database and do a redirect
	$reference = $response->reference;
	// redirect
	 Http::redirect($response->authorizationUrl);
```