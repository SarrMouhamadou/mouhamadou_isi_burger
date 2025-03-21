<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier le profil - ISI Burger</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Arial', sans-serif; }
        .container { max-width: 800px; }
        h3 { margin-top: 2rem; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1>Modifier votre profil</h1>

        <!-- Section pour mettre à jour les informations du profil -->
        <div class="card p-4 mb-4">
            @include('profile.partials.update-profile-information-form')
        </div>

        <!-- Section pour mettre à jour le mot de passe -->
        <div class="card p-4 mb-4">
            @include('profile.partials.update-password-form')
        </div>

        <!-- Section pour supprimer le compte -->
        <div class="card p-4">
            @include('profile.partials.delete-user-form')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
