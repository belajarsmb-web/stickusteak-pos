<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Stickusteak POS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --gold-primary: #D4AF37;
            --gold-light: #F4DF89;
            --gold-dark: #AA8C2C;
            --black-primary: #0a0a0a;
            --black-secondary: #1a1a1a;
            --black-tertiary: #2a2a2a;
        }
        
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Playfair Display', serif;
        }
        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, var(--black-primary) 0%, var(--black-secondary) 50%, var(--black-tertiary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(212,175,55,0.1) 0%, transparent 70%);
            animation: rotate 30s linear infinite;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .login-card {
            background: rgba(26, 26, 26, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5), 0 0 0 1px rgba(212,175,55,0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold-primary) 50%, var(--gold-light) 100%);
            color: var(--black-primary);
            padding: 40px 30px;
            text-align: center;
            position: relative;
        }
        
        .login-header::after {
            content: '';
            position: absolute;
            bottom: -20px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 4px;
            background: var(--gold-primary);
            border-radius: 2px;
        }
        
        .login-header i {
            font-size: 3.5rem;
            margin-bottom: 15px;
            display: block;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .login-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            letter-spacing: 1px;
        }
        
        .login-header p {
            margin: 10px 0 0;
            opacity: 0.8;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .login-body {
            padding: 40px 30px;
        }
        
        .form-control, .form-select {
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(212,175,55,0.3);
            color: #fff;
            padding: 14px 18px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,0.1);
            border-color: var(--gold-primary);
            box-shadow: 0 0 0 3px rgba(212,175,55,0.2);
            color: #fff;
        }
        
        .form-control::placeholder {
            color: rgba(255,255,255,0.4);
        }
        
        .form-label {
            color: rgba(255,255,255,0.8);
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        
        .btn-login {
            background: linear-gradient(135deg, var(--gold-dark) 0%, var(--gold-primary) 50%, var(--gold-light) 100%);
            border: none;
            color: var(--black-primary);
            padding: 16px;
            font-weight: 600;
            font-size: 1rem;
            border-radius: 10px;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            width: 100%;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212,175,55,0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .alert {
            border-radius: 10px;
            border: 1px solid;
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
        
        .input-group-text {
            background: rgba(212,175,55,0.2);
            border: 1px solid rgba(212,175,55,0.3);
            color: var(--gold-primary);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(212,175,55,0.2);
        }
        
        .login-footer p {
            color: rgba(255,255,255,0.4);
            font-size: 0.85rem;
            margin: 0;
        }
        
        /* Floating particles */
        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(212,175,55,0.3);
            animation: float 15s infinite;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0) translateX(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) translateX(50px);
                opacity: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Floating particles -->
    <div class="particle" style="width: 10px; height: 10px; left: 10%; bottom: -10px; animation-delay: 0s;"></div>
    <div class="particle" style="width: 15px; height: 15px; left: 30%; bottom: -10px; animation-delay: 3s;"></div>
    <div class="particle" style="width: 8px; height: 8px; left: 50%; bottom: -10px; animation-delay: 6s;"></div>
    <div class="particle" style="width: 12px; height: 12px; left: 70%; bottom: -10px; animation-delay: 9s;"></div>
    <div class="particle" style="width: 10px; height: 10px; left: 90%; bottom: -10px; animation-delay: 12s;"></div>

    <div class="login-card">
        <div class="login-header">
            <i class="bi bi-reception-4"></i>
            <h2>🍽️ Stickusteak</h2>
            <p>Restaurant Management System</p>
        </div>
        <div class="login-body">
            <div id="alertContainer"></div>
            <form id="loginForm">
                <div class="mb-4">
                    <label for="username" class="form-label">
                        <i class="bi bi-person me-2"></i>Username or Email
                    </label>
                    <input type="text" class="form-control form-control-lg" id="username" name="username" placeholder="Enter username or email" required autofocus>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-2"></i>Password
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Enter password" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword" style="border-radius: 0 10px 10px 0; border-left: none;">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-login btn-lg">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </button>
                </div>
            </form>
            <div class="login-footer">
                <p>© <?php echo date('Y'); ?> Stickusteak. Premium Edition.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const pwd = document.getElementById('password');
            const icon = this.querySelector('i');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        });

        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const orig = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Authenticating...';
            
            const data = {
                username: document.getElementById('username').value,
                password: document.getElementById('password').value
            };
            
            try {
                const res = await fetch('/php-native/api/auth/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const result = await res.json();
                
                if (result.success) {
                    document.getElementById('alertContainer').innerHTML = `
                        <div class="alert alert-success">
                            <i class="bi bi-check-circle me-2"></i>Login successful! Redirecting...
                        </div>
                    `;
                    setTimeout(() => {
                        window.location.href = '/php-native/pages/dashboard.php';
                    }, 1000);
                } else {
                    document.getElementById('alertContainer').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>${result.message || 'Invalid credentials'}
                        </div>
                    `;
                    btn.disabled = false;
                    btn.innerHTML = orig;
                }
            } catch (err) {
                document.getElementById('alertContainer').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>Connection error. Please try again.
                    </div>
                `;
                btn.disabled = false;
                btn.innerHTML = orig;
            }
        });
    </script>
</body>
</html>
