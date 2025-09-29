<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TrelloClone - @yield('title')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        .board-header {
            min-height: 100px;
            border-radius: 8px;
            color: white;
        }
        .lists-container {
            display: flex;
            overflow-x: auto;
            min-height: 400px;
            padding: 10px 0;
        }
        .list-card {
            min-width: 300px;
            margin-right: 15px;
        }
        .card-item {
            cursor: pointer;
            transition: all 0.2s;
        }
        .card-item:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
        }
        .sortable-ghost {
            opacity: 0.4;
        }
        .activity-item {
            border-left: 3px solid #007bff;
            background-color: #f8f9fa;
            padding: 8px 12px;
            margin-bottom: 8px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">
                <i class="fas fa-trello"></i> TrelloClone
            </a>
            <div class="navbar-nav ms-auto">
                @auth
                    <span class="navbar-text me-3">
                        Hello, {{ Auth::user()->name }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-outline-light btn-sm">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-sm">Login</a>
                @endauth
            </div>
        </div>
    </nav>

    <main>
        @yield('content')
    </main>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Sortable.js untuk drag & drop -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    
    @stack('scripts')
</body>
</html>