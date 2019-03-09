<?php
	/**
	 *
	 * Description
	 *
	 * @package        Paystack
	 * @category       Source
	 * @author         Michael Akanji <matscode@gmail.com>
	 * @date           2017-06-27
	 * @copyright (c)  2016 - 2017, TECRUM (http://www.tecrum.com)
	 *
	 */
	require_once "../vendor/autoload.php";

	use Matscode\Paystack\Transaction;
	use Matscode\Paystack\Utility\Debug;

	$secretKey = 'sk_test_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';

	// creating the transaction object
	$Transaction = new Transaction( $secretKey );

	// transaction can be verified by doing manual check on the response Obj
	/*
	$response = $Transaction->verify();

	Debug::print_r( $response);*/

	// OR
	$result = $Transaction->isSuccessful();

	/*
	Debug::print_r( $result);
	*/

	// To check if verified amount is the expected amount before giving value to customer

	$result = $Transaction->amountEquals(5000); // amount in kobo

	Debug::printStr( $result );


	// Getting AuthorizationCode
	/*
	$authorizationCode = $Transaction->getAuthorizationCode();

	Debug::print_r( $authorizationCode);
	*/
