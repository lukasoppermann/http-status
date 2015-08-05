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
        // This file is from https://www.iana.org/assignments/http-status-codes/http-status-codes-1.csv
        // It is a csv of all http codes & texts used for testing here
        $csv = Reader::createFromPath(__DIR__.'/data/http-status-codes-1.csv');
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
        $this->assertSame(
            count($this->statuses),
            $this->httpStatus->count());
    }

    public function testIteratorAggregate()
    {
        $this->assertInstanceOf('\IteratorAggregate', $this->httpStatus);

        foreach ($this->httpStatus as $code => $text) {
            $codes[$code] = $text;
        }

        foreach ($this->statuses as $code => $text) {
            $this->assertSame(
                $codes[$code],
                $text
            );
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
            600 => 'Custom error code',
        ];
        $Httpstatus = new Httpstatus($custom);

        $this->assertSame($custom[100], $Httpstatus->getReasonPhrase(100), 'Expected $Httpstatus->getReasonPhrase("100") to return '.$custom[100]);
        $this->assertSame($custom[101], $Httpstatus->getReasonPhrase(101), 'Expected $Httpstatus->getReasonPhrase("101") to return '.$custom[101]);
        $this->assertSame($this->statuses[200], $Httpstatus->getReasonPhrase(200), 'Expected $Httpstatus->getReasonPhrase("200") to return '.$this->statuses[200]);
        $this->assertSame($custom[404], $Httpstatus->getReasonPhrase(404), 'Expected $Httpstatus->getReasonPhrase("404") to return '.$custom[404]);
        $this->assertSame($custom[600], $Httpstatus->getReasonPhrase(600), 'Expected $Httpstatus->getReasonPhrase("600") to return '.$custom[600]);
    }

    public function testConstants()
    {
        $prefix = 'Lukasoppermann\Httpstatus\Httpstatus::HTTP_';
        foreach ($this->statuses as $code => $text) {
            $this->assertSame(
                $code,
                constant($prefix.strtoupper(str_replace([' ', '-', 'HTTP_'], ['_', '_', ''], $text)))
            );
        }
    }

    /**
     * @expectedException              OutOfBoundsException
     * @expectedExceptionMessageRegExp /Unknown http status code: `\d+`/
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
            'string' => ['great'],
            'array'  => [[]],
            'bool'  => [true],
            'min range' => [99],
            'max range' => [1000],
            'standard Object' => [(object)[]],
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
            'int' => [3],
            'array'  => [[]],
            'bool'  => [true],
            'standard Object' => [(object)[]],
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
            600 => 'Custom error code',
        ];
        $Httpstatus = new Httpstatus($custom);

        $this->assertSame(true, $Httpstatus->hasStatusCode(100), 'Expected $Httpstatus->hasStatusCode("100") to return true');
        $this->assertSame(true, $Httpstatus->hasStatusCode(600), 'Expected $Httpstatus->hasStatusCode("600") to return true');
        $this->assertSame(false, $Httpstatus->hasStatusCode(601), 'Expected $Httpstatus->hasStatusCode("601") to return false');
    }

    public function testHasReasonPhrase()
    {
        $custom = [
            600 => 'Custom error code',
        ];
        $Httpstatus = new Httpstatus($custom);

        $this->assertSame(true, $Httpstatus->hasReasonPhrase('Continue'), 'Expected $Httpstatus->hasReasonPhrase("Continue") to return true');
        $this->assertSame(true, $Httpstatus->hasReasonPhrase('Custom error code'), 'Expected $Httpstatus->hasReasonPhrase("Custom error code") to return true');
        $this->assertSame(false, $Httpstatus->hasReasonPhrase('MissingReasonPhrase'), 'Expected $Httpstatus->hasReasonPhrase("MissingReasonPhrase") to return false');
    }

    /**
     * @dataProvider isInformationalProvider
     */
    public function testIsInformational($code, $expected)
    {
        $this->assertSame($expected, $this->httpStatus->isInformational($code));
    }

    public function isInformationalProvider()
    {
        return [
            'included' => [100, true],
            'custom' => [120, true],
            'non included' => [200, false],
        ];
    }

    /**
     * @dataProvider isSuccesfulProvider
     */
    public function testIsSuccesful($code, $expected)
    {
        $this->assertSame($expected, $this->httpStatus->isSuccessful($code));
    }

    public function isSuccesfulProvider()
    {
        return [
            'too low' => [199, false],
            'included' => [200, true],
            'custom' => [220, true],
            'too high' => [300, false],
        ];
    }

    /**
     * @dataProvider isRedirectionProvider
     */
    public function testIsRedirection($code, $expected)
    {
        $this->assertSame($expected, $this->httpStatus->isRedirection($code));
    }

    public function isRedirectionProvider()
    {
        return [
            'too low' => [299, false],
            'included' => [300, true],
            'custom' => [320, true],
            'too high' => [400, false],
        ];
    }

    /**
     * @dataProvider isClientErrorProvider
     */
    public function testIsClientError($code, $expected)
    {
        $this->assertSame($expected, $this->httpStatus->isClientError($code));
    }

    public function isClientErrorProvider()
    {
        return [
            'too low' => [399, false],
            'included' => [400, true],
            'custom' => [460, true],
            'too high' => [500, false],
        ];
    }

    /**
     * @dataProvider isServerErrorProvider
     */
    public function testIsServerError($code, $expected)
    {
        $this->assertSame($expected, $this->httpStatus->isServerError($code));
    }

    public function isServerErrorProvider()
    {
        return [
            'too low' => [399, false],
            'included' => [400, true],
            'custom' => [460, true],
            'too high' => [500, false],
        ];
    }

    /**
     * @dataProvider isCustomProvider
     */
    public function testIsCustom($code, $expected)
    {
        $this->assertSame($expected, $this->httpStatus->isCustom($code));
    }

    public function isCustomProvider()
    {
        return [
            'too low' => [599, false],
            'included' => [600, true],
        ];
    }

    /**
     * @dataProvider isUnusedProvider
     */
    public function testIsUnused($code, $expected)
    {
        $this->assertSame($expected, $this->httpStatus->isUnused($code));
    }

    public function isUnusedProvider()
    {
        return [
            'too low' => [800, false],
            'included' => [306, true],
        ];
    }

    /**
     * @dataProvider isUnassignedProvider
     */
    public function testIsUnassigned($code, $expected)
    {
        $this->assertSame($expected, $this->httpStatus->isUnassigned($code));
    }

    public function isUnassignedProvider()
    {
        return [
            'is assigned' => [100, false],
            'custom informational start range 1' => [103, true],
            'custom informational end range 1' => [199, true],
            'custom successful start range 1' => [209, true],
            'custom successful end range 1' => [225, true],
            'custom successful start range 2' => [227, true],
            'custom successful end range 2' => [299, true],
            'custom redirection start range 1' => [309, true],
            'custom redirection end range 1' => [399, true],
            'custom client error start range 1' => [418, true],
            'custom client error end range 1' => [420, true],
            'custom client error 2' => [427, true],
            'custom client error 3' => [430, true],
            'custom client error start range 4' => [432, true],
            'custom client error end range 4' => [499, true],
            'custom server error 1' => [509, true],
            'custom server error start range 2' => [512, true],
            'custom server error end range 2' => [599, true],
        ];
    }
}
