<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * BaseFixture
 */
class BaseFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'base';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'cep_origem' => 1,
                'cep_destino' => 1,
                'distancia' => 1,
                'created_at' => '2024-07-22 02:00:49',
                'updated_at' => '2024-07-22 02:00:49',
            ],
        ];
        parent::init();
    }
}
