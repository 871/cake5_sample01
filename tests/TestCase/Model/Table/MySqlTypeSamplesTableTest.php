<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\MySqlTypeSamplesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\MySqlTypeSamplesTable Test Case
 */
class MySqlTypeSamplesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\MySqlTypeSamplesTable
     */
    protected $MySqlTypeSamples;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.MySqlTypeSamples',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('MySqlTypeSamples') ? [] : ['className' => MySqlTypeSamplesTable::class];
        $this->MySqlTypeSamples = $this->getTableLocator()->get('MySqlTypeSamples', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->MySqlTypeSamples);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\MySqlTypeSamplesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
