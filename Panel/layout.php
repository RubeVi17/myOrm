<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ORM Lab</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #db6f30;
            --primary-dark: #c05a1e;
            --primary-light: #e88f5e;
            --secondary: #916248;
            --background: #f5f6fa;
            --card-bg: #ffffff;
            --text-dark: #2c3e50;
            --text-muted: #6c757d;
            --border: #e0e6ed;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --shadow: 0 2px 8px rgba(0,0,0,0.08);
            --shadow-lg: 0 4px 20px rgba(0,0,0,0.12);
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--background);
            min-height: 100vh;
            color: var(--text-dark);
            line-height: 1.6;
        }

        .container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 20px;
        }

        /* ========================= */
        /* SISTEMA DE GRID MEJORADO  */
        /* ========================= */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -12px;
            margin-bottom: 24px;
        }

        .row-no-gutters {
            margin: 0;
        }

        .row-no-gutters > [class*="col-"] {
            padding: 0;
        }

        /* Columnas */
        [class*="col-"] {
            padding: 0 12px;
            width: 100%;
        }

        .col-1 { width: 8.333%; }
        .col-2 { width: 16.666%; }
        .col-3 { width: 25%; }
        .col-4 { width: 33.333%; }
        .col-5 { width: 41.666%; }
        .col-6 { width: 50%; }
        .col-7 { width: 58.333%; }
        .col-8 { width: 66.666%; }
        .col-9 { width: 75%; }
        .col-10 { width: 83.333%; }
        .col-11 { width: 91.666%; }
        .col-12 { width: 100%; }

        /* Auto width */
        .col-auto {
            flex: 0 0 auto;
            width: auto;
        }

        /* ========================= */
        /* NAVEGACIÓN               */
        /* ========================= */
        nav {
            background: var(--card-bg);
            padding: 0;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            overflow: hidden;
        }

        nav a {
            display: inline-block;
            padding: 16px 24px;
            text-decoration: none;
            color: var(--text-dark);
            font-weight: 500;
            font-size: 14px;
            transition: all 0.2s ease;
            position: relative;
        }

        nav a:hover {
            background-color: var(--primary);
            color: white;
        }

        nav a.active {
            background-color: var(--primary);
            color: white;
        }

        nav a i {
            margin-right: 8px;
        }

        /* ========================= */
        /* TÍTULOS Y TIPOGRAFÍA      */
        /* ========================= */
        h1, h2, h3, h4, h5, h6 {
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 20px;
            line-height: 1.3;
        }

        h1 { font-size: 32px; }
        h2 { font-size: 26px; }
        h3 { font-size: 20px; }
        h4 { font-size: 18px; }

        p {
            color: var(--text-muted);
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 16px;
        }

        /* ========================= */
        /* CARDS Y CONTENEDORES      */
        /* ========================= */
        .card {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 24px;
            transition: box-shadow 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-lg);
        }

        .card-header {
            padding-bottom: 16px;
            margin-bottom: 20px;
            border-bottom: 2px solid var(--border);
        }

        .card-header h3 {
            margin-bottom: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-body {
            padding: 0;
        }

        .card-footer {
            padding-top: 16px;
            margin-top: 20px;
            border-top: 1px solid var(--border);
        }

        /* ========================= */
        /* FORMULARIOS MEJORADOS     */
        /* ========================= */
        form {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 24px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -8px;
        }

        .form-row > .form-group,
        .form-row > [class*="col-"] {
            padding: 0 8px;
        }

        label {
            display: block;
            font-weight: 500;
            color: var(--text-dark);
            margin-bottom: 8px;
            font-size: 14px;
        }

        label.required::after {
            content: " *";
            color: var(--danger);
        }

        label i {
            margin-right: 6px;
            color: var(--primary);
        }

        /* Input base styles */
        input, 
        select, 
        textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.2s ease;
            background: white;
            color: var(--text-dark);
        }

        input:focus, 
        select:focus, 
        textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(219, 111, 48, 0.1);
        }

        input:disabled,
        select:disabled,
        textarea:disabled {
            background: #f8f9fa;
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* Input con iconos */
        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group-icon {
            position: absolute;
            left: 16px;
            color: var(--text-muted);
            pointer-events: none;
        }

        .input-group input {
            padding-left: 45px;
        }

        .input-group-append,
        .input-group-prepend {
            display: flex;
            align-items: center;
        }

        .input-group-text {
            padding: 12px 16px;
            background: var(--border);
            border: 2px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .input-group-prepend .input-group-text {
            border-right: none;
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .input-group-prepend + input {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        /* Select mejorado */
        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236c757d' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 40px;
            cursor: pointer;
        }

        select option {
            padding: 12px;
        }

        /* Textarea */
        textarea {
            resize: vertical;
            min-height: 100px;
            line-height: 1.6;
        }

        /* Checkbox y Radio mejorados */
        .form-check {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .form-check input[type="checkbox"],
        .form-check input[type="radio"] {
            width: 20px;
            height: 20px;
            margin-right: 10px;
            cursor: pointer;
        }

        .form-check label {
            margin-bottom: 0;
            cursor: pointer;
            font-weight: 400;
        }

        /* Input inline (varios inputs en una línea) */
        .form-inline {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 12px;
        }

        .form-inline label {
            margin-bottom: 0;
            margin-right: 8px;
        }

        .form-inline input,
        .form-inline select {
            width: auto;
            flex: 1;
            min-width: 200px;
        }

        /* Helper text */
        .form-text {
            display: block;
            margin-top: 6px;
            font-size: 13px;
            color: var(--text-muted);
        }

        .form-text i {
            margin-right: 4px;
        }

        /* Validación */
        .is-invalid {
            border-color: var(--danger) !important;
        }

        .is-valid {
            border-color: var(--success) !important;
        }

        .invalid-feedback,
        .valid-feedback {
            display: block;
            margin-top: 6px;
            font-size: 13px;
        }

        .invalid-feedback {
            color: var(--danger);
        }

        .valid-feedback {
            color: var(--success);
        }

        /* ========================= */
        /* BOTONES                   */
        /* ========================= */
        button,
        .btn,
        a.button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: inherit;
            white-space: nowrap;
        }

        /* Primary button */
        .btn-primary,
        button:not([class*="btn-"]),
        a.button:not([class*="btn-"]) {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover,
        button:not([class*="btn-"]):hover,
        a.button:not([class*="btn-"]):hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(219, 111, 48, 0.3);
        }

        /* Secondary button */
        .btn-secondary {
            background: var(--text-muted);
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        /* Outline buttons */
        .btn-outline-primary {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            color: white;
        }

        /* Other variants */
        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-warning {
            background: var(--warning);
            color: #212529;
        }

        .btn-info {
            background: var(--info);
            color: white;
        }

        /* Button sizes */
        .btn-sm {
            padding: 8px 16px;
            font-size: 13px;
        }

        .btn-lg {
            padding: 16px 32px;
            font-size: 16px;
        }

        /* Button block */
        .btn-block {
            display: flex;
            width: 100%;
            justify-content: center;
        }

        /* Button group */
        .btn-group {
            display: flex;
            gap: 8px;
        }

        button:active,
        .btn:active {
            transform: translateY(0);
        }

        /* ========================= */
        /* TABLAS MEJORADAS          */
        /* ========================= */
        .table-container {
            background: var(--card-bg);
            padding: 24px;
            border-radius: 12px;
            box-shadow: var(--shadow);
            margin-bottom: 24px;
            overflow-x: auto;
        }

        .table-title {
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--border);
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            font-size: 14px;
        }

        thead {
            background: var(--primary);
            color: white;
        }

        th {
            padding: 14px 16px;
            text-align: left;
            font-weight: 600;
            white-space: nowrap;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        th:first-child {
            border-top-left-radius: 8px;
        }

        th:last-child {
            border-top-right-radius: 8px;
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border);
            color: var(--text-dark);
        }

        tbody tr {
            transition: background-color 0.2s ease;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        tbody tr:last-child td:first-child {
            border-bottom-left-radius: 8px;
        }

        tbody tr:last-child td:last-child {
            border-bottom-right-radius: 8px;
        }

        /* Tabla striped */
        .table-striped tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        /* Tabla bordered */
        .table-bordered {
            border: 2px solid var(--border);
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid var(--border);
        }

        /* ========================= */
        /* CODE BLOCKS               */
        /* ========================= */
        code {
            background-color: #2d2d2d;
            color: #f8f8f2;
            padding: 16px;
            display: block;
            border-radius: 8px;
            font-family: 'Monaco', 'Menlo', 'Courier New', monospace;
            font-size: 13px;
            line-height: 1.6;
            overflow-x: auto;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
        }

        code::-webkit-scrollbar {
            height: 8px;
        }

        code::-webkit-scrollbar-track {
            background: #1a1a1a;
            border-radius: 4px;
        }

        code::-webkit-scrollbar-thumb {
            background: #4a4a4a;
            border-radius: 4px;
        }

        small {
            font-size: 13px;
            color: var(--text-muted);
            display: block;
            margin-bottom: 8px;
        }

        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 16px 0;
        }

        /* ========================= */
        /* BADGES Y ALERTS           */
        /* ========================= */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-primary { background: var(--primary); color: white; }
        .badge-success { background: var(--success); color: white; }
        .badge-danger { background: var(--danger); color: white; }
        .badge-warning { background: var(--warning); color: #212529; }
        .badge-info { background: var(--info); color: white; }

        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
            display: flex;
            align-items: start;
            gap: 12px;
        }

        .alert i {
            font-size: 18px;
            margin-top: 2px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-color: var(--success);
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border-color: var(--danger);
        }

        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border-color: var(--warning);
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border-color: var(--info);
        }

        /* ========================= */
        /* UTILIDADES                */
        /* ========================= */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }

        .mb-0 { margin-bottom: 0 !important; }
        .mb-1 { margin-bottom: 8px !important; }
        .mb-2 { margin-bottom: 16px !important; }
        .mb-3 { margin-bottom: 24px !important; }
        .mb-4 { margin-bottom: 32px !important; }

        .mt-0 { margin-top: 0 !important; }
        .mt-1 { margin-top: 8px !important; }
        .mt-2 { margin-top: 16px !important; }
        .mt-3 { margin-top: 24px !important; }
        .mt-4 { margin-top: 32px !important; }

        .d-flex { display: flex; }
        .d-block { display: block; }
        .d-inline { display: inline; }
        .d-none { display: none; }

        .justify-content-between { justify-content: space-between; }
        .justify-content-center { justify-content: center; }
        .justify-content-end { justify-content: end; }

        .align-items-center { align-items: center; }
        .align-items-start { align-items: start; }
        .align-items-end { align-items: end; }

        .gap-1 { gap: 8px; }
        .gap-2 { gap: 16px; }
        .gap-3 { gap: 24px; }

        .comment-content {
            margin-top: 5px;
            font-size: 14px;
            color: #555;
            word-wrap: break-word;
            overflow-wrap: break-word;  
            word-break: break-word;    
        }

        /* ========================= */
        /* LOADING SPINNER           */
        /* ========================= */
        .spinner {
            border: 3px solid rgba(0,0,0,0.1);
            border-left-color: var(--primary);
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* ========================= */
        /* RESPONSIVE                */
        /* ========================= */
        @media (max-width: 1200px) {
            .container {
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            [class*="col-"] {
                width: 100% !important;
            }

            nav a {
                display: block;
                border-bottom: 1px solid var(--border);
            }

            nav a:last-child {
                border-bottom: none;
            }

            .form-inline {
                flex-direction: column;
                align-items: stretch;
            }

            .form-inline input,
            .form-inline select {
                width: 100%;
            }

            .btn-group {
                flex-direction: column;
            }

            .btn-group button,
            .btn-group .btn {
                width: 100%;
            }
        }

        /* ========================= */
        /* SCROLLBAR CUSTOM          */
        /* ========================= */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--background);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }

        /* ============================================ */
        /* MARKDOWN CONTENT - OVERRIDES ESPECÍFICOS   */
        /* ============================================ */

        /* Code inline - Aumentar especificidad */
        .card .card-body .markdown-content code,
        .markdown-content code {
            background: #f6f8fa;
            color: #e83e8c;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 13px;
            font-family: 'Courier New', monospace;
            display: inline;
            border: 1px solid #e1e4e8;
            margin: 0 2px;
        }

        /* Pre blocks - bloques de código */
        .card .card-body .markdown-content pre,
        .markdown-content pre {
            background: #2d2d2d;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            margin: 20px 0;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
        }

        /* Code dentro de pre - fondo oscuro */
        .card .card-body .markdown-content pre code,
        .markdown-content pre code {
            background: transparent;
            color: #f8f8f2;
            padding: 0;
            display: block;
            border: none;
            font-size: 13px;
            line-height: 1.6;
        }

        /* Títulos en markdown */
        .markdown-content h1 {
            font-size: 32px;
            margin-top: 32px;
            margin-bottom: 16px;
            border-bottom: 2px solid var(--border);
            padding-bottom: 8px;
            color: var(--text-dark);
            text-shadow: none;
        }

        .markdown-content h2 {
            font-size: 26px;
            margin-top: 24px;
            margin-bottom: 12px;
            color: var(--text-dark);
            text-shadow: none;
        }

        .markdown-content h3 {
            font-size: 20px;
            margin-top: 20px;
            margin-bottom: 10px;
            color: var(--text-dark);
            text-shadow: none;
        }

        /* Párrafos */
        .markdown-content p {
            margin-bottom: 16px;
            color: var(--text-dark);
            line-height: 1.8;
        }

        /* Listas en markdown - sin estilos de card */
        .markdown-content ul,
        .markdown-content ol {
            margin-left: 24px;
            margin-bottom: 16px;
            padding: 0;
            background: transparent;
            box-shadow: none;
            border-radius: 0;
        }

        .markdown-content li {
            margin-bottom: 8px;
            color: var(--text-dark);
        }

        /* Blockquotes */
        .markdown-content blockquote {
            border-left: 4px solid var(--primary);
            padding: 12px 16px;
            margin: 16px 0;
            color: var(--text-muted);
            font-style: italic;
            background: #f8f9fa;
            border-radius: 4px;
        }

        /* Links */
        .markdown-content a {
            color: var(--primary);
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: border-color 0.2s ease;
        }

        .markdown-content a:hover {
            border-bottom-color: var(--primary);
        }

        /* Tablas */
        .markdown-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        .markdown-content table th,
        .markdown-content table td {
            border: 1px solid var(--border);
            padding: 12px;
            text-align: left;
        }

        .markdown-content table th {
            background: var(--primary);
            color: white;
            font-weight: 600;
        }

        .markdown-content table tbody tr:hover {
            background-color: #f8f9fa;
        }

        /* Imágenes */
        .markdown-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 16px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* HR */
        .markdown-content hr {
            border: none;
            border-top: 2px solid var(--border);
            margin: 32px 0;
        }

        /* Strong y Em */
        .markdown-content strong {
            font-weight: 600;
            color: var(--text-dark);
        }

        .markdown-content em {
            font-style: italic;
        }

        /* ULTRA ESPECÍFICO - solo si lo anterior no funciona */
        body .container .card .card-body .markdown-content code {
            background: #f6f8fa !important;
            color: #e83e8c !important;
        }

        body .container .card .card-body .markdown-content pre {
            background: #2d2d2d !important;
        }

        body .container .card .card-body .markdown-content pre code {
            background: transparent !important;
            color: #f8f8f2 !important;
        }

    </style>
</head>
<body>

<div class="container">
    <?php
    //si estamos en panel/index.php marcar home como activo
    $currentFile = basename($_SERVER['PHP_SELF']);

    if ($currentFile !== 'index.php') {
    ?>
    <nav>
        <a href="/panel/index.php"><i class="fas fa-home"></i> Home</a>
        <a href="/panel/migrate.php"><i class="fas fa-database"></i> Base de datos</a>
        <a href="/panel/model.php"><i class="fas fa-cube"></i> Modelo</a>
        <a href="/panel/create.php"><i class="fas fa-plus-circle"></i> Crear</a>
        <a href="/panel/update.php"><i class="fas fa-edit"></i> Actualizar</a>
        <a href="/panel/query.php"><i class="fas fa-search"></i> Query</a>
    </nav>
    <?php
    }
    ?>