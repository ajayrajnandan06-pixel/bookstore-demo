<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Bookstore Admin</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .register-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 500px;
        }
        .register-header {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .register-header i {
            font-size: 3.5rem;
            margin-bottom: 15px;
        }
        .register-header h1 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .register-header p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        .register-body {
            padding: 40px;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #11998e;
            box-shadow: 0 0 0 0.25rem rgba(17, 153, 142, 0.25);
        }
        .input-group-text {
            background-color: #f8f9fa;
            border: 2px solid #e0e0e0;
            border-right: none;
        }
        .btn-register {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            padding: 12px;
            width: 100%;
            transition: all 0.3s;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(17, 153, 142, 0.3);
        }
        .footer-links {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
        }
        .footer-links a {
            color: #11998e;
            text-decoration: none;
        }
        .footer-links a:hover {
            text-decoration: underline;
        }
        .password-requirements {
            font-size: 0.8rem;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <i class="fas fa-user-plus"></i>
            <h1>Create Account</h1>
            <p>Join Bookstore Admin Portal</p>
        </div>
        
        <div class="register-body">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    @foreach($errors->all() as $error)
                        <p class="mb-1"><i class="fas fa-exclamation-circle"></i> {{ $error }}</p>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <form method="POST" action="{{ route('register') }}">
                @csrf
                
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label fw-semibold">Full Name</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" 
                                   placeholder="Enter your full name" required>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" name="email" value="{{ old('email') }}" 
                               placeholder="Enter your email" required>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" name="password" 
                                   placeholder="Create password" required>
                        </div>
                        <div class="password-requirements">
                            Minimum 6 characters
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" name="password_confirmation" 
                                   placeholder="Confirm password" required>
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="terms" id="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="#" class="text-decoration-none">Terms & Conditions</a>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-register mb-3">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
                
                <div class="text-center mb-3">
                    <span class="text-muted">or</span>
                </div>
                
                <a href="{{ route('login') }}" class="btn btn-outline-success w-100">
                    <i class="fas fa-sign-in-alt"></i> Back to Login
                </a>
            </form>
            
            <div class="footer-links">
                <p class="text-muted small">© 2023 Bookstore Admin. All rights reserved.</p>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Password strength indicator (basic)
        document.querySelector('input[name="password"]').addEventListener('input', function(e) {
            const password = e.target.value;
            const requirements = document.querySelector('.password-requirements');
            
            if (password.length < 6) {
                requirements.innerHTML = '❌ Minimum 6 characters';
                requirements.style.color = '#dc3545';
            } else {
                requirements.innerHTML = '✅ Minimum 6 characters';
                requirements.style.color = '#198754';
            }
        });
    </script>
</body>
</html>