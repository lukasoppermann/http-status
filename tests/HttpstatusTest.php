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
        $statuses = $csv->setOffset(1)->fetchAssoc(['Value', 'Description']);
        // preapre statuses
        foreach ($statuses as $key => $code) {
            if (trim($code['Description']) !== '' && $code['Description'] !== 'Unassigned' && $code['Description'] !== '(Unused)') {
                $this->statuses[$code['Value']] = $code['Description'];
            }
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
        $Httpstatus = new Httpstatus();

        foreach ($this->statuses as $code => $text) {
            $this->assertSame(
                $code,
                $Httpstatus->code(strtolower($text)),
                'Expected $Httpstatus->code("'.$text.'") to return '.$code
            );
        }
    }

    public function testGetStatusTextCustom()
    {
        $Httpstatus = new Httpstatus([
            200 => 'Works like a charm',
            404 => 'Look somewhere else',
            600 => 'Custom error code',
        ]);

        $this->assertSame($this->statuses[100], $Httpstatus->text(100), 'Expected $Httpstatus->text("100") to return '.$this->statuses[100]);
        $this->assertSame('Works like a charm', $Httpstatus->text(200), 'Expected $Httpstatus->text("200") to return "Works like a charm"');
        $this->assertSame('Look somewhere else', $Httpstatus->text(404), 'Expected $Httpstatus->text("404") to return "Look somewhere else"');
        $this->assertSame('Custom error code', $Httpstatus->text(600), 'Expected $Httpstatus->text("600") to return "Custom error code"');
    }

    public function testConstants()
    {
        $Httpstatus = new Httpstatus();

        foreach ($this->statuses as $code => $text) {
            $this->assertSame($code, constant('Lukasoppermann\Httpstatus\Httpstatus::'.'HTTP_'.strtoupper(str_replace([' ', '-', 'HTTP_'], ['_', '_', ''], $text))));
        }
    }

    /**
     * @expectedException              OutOfBoundsException
     * @expectedExceptionMessageRegExp /Unknown http status code: `\d+`/
     */
    public function testInvalidIndexCode()
    {
        $Httpstatus = new Httpstatus();
        $Httpstatus->text(600);
    }

    /**
     * @expectedException              InvalidArgumentException
     * @expectedExceptionMessageRegExp /The submitted code must be a positive int between \d+ and \d+/
     * @dataProvider                   invalidStatusCode
     */
    public function testInvalidTypeCode()
    {
        $Httpstatus = new Httpstatus();
        $Httpstatus->text('great');
    }

    public function invalidStatusCode()
    {
        return [
            'string' => ['great'],
            'array'  => [[]],
            'min range' => [99],
            'max range' => [1000],
        ];
    }

    /**
     * @expectedException              OutOfBoundsException
     * @expectedExceptionMessageRegExp /No Http status code is associated to `.*`/
     */
    public function testInvalidIndexText()
    {
        $Httpstatus = new Httpstatus();
        $Httpstatus->code('I am a Teapot.');
    }

    /**
     * @expectedException        InvalidArgumentException
     * @expectedExceptionMessage The reason phrase must be a string
     */
    public function testInvalidText()
    {
        $Httpstatus = new Httpstatus();
        $Httpstatus->code(true);
    }

    /**
     * @expectedException              InvalidArgumentException
     * @expectedExceptionMessageRegExp /The collection must be a Traversable object or an array; received `.*`/
     */
    public function testInvalidConstructorCollection()
    {
        new Httpstatus('yo');
    }
}
