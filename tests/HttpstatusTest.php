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
            404 => 'Look somewhere else',
            600 => 'Custom error code',
        ];
        $Httpstatus = new Httpstatus($custom);

        $this->assertSame($this->statuses[100], $Httpstatus->getReasonPhrase(100), 'Expected $Httpstatus->getReasonPhrase("100") to return '.$this->statuses[100]);
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
}
