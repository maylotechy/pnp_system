<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PNP System - Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../plugins/toastr/toastr.min.css" rel="stylesheet">

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
            background: linear-gradient(135deg, var(--pnp-primary) 0%, var(--pnp-secondary) 50%, #6366f1 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
            position: relative;
            overflow-x: hidden;
            color: var(--pnp-dark);
            font-size: 14px;
            line-height: 1.6;
            letter-spacing: -0.01em;
        }

        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="25" cy="25" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="3" fill="rgba(255,255,255,0.05)"/><circle cx="50" cy="10" r="1.5" fill="rgba(255,255,255,0.08)"/><circle cx="10" cy="90" r="2.5" fill="rgba(255,255,255,0.06)"/><circle cx="90" cy="20" r="1" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
            animation: float 20s ease-in-out infinite;
            pointer-events: none;
            z-index: 1;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }

        .login-container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
        }

        .login-header {
            text-align: center;
            margin-bottom: 2rem;
            animation: fadeInDown 1s ease;
        }

        .pnp-logo {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, white 0%, var(--pnp-light) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: var(--pnp-shadow-xl);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .pnp-logo::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
            100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        }

        .pnp-logo:hover {
            transform: scale(1.05) rotate(5deg);
            box-shadow: 0 25px 50px rgba(0,0,0,0.3);
        }

        .pnp-logo img {
            width: 60px;
            height: 60px;
            z-index: 2;
            position: relative;
        }

        .system-title {
            color: white;
            font-size: 2.5rem;
            font-weight: 800;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .system-subtitle {
            color: rgba(255,255,255,0.9);
            font-size: 1.1rem;
            font-weight: 500;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.2);
            letter-spacing: -0.01em;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            box-shadow: var(--pnp-shadow-xl);
            border: 1px solid rgba(255,255,255,0.2);
            padding: 0;
            overflow: hidden;
            animation: slideInUp 0.8s ease;
            position: relative;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .card-header {
            background: linear-gradient(135deg, var(--pnp-primary) 0%, var(--pnp-secondary) 100%);
            color: white;
            padding: 2rem;
            text-align: center;
            border: none;
            position: relative;
            overflow: hidden;
        }

        .card-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            animation: slideRight 3s infinite;
        }

        @keyframes slideRight {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .card-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            letter-spacing: -0.02em;
        }

        .card-header p {
            margin-bottom: 0;
            opacity: 0.9;
            font-size: 0.95rem;
            font-weight: 400;
        }

        .card-body {
            padding: 2.5rem;
        }

        .form-floating {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-control {
            border: 2px solid var(--pnp-border);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            font-weight: 400;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.9);
            backdrop-filter: blur(10px);
        }

        .form-control:focus {
            border-color: var(--pnp-primary);
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
            background: white;
            transform: translateY(-2px);
            outline: none;
        }

        .form-control.is-invalid {
            border-color: var(--pnp-accent);
            box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
        }

        .form-control.is-valid {
            border-color: var(--pnp-success);
            box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.1);
        }

        .form-floating > label {
            color: var(--pnp-gray);
            font-weight: 500;
            font-size: 0.875rem;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--pnp-gray);
            z-index: 10;
            transition: color 0.3s ease;
            font-size: 1rem;
        }

        .password-toggle:hover {
            color: var(--pnp-primary);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--pnp-primary) 0%, var(--pnp-secondary) 100%);
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.25rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: var(--pnp-shadow-lg);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .loading-spinner {
            display: none;
        }

        .btn-login.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-login.loading .loading-spinner {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-login.loading .btn-text {
            display: none;
        }

        .forgot-password {
            text-align: center;
            margin-top: 1.5rem;
        }

        .forgot-password a {
            color: var(--pnp-primary);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.875rem;
            transition: all 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--pnp-secondary);
            text-decoration: underline;
        }

        .security-notice {
            background: linear-gradient(135deg, #fff3cd 0%, #fef3c7 100%);
            border: 1px solid #fef3c7;
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1.5rem;
            font-size: 0.75rem;
            color: #92400e;
            text-align: center;
        }

        .security-notice i {
            color: var(--pnp-warning);
            margin-right: 0.5rem;
        }

        .invalid-feedback {
            font-size: 0.75rem;
            color: var(--pnp-accent);
            font-weight: 500;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        @media (max-width: 576px) {
            .login-container {
                padding: 0 1rem;
            }

            .system-title {
                font-size: 2rem;
            }

            .card-body {
                padding: 2rem 1.5rem;
            }

            .card-header {
                padding: 1.5rem;
            }

            .card-header h2 {
                font-size: 1.5rem;
            }
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .system-title {
                font-size: 2rem;
            }

            .system-subtitle {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-header">
        <div class="pnp-logo">
            <img src="../assets/images/pnp_logo.png" alt="PNP Logo" width="60" height="60">
        </div>
        <h1 class="system-title">PNP System</h1>
        <p class="system-subtitle">Philippine National Police Management Portal</p>
    </div>

    <div class="card login-card">
        <div class="card-header">
            <h2><i class="bi bi-shield-lock"></i> Admin Login</h2>
            <p>Secure access to personnel management system</p>
        </div>
        <div class="card-body">
            <form id="loginForm" novalidate>
                <div class="form-floating">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                    <label for="username"><i class="bi bi-person me-2"></i>Username</label>
                    <div class="invalid-feedback">Please enter your username.</div>
                </div>
                <div class="form-floating position-relative">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password"><i class="bi bi-lock me-2"></i>Password</label>
                    <i class="bi bi-eye password-toggle" id="passwordToggle"></i>
                    <div class="invalid-feedback">Please enter your password.</div>
                </div>
                <button type="submit" class="btn btn-login">
                    <span class="loading-spinner">
                        <span class="spinner-border spinner-border-sm"></span>
                        Authenticating...
                    </span>
                    <span class="btn-text">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Sign In
                    </span>
                </button>
            </form>

            <div class="forgot-password">
                <a href="#" id="forgotPassword"><i class="bi bi-question-circle me-1"></i>Forgot your password?</a>
            </div>

            <div class="security-notice">
                <i class="bi bi-shield-check"></i>
                <strong>Security Notice:</strong> This system is for authorized personnel only. All access attempts are logged and monitored.
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
        progressBar: true,
        positionClass: 'toast-top-right',
        preventDuplicates: true,
        showDuration: '300',
        hideDuration: '1000',
        timeOut: '5000',
        extendedTimeOut: '1000',
        showEasing: 'swing',
        hideEasing: 'linear',
        showMethod: 'fadeIn',
        hideMethod: 'fadeOut'
    };

    // Password Toggle
    const passwordToggle = document.getElementById('passwordToggle');
    const passwordInput = document.getElementById('password');

    passwordToggle.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        this.classList.toggle('bi-eye');
        this.classList.toggle('bi-eye-slash');
    });

    // Form Submission
    $(document).ready(function () {
        $('#loginForm').on('submit', function (e) {
            e.preventDefault();
            $(this).removeClass('was-validated');
            $('.form-control').removeClass('is-invalid is-valid');

            const username = $('#username').val().trim();
            const password = $('#password').val().trim();

            let isValid = true;

            if (!username) {
                $('#username').addClass('is-invalid');
                isValid = false;
            } else {
                $('#username').addClass('is-valid');
            }

            if (!password) {
                $('#password').addClass('is-invalid');
                isValid = false;
            } else {
                $('#password').addClass('is-valid');
            }

            if (!isValid) {
                toastr.error('Please fill in all required fields.', 'Validation Error');
                return;
            }

            const submitBtn = $(this).find('.btn-login');
            submitBtn.addClass('loading');

            $.ajax({
                url: '../api/login.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    username: username,
                    password: password
                },
                success: function (response) {
                    submitBtn.removeClass('loading');
                    if (response.success) {
                        toastr.success(response.message, 'Login Successful');
                        setTimeout(() => {
                            window.location.href = 'personnel.php';
                        }, 1500);
                    } else {
                        toastr.error(response.message, 'Login Failed');
                        $('#loginForm').css('animation', 'shake 0.5s');
                        setTimeout(() => {
                            $('#loginForm').css('animation', '');
                        }, 500);
                    }
                },
                error: function (xhr, status, error) {
                    submitBtn.removeClass('loading');
                    toastr.error('An error occurred. Please try again.', 'Connection Error');
                    console.error('Login error:', error);
                }
            });
        });

        $('#forgotPassword').on('click', function (e) {
            e.preventDefault();
            toastr.info('Please contact your system administrator for password recovery.', 'Password Recovery');
        });

        // Auto-focus username field
        $('#username').focus();

        // Professional keyboard shortcuts
        $(document).keydown(function(e) {
            // Enter key to submit form when focused on inputs
            if (e.key === 'Enter' && (e.target.id === 'username' || e.target.id === 'password')) {
                $('#loginForm').submit();
            }

            // Escape key to clear form
            if (e.key === 'Escape') {
                $('#username, #password').val('').removeClass('is-invalid is-valid');
                $('#username').focus();
            }
        });

        // Enhanced form validation with real-time feedback
        $('#username, #password').on('input', function() {
            const $this = $(this);
            const value = $this.val().trim();

            if (value.length > 0) {
                $this.removeClass('is-invalid').addClass('is-valid');
            } else {
                $this.removeClass('is-valid is-invalid');
            }
        });

        // Professional loading indicator for page navigation
        $(window).on('beforeunload', function() {
            if ($('.btn-login').hasClass('loading')) {
                return;
            }

            const loadingOverlay = $('<div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-white bg-opacity-75" style="z-index: 9999;"><div class="text-center"><div class="spinner-border text-primary mb-3" role="status"><span class="visually-hidden">Loading...</span></div><p class="text-muted mb-0">Loading...</p></div></div>');
            $('body').append(loadingOverlay);
        });
    });
</script>
</body>
</html>