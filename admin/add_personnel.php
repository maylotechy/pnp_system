<?php
require_once '../config/db_connection.php';
require_once  '../config/check_admin.php';

$rank_options = [
    'Pat' => 'Pat - Patrolman/Patrolwoman',
    'PEM' => 'PEM - Police Executive Master Sergeant',
    'PMSg' => 'PMSg - Police Master Sergeant',
    'PCMS' => 'PCMS - Police Chief Master Sergeant',
    'PSMS' => 'PSMS - Police Senior Master Sergeant',
    'PEMS' => 'PEMS - Police Executive Master Sergeant',
    'PCpt' => 'PCpt - Police Captain',
    'PMaj' => 'PMaj - Police Major',
    'PLtCol' => 'PLtCol - Police Lieutenant Colonel',
    'PCol' => 'PCol - Police Colonel',
    'PBGen' => 'PBGen - Police Brigadier General',
    'PMGen' => 'PMGen - Police Major General',
    'PLtGen' => 'PLtGen - Police Lieutenant General',
    'PGen' => 'PGen - Police General'
];
$station_options = [
    // Provincial Headquarters
    'CPPO-HQ' => 'Cotabato Provincial Police Office - Headquarters',

    // City Police Stations
    'KIDAPAWAN-CPS' => 'Kidapawan City Police Station',

    // Municipal Police Stations
    'ALAMADA-MPS' => 'Alamada Municipal Police Station',
    'ALEOSAN-MPS' => 'Aleosan Municipal Police Station',
    'ANTIPAS-MPS' => 'Antipas Municipal Police Station',
    'ARAKAN-MPS' => 'Arakan Municipal Police Station',
    'BANISILAN-MPS' => 'Banisilan Municipal Police Station',
    'CARMEN-MPS' => 'Carmen Municipal Police Station',
    'KABACAN-MPS' => 'Kabacan Municipal Police Station',
    'LIBUNGAN-MPS' => 'Libungan Municipal Police Station',
    'MAGPET-MPS' => 'Magpet Municipal Police Station',
    'MAKILALA-MPS' => 'Makilala Municipal Police Station',
    'MATALAM-MPS' => 'Matalam Municipal Police Station',
    'MIDSAYAP-MPS' => 'Midsayap Municipal Police Station',
    'MLANG-MPS' => 'M\'lang Municipal Police Station',
    'PIGKAWAYAN-MPS' => 'Pigkawayan Municipal Police Station',
    'PIKIT-MPS' => 'Pikit Municipal Police Station',
    'PRESIDENT-ROXAS-MPS' => 'President Roxas Municipal Police Station',
    'TULUNAN-MPS' => 'Tulunan Municipal Police Station',

    // Special Units
    'CPPO-1PMFC' => '1st Provincial Mobile Force Company',
    'CPPO-PROVINCIAL-SAF' => 'Provincial Special Action Force',
    'CPPO-TRAFFIC' => 'Provincial Traffic Enforcement Unit',
    'CPPO-CIDG' => 'Criminal Investigation and Detection Group',

    // Other Important Stations
    'NORCOT-PPO' => 'North Cotabato Police Provincial Office',
    'KIDAPAWAN-CPO' => 'Kidapawan City Police Office',
    'MIDSAYAP-PS' => 'Midsayap Police Station',
    'KABACAN-PS' => 'Kabacan Police Station'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PNP System - Add Personnel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../plugins/toastr/toastr.min.css">
    <!-- Professional Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --pnp-primary: #1e3a8a;
            --pnp-secondary: #1e40af;
            --pnp-accent: #dc2626;
            --pnp-gold: #f59e0b;
            --pnp-success: #059669;
            --pnp-warning: #d97706;
            --pnp-light: #f8fafc;
            --pnp-lighter: #f1f5f9;
            --pnp-dark: #0f172a;
            --pnp-gray: #64748b;
            --pnp-gray-light: #94a3b8;
            --pnp-border: #e2e8f0;
            --pnp-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
            --pnp-shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --pnp-shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }

        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: var(--pnp-lighter);
            color: var(--pnp-dark);
            font-size: 14px;
            line-height: 1.6;
            letter-spacing: -0.01em;
        }

        .header-section {
            background: linear-gradient(135deg, var(--pnp-primary) 0%, var(--pnp-secondary) 100%);
            color: white;
            padding: 2rem 0;
            box-shadow: var(--pnp-shadow-lg);
            margin-bottom: 2rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .pnp-logo {
            width: 72px;
            height: 72px;
            background: white;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--pnp-shadow-lg);
            margin-right: 1.25rem;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .pnp-logo svg {
            width: 52px;
            height: 52px;
        }

        .header-text h1 {
            font-size: 1.875rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }

        .header-text h2 {
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 0;
            opacity: 0.9;
            letter-spacing: -0.01em;
        }

        .main-content {
            background: white;
            border-radius: 16px;
            box-shadow: var(--pnp-shadow-xl);
            border: 1px solid var(--pnp-border);
            overflow: hidden;
            margin-top: 2rem;
        }

        .content-header {
            background: var(--pnp-light);
            padding: 2rem;
            border-bottom: 1px solid var(--pnp-border);
        }

        .content-header h3 {
            color: var(--pnp-dark);
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 0;
            letter-spacing: -0.02em;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid var(--pnp-border);
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            font-weight: 400;
            transition: all 0.2s ease;
            background-color: white;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--pnp-primary);
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
            outline: none;
        }

        .form-floating>label {
            color: var(--pnp-gray);
            font-size: 0.875rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--pnp-dark);
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
        }

        .btn {
            border-radius: 8px;
            padding: 0.75rem 1.25rem;
            font-weight: 600;
            font-size: 0.875rem;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            letter-spacing: -0.01em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn:focus {
            box-shadow: 0 0 0 3px rgba(0,0,0,0.1);
        }

        .btn-primary {
            background-color: var(--pnp-primary);
            border-color: var(--pnp-primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--pnp-secondary);
            border-color: var(--pnp-secondary);
            transform: translateY(-1px);
            box-shadow: var(--pnp-shadow-lg);
        }

        .btn-success {
            background-color: var(--pnp-success);
            border-color: var(--pnp-success);
            color: white;
        }

        .btn-secondary {
            background-color: var(--pnp-gray);
            border-color: var(--pnp-gray);
            color: white;
        }

        .btn-secondary:hover {
            background-color: #475569;
            border-color: #475569;
            transform: translateY(-1px);
            box-shadow: var(--pnp-shadow-lg);
        }

        .btn-danger {
            background-color: var(--pnp-accent);
            border-color: var(--pnp-accent);
            color: white;
        }

        .section-title {
            color: var(--pnp-primary);
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-divider {
            border: none;
            height: 1px;
            background-color: var(--pnp-border);
            margin: 2rem 0;
        }

        .file-upload-area {
            border: 2px dashed var(--pnp-border);
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            transition: all 0.2s ease;
            background-color: var(--pnp-light);
        }

        .file-upload-area:hover {
            border-color: var(--pnp-primary);
            background-color: rgba(30, 58, 138, 0.05);
        }

        .file-upload-area.dragover {
            border-color: var(--pnp-primary);
            background-color: rgba(30, 58, 138, 0.1);
        }

        .form-check-input:checked {
            background-color: var(--pnp-primary);
            border-color: var(--pnp-primary);
        }

        .is-invalid {
            border-color: var(--pnp-accent) !important;
        }

        .invalid-feedback {
            font-size: 0.75rem;
            color: var(--pnp-accent);
        }

        .is-valid {
            border-color: var(--pnp-success) !important;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .content-header {
                padding: 1.5rem;
            }

            .content-header h3 {
                font-size: 1.25rem;
            }

            .file-upload-area {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>

<!-- Header Section -->
<div class="header-section">
    <div class="container">
        <div class="d-flex align-items-center">
            <div class="pnp-logo">
                <svg width="52" height="52" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="50" cy="50" r="45" fill="#1e3a8a"/>
                    <circle cx="50" cy="50" r="35" fill="#f59e0b"/>
                    <polygon points="50,20 60,40 40,40" fill="#1e3a8a"/>
                    <circle cx="50" cy="60" r="10" fill="#1e3a8a"/>
                    <text x="50" y="75" text-anchor="middle" fill="#1e3a8a" font-size="8" font-weight="bold">PNP</text>
                </svg>
            </div>
            <div class="header-text">
                <h1>Philippine National Police</h1>
                <h2>Personnel Management System</h2>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <!-- Main Content Card -->
    <div class="main-content">
        <!-- Content Header -->
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-plus-fill me-3 text-primary" style="font-size: 1.5rem;"></i>
                    <div>
                        <h3>Add New Personnel</h3>
                        <p class="text-muted mb-0 small">Create a new personnel record in the system</p>
                    </div>
                </div>
                <a href="personnel.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to List
                </a>
            </div>
        </div>

        <!-- Content Body -->
        <div class="p-4">
            <form method="POST" enctype="multipart/form-data" id="addPersonnelForm">
                <div class="row g-4">
                    <!-- Basic Information Section -->
                    <div class="col-12">
                        <h5 class="section-title">
                            <i class="bi bi-info-circle"></i> Basic Information
                        </h5>
                    </div>

                    <div class="col-md-6">
                        <label for="badge_number" class="form-label">
                            <i class="bi bi-shield-check"></i> Badge Number *
                        </label>
                        <input type="text" class="form-control" id="badge_number" name="badge_number" required>
                        <div class="invalid-feedback">Please provide a valid badge number.</div>
                    </div>

                    <div class="col-md-6">
                        <label for="rank" class="form-label">
                            <i class="bi bi-star"></i> Rank *
                        </label>
                        <select class="form-select" id="rank" name="rank" required>
                            <option value="">Select Rank</option>
                            <?php foreach ($rank_options as $value => $label): ?>
                                <option value="<?= $value ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Please select a rank.</div>
                    </div>

                    <div class="col-md-4">
                        <label for="first_name" class="form-label">
                            <i class="bi bi-person"></i> First Name *
                        </label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                        <div class="invalid-feedback">Please provide a first name.</div>
                    </div>

                    <div class="col-md-4">
                        <label for="middle_name" class="form-label">
                            <i class="bi bi-person"></i> Middle Name
                        </label>
                        <input type="text" class="form-control" id="middle_name" name="middle_name">
                    </div>

                    <div class="col-md-4">
                        <label for="last_name" class="form-label">
                            <i class="bi bi-person"></i> Last Name *
                        </label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                        <div class="invalid-feedback">Please provide a last name.</div>
                    </div>
                    <!-- Add this after the Basic Information section -->
                    <div class="col-md-12">
                        <label for="station" class="form-label">
                            <i class="bi bi-building"></i> Station/Unit *
                        </label>
                        <select class="form-select" id="station" name="station" required>
                            <option value="">Select Station/Unit</option>
                            <?php foreach ($station_options as $value => $label): ?>
                                <option value="<?= htmlspecialchars($value) ?>"><?= htmlspecialchars($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="invalid-feedback">Please select a station/unit.</div>
                    </div>
                    <hr class="section-divider">

                    <hr class="section-divider">

                    <!-- CDLB Information Section -->
                    <div class="col-12">
                        <h5 class="section-title">
                            <i class="bi bi-card-checklist"></i> CDLB Information
                        </h5>
                    </div>

                    <div class="col-md-12 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="has_cdlb" name="has_cdlb">
                            <label class="form-check-label" for="has_cdlb">Has CDLB?</label>
                        </div>
                    </div>

                    <div class="col-md-6 cdlb-field" style="display: none;">
                        <label for="cdlb_type" class="form-label">
                            <i class="bi bi-card-heading"></i> CDLB Type *
                        </label>
                        <select class="form-select" id="cdlb_type" name="cdlb_type">
                            <option value="">Select Type</option>
                            <option value="nqh">NQH</option>
                            <option value="rqh">RQH</option>
                        </select>
                        <div class="invalid-feedback">Please select a CDLB type.</div>
                    </div>

                    <div class="col-md-6 cdlb-field" style="display: none;">
                        <label for="cdlb_printed_date" class="form-label">
                            <i class="bi bi-calendar"></i> CDLB Printed Date *
                        </label>
                        <input type="date" class="form-control" id="cdlb_printed_date" name="cdlb_printed_date">
                        <div class="invalid-feedback">Please provide a printed date.</div>
                    </div>

                    <hr class="section-divider">

                    <!-- File Upload Section -->
                    <div class="col-12">
                        <h5 class="section-title">
                            <i class="bi bi-upload"></i> Photo Upload
                        </h5>
                    </div>

                    <div class="col-md-12">
                        <label for="photo" class="form-label">
                            <i class="bi bi-camera"></i> Profile Photo
                        </label>
                        <div class="file-upload-area">
                            <i class="bi bi-image" style="font-size: 2rem; color: var(--pnp-gray);"></i>
                            <p class="mb-2">Click to upload or drag and drop</p>
                            <input type="file" class="form-control d-none" id="photo" name="photo" accept="image/*">
                            <small class="text-muted">JPG, PNG, GIF up to 5MB</small>
                        </div>
                    </div>
                    <!-- Add this after the Photo Upload section -->
                    <hr class="section-divider">

                    <!-- Document Upload Section -->
                    <div class="col-12">
                        <h5 class="section-title">
                            <i class="bi bi-file-earmark-pdf"></i> Document Upload
                        </h5>
                    </div>

                    <div class="col-md-12">
                        <label for="document_pdf" class="form-label">
                            <i class="bi bi-file-earmark-pdf"></i> Supporting Documents (PDF)
                        </label>
                        <div class="file-upload-area">
                            <i class="bi bi-file-earmark-pdf" style="font-size: 2rem; color: var(--pnp-gray);"></i>
                            <p class="mb-2">Click to upload or drag and drop PDF files</p>
                            <input type="file" class="form-control d-none" id="document_pdf" name="document_pdf" accept=".pdf">
                            <small class="text-muted">PDF files up to 10MB</small>
                        </div>
                    </div>

                    <hr class="section-divider">

                    <div class="col-12 text-center mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Personnel Record
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../plugins/toastr/toastr.min.js"></script>
<script>
    // Configure Toastr for professional notifications
    toastr.options = {
        closeButton: true,
        debug: false,
        newestOnTop: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        preventDuplicates: true,
        onclick: null,
        showDuration: '300',
        hideDuration: '1000',
        timeOut: '5000',
        extendedTimeOut: '1000',
        showEasing: 'swing',
        hideEasing: 'linear',
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut'
    };

    // Test toastr is working
    // toastr.success('Toastr is working!', 'Test Message');

    // CDLB toggle functionality
    document.getElementById('has_cdlb').addEventListener('change', function() {
        const cdlbFields = document.querySelectorAll('.cdlb-field');
        const isChecked = this.checked;

        cdlbFields.forEach(field => {
            field.style.display = isChecked ? 'block' : 'none';
            if (!isChecked) {
                // Clear values when unchecked
                const inputs = field.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.value = '';
                    input.classList.remove('is-invalid', 'is-valid');
                });
            }
        });

        // Toggle required attribute
        document.getElementById('cdlb_type').required = isChecked;
        document.getElementById('cdlb_printed_date').required = isChecked;
    });

    // File upload drag and drop functionality
    document.querySelectorAll('.file-upload-area').forEach(area => {
        const input = area.querySelector('input[type="file"]');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            area.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            area.addEventListener(eventName, () => area.classList.add('dragover'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            area.addEventListener(eventName, () => area.classList.remove('dragover'), false);
        });

        area.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            input.files = files;
            updateFileUploadDisplay(area, files[0]);
        }

        // Click to upload
        area.addEventListener('click', () => input.click());

        // File input change
        input.addEventListener('change', function() {
            if (this.files.length > 0) {
                updateFileUploadDisplay(area, this.files[0]);
            }
        });
    });

    function updateFileUploadDisplay(area, file) {
        const fileName = file.name;
        const fileSize = (file.size / (1024 * 1024)).toFixed(2); // in MB

        // Update the display but preserve the input
        const originalInput = area.querySelector('input[type="file"]');
        area.innerHTML = `
        <i class="bi bi-check-circle-fill text-success" style="font-size: 2rem;"></i>
        <p class="mb-1 fw-bold">${fileName}</p>
        <small class="text-muted">${fileSize} MB</small>
        `;

        // Re-append the input with the file
        area.appendChild(originalInput);

        toastr.info('File selected for upload', 'Success');
    }

    // Form validation and submission
    $(document).ready(function() {
        $('#addPersonnelForm').on('submit', function(e) {
            e.preventDefault();

            console.log('Form submission initiated'); // Debug log

            // Reset validation states
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').text(''); // Clear previous error messages

            // Validate required fields
            let isValid = true;
            const requiredFields = ['badge_number', 'first_name', 'last_name', 'rank', 'station'];

            requiredFields.forEach(field => {
                const element = $(`#${field}`);
                if (!element.val() || !element.val().trim()) {
                    element.addClass('is-invalid');
                    isValid = false;
                    console.log(`Field ${field} is invalid`); // Debug log
                } else {
                    element.removeClass('is-invalid').addClass('is-valid');
                }
            });

            // Validate CDLB fields if checked
            if ($('#has_cdlb').is(':checked')) {
                const cdlbRequired = ['cdlb_type', 'cdlb_printed_date'];
                cdlbRequired.forEach(field => {
                    const element = $(`#${field}`);
                    if (!element.val() || !element.val().trim()) {
                        element.addClass('is-invalid');
                        isValid = false;
                        console.log(`CDLB field ${field} is invalid`); // Debug log
                    } else {
                        element.removeClass('is-invalid').addClass('is-valid');
                    }
                });
            }

            if (!isValid) {
                toastr.error('Please fill in all required fields', 'Validation Error');
                return false;
            }

            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.html('<span class="spinner-border spinner-border-sm me-2"></span> Saving...');
            submitBtn.prop('disabled', true);

            // Create FormData object
            const formData = new FormData(this);

            // Debug: Log form data
            console.log('Form data being sent:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            // Submit via AJAX with error handling and timeout
            $.ajax({
                url: '../api/add_personnel.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                timeout: 30000,
                dataType: 'json', // Explicitly expect JSON response
                beforeSend: function() {
                    console.log('Sending AJAX request...');
                },
                success: function(response, textStatus, xhr) {
                    console.log('AJAX Success:', response);
                    console.log('Status:', textStatus);
                    console.log('XHR:', xhr);

                    // Always reset button state
                    submitBtn.html(originalText);
                    submitBtn.prop('disabled', false);

                    // Check if response is valid
                    if (typeof response === 'object' && response !== null) {
                        if (response.success === true) {
                            toastr.success(response.message || 'Personnel added successfully', 'Success');
                            setTimeout(() => {
                                window.location.href = 'personnel.php';
                            }, 1500);
                        } else {
                            // Handle error response
                            toastr.error(response.message || 'Operation failed', 'Error');

                            // Highlight fields with errors
                            if (response.errors && typeof response.errors === 'object') {
                                Object.keys(response.errors).forEach(field => {
                                    const element = $(`#${field}`);
                                    element.addClass('is-invalid');
                                    const errorMessage = response.errors[field];
                                    if (errorMessage) {
                                        element.next('.invalid-feedback').text(errorMessage);
                                    }
                                });
                            }
                        }
                    } else {
                        console.error('Invalid response format:', response);
                        toastr.error('Received invalid response from server', 'Error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText,
                        readyState: xhr.readyState,
                        statusCode: xhr.status
                    });

                    // Reset button state on error
                    submitBtn.html(originalText);
                    submitBtn.prop('disabled', false);

                    let errorMessage = 'An error occurred while processing your request.';

                    if (status === 'timeout') {
                        errorMessage = 'Request timed out. Please try again.';
                    } else if (status === 'parsererror') {
                        errorMessage = 'Server returned invalid data. Please check the response format.';
                        console.error('Response that failed to parse:', xhr.responseText);

                        // Try to show the raw response if parsing failed
                        try {
                            toastr.error(xhr.responseText, 'Raw Server Response');
                        } catch (e) {
                            console.error('Could not display raw response:', e);
                        }
                    } else if (xhr.status === 404) {
                        errorMessage = 'API endpoint not found. Please check the file path.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Internal server error. Please check server logs.';
                    } else if (xhr.responseText) {
                        try {
                            // Try to parse as JSON even if dataType failed
                            const errorResponse = JSON.parse(xhr.responseText);
                            if (errorResponse.message) {
                                errorMessage = errorResponse.message;
                            }
                        } catch (e) {
                            errorMessage = xhr.responseText.substring(0, 200);
                        }
                    }

                    toastr.error(errorMessage, 'Error');
                }
            });
        });

        // Real-time validation
        $('input[required], select[required]').on('blur', function() {
            if ($(this).val() && $(this).val().trim()) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
            }
        });

        // Clear validation states on input
        $('input, select').on('input change', function() {
            $(this).removeClass('is-invalid');
        });
    });
</script>
</body>
</html>