<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - SUPMTI</title>
    
    <!-- Favicons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Meta tags -->
    <meta name="theme-color" content="#0f766e">
    <meta name="description" content="Connexion à l'interface d'administration - SUPMTI">
</head>
<body>
    <div class="container">
        <!-- Logo SUPMTI -->
        <div class="logo-container">
            <img src="{{ asset('images/Logosup.png') }}" alt="SUPMTI Logo" class="logo">
        </div>

        <!-- Formulaire de connexion -->
        <div class="form-container">
            <h2 class="form-title">Connexion</h2>
            
            @if($errors->any())
                <div style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 20px; font-size: 14px;">
                    {{ $errors->first() }}
                </div>
            @endif
            
            <form action="{{ route('login.submit') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email" class="label">
                        Adresse Email
                    </label>
                    <input id="email" name="email" type="email" required 
                           class="input"
                           placeholder="votre.email@supmti.ac.ma"
                           value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label for="password" class="label">
                        Mot de Passe
                    </label>
                    <div class="password-group">
                        <input id="password" name="password" type="password" required 
                               class="input"
                               placeholder="Entrez votre mot de passe">
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i id="password-icon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="checkbox-container">
                    <input id="remember" name="remember" type="checkbox" class="checkbox" value="1" 
                           {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="checkbox-label">
                        Se souvenir de moi
                    </label>
                </div>

                <div>
                    <button type="submit" class="submit-btn">
                        Se Connecter
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>&copy; 2025 SUPMTI Oujda. Tous droits réservés.</p>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }

        // Améliorer l'accessibilité et l'interaction
        document.addEventListener('DOMContentLoaded', function() {
            const rememberCheckbox = document.getElementById('remember');
            const emailInput = document.getElementById('email');
            
            // Si "Se souvenir de moi" était coché, pré-remplir l'email depuis localStorage
            if (localStorage.getItem('rememberedEmail') && !emailInput.value) {
                emailInput.value = localStorage.getItem('rememberedEmail');
                rememberCheckbox.checked = true;
            }
            
            // Sauvegarder l'email quand "Se souvenir de moi" est coché
            rememberCheckbox.addEventListener('change', function() {
                if (this.checked && emailInput.value) {
                    localStorage.setItem('rememberedEmail', emailInput.value);
                } else if (!this.checked) {
                    localStorage.removeItem('rememberedEmail');
                }
            });
            
            // Sauvegarder l'email lors de la saisie si "Se souvenir de moi" est coché
            emailInput.addEventListener('input', function() {
                if (rememberCheckbox.checked) {
                    localStorage.setItem('rememberedEmail', this.value);
                }
            });
            
            // Support du clavier pour la checkbox (espace pour cocher/décocher)
            rememberCheckbox.addEventListener('keydown', function(e) {
                if (e.key === ' ') {
                    e.preventDefault();
                    this.checked = !this.checked;
                    this.dispatchEvent(new Event('change'));
                }
            });
        });
    </script>
</body>
</html>