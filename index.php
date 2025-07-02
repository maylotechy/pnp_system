<?php
require_once 'config/db_connection.php';

$result = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['badge_number'])) {
    $badge_number = trim($_POST['badge_number']);
    $conn = getDBConnection();
    $stmt = $conn->prepare("SELECT * FROM personnel WHERE badge_number = ?");
    $stmt->bind_param("s", $badge_number);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PNP System - Personnel Search</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="plugins/toastr/toastr.min.css">
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

        .profile-section {
            text-align: center;
            padding: 2rem;
            background: var(--pnp-light);
            border-radius: 12px;
            margin-bottom: 2rem;
            border: 1px solid var(--pnp-border);
        }

        .profile-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: var(--pnp-shadow-lg);
            margin-bottom: 1.5rem;
        }

        .no-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: var(--pnp-lighter);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 4px solid white;
            box-shadow: var(--pnp-shadow-lg);
            margin-bottom: 1.5rem;
            color: var(--pnp-gray);
        }

        .personnel-name {
            font-weight: 700;
            color: var(--pnp-dark);
            margin-bottom: 1rem;
            font-size: 1.25rem;
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

        .details-table {
            width: 100%;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid var(--pnp-border);
        }

        .details-table th {
            background-color: var(--pnp-dark);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-size: 0.75rem;
            border: none;
            padding: 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            width: 35%;
        }

        .details-table td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--pnp-border);
        }

        .details-table tr:last-child td {
            border-bottom: none;
        }

        .alert-warning {
            background-color: rgba(220, 38, 38, 0.1);
            border-left: 4px solid var(--pnp-accent);
            border-radius: 8px;
            padding: 1rem;
        }

        .empty-state {
            padding: 4rem 2rem;
            text-align: center;
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--pnp-gray-light);
            margin-bottom: 1rem;
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

            .profile-photo, .no-photo {
                width: 120px;
                height: 120px;
            }
        }
    </style>
</head>
<body>

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
                <h2>Personnel Search System</h2>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="main-content">
        <div class="content-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-search me-3 text-primary" style="font-size: 1.5rem;"></i>
                    <div>
                        <h3>Personnel Search</h3>
                        <p class="text-muted mb-0 small">Search for personnel records by badge number</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="p-4">
            <div class="search-card">
                <div class="card-body p-4">
                    <form method="POST" class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label small text-muted fw-medium">Search by Badge Number</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-shield-check"></i>
                                </span>
                                <input type="text" name="badge_number" class="form-control"
                                       placeholder="Enter personnel badge number..."
                                       value="<?= htmlspecialchars(isset($_POST['badge_number']) ? $_POST['badge_number'] : '') ?>"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="bi bi-search"></i> Search
                                </button>
                                <?php if (!empty($_POST['badge_number'])): ?>
                                    <a href="search.php" class="btn btn-secondary">
                                        <i class="bi bi-arrow-clockwise"></i> Clear
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($result): ?>
                <div class="row mt-4">
                    <div class="col-md-4">
                        <div class="profile-section">
                            <?php if ($result['photo_path']): ?>
                                <img src="<?= htmlspecialchars($result['photo_path']) ?>"
                                     class="profile-photo"
                                     alt="<?= htmlspecialchars($result['first_name']) ?>'s photo">
                            <?php else: ?>
                                <div class="no-photo">
                                    <i class="bi bi-person-fill" style="font-size: 3rem;"></i>
                                </div>
                            <?php endif; ?>

                            <div class="personnel-name">
                                <?= htmlspecialchars($result['last_name']) ?>,
                                <?= htmlspecialchars($result['first_name']) ?>
                                <?= htmlspecialchars($result['middle_name']) ?>
                            </div>

                            <div class="mb-3">
                                <span class="badge-number">
                                    <?= htmlspecialchars($result['badge_number']) ?>
                                </span>
                            </div>

                            <div class="mb-3">
                                <span class="rank-badge">
                                    <?= getRankTitle($result['rank']) ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <table class="details-table">
                            <tbody>
                            <tr>
                                <th>Badge Number</th>
                                <td><?= htmlspecialchars($result['badge_number']) ?></td>
                            </tr>
                            <tr>
                                <th>Rank</th>
                                <td>
                                    <span class="rank-badge">
                                        <?= getRankTitle($result['rank']) ?>
                                    </span>
                                    <small class="text-muted ms-2">(<?= $result['rank'] ?>)</small>
                                </td>
                            </tr>
                            <tr>
                                <th>Full Name</th>
                                <td>
                                    <?= htmlspecialchars($result['last_name']) ?>,
                                    <?= htmlspecialchars($result['first_name']) ?>
                                    <?= htmlspecialchars($result['middle_name']) ?>
                                </td>
                            </tr>
                            <?php if ($result['has_cdlb']): ?>
                                <tr>
                                    <th>CDLB Type</th>
                                    <td><?= $result['cdlb_type'] ?? 'N/A' ?></td>
                                </tr>
                                <tr>
                                    <th>CDLB Printed Date</th>
                                    <td>
                                        <?= $result['cdlb_printed_date'] ? date('F j, Y', strtotime($result['cdlb_printed_date'])) : 'N/A' ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>CDLB Document</th>
                                    <td>
                                        <?php if (!empty($result['pdf_path'])): ?>
                                            <a href="download_cdlb.php?badge_number=<?= urlencode($result['badge_number']) ?>"
                                               class="btn btn-sm btn-success"
                                               onclick="showDownloadLoading()">
                                                <i class="bi bi-file-earmark-pdf"></i> Download CDLB
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">CDLB document not uploaded</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <th>CDLB Status</th>
                                    <td class="text-muted">No CDLB</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="alert alert-warning mt-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div>
                            <strong>No personnel found</strong>
                            <p class="mb-0">No record found for badge number: <?= htmlspecialchars($_POST['badge_number']) ?></p>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-search"></i>
                    <h5>Search for Personnel</h5>
                    <p>Enter a badge number above to search for personnel records</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="plugins/toastr/toastr.min.js"></script>
<script>
    function showDownloadLoading() {
        toastr.info('Preparing CDLB document... Please wait', 'Processing', {
            timeOut: 3000,
            progressBar: true
        });
    }

    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: 'toast-top-right',
        timeOut: 5000
    };

    <?php if ($result): ?>
    toastr.success('Personnel record found', 'Search Complete');
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    toastr.error('No personnel found with that badge number', 'Search Complete');
    <?php endif; ?>

    document.querySelector('form').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalHTML = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Searching...';
        submitBtn.disabled = true;

        setTimeout(() => {
            submitBtn.innerHTML = originalHTML;
            submitBtn.disabled = false;
        }, 10000);
    });

    const searchInput = document.querySelector('input[name="badge_number"]');
    if (searchInput && !searchInput.value) {
        searchInput.focus();
    }
</script>
</body>
</html>