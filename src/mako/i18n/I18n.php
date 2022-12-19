<?php

/**
 * @copyright Frederic G. Østby
 * @license   http://www.makoframework.com/license
 */

namespace mako\i18n;

use mako\i18n\exceptions\I18nException;
use mako\i18n\loaders\LoaderInterface;
use mako\utility\Arr;

use function explode;
use function is_string;
use function localeconv;
use function number_format;
use function preg_replace_callback;
use function stripos;
use function strpos;
use function vsprintf;

/**
 * Internationalization class.
 */
class I18n
{
	/**
	 * Regex that matches pluralization tags.
	 *
	 * @var string
	 */
	protected const PLURALIZATION_TAG_REGEX = '/\<pluralize:([0-9]+)\>(\w*)\<\/pluralize\>/iu';

	/**
	 * Regex that matches number tags.
	 *
	 * @var string
	 */
	protected const NUMBER_TAG_REGEX = '/\<number(:([0-9]+)(,(.)(,(.))?)?)?\>([0-9-.e]*)\<\/number\>/iu';

	/**
	 * Loaded language strings.
	 *
	 * @var array
	 */
	protected $strings = [];

	/**
	 * Loaded language inflections.
	 *
	 * @var array
	 */
	protected $inflections = [];

	/**
	 * Constructor.
	 *
	 * @param \mako\i18n\loaders\LoaderInterface $loader   Loader instance
	 * @param string                             $language Default language pack name
	 */
	public function __construct(
		protected LoaderInterface $loader,
		protected string $language
	)
	{}

	/**
	 * Returns the language loader.
	 *
	 * @return \mako\i18n\loaders\LoaderInterface
	 */
	public function getLoader(): LoaderInterface
	{
		return $this->loader;
	}

	/**
	 * Gets the current language.
	 *
	 * @return string
	 */
	public function getLanguage(): string
	{
		return $this->language;
	}

	/**
	 * Sets the current language.
	 *
	 * @param string $language Name of the language pack
	 */
	public function setLanguage(string $language): void
	{
		$this->language = $language;
	}

	/**
	 * Loads inflection closure and rules.
	 *
	 * @param string $language Name of the language pack
	 */
	protected function loadInflection(string $language): void
	{
		$this->inflections[$language] = $this->loader->loadInflection($language);
	}

	/**
	 * Returns the plural form of a noun.
	 *
	 * @param  string      $word     Noun to pluralize
	 * @param  int|null    $count    Number of nouns
	 * @param  string|null $language Language rules to use for pluralization
	 * @return string
	 */
	public function pluralize(string $word, ?int $count = null, ?string $language = null): string
	{
		$language ??= $this->language;

		if(!isset($this->inflections[$language]))
		{
			$this->loadInflection($language);
		}

		if(empty($this->inflections[$language]))
		{
			throw new I18nException(vsprintf('The [ %s ] language pack does not include any inflection rules.', [$language]));
		}

		$pluralizer = $this->inflections[$language]['pluralize'];

		return $pluralizer($word, (int) $count, $this->inflections[$language]['rules']);
	}

	/**
	 * Format number according to locale or desired format.
	 *
	 * @param  float       $number             Number to format
	 * @param  int         $decimals           Number of decimals
	 * @param  string|null $decimalPoint       Decimal point
	 * @param  string|null $thousandsSeparator Thousands separator
	 * @return string
	 */
	public function number(float $number, int $decimals = 0, ?string $decimalPoint = null, ?string $thousandsSeparator = null): string
	{
		static $defaults;

		if(empty($defaults))
		{
			$localeconv = localeconv();

			$defaults =
			[
				'decimal'   => $localeconv['decimal_point'] ?: '.',
				'thousands' => $localeconv['thousands_sep'] ?: ',',
			];
		}

		return number_format($number, $decimals, ($decimalPoint ?: $defaults['decimal']), ($thousandsSeparator ?: $defaults['thousands']));
	}

	/**
	 * Parses the language key.
	 *
	 * @param  string $key Language key
	 * @return array
	 */
	protected function parseKey(string $key): array
	{
		return strpos($key, '.') === false ? [$key, null] : explode('.', $key, 2);
	}

	/**
	 * Loads all strings for the language.
	 *
	 * @param string $language Name of the language pack
	 * @param string $file     File from which we are loading the strings
	 */
	protected function loadStrings(string $language, string $file): void
	{
		$this->strings[$language][$file] = $this->loader->loadStrings($language, $file);
	}

	/**
	 * Returns all strings from the chosen language file.
	 *
	 * @param  string $language Name of the language pack
	 * @param  string $file     File from which we are getting the strings
	 * @return array
	 */
	protected function getStrings(string $language, string $file): array
	{
		if(!isset($this->strings[$language][$file]))
		{
			$this->loadStrings($language, $file);
		}

		return $this->strings[$language][$file];
	}

	/**
	 * Returns TRUE if the string exists and FALSE if not.
	 *
	 * @param  string      $key      String to translate
	 * @param  string|null $language Name of the language pack
	 * @return bool
	 */
	public function has(string $key, ?string $language = null): bool
	{
		[$file, $string] = $this->parseKey($key);

		if($string === null)
		{
			return false;
		}

		$strings = $this->getStrings($language ?? $this->language, $file);

		return Arr::has($strings, $string) && is_string(Arr::get($strings, $string));
	}

	/**
	 * Pluralize words between pluralization tags.
	 *
	 * @param  string $string String to parse
	 * @return string
	 */
	protected function parsePluralizationTags(string $string): string
	{
		return preg_replace_callback(static::PLURALIZATION_TAG_REGEX, fn ($matches) => $this->pluralize($matches[2], (int) $matches[1]), $string);
	}

	/**
	 * Format numbers between number tags.
	 *
	 * @param  string $string String to parse
	 * @return string
	 */
	protected function parseNumberTags(string $string): string
	{
		return preg_replace_callback(static::NUMBER_TAG_REGEX, fn ($matches) => $this->number((float) $matches[7], ($matches[2] ?: 0), $matches[4], $matches[6]), $string);
	}

	/**
	 * Parses tags.
	 *
	 * @param  string $string String to parse
	 * @return string
	 */
	protected function parseTags(string $string): string
	{
		if(stripos($string, '</pluralize>') !== false)
		{
			$string = $this->parsePluralizationTags($string);
		}

		if(stripos($string, '</number>') !== false)
		{
			$string = $this->parseNumberTags($string);
		}

		return $string;
	}

	/**
	 * Returns the chosen string from the current language.
	 *
	 * @param  string      $key      String to translate
	 * @param  array       $vars     Array of values to replace in the translated text
	 * @param  string|null $language Name of the language pack
	 * @return string
	 */
	public function get(string $key, array $vars = [], ?string $language = null): string
	{
		[$file, $string] = $this->parseKey($key);

		if($string === null)
		{
			return $key;
		}

		$string = Arr::get($this->getStrings($language ?? $this->language, $file), $string, $key);

		if(!empty($vars))
		{
			$string = vsprintf($string, $vars);

			if(stripos($string, '</') !== false)
			{
				$string = $this->parseTags($string);
			}
		}

		return $string;
	}
}
