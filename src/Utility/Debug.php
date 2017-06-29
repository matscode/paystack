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

	namespace Matscode\Paystack\Utility;


	class Debug
	{
		private
		static
			$openTag = '<pre style="overflow: auto; max-height: 70%; max-width: 95%; position: fixed; z-index: 9999; left: 15px; top: 15px; padding: 15px; background-color: #fcfcfc; border: solid 1px #aaa; line-height: 1.1rem;">',
			$closeTag = '</pre>';

		public static function printStr( $value )
		{
			echo self::$openTag .
			     $value .
			     self::$closeTag;
		}

		public static function print_r( $value )
		{
			echo self::$openTag .
			     print_r( $value, true ) .
			     self::$closeTag;
		}

		public static function var_dump( $value )
		{
			echo self::$openTag;
			var_dump( $value );
			echo self::$closeTag;
		}
	}