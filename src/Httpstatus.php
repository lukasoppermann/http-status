<?php

namespace Lukasoppermann\Httpstatus;

use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use OutOfBoundsException;
use RuntimeException;

class Httpstatus implements Countable, IteratorAggregate
{
    /**
     * Allowed range for a valid HTTP status code.
     */
    const MINIMUM = 100;
    const MAXIMUM = 599;

    /**
     * The first digit of the Status-Code defines the class of response.
     */
    const CLASS_INFORMATIONAL = 1;
    const CLASS_SUCCESS = 2;
    const CLASS_REDIRECTION = 3;
    const CLASS_CLIENT_ERROR = 4;
    const CLASS_SERVER_ERROR = 5;


    /**
     * Create a new Httpstatus Instance.
     *
     * @param Traversable|array $statusArray a collection of HTTP status code and
     *                                       their associated reason phrase
     *
     * @throws InvalidArgumentException if the collection is not valid
     */
    public function __construct($statusArray = [])
    {
        $this->setLanguage('en');

        foreach ($this->filterCollection($statusArray) as $code => $text) {
            $this->mergeHttpStatus($code, $text);
        }
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function count()
    {
        return count($this->httpStatus);
    }

    /**
     * {@inheritdoc}
     */
    #[\ReturnTypeWillChange]
    public function getIterator()
    {
        return new ArrayIterator($this->httpStatus);
    }

    /**
     * Filter a Collection array.
     *
     * @param Traversable|array $collection
     *
     * @throws InvalidArgumentException if the collection is not valid
     *
     * @return Traversable|array
     */
    protected function filterCollection($collection)
    {
        if (!$collection instanceof Traversable && !is_array($collection)) {
            throw new InvalidArgumentException('The collection must be a Traversable object or an array');
        }

        return $collection;
    }

    /**
     * Add or Update the HTTP Status array.
     *
     * @param int    $code a HTTP status Code
     * @param string $text a associated reason phrase
     *
     * @throws RuntimeException if the HTTP status code or the reason phrase are invalid
     */
    public function mergeHttpStatus($code, $text)
    {
        $code = $this->filterStatusCode($code);
        $text = $this->filterReasonPhrase($text);
        if ($this->hasReasonPhrase($text) && $this->getStatusCode($text) !== $code) {
            throw new RuntimeException('The submitted reason phrase is already present in the collection');
        }

        $this->httpStatus[$code] = $text;
    }

    /**
     * Filter a HTTP Status code.
     *
     * @param int $code
     *
     * @throws InvalidArgumentException if the HTTP status code is invalid
     *
     * @return int
     */
    protected function filterStatusCode($code)
    {
        $code = filter_var($code, FILTER_VALIDATE_INT, ['options' => [
            'min_range' => self::MINIMUM,
            'max_range' => self::MAXIMUM,
        ]]);
        if (!$code) {
            throw new InvalidArgumentException(
                'The submitted code must be a positive integer between '.self::MINIMUM.' and '.self::MAXIMUM
            );
        }

        return $code;
    }

    /**
     * Filter a Reason Phrase.
     *
     * @param string $text
     *
     * @throws InvalidArgumentException if the reason phrase is not a string
     * @throws InvalidArgumentException if the reason phrase contains carriage return characters
     *
     * @see http://tools.ietf.org/html/rfc2616#section-6.1.1
     *
     * @return string
     */
    protected function filterReasonPhrase($text)
    {
        if (!(is_object($text) && method_exists($text, '__toString')) && !is_string($text)) {
            throw new InvalidArgumentException('The reason phrase must be a string');
        }

        $text = trim($text);
        if (preg_match(',[\r\n],', $text)) {
            throw new InvalidArgumentException('The reason phrase can not contain carriage return characters');
        }

        return $text;
    }

    /**
     * Get the text for a given status code.
     *
     * @param string $statusCode http status code
     *
     * @throws InvalidArgumentException If the requested $statusCode is not valid
     * @throws OutOfBoundsException     If the requested $statusCode is not found
     *
     * @return string Returns text for the given status code
     */
    public function getReasonPhrase($statusCode)
    {
        $statusCode = $this->filterStatusCode($statusCode);

        if (!isset($this->httpStatus[$statusCode])) {
            throw new OutOfBoundsException(sprintf('Unknown http status code: `%s`', $statusCode));
        }

        return $this->httpStatus[$statusCode];
    }

    /**
     * Get the code for a given status text.
     *
     * @param string $statusText http status text
     *
     * @throws InvalidArgumentException If the requested $statusText is not valid
     * @throws OutOfBoundsException     If not status code is found
     *
     * @return string Returns code for the given status text
     */
    public function getStatusCode($statusText)
    {
        $statusText = $this->filterReasonPhrase($statusText);
        $statusCode = $this->fetchStatusCode($statusText);
        if ($statusCode !== false) {
            return $statusCode;
        }

        throw new OutOfBoundsException(sprintf('No Http status code is associated to `%s`', $statusText));
    }

    /**
     * Fetch the status code for a given reason phrase.
     *
     * @param string $text the reason phrase
     *
     * @return mixed
     */
    protected function fetchStatusCode($text)
    {
        return array_search(strtolower($text), array_map('strtolower', $this->httpStatus));
    }

    /**
     * Check if the code exists in a collection.
     *
     * @param int $statusCode http status code
     *
     * @throws InvalidArgumentException If the requested $statusCode is not valid
     *
     * @return bool true|false
     */
    public function hasStatusCode($statusCode)
    {
        try {
            $statusCode = $this->filterStatusCode($statusCode);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        return isset($this->httpStatus[$statusCode]);
    }

    /**
     * Check if the hasReasonPhrase exists in a collection.
     *
     * @param int $statusText http status text
     *
     * @throws InvalidArgumentException If the requested $statusText is not valid
     *
     * @return bool true|false
     */
    public function hasReasonPhrase($statusText)
    {
        try {
            $statusText = $this->filterReasonPhrase($statusText);
        } catch (InvalidArgumentException $e) {
            return false;
        }

        return (bool) $this->fetchStatusCode($statusText);
    }

    /**
     * Determines the response class of a response code.
     *
     * See the `CLASS_` constants for possible return values
     *
     * @param int $statusCode
     *
     * @throws InvalidArgumentException If the requested $statusCode is not valid
     *
     * @return int
     */
    public function getResponseClass($statusCode)
    {
        $responseClass = [
            1 => self::CLASS_INFORMATIONAL,
            2 => self::CLASS_SUCCESS,
            3 => self::CLASS_REDIRECTION,
            4 => self::CLASS_CLIENT_ERROR,
            5 => self::CLASS_SERVER_ERROR,
        ];

        $statusCode = $this->filterStatusCode($statusCode);

        return $responseClass[(int) substr($statusCode, 0, 1)];
    }


    /**
     * Sets the http status code to the choosen language
     *
     * @param string $language
     *
     * @throws InvalidArgumentException If the language is unsupported
     */
    public function setLanguage($language)
    {
        $supportedLanguages = ['en', 'fr'];
        if (!in_array($language, $supportedLanguages)) {
            throw new InvalidArgumentException('Unsupported language '.$language);
        }

        /** @var LanguageInterface $langClass */
        $languageClassNamespace = 'Lukasoppermann\\Httpstatus\\languages\\'.$language;
        $langClass = new $languageClassNamespace();
        $this->httpStatus = $langClass->getHttpStatus();
    }
}
