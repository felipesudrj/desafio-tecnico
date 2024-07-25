<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;


class BaseTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('base');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('cep_origem')
            ->allowEmptyString('cep_origem');

        $validator
            ->integer('cep_destino')
            ->allowEmptyString('cep_destino');

        $validator
            ->numeric('distancia')
            ->allowEmptyString('distancia');

        $validator
            ->dateTime('created_at')
            ->allowEmptyDateTime('created_at');

        $validator
            ->dateTime('updated_at')
            ->allowEmptyDateTime('updated_at');

        return $validator;
    }

    public function getInfo()
    {
        $query = $this->find('all');

        //RETORNAR APENAS CEP ORIGEM, CEP DESTINO E DISTANCIA

        $query->formatResults(function ($results) {
            return $results->map(function ($row) {
                return [
                    'id' => $row->id,
                    'cep_origem' => $row->cep_origem,
                    'cep_destino' => $row->cep_destino,
                    'distancia' => $row->distancia,
                    'created_at' => $row->created_at->format('d/m/Y H:i:s'),
                    'updated_at' => $row->updated_at->format('d/m/Y H:i:s'),
                ];
            });
        });

        return $query->toArray();
    }


    public function salvar($cep_origem, $cep_destino, $distancia)
    {

        try {
            $base = $this->newEmptyEntity();
            $base->cep_origem = $cep_origem;
            $base->cep_destino = $cep_destino;
            $base->distancia = $distancia;
            $base->created_at = date('Y-m-d H:i:s');
            $base->updated_at = date('Y-m-d H:i:s');
            $this->save($base);
        } catch (\Exception $e) {
            return false;
        }

        return $base;
    }
}
