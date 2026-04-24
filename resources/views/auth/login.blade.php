<!doctype html>
<html lang="es" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Iniciar Sesión</title>

  {{-- Bootstrap + Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-container {
      max-width: 420px;
      width: 100%;
      animation: fadeInUp 0.6s ease-out;
      margin: auto;
    }

    .container-fluid {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0;
    }

    .login-card {
      background: rgba(255, 255, 255, 0.95);
      backdrop-filter: blur(10px);
      border: none;
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      overflow: visible;
      margin: auto;
    }

    .login-header {
      background: linear-gradient(135deg, #157d33 0%, #0d6b2a 100%);
      color: white;
      padding: 2rem 1.5rem;
      text-align: center;
      position: relative;
    }

    .login-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1" fill="rgba(255,255,255,0.1)"/><circle cx="50" cy="10" r="0.5" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
      opacity: 0.3;
    }

    .logo-container {
      position: relative;
      z-index: 1;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .brand-logo {
      width: 120px;
      max-width: 100%;
      height: auto;
      border-radius: 0;
      border: none;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.18);
      transition: transform 0.3s ease;
    }

    .brand-logo:hover {
      transform: scale(1.05);
    }

    .login-title {
      font-size: 1.8rem;
      font-weight: 600;
      margin: 1rem 0 0.5rem 0;
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .login-subtitle {
      font-size: 0.9rem;
      opacity: 0.9;
      margin: 0;
    }

    .login-body {
      padding: 2rem 1.5rem;
    }

    .form-floating {
      margin-bottom: 1.5rem;
    }

    .form-floating > .form-control {
      border: 2px solid #e9ecef;
      border-radius: 12px;
      padding: 1rem 1rem;
      font-size: 1rem;
      transition: all 0.3s ease;
      background-color: #f8f9fa;
    }

    .form-floating > .form-control:focus {
      border-color: #157d33;
      box-shadow: 0 0 0 0.2rem rgba(21, 125, 51, 0.15);
      background-color: #fff;
    }

    .form-floating > label {
      padding: 1rem;
      color: #6c757d;
      font-weight: 500;
    }

    .password-container {
      position: relative;
    }

    .password-toggle {
      position: absolute;
      right: 12px;
      top: 50%;
      transform: translateY(-50%);
      background: none;
      border: none;
      color: #6c757d;
      cursor: pointer;
      padding: 4px;
      border-radius: 4px;
      transition: all 0.2s ease;
      z-index: 10;
    }

    .password-toggle:hover {
      color: #157d33;
      background-color: rgba(21, 125, 51, 0.1);
    }

    .password-toggle:focus {
      outline: none;
      box-shadow: 0 0 0 2px rgba(21, 125, 51, 0.3);
    }

    .form-check {
      margin-bottom: 1.5rem;
    }

    .form-check-input:checked {
      background-color: #157d33;
      border-color: #157d33;
    }

    .form-check-label {
      color: #6c757d;
      font-weight: 500;
      cursor: pointer;
    }

    .btn-login {
      background: linear-gradient(135deg, #157d33 0%, #0d6b2a 100%);
      border: none;
      border-radius: 12px;
      padding: 0.875rem 1.5rem;
      font-weight: 600;
      font-size: 1rem;
      color: white;
      width: 100%;
      transition: all 0.3s ease;
      box-shadow: 0 4px 12px rgba(21, 125, 51, 0.3);
    }

    .btn-login:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(21, 125, 51, 0.4);
      background: linear-gradient(135deg, #0d6b2a 0%, #0a5a22 100%);
    }

    .btn-login:active {
      transform: translateY(0);
    }

    .alert {
      border-radius: 12px;
      border: none;
      font-size: 0.9rem;
    }

    .alert-danger {
      background: linear-gradient(135deg, #fee, #fdd);
      color: #c53030;
    }

    .footer-text {
      text-align: center;
      color: rgba(37, 36, 36, 0.8);
      font-size: 0.85rem;
      margin-top: 2rem;
      font-weight: 500;
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 576px) {
      .login-container {
        margin: 1rem;
      }

      .login-header {
        padding: 1.5rem 1rem;
      }

      .login-body {
        padding: 1.5rem 1rem;
      }
    }
  </style>
</head>
<body>
  <div class="container-fluid p-0">
    <div class="login-container">
      <div class="login-card">
        <div class="login-header">
          <div class="logo-container">
            <img src="{{ asset('images/BioProductosLogo.jpg') }}" alt="BioProductos Logo" class="brand-logo">
          </div>
          <h1 class="login-title">Bienvenido</h1>
          <p class="login-subtitle">Sistema de Cotización Bioproductos</p>
        </div>

        <div class="login-body">
          @if ($errors->any())
            <div class="alert alert-danger">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              @foreach ($errors->all() as $e)
                <div>{{ $e }}</div>
              @endforeach
            </div>
          @endif

          <form method="POST" action="{{ route('login.post') }}" novalidate>
            @csrf

            <div class="form-floating">
              <input
                type="email"
                name="correo"
                class="form-control @error('correo') is-invalid @enderror"
                id="correo"
                placeholder="correo@ejemplo.com"
                value="{{ old('correo') }}"
                required
                autofocus
              >
              <label for="correo">
                <i class="bi bi-envelope-fill me-2"></i>Correo electrónico
              </label>
              @error('correo')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-floating password-container">
              <input
                type="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                id="password"
                placeholder="Contraseña"
                required
              >
              <label for="password">
                <i class="bi bi-lock-fill me-2"></i>Contraseña
              </label>
              <button type="button" class="password-toggle" id="togglePassword" title="Mostrar contraseña">
                <i class="bi bi-eye-fill"></i>
              </button>
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <div class="form-check">
              <input
                class="form-check-input"
                type="checkbox"
                name="remember"
                id="remember"
                {{ old('remember') ? 'checked' : '' }}
              >
              <label class="form-check-label" for="remember">
                <i class="bi bi-check-circle me-2"></i>Mantener sesión iniciada
              </label>
            </div>

            <button type="submit" class="btn btn-login">
              <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar Sesión
            </button>
          </form>
          <p class="footer-text">© {{ date('Y') }} BIOPRODUCTOS - Sistema de Cotización</p>
        </div>
      </div>
    </div>
  </div>

  

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Toggle password visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
      const passwordInput = document.getElementById('password');
      const icon = this.querySelector('i');

      if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('bi-eye-fill');
        icon.classList.add('bi-eye-slash-fill');
        this.title = 'Ocultar contraseña';
      } else {
        passwordInput.type = 'password';
        icon.classList.remove('bi-eye-slash-fill');
        icon.classList.add('bi-eye-fill');
        this.title = 'Mostrar contraseña';
      }
    });

    // Auto-focus and smooth animations
    document.addEventListener('DOMContentLoaded', function() {
      // Add subtle animation to form elements
      const formElements = document.querySelectorAll('.form-floating');
      formElements.forEach((element, index) => {
        element.style.animationDelay = `${index * 0.1}s`;
        element.style.animation = 'fadeInUp 0.5s ease-out forwards';
        element.style.opacity = '0';
      });

      // Focus on email if no errors, otherwise on first error field
      const firstErrorField = document.querySelector('.is-invalid');
      if (firstErrorField) {
        firstErrorField.focus();
      }
    });

    // Enhanced form validation feedback
    document.querySelectorAll('.form-control').forEach(input => {
      input.addEventListener('blur', function() {
        if (this.value.trim() !== '') {
          this.classList.add('is-valid');
        } else {
          this.classList.remove('is-valid');
        }
      });
    });
  </script>
</body>
</html>
