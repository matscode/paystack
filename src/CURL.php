<?php
	/**
	 *
	 * Description
	 *
	 * @package        Paystack
	 * @category       Source
	 * @author         Michael Akanji <matscode@gmail.com>
	 * @date           2017-06-25
	 * @copyright (c)  2016 - 2017, TECRUM (http://www.tecrum.com)
	 *
	 */

	namespace Matscode\Paystack;

	class CURL
	{
		private
			$_url,
			$_curl,
			$_requestWithPost = false,
			$_allowedReqMethodForPost =
			[
				'POST',
				'UPDATE',
				'PUT',
				'PATCH',
			],
			$_errorCode,
			$_errorMessage;

		public
			$requestMethod,
			$requestHeader = [];

		const
			USER_AGENT = 'PHP CURL/1.0 (@matscode)';


		public function __construct( $url, $requestMethod = null )
		{
			if ( ! extension_loaded( 'curl' ) ) {
				throw new \ErrorException( 'CURL Extension not loaded, Install libcurl php extension' );
			}

			//initialize CUrl
			$this->_url = $url;
			$this->_curlInit( $this->_url );

			//set requestMethod property
			$this->requestMethod = $requestMethod;

			//set the default request method to 'POST'
			if ( ! is_null( $this->requestMethod ) ) {
				$this->setRequestMethod( $this->requestMethod );
			}

			return $this;
		}

		private function _curlInit( $url )
		{
			$this->_curl = curl_init();

			$this->setOption( CURLOPT_USERAGENT, self::USER_AGENT )
			     ->setOption( CURLOPT_URL, $url )
				// return response
				 ->setOption( CURLOPT_RETURNTRANSFER, true );

			// turn off caching
			$this->setRequestHeader( 'Cache-Control', 'no-cache' );

			return $this;
		}

		public function setOption( $option, $value )
		{
			// Set curl option
			curl_setopt( $this->_curl, $option, $value );

			return $this;
		}

		public function setRequestMethod( $requestMethod )
		{
			switch ( $requestMethod ) {
				case 'GET' :
					$this->setOption( CURLOPT_HTTPGET, true );
					break;
				case 'UPDATE' :
					$this->setOption( CURLOPT_CUSTOMREQUEST, 'UPDATE' );
					break;
				case 'PUT' :
					$this->setOption( CURLOPT_CUSTOMREQUEST, 'PUT' );
					break;
				case 'PATCH' :
					$this->setOption( CURLOPT_CUSTOMREQUEST, 'PATCH' );
					break;
				case 'DELETE' :
					$this->setOption( CURLOPT_CUSTOMREQUEST, 'DELETE' );
					break;
				case 'POST':
					$this->doPostRequest();

				default:
					$this->setOption( CURLOPT_POST, true );
					break;
			}

			return $this;
		}


		/**
		 * @param null $qStringArray
		 *
		 * @return string
		 */
		public function getUrl( $qStringArray = null )
		{
			if ( ! is_null( $qStringArray ) &&
			     ( is_array( $qStringArray ) || is_object( $qStringArray ) )
			) {
				$this->_url .= '?' . http_build_query( $qStringArray );
			}

			return $this->_url;
		}

		/**
		 * @param string $url
		 *
		 * @return $this
		 */
		public function setUrl(
			$url
		) {
			$this->setOption( CURLOPT_URL, $url );
			$this->_url = $url;

			return $this;
		}

		public function doPostRequest()
		{
			if ( in_array( $this->requestMethod, $this->_allowedReqMethodForPost ) ) {
				$this->_requestWithPost = true;
			}

			return $this;
		}

		public function run( $data, $as = 'urlencoded', $closeCurl = false ) // urlencoded | json | form-data
		{
			if ( $this->_requestWithPost ) {
				//make a post request
				switch ( $as ) {
					case 'json':
						if ( is_array( $data ) ) {
							$data = json_encode( $data );
							$this->setRequestHeader( 'Content-Type', 'application/json' );
						} else {
							throw new \ErrorException( 'Data argument passed to the run method must be of datatype Array when posting as JSON' );
						}
						break;
					case 'form-data':
						if ( is_array( $data ) || is_object( $data ) ) {
							$this->setRequestHeader( 'Content-Type', 'multipart/form-data' );
						} else {
							throw new \ErrorException( 'Data argument passed to the run method must be of datatype Array or Object when postiing as FORM-DATA' );
						}
						break;
					case 'urlenconded':
						// convert data to queryString - Native
						$data = http_build_query( $data );
					// continue to default
					default:
						$this->setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
						break;
				}

				$this->setOption( CURLOPT_POSTFIELDS, $data );  //Post Fields
			} else {
				//make a built query string and reset CURLOPT_URL
				$this->setOption( CURLOPT_URL, $this->getUrl( $data ) );
			}

			// execute curl
			$response = curl_exec( $this->_curl );
			// save error details in memory
			$this->_errorCode    = curl_errno( $this->_curl );
			$this->_errorMessage = curl_error( $this->_curl );

			if ( $closeCurl ) {
				// close curl connection by default
				if ( is_resource( $this->_curl ) ) {
					curl_close( $this->_curl );
				}
			}

			// return response from endpoint
			return $response;
		}

		/**
		 * @param $key
		 * @param $value
		 *
		 * @return $this
		 */
		public function setRequestHeader( $key, $value = null )
		{
			if ( ! is_array( $key ) ) {
				// assist with capitalizing http header keys
				$headers[] = ucwords( $key ) . ': ' . $value;
			} else {
				$headers = $key;
			}

			// merge requestHeader to base header
			$this->requestHeader = array_merge( $headers, $this->requestHeader );

			$this->setOption( CURLOPT_HTTPHEADER, $this->requestHeader );

			return $this;
		}


		public function getErrorCode()
		{
			return $this->_errorCode;
		}

		public function getErrorMessage()
		{
			return $this->_errorMessage;
		}

		/**
		 * @return array
		 */
		public function getRequestHeader()
		{
			return $this->requestHeader;
		}

	}