<?php

/**
 * @copyright  Frederic G. Østby
 * @license    http://www.makoframework.com/license
 */

namespace mako\security\crypto\adapters;

/**
 * Base encrypter adapter.
 *
 * @author  Frederic G. Østby
 */

abstract class Encrypter
{
	//---------------------------------------------
	// Class properties
	//---------------------------------------------

	/**
	 * Derivation hash.
	 * 
	 * @var string
	 */

	const DERIVATION_HASH = 'sha256';

	/**
	 * Derivation iterations.
	 * 
	 * @var int
	 */

	const DERIVATION_ITERATIONS = 1024;

	//---------------------------------------------
	// Class constructor, destructor etc ...
	//---------------------------------------------

	// Nothing here

	//---------------------------------------------
	// Class methods
	//---------------------------------------------

	/**
	 * Generate a PBKDF2 key derivation of a supplied key.
	 * 
	 * @access  protected
	 * @param   string     $key      The key to derive
	 * @param   string     $salt     The salt
	 * @param   string     $keySize  The desired key size
	 * @return  string
	 */

	protected function deriveKey($key, $salt, $keySize)
	{
		if(function_exists('hash_pbkdf2'))
		{
			return hash_pbkdf2(static::DERIVATION_HASH, $key, $salt, static::DERIVATION_ITERATIONS, $keySize, true);
		}
		else
		{
			$derivedKey = '';

			for($block = 1; $block <= $keySize; $block++)
			{
				$ib = $h = hash_hmac(static::DERIVATION_HASH, $salt . pack('N', $block), $key, true);

				for($i = 1; $i < static::DERIVATION_ITERATIONS; $i++)
				{
					$ib ^= ($h = hash_hmac(static::DERIVATION_HASH, $h, $key, true));
				}

				$derivedKey .= $ib;
			}

			return substr($derivedKey, 0, $keySize);
		}
	}
}