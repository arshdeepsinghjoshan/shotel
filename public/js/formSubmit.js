
function handleResponse(response) {
    var toastG = document.getElementById('toastG');
    var toastBody = toastG.querySelector('.toast-body');
    if (response.status === 200) {
        toastG.classList.remove('bg-danger'); // Remove error class if previously set
        toastG.classList.add('bg-success'); // Set success class
        // Update toast message
        toastBody.innerText = response.message;
        if (typeof $('#cart_list').DataTable === 'function') {
            $('#cart_list').DataTable().ajax.reload();
        }
        else{
            $('#cart_list').DataTable().ajax.reload();

        }
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
    toastG.classList.remove('bg-success'); // Remove success class if previously set
    toastG.classList.add('bg-danger'); // Set error class
    // Update toast message
    toastBody.innerText = error;
    if (typeof $('#cart_list').DataTable === 'function') {
        $('#cart_list').DataTable().ajax.reload();
    }
    // Show toast using Bootstrap's method
    var bsToast = new bootstrap.Toast(toastG);
    bsToast.show();
}
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('.ajax-form');

    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const submitButton = this.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;

            // Add loader to button
            submitButton.disabled = true;
            submitButton.innerHTML = `<span class="spinner"></span> Loading...`;

            const formData = new FormData(this);
            const actionUrl = this.getAttribute('action');
            const method = this.getAttribute('method') || 'POST';

            fetch(actionUrl, {
                method: method,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;

                    handleResponse(data); // Use the provided function for success
                })
                .catch(error => {
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;

                    handleError(error.message || 'An unexpected error occurred.'); // Use the provided function for error
                });
        });
    });
});