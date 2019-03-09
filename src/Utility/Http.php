<?php
	/**
	 *
	 * Description
	 *
	 * @package        Paystack
	 * @category       Source
	 * @author         Michael Akanji <matscode@gmail.com>
	 * @date           2017-06-27
	 *
	 */

	namespace Matscode\Paystack\Utility;

	class Http
	{
		public static function redirect( $location, $replace = true, $httpResponseCode = null )
		{
			// do a redirect
			header( 'Location: ' . $location, $replace, $httpResponseCode );
		}

	}
