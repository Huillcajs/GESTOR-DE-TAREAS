<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión Social</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h2>Gestor de Tareas</h2>
                        <h5>Inicia sesión para continuar</h5>
                    </div>
                    <div class="card-body text-center">
                        {{-- Botones de Login --}}
                        <a href="{{ route('social.redirect', ['provider' => 'google']) }}" class="btn btn-danger btn-lg mb-3 w-100">
                            Login con Google 🚀
                        </a>
                        <a href="{{ route('social.redirect', ['provider' => 'github']) }}" class="btn btn-dark btn-lg w-100">
                            Login con GitHub 🐙
                        </a>

                        @error('social_login_error')
                            <div class="alert alert-danger mt-3">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>