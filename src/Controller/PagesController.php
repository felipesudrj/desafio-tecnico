<?php

declare(strict_types=1);


namespace App\Controller;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\NotFoundException;
use Cake\Http\Response;
use Cake\View\Exception\MissingTemplateException;
use Exception;

class PagesController extends AppController
{

    private function getCurl($cep)
    {

        $url = "https://brasilapi.com.br/api/cep/v2/{$cep}";

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $response = curl_exec($curl);

        if (curl_errno($curl)) {
            throw new Exception('Erro ao buscar CEP: ' . curl_error($curl));
        }

        $response = json_decode($response, true);

        if (empty($response)) {
            throw new Exception('CEP não encontrado');
        }


        if(isset($response['type'])){
           
            if($response['type'] == 'service_error'){
                throw new Exception('Serviço de CEP fora do ar, tente novamente mais tarde');
            }

        }
        

        return $response;
    }

    private function validateCep($cep)
    {
        try {

            //VERIFICAR SE CEP JÁ ESTÁ EM CACHE
            $coordinates = Cache::read($cep);
            if ($coordinates) {
                return true;
            }

            //TRATAR ERRO DE CONEXÃO DO FILE_GET_CONTENTS
            $response = $this->getCurl($cep);


            if (empty($response)) {
                throw new Exception('CEP não encontrado');
            };

            /**
             * SE NÃO ENCONTRAR AS COORDENADAS, ENTÃO VALIDA PELO CEP GERAL DA CIDADE
             * TRATAR ERRO BUSCANDO A COORDENADA EM OUTRA API (GOOGLE MAPS POR EXEMPLO)
             */
            if (empty($response['location']['coordinates']['latitude']) || empty($response['location']['coordinates']['longitude'])) {
                throw new Exception('Não é possível calcular o frete para este CEP, para mais informações envie um email para (88)8888-8888');
            }



            //PARA PERFORMANCE SALVAR ESSAS INFORMAÇÕES EM CACHE
            $this->saveCoordinates($cep, $response['location']['coordinates']['latitude'], $response['location']['coordinates']['longitude']);
        } catch (\Exception $e) {

            throw new Exception($e->getMessage());

        }

        return $response !== false;
    }

    private function saveCoordinates($cep, $lat, $lon)
    {
        //SALVAR EM CACHE
        Cache::write($cep, ['lat' => $lat, 'lon' => $lon]);
    }


    private function calculateDistance($cepOrigem, $cepDestino)
    {
        try {
            $coordOrigem = $this->getCoordinates($cepOrigem);
            $coordDestino = $this->getCoordinates($cepDestino);

            // Raio da Terra em quilômetros
            $earthRadius = 6371;


            // Certifique-se de que as coordenadas são floats
            $latOrigem = (float)$coordOrigem['lat'];
            $lonOrigem = (float)$coordOrigem['lon'];
            $latDestino = (float)$coordDestino['lat'];
            $lonDestino = (float)$coordDestino['lon'];

            // Conversão de graus para radianos
            $latOrigemRad = deg2rad($latOrigem);
            $lonOrigemRad = deg2rad($lonOrigem);
            $latDestinoRad = deg2rad($latDestino);
            $lonDestinoRad = deg2rad($lonDestino);

            // Diferenças de latitude e longitude
            $latDiff = $latDestinoRad - $latOrigemRad;
            $lonDiff = $lonDestinoRad - $lonOrigemRad;

            // Fórmula de Haversine
            $a = sin($latDiff / 2) * sin($latDiff / 2) +
                cos($latOrigemRad) * cos($latDestinoRad) *
                sin($lonDiff / 2) * sin($lonDiff / 2);

            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

            // Cálculo da distância
            $distance = $earthRadius * $c;

            // Formatar o resultado para duas casas decimais
            return number_format($distance, 2, '.', '');
        } catch (\Exception $e) {
            throw new Exception('Não foi possível calcular a distância: ' . $e->getMessage());
        }
    }


    /**
     * Função resposnsável por buscar as coordenadas, eu poderia pegar esses
     * dados na primeira consulta, ou seja quando faço a validação do cep, porém
     * fazendo isso quebro o principio de responsabilidade única, então optei por
     * criar uma função separada para buscar as coordenadas.
     * 
     * Para melhor desempenho, poderia salvar esses dados em cache, ou seja, salvar
     * em um banco de dados, ou em um arquivo, ou até mesmo em uma variável de sessão
     * por um determinado tempo, para que não seja necessário buscas as informações em vez de fazer a consulta toda vez
     * que for calcular a distância para um cep.
     */
    private function getCoordinates($cep)
    {


        $coordinates = Cache::read($cep);
        if ($coordinates) {
            return $coordinates;
        }


        
        $response = $this->getCurl($cep);
        
        return [
            'lat' => $response['location']['coordinates']['latitude'],
            'lon' => $response['location']['coordinates']['longitude'],
        ];
    }


    public function index()
    {
    }


    public function load()
    {

        try {
            $distancias = $this->getTableLocator()->get('Base')->getInfo();
        } catch (\Exception $e) {
            $this->responseJsonError($e->getMessage());
        }

        $this->responseJson($distancias);
    }


    public function calculate()
    {
        try {


            if ($this->request->is('post')) {
                $data = $this->request->getData();

                if ($this->validateCep($data['cep_origem']) && $this->validateCep($data['cep_destino'])) {

                    $distancia = $this->calculateDistance($data['cep_origem'], $data['cep_destino']);

                    //SALVAR DISTANCIA NO BANCO DE DADOS
                    $this->saveDistance($data['cep_origem'], $data['cep_destino'], $distancia);
                }
            }
        } catch (\Exception $e) {

            return $this->responseJsonError($e->getMessage());
        }

        $this->responseJson(['distancia' => $distancia]);
    }

    private function saveDistance($cepOrigem, $cepDestino, $distancia)
    {
        //SALVAR NO BANCO DE DADOS
        $baseTable = $this->getTableLocator()->get('Base');
        $baseTable->salvar($cepOrigem, $cepDestino, $distancia);
    }
}
