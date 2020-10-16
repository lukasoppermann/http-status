<?php

namespace Lukasoppermann\Httpstatus\tests;

use League\Csv\Reader;
use Lukasoppermann\Httpstatus\Httpstatus;
use PHPUnit_Framework_TestCase;

/**
 * @group formatter
 */
class HttpstatusTest extends PHPUnit_Framework_TestCase
{
    protected $statuses;

    protected $httpStatus;

    public function setUp()
    {
        $language = 'en';

        // This file is from https://www.iana.org/assignments/http-status-codes/http-status-codes-1.csv
        // It is a csv of all http codes & texts used for testing here
        $csv = Reader::createFromPath(__DIR__."/data/http-status-codes-$language.csv");
        $statuses = $csv->setOffset(1)->addFilter(function ($row) {
            if (!isset($row[1])) {
                return false;
            }
            $desc = trim($row[1]);

            return !(empty($desc) || in_array($desc, ['Unassigned', '(Unused)']));
        })->fetchAssoc(['Value', 'Description']);

        // preapre statuses
        foreach ($statuses as $key => $code) {
            $this->statuses[$code['Value']] = $code['Description'];
        }
        $this->httpStatus = new Httpstatus();
    }

    /**
     * @expectedException        RuntimeException
     * @expectedExceptionMessage The submitted reason phrase is already present in the collection
     * @dataProvider             invalidStatusArray
     */
    public function testInvalidCustomTexts($statusArray)
    {
        (new Httpstatus($statusArray));
    }

    public function invalidStatusArray()
    {
        return [
            [[
                100 => 'failed',
                200 => 'failed',
            ]],
            [[
                100 => 'failed',
                300 => 'Failed',
            ]],
            [[
                100 => 'failed',
                400 => 'FAILED',
            ]],
            [[
                101 => 'Continue',
            ]],
            [[
                101 => 'CONTINUE',
            ]],
        ];
    }

    public function testCountable()
    {
        $this->assertInstanceOf('\Countable', $this->httpStatus);
        $this->assertSame(count($this->statuses), $this->httpStatus->count());
    }

    public function testIteratorAggregate()
    {
        $this->assertInstanceOf('\IteratorAggregate', $this->httpStatus);
        foreach ($this->httpStatus as $code => $text) {
            $this->assertSame($this->statuses[$code], $text);
        }
    }

    public function testGetStatusText()
    {
        foreach ($this->statuses as $code => $text) {
            $this->assertSame(
                $text,
                $this->httpStatus->getReasonPhrase($code),
                'Expected $Httpstatus->getReasonPhrase('.$code.') to return '.$text
            );
        }
    }

    public function testGetStatusCode()
    {
        foreach ($this->statuses as $code => $text) {
            $this->assertSame(
                $code,
                $this->httpStatus->getStatusCode($text),
                'Expected $Httpstatus->getStatusCode("'.$text.'") to return '.$code
            );
        }
    }

    public function testGetStatusCodeCaseInsensitive()
    {
        foreach ($this->statuses as $code => $text) {
            $this->assertSame(
                $code,
                $this->httpStatus->getStatusCode(strtolower($text)),
                'Expected $Httpstatus->getStatusCode("'.$text.'") to return '.$code
            );
        }
    }

    public function testGetStatusTextCustom()
    {
        $custom = [
            100 => 'New Continue',
            101 => 'Continue',
            404 => 'Look somewhere else',
            404 => 'Look somewhere else', // duplicate intended for testing
        ];
        $Httpstatus = new Httpstatus($custom);

        $this->assertSame($custom[100], $Httpstatus->getReasonPhrase(100), 'Expected $Httpstatus->getReasonPhrase("100") to return '.$custom[100]);
        $this->assertSame($custom[101], $Httpstatus->getReasonPhrase(101), 'Expected $Httpstatus->getReasonPhrase("101") to return '.$custom[101]);
        $this->assertSame($this->statuses[200], $Httpstatus->getReasonPhrase(200), 'Expected $Httpstatus->getReasonPhrase("200") to return '.$this->statuses[200]);
        $this->assertSame($custom[404], $Httpstatus->getReasonPhrase(404), 'Expected $Httpstatus->getReasonPhrase("404") to return '.$custom[404]);
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The reason phrase can not contain carriage return characters
     */
    public function testInvalidReasonPhraseWithCarriageReturnCharacter()
    {
        $reasonPhrase = 'Hello There'.PHP_EOL.'How Are you!!';
        (new Httpstatus())->mergeHttpStatus(404, $reasonPhrase);
    }

    /**
     * @expectedException              OutOfBoundsException
     * @expectedExceptionMessageRegExp /Unknown http status code: `\d+`/
     */
    public function testNonExistentIndexCode()
    {
        (new Httpstatus())->getReasonPhrase(499);
    }

    /**
     * @expectedException              InvalidArgumentException
     * @expectedExceptionMessageRegExp /The submitted code must be a positive integer between \d+ and \d+/
     */
    public function testInvalidIndexCode()
    {
        (new Httpstatus())->getReasonPhrase(600);
    }

    /**
     * @expectedException              InvalidArgumentException
     * @expectedExceptionMessageRegExp /The submitted code must be a positive integer between \d+ and \d+/
     * @dataProvider                   invalidStatusCode
     */
    public function testInvalidTypeCode($code)
    {
        (new Httpstatus())->getReasonPhrase($code);
    }

    public function invalidStatusCode()
    {
        return [
            'string'          => ['great'],
            'array'           => [[]],
            'bool'            => [true],
            'min range'       => [99],
            'max range'       => [1000],
            'standard Object' => [(object) []],
        ];
    }

    /**
     * @expectedException              OutOfBoundsException
     * @expectedExceptionMessageRegExp /No Http status code is associated to `.*`/
     */
    public function testInvalidIndexText()
    {
        (new Httpstatus())->getStatusCode('I am a Teapot.');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The reason phrase must be a string
     * @dataProvider             invalidReasonPhrase
     */
    public function testInvalidText($text)
    {
        (new Httpstatus())->getStatusCode($text);
    }

    public function invalidReasonPhrase()
    {
        return [
            'int'             => [3],
            'array'           => [[]],
            'bool'            => [true],
            'standard Object' => [(object) []],
        ];
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The collection must be a Traversable object or an array
     */
    public function testInvalidConstructorCollection()
    {
        new Httpstatus('yo');
    }

    public function testHasStatusCode()
    {
        $custom = [
            498 => 'Custom error code',
        ];
        $Httpstatus = new Httpstatus($custom);

        $this->assertSame(true, $Httpstatus->hasStatusCode(100), 'Expected $Httpstatus->hasStatusCode("100") to return true');
        $this->assertSame(true, $Httpstatus->hasStatusCode(498), 'Expected $Httpstatus->hasStatusCode("498") to return true');
        $this->assertSame(false, $Httpstatus->hasStatusCode(499), 'Expected $Httpstatus->hasStatusCode("499") to return false');

        // Outside of normal range
        $this->assertSame(false, $Httpstatus->hasStatusCode(0), 'Expected $Httpstatus->hasStatusCode("0") to return false');
        $this->assertSame(false, $Httpstatus->hasStatusCode(600), 'Expected $Httpstatus->hasStatusCode("600") to return false');
    }

    public function testHasReasonPhrase()
    {
        $custom = [
            498 => 'Custom error code',
        ];
        $Httpstatus = new Httpstatus($custom);

        $this->assertSame(true, $Httpstatus->hasReasonPhrase('Continue'), 'Expected $Httpstatus->hasReasonPhrase("Continue") to return true');
        $this->assertSame(true, $Httpstatus->hasReasonPhrase('Custom error code'), 'Expected $Httpstatus->hasReasonPhrase("Custom error code") to return true');
        $this->assertSame(false, $Httpstatus->hasReasonPhrase('MissingReasonPhrase'), 'Expected $Httpstatus->hasReasonPhrase("MissingReasonPhrase") to return false');

        // Invalid phrases
        $this->assertSame(false, $Httpstatus->hasReasonPhrase(false), 'Expected $Httpstatus->hasReasonPhrase(false) to return false');
        $this->assertSame(false, $Httpstatus->hasReasonPhrase(0), 'Expected $Httpstatus->hasReasonPhrase(0) to return false');
        $this->assertSame(false, $Httpstatus->hasReasonPhrase([]), 'Expected $Httpstatus->hasReasonPhrase([]) to return false');
        $this->assertSame(false, $Httpstatus->hasReasonPhrase("a\nb"), 'Expected $Httpstatus->hasReasonPhrase("a\nb") to return false');
    }

    /**
     * @param int $expectedClass
     * @param int $statusCode
     *
     * @dataProvider responseClasses
     */
    public function testGetResponseClass($expectedClass, $statusCode)
    {
        $this->assertSame($expectedClass, $this->httpStatus->getResponseClass($statusCode));
    }

    /**
     * @return array
     */
    public function responseClasses()
    {
        $Httpstatuscodes = $this->getMock('Lukasoppermann\Httpstatus\Httpstatuscodes');

        return [
            [Httpstatus::CLASS_INFORMATIONAL, $Httpstatuscodes::HTTP_CONTINUE],
            [Httpstatus::CLASS_INFORMATIONAL, $Httpstatuscodes::HTTP_SWITCHING_PROTOCOLS],
            [Httpstatus::CLASS_SUCCESS, $Httpstatuscodes::HTTP_OK],
            [Httpstatus::CLASS_SUCCESS, $Httpstatuscodes::HTTP_PARTIAL_CONTENT],
            [Httpstatus::CLASS_REDIRECTION, $Httpstatuscodes::HTTP_MULTIPLE_CHOICES],
            [Httpstatus::CLASS_REDIRECTION, $Httpstatuscodes::HTTP_MOVED_PERMANENTLY],
            [Httpstatus::CLASS_CLIENT_ERROR, $Httpstatuscodes::HTTP_BAD_REQUEST],
            [Httpstatus::CLASS_CLIENT_ERROR, $Httpstatuscodes::HTTP_NOT_FOUND],
            [Httpstatus::CLASS_SERVER_ERROR, $Httpstatuscodes::HTTP_INTERNAL_SERVER_ERROR],
            [Httpstatus::CLASS_SERVER_ERROR, $Httpstatuscodes::HTTP_GATEWAY_TIMEOUT],
        ];
    }

    /**
     * @expectedException InvalidArgumentException
     * @dataProvider      invalidResponseCodes
     */
    public function testGetResponseClassForInvalidCodes($statusCode)
    {
        $this->httpStatus->getResponseClass($statusCode);
    }

    /**
     * @return array
     */
    public function invalidResponseCodes()
    {
        return [
            [0],
            [000],
            [600],
            ['Not Found'],
        ];
    }
}
