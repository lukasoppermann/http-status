<?php

namespace Lukasoppermann\Httpstatus\tests;

use League\Csv\Reader;
use PHPUnit\Framework\TestCase;

/**
 * @group formatter
 */
class HttpstatuscodesTest extends TestCase
{
    protected $statuses;

    public function setUp(): void
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
    }

    public function testConstants()
    {
        $prefix = 'Lukasoppermann\Httpstatus\Httpstatuscodes::HTTP_';
        foreach ($this->statuses as $code => $text) {
            $this->assertSame(
                $code,
                constant($prefix.strtoupper(str_replace([' ', '-', 'HTTP_', "'"], ['_', '_', '', ''], $text)))
            );
        }
    }
}
