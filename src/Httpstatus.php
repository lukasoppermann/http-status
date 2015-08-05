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
     * Allowed range for a valid HTTP status code
     */
    const MINIMUM = 100;
    const MAXIMUM = 999;

    /**
     * Every standard HTTP status code as a constant
     */
    const HTTP_CONTINUE = 100;
    const HTTP_SWITCHING_PROTOCOLS = 101;
    const HTTP_PROCESSING = 102;            // RFC2518
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_NON_AUTHORITATIVE_INFORMATION = 203;
    const HTTP_NO_CONTENT = 204;
    const HTTP_RESET_CONTENT = 205;
    const HTTP_PARTIAL_CONTENT = 206;
    const HTTP_MULTI_STATUS = 207;          // RFC4918
    const HTTP_ALREADY_REPORTED = 208;      // RFC5842
    const HTTP_IM_USED = 226;               // RFC3229
    const HTTP_MULTIPLE_CHOICES = 300;
    const HTTP_MOVED_PERMANENTLY = 301;
    const HTTP_FOUND = 302;
    const HTTP_SEE_OTHER = 303;
    const HTTP_NOT_MODIFIED = 304;
    const HTTP_USE_PROXY = 305;
    const HTTP_RESERVED = 306;
    const HTTP_TEMPORARY_REDIRECT = 307;
    const HTTP_PERMANENT_REDIRECT = 308;  // RFC7238
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE = 406;
    const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;
    const HTTP_REQUEST_TIMEOUT = 408;
    const HTTP_CONFLICT = 409;
    const HTTP_GONE = 410;
    const HTTP_LENGTH_REQUIRED = 411;
    const HTTP_PRECONDITION_FAILED = 412;
    const HTTP_PAYLOAD_TOO_LARGE = 413;
    const HTTP_URI_TOO_LONG = 414;
    const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    const HTTP_RANGE_NOT_SATISFIABLE = 416;
    const HTTP_EXPECTATION_FAILED = 417;
    const HTTP_MISDIRECTED_REQUEST = 421;
    const HTTP_UNPROCESSABLE_ENTITY = 422;                                        // RFC4918
    const HTTP_LOCKED = 423;                                                      // RFC4918
    const HTTP_FAILED_DEPENDENCY = 424;                                           // RFC4918
    const HTTP_RESERVED_FOR_WEBDAV_ADVANCED_COLLECTIONS_EXPIRED_PROPOSAL = 425;   // RFC2817
    const HTTP_UPGRADE_REQUIRED = 426;                                            // RFC2817
    const HTTP_PRECONDITION_REQUIRED = 428;                                       // RFC6585
    const HTTP_TOO_MANY_REQUESTS = 429;                                           // RFC6585
    const HTTP_REQUEST_HEADER_FIELDS_TOO_LARGE = 431;                             // RFC6585
    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;
    const HTTP_BAD_GATEWAY = 502;
    const HTTP_SERVICE_UNAVAILABLE = 503;
    const HTTP_GATEWAY_TIMEOUT = 504;
    const HTTP_VERSION_NOT_SUPPORTED = 505;
    const HTTP_VARIANT_ALSO_NEGOTIATES = 506;                                     // RFC2295
    const HTTP_INSUFFICIENT_STORAGE = 507;                                        // RFC4918
    const HTTP_LOOP_DETECTED = 508;                                               // RFC5842
    const HTTP_NOT_EXTENDED = 510;                                                // RFC2774
    const HTTP_NETWORK_AUTHENTICATION_REQUIRED = 511;                             // RFC6585

    protected $httpStatus = [
      100 => 'Continue',
      101 => 'Switching Protocols',
      102 => 'Processing',
      200 => 'OK',
      201 => 'Created',
      202 => 'Accepted',
      203 => 'Non-Authoritative Information',
      204 => 'No Content',
      205 => 'Reset Content',
      206 => 'Partial Content',
      207 => 'Multi-Status',
      208 => 'Already Reported',
      226 => 'IM Used',
      300 => 'Multiple Choices',
      301 => 'Moved Permanently',
      302 => 'Found',
      303 => 'See Other',
      304 => 'Not Modified',
      305 => 'Use Proxy',
      307 => 'Temporary Redirect',
      308 => 'Permanent Redirect',
      400 => 'Bad Request',
      401 => 'Unauthorized',
      402 => 'Payment Required',
      403 => 'Forbidden',
      404 => 'Not Found',
      405 => 'Method Not Allowed',
      406 => 'Not Acceptable',
      407 => 'Proxy Authentication Required',
      408 => 'Request Timeout',
      409 => 'Conflict',
      410 => 'Gone',
      411 => 'Length Required',
      412 => 'Precondition Failed',
      413 => 'Payload Too Large',
      414 => 'URI Too Long',
      415 => 'Unsupported Media Type',
      416 => 'Range Not Satisfiable',
      417 => 'Expectation Failed',
      421 => 'Misdirected Request',
      422 => 'Unprocessable Entity',
      423 => 'Locked',
      424 => 'Failed Dependency',
      426 => 'Upgrade Required',
      428 => 'Precondition Required',
      429 => 'Too Many Requests',
      431 => 'Request Header Fields Too Large',
      500 => 'Internal Server Error',
      501 => 'Not Implemented',
      502 => 'Bad Gateway',
      503 => 'Service Unavailable',
      504 => 'Gateway Timeout',
      505 => 'HTTP Version Not Supported',
      506 => 'Variant Also Negotiates',
      507 => 'Insufficient Storage',
      508 => 'Loop Detected',
      510 => 'Not Extended',
      511 => 'Network Authentication Required',
    ];

    /**
     * Create a new Httpstatus Instance
     *
     * @param Traversable|array $statusArray a collection of HTTP status code and
     *                                       their associated reason phrase
     *
     * @throws InvalidArgumentException if the collection is not valid
     */
    public function __construct($statusArray = [])
    {
        foreach ($this->filterCollection($statusArray) as $code => $text) {
            $this->mergeHttpStatus($code, $text);
        }
    }

    public function count()
    {
        return count($this->httpStatus);
    }

    public function getIterator()
    {
        return new ArrayIterator($this->httpStatus);
    }
    /**
     * Filter a Collection array
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
     * Add or Update the HTTP Status array
     *
     * @param int    $code a HTTP status Code
     * @param string $text a associated reason phrase
     *
     * @throws InvalidArgumentException if the HTTP status code or the reason phrase are invalid
     */
    public function mergeHttpStatus($code, $text)
    {
        $code = $this->filterHttpStatusCode($code);
        $text = $this->filterReasonPhrase($text);
        if ($this->hasReasonPhrase($text) && $this->getStatusCode($text) !== $code) {
            throw new RuntimeException('The submitted reason phrase is already present in the collection');
        }

        $this->httpStatus[$code] = $text;
    }

    /**
     * Filter a HTTP Status code
     *
     * @param int $code
     *
     * @throws InvalidArgumentException if the HTTP status code is invalid
     *
     * @return int
     */
    protected function filterHttpStatusCode($code)
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
     * Filter a Reason Phrase
     *
     * @param string $text
     *
     * @throws InvalidArgumentException if the reason phrase is invalid
     *
     * @return string
     */
    protected function filterReasonPhrase($text)
    {
        if ((is_object($text) && method_exists($text, '__toString')) || is_string($text)) {
            return trim($text);
        }

        throw new InvalidArgumentException('The reason phrase must be a string');
    }

    /**
     * Get the text for a given status code
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
        $statusCode = $this->filterHttpStatusCode($statusCode);

        if (!isset($this->httpStatus[$statusCode])) {
            throw new OutOfBoundsException(sprintf('Unknown http status code: `%s`', $statusCode));
        }

        return $this->httpStatus[$statusCode];
    }

    /**
     * Get the code for a given status text
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
        $statusCode = array_search(strtolower($statusText), array_map('strtolower', $this->httpStatus));
        if ($statusCode !== false) {
            return $statusCode;
        }

        throw new OutOfBoundsException(sprintf('No Http status code is associated to `%s`', $statusText));
    }
    /**
     * Check if the code exists in a collection
     *
     * @param int $statusCode http status code
     *
     * @throws InvalidArgumentException If the requested $statusCode is not valid
     *
     * @return bool true|false
     */
    public function hasStatusCode($statusCode)
    {
        $statusCode = $this->filterHttpStatusCode($statusCode);

        if (!isset($this->httpStatus[$statusCode])) {
            return false;
        }

        return true;
    }

    /**
     * Check if the hasReasonPhrase exists in a collection
     *
     * @param int $statusText http status text
     *
     * @throws InvalidArgumentException If the requested $statusText is not valid
     *
     * @return bool true|false
     */
    public function hasReasonPhrase($statusText)
    {
        $statusText = $this->filterReasonPhrase($statusText);

        if (!array_search(strtolower($statusText), array_map('strtolower', $this->httpStatus))) {
            return false;
        }

        return true;
    }
}
