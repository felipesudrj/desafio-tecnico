$(document).ready(function() {
    
    // Fetch and display distances on page load
    function fetchDistances() {
        $.ajax({
            url: '/pages/load',
            method: 'GET',
            success: function(response) {
            
               
                let tbody = $("#distance-table-body");
                tbody.empty();
                response.info.forEach(function(distance) {
                    tbody.append(
                        `<tr>
                            <td>${distance.id}</td>
                            <td>${distance.cep_origem}</td>
                            <td>${distance.cep_destino}</td>
                            <td>${distance.distancia}</td>
                            <td>${distance.created_at}</td>
                            <td>${distance.updated_at}</td>
                        </tr>`
                    );
                });
            }
        });
    }

    fetchDistances();

    // Display the Add Distance modal
    $("#btn-add-new").click(function() {
        $("#addDistanceModal").modal('show');
    });

    // Handle the save distance button
    $("#btn-save-distance").click(function() {
        let formData = $("#add-distance-form").serialize();
        var data = $.ajax({
            url: '/pages/calculate',
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    fetchDistances();
                    $("#addDistanceModal").modal('hide');
                } else {
                    alert('Failed to add distance. Please check the CEP values.');
                }
            }
        });
        
        console.log(data);

        //TRATAR ERROR
        if(data.status == 400){
            alert(data.responseJSON.info);
        }
    });

    // Display the Import CSV modal
    $("#btn-import-csv").click(function() {
        $("#importCsvModal").modal('show');
    });

    // Handle the import CSV button
    $("#btn-import-csv-submit").click(function() {
        let formData = new FormData($("#import-csv-form")[0]);
        $.ajax({
            url: 'http://localhost/path/to/your/cakephp/app/distances/import',
            method: 'POST',
            data: formData,
            processData: false, // prevent jQuery from automatically transforming the data into a query string
            contentType: false,
            success: function(response) {
                if (response.success) {
                    fetchDistances();
                    $("#importCsvModal").modal('hide');
                } else {
                    alert('Failed to import CSV.');
                }
            }
        });
    });
});