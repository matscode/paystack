# matscode/paystack
###### This package is for communicating with PAYSTACK RESTful API. [Paystack](https://paystack.com/)
Having other resource point available on PAYSTACK API, Resources like; 
- Transaction
- Customers
- Plans
- Subscription
- Transfers
- Charges
- and many more

Just to name a few, it is only the Transaction Resource that is made available currently in this package. Development is ongoing while releases are Stable. Incase you find a BUG/Security Issue, Please, do be kind to open an issue or email [matscode(at)Gmail(dot)Com](mailto://matscode@gmail.com).
<br>
**PS**: This library also works for you if you use paystack inline. You just have to use the `verify()` method, passing in your transaction `$reference|$token|$id` is compulsory.

## Requirements
- Curl 

## Install

### Via Composer

``` bash
$ composer require matscode/paystack
```
If you use a Framework, check your documentation for how vendor packages are autoloaded else Add this to the top of your source file;

``` php
require_once __DIR__ . "/vendor/autoload.php";
```

## Making Transactions/Recieving Payment

### Starting Up Paystack Transaction

``` php
use Matscode\Paystack\Transaction;
use Matscode\Paystack\Utility\Debug; // for Debugging purpose
use Matscode\Paystack\Utility\Http;

$secretKey = 'sk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

// creating the transaction object
$Transaction = new Transaction( $secretKey );
```

### Initializing Transaction

Set data/payload/requestBody to post with initialize request. Minimum required data are email and amount.

``` php
// Set data to post using array
$data = 
[
    'email'  => 'customer@email.com',
    'amount' => 500000 // amount is treated in kobo using this method
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
            ->setAmount( 75000 ) // amount is treated in Naira while using this method
            ->initialize();
```
If you want to get the 200OK raw Object as it is sent by Paystack, Set the 2nd argument of the `initialize()` to `true`, example below
``` php
// Set data to post using this method
$response =
        $Transaction 
            ->setEmail( 'matscode@gmail.com' )
            ->setAmount( 75000 ) // amount is treated in Naira while using this method
            ->initialize([], true);
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
Using a Framework? It is recommended you use the reverse routing/redirection functions provided by your Framework


### Verifying Transaction
This part would live in your callback file i.e `callback.php` or `whatsoever_you_name.php`
<br>
It is also imperative that you create Transaction Obj once more.
<br>
This method would return the Transaction Obj but `false` if saved `$reference` is not passed in as argument and also cant be guessed. Using `verify()` would require you do a manual check on the response Obj
``` php
$response = $Transaction->verify();
// Debuging the $response
Debug::print_r( $response);
```
OR
``` php
// This method does the check for you and return `(bool) true|false` 
$response = $Transaction->isSuccessful();
```
The two methods above try to guess your Transaction `$reference` but it is highly recommended you pass the Transaction `$reference` as an argument on the method as follows
``` php
// This method does the check for you and return `(bool) true|false`
$response = $Transaction->isSuccessful($reference);
```
Moreso, you can also compare if amount paid by customer is the amount expected. This method only works after calling `verify()` or `isSuccessful()` in the same script. It is recommended to do this if you use paystack inline to initialize the transaction.
``` php
$amountExpected = 5000; // amount must be in kobo
// returns `(bool) true|false`
$Transaction->amountEquals($amountExpected);
```
Now you can process Customer Valuable.
<br>
<br>
You might wanna save Transaction `$authorizationCode` for the current customer subsequent Transaction but not a nessecity. It would only counts to future updates of this package or if you choose to extend the package.
``` php
// returns Auth_xxxxxxx 
$response = $Transaction->authorizationCode($reference); // can also guess Transaction $reference
```

## Hire Me
I am available on contract if you need help integrating paystack into your Business Website or something.
<br>
Contact me via my email above - [michaelakanji.com](http://michaelakanji.com)

## Contributions
If you seem to understand the architecture, you are welcome to fork and pull else you can wait a bit more till when i provide convention documentation.

## Licence
GNU GPLV3