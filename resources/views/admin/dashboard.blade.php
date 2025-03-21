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
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
        }

        .sidebar {
            background-color: #f97316;
            color: white;
            min-height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar a {
            color: white;
            padding: 10px 15px;
            display: block;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .sidebar a:hover {
            background-color: #e65c00;
        }

        .sidebar .nav-link.active {
            background-color: #e65c00;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        .theme-dark {
            background-color: #1a2526;
            color: white;
        }

        .theme-dark .card {
            background-color: #2c3e50;
            color: white;
        }

        .theme-dark .sidebar {
            background-color: #1a2526;
        }

        .theme-dark .table {
            background-color: #2c3e50;
            color: white;
        }

        .theme-toggle {
            display: flex;
            align-items: center;
        }

        .theme-toggle input {
            display: none;
        }

        .theme-toggle label {
            cursor: pointer;
            font-size: 1.5rem;
        }

        .table th, .table td {
            vertical-align: middle;
        }

        .btn-sm {
            font-size: 0.875rem;
        }

        .loading-spinner {
            font-size: 1.2rem;
            color: #f97316;
        }

        .pagination-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }

        .pagination-controls a {
            color: #f97316;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .pagination-controls a:hover {
            background-color: #f97316;
            color: white;
        }

        .pagination-controls a.disabled {
            color: #ccc;
            pointer-events: none;
        }

        .pagination-info {
            font-size: 0.9rem;
            color: #666;
        }

        .modal-content {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border: none;
        }

        .modal-dialog {
            max-width: 400px; /* Réduire la largeur de la modale */
        }

        .modal-body {
            padding: 20px;
            text-align: center;
        }

        .modal-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .modal-subtitle {
            font-size: 1.2rem;
            font-weight: 500;
            color: #333;
            margin-bottom: 20px;
        }

        .modal-info {
            text-align: left;
            margin-bottom: 20px;
        }

        .modal-info p {
            margin: 5px 0;
            font-size: 0.95rem;
            color: #555;
        }

        .modal-info p strong {
            color: #333;
            width: 120px;
            display: inline-block;
        }

        .modal-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 15px;
        }

        .modal-actions .btn {
            font-size: 1.2rem;
            padding: 8px;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-actions .btn-toggle-status.active {
            background-color: #f1c40f;
            color: white;
        }

        .modal-actions .btn-toggle-status.inactive {
            background-color: #7f8c8d;
            color: white;
        }

        .modal-actions .btn-delete {
            background-color: #e74c3c;
            color: white;
        }

        .modal-actions .btn-message {
            background-color: #3498db;
            color: white;
        }

        .modal-footer {
            border-top: none;
            justify-content: center;
        }

        .modal-footer .btn-secondary {
            background-color: #f97316;
            border: none;
            color: white;
        }

        .modal-footer .btn-secondary:hover {
            background-color: #e65c00;
        }

        .profile-image {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 15px;
            display: block;
            border: 2px solid #f97316;
        }
    </style>
</head>
<body class="h-full">
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar p-3">
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
        <div class="flex-grow-1 p-4 main-content">
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
            <div id="content">
                <div class="text-center">
                    <p class="loading-spinner">Chargement...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Gestionnaire Details -->
    <div class="modal fade" id="gestionnaireModal" tabindex="-1" aria-labelledby="gestionnaireModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <h5 class="modal-title">Détails du Gestionnaire</h5>
                    <img src="https://via.placeholder.com/80" alt="Profile Image" class="profile-image" id="profileImage">
                    <div class="modal-subtitle" id="gestionnaireName"></div>
                    <div class="modal-info" id="detailsContent">
                        <!-- Les détails seront insérés ici -->
                    </div>
                    <div class="modal-actions" id="gestionnaireActions">
                        <!-- Les icônes d'action seront insérées ici -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
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

        function loadContent(section, url = `/admin/data/${section}`) {
            console.log(`Chargement du contenu pour la section : ${section}, URL : ${url}`);
            fetch(url)
                .then(response => {
                    console.log('Réponse reçue:', response);
                    if (!response.ok) {
                        throw new Error(`Erreur HTTP : ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Données JSON reçues:', data);
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
                                <div class="pagination-controls">
                                    <div>
                                        <a href="#" class="pagination-link ${!data.pagination.prev_page_url ? 'disabled' : ''}" data-url="${data.pagination.prev_page_url || ''}" data-section="${section}">« Previous</a>
                                        <a href="#" class="pagination-link ${!data.pagination.next_page_url ? 'disabled' : ''}" data-url="${data.pagination.next_page_url || ''}" data-section="${section}">Next »</a>
                                    </div>
                                    <div class="pagination-info">
                                        Showing ${(data.pagination.current_page - 1) * data.pagination.per_page + 1} to ${Math.min(data.pagination.current_page * data.pagination.per_page, data.pagination.total)} of ${data.pagination.total} results
                                    </div>
                                </div>
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
                                <div class="pagination-controls">
                                    <div>
                                        <a href="#" class="pagination-link ${!data.pagination.prev_page_url ? 'disabled' : ''}" data-url="${data.pagination.prev_page_url || ''}" data-section="${section}">« Previous</a>
                                        <a href="#" class="pagination-link ${!data.pagination.next_page_url ? 'disabled' : ''}" data-url="${data.pagination.next_page_url || ''}" data-section="${section}">Next »</a>
                                    </div>
                                    <div class="pagination-info">
                                        Showing ${(data.pagination.current_page - 1) * data.pagination.per_page + 1} to ${Math.min(data.pagination.current_page * data.pagination.per_page, data.pagination.total)} of ${data.pagination.total} results
                                    </div>
                                </div>
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
                                        loadContent('dashboard');
                                    } else {
                                        Toastify({
                                            text: 'Erreur lors de l\'ajout du gestionnaire.',
                                            backgroundColor: 'red',
                                            duration: 3000
                                        }).showToast();
                                    }
                                })
                                .catch(error => {
                                    console.error('Erreur lors de l\'ajout du gestionnaire:', error);
                                    Toastify({
                                        text: 'Une erreur est survenue.',
                                        backgroundColor: 'red',
                                        duration: 3000
                                    }).showToast();
                                });
                            });
                        }
                    }

                    attachViewDetailsListeners();
                    attachPaginationListeners();
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des données:', error);
                    contentDiv.innerHTML = `
                        <div class="alert alert-danger">
                            Erreur lors du chargement des données. Veuillez réessayer.
                        </div>
                    `;
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
                            // Remplir le nom du gestionnaire
                            document.getElementById('gestionnaireName').textContent = data.name;

                            // Remplir les détails
                            const details = document.getElementById('detailsContent');
                            details.innerHTML = `
                                <p><strong>Username:</strong> ${data.username}</p>
                                <p><strong>Email:</strong> ${data.email}</p>
                                <p><strong>Statut:</strong> ${data.is_active ? 'Actif' : 'Inactif'}</p>
                            `;

                            // Remplir l'image de profil
                            const profileImage = document.getElementById('profileImage');
                            profileImage.src = data.profile_image_url || 'https://via.placeholder.com/80';

                            // Remplir les icônes d'action
                            const actions = document.getElementById('gestionnaireActions');
                            actions.innerHTML = `
                                <button class="btn btn-toggle-status ${data.is_active ? 'active' : 'inactive'}" data-id="${data.id}" title="${data.is_active ? 'Désactiver' : 'Activer'}">
                                    <i class="fas ${data.is_active ? 'fa-times' : 'fa-check'}"></i>
                                </button>
                                <button class="btn btn-delete" data-id="${data.id}" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn btn-message" data-id="${data.id}" title="Envoyer un Message">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            `;

                            // Ajouter les écouteurs pour les actions
                            attachActionListeners();

                            // Afficher la modale
                            const modal = new bootstrap.Modal(document.getElementById('gestionnaireModal'));
                            modal.show();
                        })
                        .catch(error => {
                            console.error('Erreur lors du chargement des détails:', error);
                            Toastify({
                                text: 'Erreur lors du chargement des détails.',
                                backgroundColor: 'red',
                                duration: 3000
                            }).showToast();
                        });
                });
            });
        }

        // Fonction pour attacher les écouteurs des actions (Activer/Désactiver, Supprimer)
        function attachActionListeners() {
            // Activer/Désactiver
            document.querySelectorAll('.btn-toggle-status').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.dataset.id;
                    fetch(`/admin/gestionnaires/${id}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
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

                            // Mettre à jour le statut dans la modale
                            const statusText = document.querySelector('#detailsContent p:last-child');
                            statusText.innerHTML = `<strong>Statut:</strong> ${data.is_active ? 'Actif' : 'Inactif'}`;

                            // Mettre à jour l'icône et la classe du bouton
                            this.classList.remove('active', 'inactive');
                            this.classList.add(data.is_active ? 'active' : 'inactive');
                            this.title = data.is_active ? 'Désactiver' : 'Activer';
                            this.innerHTML = `<i class="fas ${data.is_active ? 'fa-times' : 'fa-check'}"></i>`;
                        } else {
                            Toastify({
                                text: data.message || 'Erreur lors de la mise à jour du statut.',
                                backgroundColor: 'red',
                                duration: 3000
                            }).showToast();
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la mise à jour du statut:', error);
                        Toastify({
                            text: 'Une erreur est survenue.',
                            backgroundColor: 'red',
                            duration: 3000
                        }).showToast();
                    });
                });
            });

            // Supprimer
            document.querySelectorAll('.btn-delete').forEach(button => {
                button.addEventListener('click', function() {
                    if (!confirm('Êtes-vous sûr de vouloir supprimer ce gestionnaire ?')) {
                        return;
                    }

                    const id = this.dataset.id;
                    fetch(`/admin/gestionnaires/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
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

                            // Fermer la modale
                            const modal = bootstrap.Modal.getInstance(document.getElementById('gestionnaireModal'));
                            modal.hide();

                            // Recharger la liste des gestionnaires
                            const activeSection = document.querySelector('.nav-link.active').dataset.section;
                            loadContent(activeSection);
                        } else {
                            Toastify({
                                text: data.message || 'Erreur lors de la suppression.',
                                backgroundColor: 'red',
                                duration: 3000
                            }).showToast();
                        }
                    })
                    .catch(error => {
                        console.error('Erreur lors de la suppression:', error);
                        Toastify({
                            text: 'Une erreur est survenue.',
                            backgroundColor: 'red',
                            duration: 3000
                        }).showToast();
                    });
                });
            });

            // Laisser l'action "Envoyer un Message" pour plus tard
            document.querySelectorAll('.btn-message').forEach(button => {
                button.addEventListener('click', function() {
                    Toastify({
                        text: 'Fonctionnalité d\'envoi de message à implémenter plus tard.',
                        backgroundColor: 'blue',
                        duration: 3000
                    }).showToast();
                });
            });
        }

        // Fonction pour attacher les écouteurs des liens de pagination
        function attachPaginationListeners() {
            document.querySelectorAll('.pagination-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.dataset.url;
                    const section = this.dataset.section;
                    if (url) {
                        loadContent(section, url);
                    }
                });
            });
        }

        // Gestion de la navigation dans la barre latérale
        navLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                console.log('Clic sur le lien de navigation:', this.dataset.section);
                e.preventDefault();
                navLinks.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                const section = this.dataset.section;
                loadContent(section);
            });
        });

        // Charger le contenu du dashboard au chargement de la page
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Page chargée, chargement du contenu du dashboard...');
            loadContent('dashboard');
        });
    </script>
</body>
</html>
