// Initialize Toastr
document.addEventListener('DOMContentLoaded', function() {
    // Show toast if exists
    if (typeof Toastr !== 'undefined' && typeof toastData !== 'undefined') {
        Toastr[toastData.type](toastData.message);
    }

    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// AJAX helper function
function ajaxRequest(url, data, successCallback, method = 'POST') {
    const formData = new FormData();
    for (const key in data) {
        formData.append(key, data[key]);
    }

    fetch(url, {
        method: method,
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (successCallback) successCallback(data);
                if (data.message) {
                    Toastr.success(data.message);
                }
            } else {
                Toastr.error(data.message || 'An error occurred');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Toastr.error('Request failed');
        });
}