<?php
require_once '../config/db_connection.php';
require_once  '../config/check_admin.php';

$conn = getDBConnection();
$personnel = [];
$search = '';

if (isset($_GET['search'])) {
    $search = $conn->real_escape_string($_GET['search']);
    $query = "SELECT * FROM personnel WHERE 
              badge_number LIKE '%$search%' OR 
              first_name LIKE '%$search%' OR 
              last_name LIKE '%$search%'";
    $result = $conn->query($query);
    if ($result) {
        $personnel = $result->fetch_all(MYSQLI_ASSOC);
    }
} else {
    $result = $conn->query("SELECT * FROM personnel ORDER BY last_name, first_name");
    if ($result) {
        $personnel = $result->fetch_all(MYSQLI_ASSOC);
    }
}



const STATION_OPTIONS = [
    'CPPO-HQ', 'KIDAPAWAN-CPS', 'ALAMADA-MPS', 'ALEOSAN-MPS', 'ANTIPAS-MPS',
    'ARAKAN-MPS', 'BANISILAN-MPS', 'CARMEN-MPS', 'KABACAN-MPS', 'LIBUNGAN-MPS',
    'MAGPET-MPS', 'MAKILALA-MPS', 'MATALAM-MPS', 'MIDSAYAP-MPS', 'MLANG-MPS',
    'PIGKAWAYAN-MPS', 'PIKIT-MPS', 'PRESIDENT-ROXAS-MPS', 'TULUNAN-MPS',
    'CPPO-1PMFC', 'CPPO-PROVINCIAL-SAF', 'CPPO-TRAFFIC', 'CPPO-CIDG',
    'NORCOT-PPO', 'KIDAPAWAN-CPO', 'MIDSAYAP-PS', 'KABACAN-PS'
];

const RANK_OPTIONS = [
    'Pat', 'PEM', 'PMSg', 'PCMS', 'PSMS', 'PEMS', 'PCpt', 'PMaj',
    'PLtCol', 'PCol', 'PBGen', 'PMGen', 'PLtGen', 'PGen'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PNP System - Manage Personnel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../plugins/toastr/toastr.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">

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

        .search-card {
            background: white;
            border: 1px solid var(--pnp-border);
            border-radius: 12px;
            box-shadow: var(--pnp-shadow);
            margin-bottom: 2rem;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid var(--pnp-border);
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            font-weight: 400;
            transition: all 0.2s ease;
            background-color: white;
        }

        .form-control:focus {
            border-color: var(--pnp-primary);
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
            outline: none;
        }

        .form-control::placeholder {
            color: var(--pnp-gray-light);
            font-weight: 400;
        }

        .input-group-text {
            background-color: var(--pnp-light);
            border: 1px solid var(--pnp-border);
            color: var(--pnp-gray);
            font-size: 0.875rem;
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

        .btn-success:hover {
            background-color: #047857;
            border-color: #047857;
            transform: translateY(-1px);
            box-shadow: var(--pnp-shadow-lg);
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

        .btn-danger:hover {
            background-color: #b91c1c;
            border-color: #b91c1c;
            transform: translateY(-1px);
            box-shadow: var(--pnp-shadow-lg);
        }

        .table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--pnp-border);
        }

        .table {
            margin-bottom: 0;
            font-size: 0.875rem;
        }

        .table thead th {
            background-color: var(--pnp-dark);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.75rem;
            border: none;
            padding: 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--pnp-border);
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: var(--pnp-light);
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .badge-number {
            background: linear-gradient(135deg, var(--pnp-gold) 0%, #fbbf24 100%);
            color: var(--pnp-dark);
            padding: 0.375rem 0.875rem;
            border-radius: 20px;
            font-weight: 700;
            font-size: 0.75rem;
            font-family: 'JetBrains Mono', monospace;
            letter-spacing: 0.05em;
            display: inline-block;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .rank-badge {
            background-color: var(--pnp-primary);
            color: white;
            padding: 0.375rem 0.875rem;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-block;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-buttons .btn {
            padding: 0.5rem;
            border-radius: 6px;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
        }

        .action-buttons .btn:hover {
            transform: translateY(-1px);
        }

        .fw-bold {
            font-weight: 600;
            color: var(--pnp-dark);
            font-size: 0.875rem;
        }

        .text-muted {
            color: var(--pnp-gray) !important;
        }

        .modal-content {
            border-radius: 12px;
            border: 1px solid var(--pnp-border);
            box-shadow: var(--pnp-shadow-xl);
        }

        .modal-header {
            border-bottom: 1px solid var(--pnp-border);
            padding: 1.5rem;
        }

        .modal-body {
            padding: 1.5rem;
            font-size: 0.875rem;
        }

        .modal-footer {
            border-top: 1px solid var(--pnp-border);
            padding: 1.5rem;
        }

        .modal-title {
            font-weight: 700;
            font-size: 1.125rem;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        .display-4 {
            font-size: 3rem;
            color: var(--pnp-gray-light);
        }

        .empty-state {
            padding: 4rem 2rem;
        }

        .empty-state h5 {
            font-weight: 600;
            color: var(--pnp-gray);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--pnp-gray-light);
            font-size: 0.875rem;
        }

        /* Professional loading states */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255,255,255,0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .header-text h1 {
                font-size: 1.5rem;
            }

            .header-text h2 {
                font-size: 0.875rem;
            }

            .content-header {
                padding: 1.5rem;
            }

            .content-header h3 {
                font-size: 1.25rem;
            }

            .action-buttons {
                flex-direction: column;
                gap: 0.25rem;
            }
        }

        /* Dark mode support for future enhancement */
        @media (prefers-color-scheme: dark) {
            /* Keeping light theme for professional government appearance */
        }
    </style>
</head>
<body>

<!-- Header Section -->
<!-- Header Section -->
<div class="header-section">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between">
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

            <!-- Admin Info and Logout -->
            <div class="d-flex align-items-center">
                <div class="text-end me-3">
                    <div class="text-white small">Logged in as</div>
                    <div class="text-white fw-bold"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></div>
                </div>
                <a href="../admin/logout.php" class="btn btn-outline-light" title="Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Are you sure you want to delete the record for <strong><span id="personnelName"></span></strong>?</p>
                <div class="alert alert-warning d-flex align-items-center" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <small>This action cannot be undone and will permanently remove all associated data.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDelete">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="bi bi-trash3 me-1"></i>
                    <span id="deleteButtonText">Delete Record</span>
                    <span id="deleteSpinner" class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Edit Personnel Modal -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editModalLabel">
                    <i class="bi bi-pencil-square me-2"></i>Edit Personnel Record
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editPersonnelForm">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_badge_number" class="form-label">Badge Number *</label>
                            <input type="text" class="form-control" id="edit_badge_number" name="badge_number" required>
                        </div>
                        <div class="col-md-6">
                            <label for="edit_rank" class="form-label">Rank *</label>
                            <select class="form-select" id="edit_rank" name="rank" required>
                                <option value="">Select Rank</option>
                                <?php foreach (RANK_OPTIONS as $rank): ?>
                                    <option value="<?= htmlspecialchars($rank) ?>"><?= htmlspecialchars($rank) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_first_name" class="form-label">First Name *</label>
                            <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_middle_name" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="edit_middle_name" name="middle_name">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_last_name" class="form-label">Last Name *</label>
                            <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                        </div>
                        <div class="col-md-12">
                            <label for="edit_station" class="form-label">Station/Unit *</label>
                            <select class="form-select" id="edit_station" name="station" required>
                                <option value="">Select Station/Unit</option>
                                <?php foreach (STATION_OPTIONS as $station): ?>
                                    <option value="<?= htmlspecialchars($station) ?>"><?= htmlspecialchars($station) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_has_cdlb" name="has_cdlb">
                                <label class="form-check-label" for="edit_has_cdlb">Has CDLB?</label>
                            </div>
                        </div>
                        <div class="col-md-6 cdlb-field" style="display: none;">
                            <label for="edit_cdlb_type" class="form-label">CDLB Type</label>
                            <select class="form-select" id="edit_cdlb_type" name="cdlb_type">
                                <option value="">Select Type</option>
                                <option value="nqh">NQH</option>
                                <option value="rqh">RQH</option>
                            </select>
                        </div>
                        <div class="col-md-6 cdlb-field" style="display: none;">
                            <label for="edit_cdlb_printed_date" class="form-label">CDLB Printed Date</label>
                            <input type="date" class="form-control" id="edit_cdlb_printed_date" name="cdlb_printed_date">
                        </div>
                        <div class="col-md-12">
                            <label for="edit_pdf_file" class="form-label">Upload PDF Document</label>
                            <input type="file" class="form-control" id="edit_pdf_file" name="pdf_file" accept=".pdf">
                            <small class="text-muted">Max size: 5MB. Only PDF files accepted.</small>
                            <div id="current_pdf_container" class="mt-2" style="display: none;">
                                <span class="badge bg-secondary">Current file: </span>
                                <a href="#" id="current_pdf_link" target="_blank"></a>
                                <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="remove_pdf">
                                    <i class="bi bi-trash"></i> Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-primary" id="confirmEdit">
                    <i class="bi bi-save me-1"></i>
                    <span id="editButtonText">Save Changes</span>
                    <span id="editSpinner" class="spinner-border spinner-border-sm d-none ms-2" role="status" aria-hidden="true"></span>
                </button>
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
                    <i class="bi bi-people-fill me-3 text-primary" style="font-size: 1.5rem;"></i>
                    <div>
                        <h3>Personnel Records</h3>
                        <p class="text-muted mb-0 small">Manage and view all personnel information</p>
                    </div>
                </div>
                <a href="../admin/add_personnel.php" class="btn btn-success">
                    <i class="bi bi-plus-circle"></i>Add New Personnel
                </a>
            </div>
        </div>

        <!-- Content Body -->
        <div class="p-4">
            <!-- Search Card -->
            <div class="search-card">
                <div class="card-body p-4">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label small text-muted fw-medium">Search Personnel</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" name="search" class="form-control"
                                       placeholder="Enter badge number, first name, or last name..."
                                       value="<?= htmlspecialchars($search) ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="bi bi-search"></i>Search
                                </button>
                                <?php if (!empty($search)): ?>
                                    <a href="personnel.php" class="btn btn-secondary">
                                        <i class="bi bi-arrow-clockwise"></i>Clear
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Summary -->
            <?php if (!empty($search)): ?>
                <div class="alert alert-info d-flex align-items-center mb-4">
                    <i class="bi bi-info-circle me-2"></i>
                    <span>Showing results for "<strong><?= htmlspecialchars($search) ?></strong>" - <?= count($personnel) ?> record(s) found</span>
                </div>
            <?php endif; ?>

            <!-- Table Container -->
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th><i class="bi bi-shield-check me-2"></i>Badge Number</th>
                            <th><i class="bi bi-person me-2"></i>Full Name</th>
                            <th><i class="bi bi-star me-2"></i>Rank</th>
                            <th><i class="bi bi-gear me-2"></i>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (empty($personnel)): ?>
                            <tr>
                                <td colspan="4" class="text-center">
                                    <div class="empty-state">
                                        <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                        <h5>No Personnel Found</h5>
                                        <p>
                                            <?php if (!empty($search)): ?>
                                                No records match your search criteria. Try different keywords or <a href="personnel.php">view all personnel</a>.
                                            <?php else: ?>
                                                No personnel records available. <a href="../admin/add_personnel.php">Add the first personnel record</a>.
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($personnel as $person): ?>
                                <tr>
                                    <td>
                                        <span class="badge-number">
                                            <?= htmlspecialchars($person['badge_number']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">
                                            <?= htmlspecialchars($person['last_name']) ?>,
                                            <?= htmlspecialchars($person['first_name']) ?>
                                            <?= htmlspecialchars($person['middle_name']) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="rank-badge">
                                            <?= getRankTitle($person['rank']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-primary btn-sm edit-btn"
                                                    data-id="<?= $person['id'] ?>"
                                                    title="Edit Personnel Record"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <button class="btn btn-danger btn-sm delete-btn"
                                                    data-id="<?= $person['id'] ?>"
                                                    data-name="<?= htmlspecialchars($person['last_name'] . ', ' . $person['first_name']) ?>"
                                                    title="Delete Personnel Record"
                                                    data-bs-toggle="tooltip"
                                                    data-bs-placement="top">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Footer Info -->
            <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <small class="text-muted">
                    Total Records: <strong><?= count($personnel) ?></strong>
                </small>
                <small class="text-muted">
                    Last Updated: <?= date('M d, Y \a\t g:i A') ?>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../plugins/toastr/toastr.min.js"></script>

<script>
    // Initialize toastr with professional settings
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

    // Enhanced delete functionality - fixed version
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));

            // Set the personnel name in the modal
            document.getElementById('personnelName').textContent = name;

            // Reset modal state
            const confirmBtn = document.getElementById('confirmDelete');
            confirmBtn.innerHTML = '<i class="bi bi-trash3 me-1"></i><span id="deleteButtonText">Delete Record</span>';
            confirmBtn.disabled = false;

            // Clear any previous event listeners
            const newConfirmBtn = confirmBtn.cloneNode(true);
            confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);

            // Show the modal
            deleteModal.show();

            // Set up the confirmation button
            document.getElementById('confirmDelete').addEventListener('click', function onClick() {
                // Show loading state
                this.innerHTML = '<i class="bi bi-trash3 me-1"></i><span id="deleteButtonText">Deleting...</span><span class="spinner-border spinner-border-sm ms-2" role="status"></span>';
                this.disabled = true;

                // Send delete request
                fetch('../api/delete_personnel.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + encodeURIComponent(id)
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Network error');
                        return response.json();
                    })
                    .then(data => {
                        if (!data) throw new Error('No response data');

                        // Hide modal first
                        deleteModal.hide();

                        // Always show toastr - for debugging
                        toastr.success(data.message || 'Record deleted successfully', 'Success');

                        // Reload after delay
                        setTimeout(() => window.location.reload(), 1500);
                    })
                    .catch(error => {
                        console.error('Delete Error:', error);
                        deleteModal.hide();
                        toastr.error(error.message || 'Deletion failed', 'Error');
                    });
            });
        });
    });

    // Toggle CDLB fields function
    function toggleCDLBFields(show) {
        document.querySelectorAll('.cdlb-field').forEach(field => {
            field.style.display = show ? 'block' : 'none';
        });
    }

    // CDLB toggle event
    document.getElementById('edit_has_cdlb').addEventListener('change', function() {
        toggleCDLBFields(this.checked);
    });

    // Edit functionality
    const editModal = new bootstrap.Modal(document.getElementById('editModal'));
    const editForm = document.getElementById('editPersonnelForm');
    const editSpinner = document.getElementById('editSpinner');
    const editButtonText = document.getElementById('editButtonText');

    // Edit button handler
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-btn')) {
            const btn = e.target.closest('.edit-btn');
            const id = btn.getAttribute('data-id');

            editSpinner.classList.remove('d-none');
            editButtonText.textContent = 'Loading...';

            fetch(`../api/get_personnel.php?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) throw new Error(data.message || 'Failed to load data');

                    const p = data.data;
                    document.getElementById('edit_id').value = p.id;
                    document.getElementById('edit_badge_number').value = p.badge_number || '';
                    document.getElementById('edit_first_name').value = p.first_name || '';
                    document.getElementById('edit_middle_name').value = p.middle_name || '';
                    document.getElementById('edit_last_name').value = p.last_name || '';
                    document.getElementById('edit_rank').value = p.rank || '';
                    document.getElementById('edit_station').value = p.station || '';

                    const hasCDLB = p.has_cdlb === '1' || p.has_cdlb === true;
                    document.getElementById('edit_has_cdlb').checked = hasCDLB;
                    toggleCDLBFields(hasCDLB);

                    if (hasCDLB) {
                        document.getElementById('edit_cdlb_type').value = p.cdlb_type || '';
                        document.getElementById('edit_cdlb_printed_date').value = p.cdlb_printed_date || '';
                    }

                    editModal.show();
                })
                .catch(error => {
                    toastr.error(error.message);
                })
                .finally(() => {
                    editSpinner.classList.add('d-none');
                    editButtonText.textContent = 'Save Changes';
                });
        }
    });

    // Save edited data
    document.getElementById('confirmEdit').addEventListener('click', function() {
        editSpinner.classList.remove('d-none');
        editButtonText.textContent = 'Saving...';

        const formData = new FormData(editForm);
        formData.set('has_cdlb', document.getElementById('edit_has_cdlb').checked ? '1' : '0');

        fetch('../api/update_personnel.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (!data.success) throw new Error(data.message);

                toastr.success(data.message);
                editModal.hide();
                setTimeout(() => window.location.reload(), 1500);
            })
            .catch(error => {
                toastr.error(error.message || 'Update failed');
            })
            .finally(() => {
                editSpinner.classList.add('d-none');
                editButtonText.textContent = 'Save Changes';
            });
    });

    // Enhanced search form with loading state
    const searchForm = document.querySelector('form[method="GET"]');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalHTML = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Searching...';
            submitBtn.disabled = true;

            // Re-enable if form submission fails
            setTimeout(() => {
                if (submitBtn.disabled) {
                    submitBtn.innerHTML = originalHTML;
                    submitBtn.disabled = false;
                }
            }, 10000);
        });
    }

    // Auto-focus search input if it has a value
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput && searchInput.value) {
        searchInput.focus();
        searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
    }

    // Professional keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + K to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.focus();
                searchInput.select();
            }
        }

        // Escape to clear search
        if (e.key === 'Escape' && searchInput && searchInput.value) {
            window.location.href = 'personnel.php';
        }
    });

    // Smooth animations for table rows
    const tableRows = document.querySelectorAll('tbody tr');
    tableRows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(10px)';

        setTimeout(() => {
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, index * 50);
    });

    // Enhanced error handling for API calls
    window.addEventListener('unhandledrejection', function(event) {
        console.error('Unhandled promise rejection:', event.reason);
        toastr.error('An unexpected error occurred. Please try again.', 'Error');
    });

    // Professional loading indicator for page navigation
    window.addEventListener('beforeunload', function() {
        const loadingOverlay = document.createElement('div');
        loadingOverlay.className = 'loading-overlay';
        loadingOverlay.innerHTML = `
            <div class="text-center">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mb-0">Loading...</p>
            </div>
        `;
        document.body.appendChild(loadingOverlay);
    });
</script>
</body>
</html>