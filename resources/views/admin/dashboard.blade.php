<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ISI Burger - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Arial', sans-serif; }
        .sidebar { background-color: #f97316; color: white; min-height: 100vh; }
        .sidebar a { color: white; }
        .sidebar a:hover { background-color: #e65c00; }
        .card { border-radius: 10px; }
        .theme-dark { background-color: #1a2526; color: white; }
        .theme-dark .card { background-color: #2c3e50; color: white; }
        .theme-dark .sidebar { background-color: #1a2526; }
        .theme-dark .table { background-color: #2c3e50; color: white; }
        .theme-toggle { display: flex; align-items: center; }
        .theme-toggle input { display: none; }
        .theme-toggle label { cursor: pointer; font-size: 1.5rem; }
        .nav-link.active { background-color: #e65c00; }
    </style>
</head>
<body class="h-full">
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3" style="width: 250px;">
            <h4 class="text-center">ISI Burger</h4>
            <hr>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-section="dashboard">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-section="gestionnaires">Gestionnaires</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-section="actifs">Actifs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-section="desactives">Désactivés</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-section="ajouter">Ajouter</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Déconnexion
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="flex-grow-1 p-4">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Tableau de Bord</h2>
                <div class="d-flex align-items-center">
                    <div class="theme-toggle me-3">
                        <input type="checkbox" id="theme-toggle">
                        <label for="theme-toggle">
                            <i class="fas fa-sun text-warning" id="theme-icon"></i>
                        </label>
                    </div>
                    <div class="dropdown">
                        <a class="dropdown-toggle text-dark" href="#" role="button" id="dropdownMenuLink" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="{{ route('profile.edit') }}">Profil</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form-header').submit();">
                                    Déconnexion
                                </a>
                                <form id="logout-form-header" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Dynamic Content -->
            <div id="content"></div>

            <!-- Gestionnaire Details -->
            <div class="card p-3 d-none" id="gestionnaireDetails">
                <h5>Détails du Gestionnaire</h5>
                <div id="detailsContent"></div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        // Theme Toggle
        const themeToggle = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const html = document.documentElement;
        const currentTheme = localStorage.getItem('theme') || 'light';

        // Appliquer le thème initial
        html.setAttribute('data-theme', currentTheme);
        if (currentTheme === 'dark') {
            document.body.classList.add('theme-dark');
            themeIcon.classList.remove('fa-sun');
            themeIcon.classList.add('fa-moon');
            themeToggle.checked = true;
        }

        themeToggle.addEventListener('change', () => {
            const newTheme = themeToggle.checked ? 'dark' : 'light';
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            document.body.classList.toggle('theme-dark');
            themeIcon.classList.toggle('fa-sun');
            themeIcon.classList.toggle('fa-moon');
        });

        // Load Content Dynamically
        const contentDiv = document.getElementById('content');
        const navLinks = document.querySelectorAll('.nav-link[data-section]');

        function loadContent(section) {
            fetch(`/admin/data/${section}`)
                .then(response => response.json())
                .then(data => {
                    if (section === 'dashboard') {
                        contentDiv.innerHTML = `
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="card p-3">
                                        <h5>Total Gestionnaires</h5>
                                        <h3>${data.totalGestionnaires}</h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card p-3">
                                        <h5>Gestionnaires Actifs</h5>
                                        <h3>${data.activeGestionnaires}</h3>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card p-3">
                                        <h5>Gestionnaires Inactifs</h5>
                                        <h3>${data.inactiveGestionnaires}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="card p-3 mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Liste des Gestionnaires</h5>
                                </div>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="gestionnairesTable">
                                        ${data.gestionnaires.map(g => `
                                            <tr data-id="${g.id}">
                                                <td>${g.name}</td>
                                                <td>${g.username}</td>
                                                <td>${g.email}</td>
                                                <td>${g.is_active ? 'Actif' : 'Inactif'}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-info view-details">Détails</button>
                                                </td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                                <div>${data.pagination}</div>
                            </div>
                        `;
                    } else if (section === 'gestionnaires' || section === 'actifs' || section === 'desactives') {
                        contentDiv.innerHTML = `
                            <div class="card p-3">
                                <h5>Liste des Gestionnaires</h5>
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Statut</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="gestionnairesTable">
                                        ${data.gestionnaires.map(g => `
                                            <tr data-id="${g.id}">
                                                <td>${g.name}</td>
                                                <td>${g.username}</td>
                                                <td>${g.email}</td>
                                                <td>${g.is_active ? 'Actif' : 'Inactif'}</td>
                                                <td>
                                                    <button class="btn btn-sm btn-info view-details">Détails</button>
                                                </td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        `;
                    } else if (section === 'ajouter') {
                        contentDiv.innerHTML = `
                            <div class="card p-3">
                                <h5>Ajouter un Gestionnaire</h5>
                                <form id="addGestionnaireForm">
                                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').content}">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Ajouter</button>
                                </form>
                            </div>
                        `;
                        // Ajouter un écouteur d'événement pour le formulaire
                        const addForm = document.getElementById('addGestionnaireForm');
                        if (addForm) {
                            addForm.addEventListener('submit', function(e) {
                                e.preventDefault();
                                const formData = new FormData(this);
                                fetch('/admin/gestionnaires', {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        Toastify({
                                            text: data.message,
                                            backgroundColor: 'green',
                                            duration: 3000
                                        }).showToast();
                                        loadContent('dashboard'); // Recharger le dashboard après ajout
                                    } else {
                                        Toastify({
                                            text: 'Erreur lors de l\'ajout du gestionnaire.',
                                            backgroundColor: 'red',
                                            duration: 3000
                                        }).showToast();
                                    }
                                })
                                .catch(error => {
                                    Toastify({
                                        text: 'Une erreur est survenue.',
                                        backgroundColor: 'red',
                                        duration: 3000
                                    }).showToast();
                                });
                            });
                        }
                    }

                    // Ré-attacher les écouteurs pour les détails
                    attachViewDetailsListeners();
                })
                .catch(error => {
                    Toastify({
                        text: 'Erreur lors du chargement des données.',
                        backgroundColor: 'red',
                        duration: 3000
                    }).showToast();
                });
        }

        // Fonction pour attacher les écouteurs des boutons "Détails"
        function attachViewDetailsListeners() {
            document.querySelectorAll('.view-details').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id || this.closest('tr').dataset.id;
                    fetch(`/admin/gestionnaires/${id}`)
                        .then(response => response.json())
                        .then(data => {
                            const details = document.getElementById('detailsContent');
                            details.innerHTML = `
                                <p><strong>Nom:</strong> ${data.name}</p>
                                <p><strong>Username:</strong> ${data.username}</p>
                                <p><strong>Email:</strong> ${data.email}</p>
                                <p><strong>Statut:</strong> ${data.is_active ? 'Actif' : 'Inactif'}</p>
                                <button class="btn btn-sm btn-warning toggle-status" data-id="${data.id}">
                                    ${data.is_active ? 'Désactiver' : 'Activer'}
                                </button>
                                <button class="btn btn-sm btn-danger delete-gestionnaire" data-id="${data.id}">Supprimer</button>
                                <button class="btn btn-sm btn-primary send-message" data-id="${data.id}">Envoyer un Message</button>
                            `;
                            document.getElementById('gestionnaireDetails').classList.remove('d-none');
                        });
                });
            });
        }

        // Gestion de la navigation dans la barre latérale
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                navLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                const section = this.dataset.section;
                loadContent(section);
            });
        });

        // Charger le contenu du dashboard au chargement de la page
        document.addEventListener('DOMContentLoaded', () => {
            loadContent('dashboard');
        });
    </script>
</body>
</html>
