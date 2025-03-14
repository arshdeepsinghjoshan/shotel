(function ($) {

    function reloadTables(tables) {
        tables.forEach(tableId => {
            let table = $(tableId).DataTable();
            table.ajax.reload();
        });
    }

    $('#add-pending-payment').click(function (e) {
        e.preventDefault();

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        const form = $(this).closest('form'); // Get the closest form element
        const url = installmentStore; // Get the form's action URL
        const formData = new FormData(form[0]); // Collect form data
        const grindPrice = $('#grindPrice').val(); // Get the product ID from the data attribute
        formData.append('grindPrice', grindPrice ?? 2);
        // Disable button and show a loader
        const submitButton = $(this);
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Submitting...');

        // Send AJAX request
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false, // Important for FormData
            contentType: false, // Important for FormData
            success: function (response) {
                if (response.status === 200) {
                    $('#pending_amount').val(response.remaining_amount); //
                    $('#paid_amount').val(response.paid_amount); //
                    $('#amount').val(''); //
                    let table = $('#installments_table').DataTable();
                    table.ajax.reload();
                }
                handleResponse(response); // Handle success response
            },
            error: function (xhr) {
                const error = xhr.responseJSON?.message || 'An error occurred!';
                handleError(error); // Handle error response

            },
            complete: function () {
                // Enable button and reset text
                submitButton.prop('disabled', false).html('Add Payment');
            }
        });

    });


    $(document).on('click', '#placeOrder', function (e) {
        // this.disabled = true;

        e.preventDefault();
        const user_id = $('#user_id').val();
        sendAjaxRequest({
            url: orderAddUrl, data: {
                user_id: user_id
            }, reloadTable: ['#cart_list', '#cart_checkout', '#order_product_table']
        })
    });

    $(document).on('click', '.deleteCartItem', function (e) {
        e.preventDefault();
        var cartid = JSON.parse(this.getAttribute("data-cartid"));
        const grindPrice = $('#grindPrice').val(); // Get the product ID from the data attribute
        sendAjaxRequest({
            url: cartDeletItemUrl, data: {
                cartid: cartid,
                grindPrice: grindPrice ?? 2,
            }, reloadTable: ['#cart_list', '#cart_checkout', '#order_product_table']
        })
    });


    let keyupTimeout; // Declare a timeout variable

    // let keyupTimeout; // Declare globally

    $(document).on('keyup', '#form1', function (e) {
        clearTimeout(keyupTimeout);

        keyupTimeout = setTimeout(() => {
            // Ensure `data-product` and `data-cartid` exist
            const productData = $(this).data("product");
            const cartid = $(this).data("cartid");

            if (!productData || !cartid) {
                console.error("Missing product or cart ID data.");
                return;
            }

            const product_id = productData?.product?.id || 0;
            const grindPrice = $('#grindPrice').val() || 2; // Default value
            const quantity = $(this).val(); // Get input value

            // Ensure `quantity` is valid
            if (!quantity || isNaN(quantity)) {
                console.error("Invalid quantity");
                return;
            }

            sendAjaxRequest({
                url: cartUpdateQuantityUrl,
                data: {
                    product_id: product_id,
                    quantity: quantity,
                    cartid: cartid,
                    grindPrice: grindPrice,
                },
                reloadTable: ['#cart_list', '#cart_checkout']
            });

        }, 500); // Delay of 500ms to debounce
    });

    $(document).on('keyup', '#grindPrice', function (e) {
        // Clear any existing timeout to debounce
        clearTimeout(keyupTimeout);
        // Set a new timeout for 1 second (1000 ms)
        keyupTimeout = setTimeout(() => {
            // Prevent default action (if needed)
            e.preventDefault();
            const grindPrice = $('#grindPrice').val(); // Get the product ID from the data attribute
            sendAjaxRequest({
                url: cartUpdateGrindPriceUrl, data: {
                    grindPrice: grindPrice ?? 2,
                }, reloadTable: ['#cart_list', '#cart_checkout']
            })
            // Reload the table
        }, 500); // Delay of 1 second

    });


    $(document).on('click', '.changeQuantity', function (e) {
        e.preventDefault();
        var product = JSON.parse(this.getAttribute("data-product"));
        var cartid = JSON.parse(this.getAttribute("data-cartid"));
        var product_id = product?.product?.id || 0;
        var type_id = this.getAttribute("data-type");
        const grindPrice = $('#grindPrice').val(); // Get the product ID from the data attribute
        sendAjaxRequest({
            url: cartChangeQuantityUrl, data: {
                product_id: product_id,
                type_id: type_id,
                cartid: cartid,
                grindPrice: grindPrice,
            }, reloadTable: ['#cart_list', '#cart_checkout']
        })
    });
    $(document).on('click', '#submit-button', function (e) {
        e.preventDefault(); // Prevent the default form submission behavior
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        const form = $(this).closest('form'); // Get the closest form element
        const url = cartCustomUpdateQuantityUrl; // Get the form's action URL
        const formData = new FormData(form[0]); // Collect form data
        const grindPrice = $('#grindPrice').val(); // Get the product ID from the data attribute
        formData.append('grindPrice', grindPrice ?? 2);
        // Disable button and show a loader
        const submitButton = $(this);
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Submitting...');

        // Send AJAX request
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false, // Important for FormData
            contentType: false, // Important for FormData
            success: function (response) {

                handleResponse(response); // Handle success response
            },
            error: function (xhr) {
                const error = xhr.responseJSON?.message || 'An error occurred!';
                handleError(error); // Handle error response

            },
            complete: function () {
                // Enable button and reset text
                submitButton.prop('disabled', false).html('Submit');
            }
        });

        reloadTables(['#cart_list', '#cart_checkout'




        ])
    });
    $(document).on('click', '#add-customer', function (e) {
        e.preventDefault(); // Prevent the default form submission behavior
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        const form = $(this).closest('form'); // Get the closest form element
        const url = userAdd; // Get the form's action URL
        const formData = new FormData(form[0]); // Collect form data
        const grindPrice = $('#grindPrice').val(); // Get the product ID from the data attribute
        formData.append('grindPrice', grindPrice ?? 2);
        // Disable button and show a loader
        const submitButton = $(this);
        submitButton.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Submitting...');

        // Send AJAX request
        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false, // Important for FormData
            contentType: false, // Important for FormData
            success: function (response) {
                $('#user_id').val(response.user_id);
                handleResponse(response); // Handle success response
            },
            error: function (xhr) {
                const error = xhr.responseJSON?.message || 'An error occurred!';
                handleError(error); // Handle error response

            },
            complete: function () {
                // Enable button and reset text
                submitButton.prop('disabled', false).html('Submit');
            }
        });

        reloadTables(['#cart_list', '#cart_checkout'




        ])
    });
    $(document).on('click', '.select-product', function (e) {
        // e.preventDefault();

        const productId = $(this).data('product_id');
        const grindPrice = $('#grindPrice').val();
        const isChecked = $(this).is(':checked');
        let type_id = isChecked ? '1' : '0';

        let $checkbox = $(this);
        $checkbox.prop('disabled', true); // Disable checkbox during API request

        sendAjaxRequest({
            url: cartUrl,
            data: { product_id: productId, type_id: type_id, grindPrice: grindPrice ?? 2 },
            reloadTable: ['#cart_list', '#cart_checkout']
        }).then(() => {
            $checkbox.prop('disabled', false); // Re-enable on success
        }).catch(() => {
            $checkbox.prop('disabled', false); // Re-enable on error
        });
    });

    function handleResponse(response) {
        var toastG = document.getElementById('toastG');
        var toastBody = toastG.querySelector('.toast-body');
        if (response.status === 200) {
            toastG.classList.remove('bg-danger'); // Remove error class if previously set
            toastG.classList.add('bg-success'); // Set success class
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
        toastG.classList.remove('bg-success'); // Remove success class if previously set
        toastG.classList.add('bg-danger'); // Set error class
        // Update toast message
        toastBody.innerText = error;
        // Show toast using Bootstrap's method
        var bsToast = new bootstrap.Toast(toastG);
        bsToast.show();
    }


    function sendAjaxRequest({ url, method = 'POST', data = {}, reloadTable }) {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        const isFormData = data instanceof FormData;

        // Return the AJAX request so that it can be used with `.then()`
        return $.ajax({
            url: url,
            type: method,
            data: data,
            processData: !isFormData,
            contentType: isFormData ? false : 'application/x-www-form-urlencoded; charset=UTF-8',
        }).done(function (response) {

            handleResponse(response);
            reloadTables(reloadTable);
        }).fail(function (xhr) {
            handleResponse(xhr.responseJSON || xhr);
        });
    }








})(jQuery)