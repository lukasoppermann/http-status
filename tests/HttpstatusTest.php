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
                $this->httpStatus->text($code),
                'Expected $Httpstatus->text('.$code.') to return '.$text
            );
        }
    }

    public function testGetStatusCode()
    {
        foreach ($this->statuses as $code => $text) {
            $this->assertSame(
                $code,
                $this->httpStatus->code($text),
                'Expected $Httpstatus->code("'.$text.'") to return '.$code
            );
        }
    }

    public function testGetStatusCodeCaseInsensitive()
    {
        foreach ($this->statuses as $code => $text) {
            $this->assertSame(
                $code,
                $this->httpStatus->code(strtolower($text)),
                'Expected $Httpstatus->code("'.$text.'") to return '.$code
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

        $this->assertSame($this->statuses[100], $Httpstatus->text(100), 'Expected $Httpstatus->text("100") to return '.$this->statuses[100]);
        $this->assertSame($custom[404], $Httpstatus->text(404), 'Expected $Httpstatus->text("404") to return '.$custom[404]);
        $this->assertSame($custom[600], $Httpstatus->text(600), 'Expected $Httpstatus->text("600") to return '.$custom[600]);
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
        (new Httpstatus())->text(600);
    }

    /**
     * @expectedException              InvalidArgumentException
     * @expectedExceptionMessageRegExp /The submitted code must be a positive integer between \d+ and \d+/
     * @dataProvider                   invalidStatusCode
     */
    public function testInvalidTypeCode($code)
    {
        (new Httpstatus())->text($code);
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
        (new Httpstatus())->code('I am a Teapot.');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The reason phrase must be a string
     * @dataProvider             invalidReasonPhrase
     */
    public function testInvalidText($text)
    {
        (new Httpstatus())->code($text);
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
}
