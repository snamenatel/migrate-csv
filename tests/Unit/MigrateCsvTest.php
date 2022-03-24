<?php

namespace Tests\Unit;

use App\Console\Commands\MigrateCsv;
use PHPUnit\Framework\TestCase;

class MigrateCsvTest extends TestCase
{
    protected MigrateCsv $obj;

    protected function setUp(): void
    {
        $this->obj = new MigrateCsv();
    }

    public function test_clear_strings()
    {
        $result = $this->obj->clearStrings(['"12,Taylor Washington,drolsky@att.net,""""""020"""""""""",Armenia"']);
        $this->assertEquals('12,Taylor Washington,drolsky@att.net,""""""020"""""""""",Armenia', $result[0]);
    }

    public function test_get_rows_columns()
    {
        $result = $this->obj->getRowsColumns([
            '12,Taylor Washington,drolsky@att.net,""""""020"""""""""","""""""""""",,0',
        ]);
        $this->assertEquals([
            '12',
            'Taylor Washington',
            'drolsky@att.net',
            '020',
            '',
            '',
            '0',
        ], $result[0]->values()->toArray());
    }
}
