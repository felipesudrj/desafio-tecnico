<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;


class Base extends Entity
{

    protected $_accessible = [
        'cep_origem' => true,
        'cep_destino' => true,
        'distancia' => true,
        'created_at' => true,
        'updated_at' => true,
    ];
}
