<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BaseTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BaseTable Test Case
 */
class BaseTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\BaseTable
     */
    protected $Base;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'app.Base',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Base') ? [] : ['className' => BaseTable::class];
        $this->Base = $this->getTableLocator()->get('Base', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Base);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\BaseTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
