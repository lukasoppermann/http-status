<?php

namespace Lukasoppermann\Httpstatus\tests;

use Lukasoppermann\Httpstatus\Httpstatus;
use PHPUnit_Framework_TestCase;
use League\Csv\Reader;

/**
 * @group formatter
 */
class HttpstatusTest extends PHPUnit_Framework_TestCase
{
    protected $statuses;

    public function setUp()
    {
        // This file is from https://www.iana.org/assignments/http-status-codes/http-status-codes-1.csv
        // It is a csv of all http codes & texts used for testing here
        $csv = Reader::createFromPath(__DIR__."/data/http-status-codes-1.csv");
        $statuses = $csv->setOffset(1)->fetchAssoc(['Value', 'Description']);
        // preapre statuses
        foreach ($statuses as $key => $code) {
            if (trim($code['Description']) !== "" && $code['Description'] !== 'Unassigned' && $code['Description'] !== '(Unused)') {
                $this->statuses[$code['Value']] = $code['Description'];
            }
        }
    }

    public function testGetStatusText()
    {
        $Httpstatus = new Httpstatus();

        foreach ($this->statuses as $code => $text) {
            $this->assertSame($text, $Httpstatus->text($code), 'Expected $Httpstatus->text('.$code.') to return '.$text);
        }
    }

    public function testGetStatusCode()
    {
        $Httpstatus = new Httpstatus();

        foreach ($this->statuses as $code => $text) {
            $this->assertSame($code, $Httpstatus->code($text), 'Expected $Httpstatus->code("'.$text.'") to return '.$code);
        }
    }

    public function testGetStatusCodeCaseInsensitive()
    {
        $Httpstatus = new Httpstatus();

        foreach ($this->statuses as $code => $text) {
            $this->assertSame($code, $Httpstatus->code(strtolower($text)), 'Expected $Httpstatus->code("'.$text.'") to return '.$code);
        }
    }

    public function testConstants()
    {
        $Httpstatus = new Httpstatus();

        foreach ($this->statuses as $code => $text) {
            $this->assertSame($code, constant('Lukasoppermann\Httpstatus\Httpstatus::'.'HTTP_'.strtoupper(str_replace([' ', '-', 'HTTP_'], ['_', '_', ''], $text))));
        }
    }
    /**
     * @expectedException Exception
     */
    public function testInvalidCode()
    {
        $Httpstatus = new Httpstatus();
        try {
            $Httpstatus->text(99);
            $this->fail("Expected exception with message 'Invalid http status code' not thrown");
        } catch (Exception $e) {
            $this->assertEquals("Invalid http status code", $e->getMessage());
        }
    }
    /**
     * @expectedException Exception
     */
    public function testInvalidText()
    {
        $Httpstatus = new Httpstatus();
        try {
            $Httpstatus->text('I am a Teapot.');
            $this->fail("Expected exception with message 'Invalid http status text' not thrown");
        } catch (Exception $e) {
            $this->assertEquals("Invalid http status text", $e->getMessage());
        }
    }
}
