
$(document).ready(function () {
    // Set up CSRF token for AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Event listener for state change
    $(document).on('change', '.state-change', function(e) {

        handleStateChange(this);
    });
    $(".open-pending-payment-modal").click(function (e) {
        e.preventDefault();
        $("#customerModal").modal("show");
    });
 

    async function handleStateChange(element) {
        const unique_id = element.value;
        const model_id = $(element).data('id');
        const model_type = $(element).data('modeltype');

        try {
            const res = await updateState(unique_id, model_id, model_type);
            handleResponse(res);
        } catch (error) {
            handleError(error);
        }
    }

    async function updateState(unique_id, model_id, model_type) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: $('#app').data('state-change-url'),
                type: 'POST',
                data: {
                    model_type: model_type,
                    model_id: model_id,
                    attribute: 'state_id', // Replace with your actual attribute
                    workflow: unique_id, // Assuming unique_id is the workflow state
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    resolve(response);
                },
                error: function (xhr, status, error) {
                    reject(xhr.responseText);
                }
            });
        });
    }

    function handleResponse(response) {
        var toastG = document.getElementById('toastG');
        var toastBody = toastG.querySelector('.toast-body');

        if (response.status === 200) {
            toastG.classList.remove('bg-danger'); // Remove error class if previously set
            toastG.classList.add('bg-success');   // Set success class

            // Update toast message
            toastBody.innerText = response.message;

            // Show toast using Bootstrap's method
            var bsToast = new bootstrap.Toast(toastG);
            bsToast.show();
        } else {
            handleError(response.message);
        }
    }


    function handleError(error) {
        var toastG = document.getElementById('toastG');
        var toastBody = toastG.querySelector('.toast-body');

        toastG.classList.remove('bg-success');  // Remove success class if previously set
        toastG.classList.add('bg-danger');       // Set error class

        // Update toast message
        toastBody.innerText = error;

        // Show toast using Bootstrap's method
        var bsToast = new bootstrap.Toast(toastG);
        bsToast.show();
    }
});
