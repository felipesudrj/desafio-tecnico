<?php

/**
 * Chamar arquivo custom.js
 */

$this->Html->script('/js/custom.js', ['block' => true]);

?>

<div class="container mt-5">
    <div class="row mb-3">
        <div class="col-md-12">
            <h2>CALCULADORA DE DISTANCIA</h2>
            <button id="btn-add-new" class="btn btn-primary float-right">ADICIONAR</button>
            <button id="btn-import-csv" class="btn btn-secondary float-right mr-2">Import CSV</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>CEP Origem</th>
                        <th>CEP Destino</th>
                        <th>Distância (km)</th>
                        <th>Data de Cadastro</th>
                        <th>Data de Alteração</th>
                    </tr>
                </thead>
                <tbody id="distance-table-body">
                    <!-- Dynamic rows will be added here -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Distance Modal -->
    <div class="modal fade" id="addDistanceModal" tabindex="-1" role="dialog" aria-labelledby="addDistanceModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDistanceModalLabel">Add New Distance</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="add-distance-form">
                        <div class="form-group">
                            <label for="cep_origem">CEP Origem</label>
                            <input type="text" class="form-control" id="cep_origem" name="cep_origem" required>
                        </div>
                        <div class="form-group">
                            <label for="cep_destino">CEP Destino</label>
                            <input type="text" class="form-control" id="cep_destino" name="cep_destino" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btn-save-distance">Save changes</button>
                </div>
            </div>
        </div>
    </div>

