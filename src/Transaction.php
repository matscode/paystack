<?php
	/**
	 *
	 * Class for Transaction Logic
	 *
	 * @package        Paystack
	 * @category       Source
	 * @author         Michael Akanji <matscode@gmail.com>
	 * @date           2017-06-25
	 * @copyright (c)  2016 - 2017, TECRUM (http://www.tecrum.com)
	 *
	 */

	namespace Matscode\Paystack;


	use Matscode\Paystack\Utility\Text;

	class Transaction extends Base
	{
		public
			$amount = 0,
			$email = null,
			$reference = null,
			$transactionResponse = [];

		private
			$_callbackUrl = null;


		public function __construct( $secretKey = null )
		{
			if ( ! is_null( $secretKey ) ) {
				parent::__construct( $secretKey );
			}
			// set the default resource for current class
			$this
				->setResource( 'transaction' );

			return $this;
		}

		public function initialize( array $data = [], $rawResponse = false )
		{
			// values set via mutator
			$data['callback_url'] = $this->_callbackUrl;

			// override refernce $data['reference'] value
			if ( $this->reference ) {
				$data['reference'] = $this->reference;
			} elseif ( ! isset( $data['reference'] ) ) {
				$this->reference = $data['reference'] = Text::uniqueRef();
			} else {
				$this->reference = $data['reference'];
			}

			// override amount $data['amount'] value
			if ( $this->amount ) {
				$data['amount'] = $this->amount;
			} else {
				// save amount in memory
				$this->amount = $data['amount'];
			}

			// override email $data['email'] value
			if ( ! is_null( $this->email ) ) {
				$data['email'] = $this->email;
			} else {
				// save amount in memory
				$this->email = $data['email'];
			}

			$this->transactionResponse['initialize'] =
				$this
					->setAction( 'initialize' )
					->sendRequest( $data );

			// Initialize a new Obj to save Striped response
			$response                   = new \stdClass();
			$response->authorizationUrl = $this->transactionResponse['initialize']->data->authorization_url;
			$response->reference        = $this->transactionResponse['initialize']->data->reference;

			if ( $rawResponse ) {
				$response =
					$this->transactionResponse['initialize'];
			}

			return $response;
		}

		public function verify( $reference = null )
		{
			// try to guess reference if not set
			if ( is_null( $reference ) ) {
				// guess reference
				if ( isset( $_GET['reference'] ) ) {
					$reference = $_GET['reference'];
				} else {
					throw new \Exception( 'Pass reference code as argument to ' . __FUNCTION__ . ' method, it can not be guessed' );
				}
			}

			$this->transactionResponse['verify'] =
				$this
					->setAction( 'verify', [ $reference ] )
					->sendRequest( [], 'GET' );

			return $this->transactionResponse['verify'];
		}

		/**
		 * @param null $reference
		 *
		 * @return mixed
		 */
		public function isSuccessful( $reference = null )
		{
			// get verify response
			$response = $this->verify( $reference );

			// Initialize as !isSuccessful
			$isSuccessful = false;

			// check if transaction is successful
			if ( is_object( $response->data ) &&
			     $response->status == true &&
			     $response->data->status == 'success'
			) {
				$isSuccessful = true;
			}

			return $isSuccessful;
		}

		/**
		 * @param null $reference
		 *
		 * @return string|null
		 */
		public function getAuthorizationCode( $reference = null )
		{
			$authorizationCode = null;
			// get verify response
			if ( $this->isSuccessful( $reference ) ) {
				$response          = $this->verify( $reference );
				$authorizationCode = $response->data->authorization->authorization_code;
			}

			return $authorizationCode;
		}

		/**
		 * @param $email
		 *
		 * @return $this
		 */
		public function setEmail( $email )
		{
			// setting the email
			$this->email = $email;

			return $this;
		}

		public function getEmail( $email )
		{
			// setting the email
			$this->email = $email;
		}

		/**
		 * @param int $amount
		 *
		 * @todo Allow to set kobo using '.' syntax
		 * @return $this
		 */
		public function setAmount( $amount )
		{
			// setting amount in naira //TODO: Allow to set kobo using '.' syntax
			$this->amount = ( $amount * 100 );

			return $this;
		}

		/**
		 * @return int
		 */
		public function getAmount()
		{
			return $this->amount;
		}

		/**
		 * @param null $reference
		 */
		public function setReference( $reference )
		{
			$this->reference = $reference;
		}

		/**
		 * @param bool $afterInitialize
		 *
		 * @return null
		 *
		 */
		public function getReference( $afterInitialize = false )
		{
			if ( $afterInitialize ) {
				$reference = $this->response->data->reference;
			} else {
				$reference = $this->reference;
			}

			return $reference;
		}

		/**
		 * @param string $callbackUrl
		 *
		 * @return $this
		 */
		public function setCallbackUrl( $callbackUrl )
		{
			$this->_callbackUrl = $callbackUrl;

			return $this;
		}

	}