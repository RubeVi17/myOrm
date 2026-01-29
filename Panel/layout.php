<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>ORM Lab</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #d8dbe7 0%, #db6f30 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Sistema de Grid */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }

        .col-12 { width: 100%; padding: 0 10px; }
        .col-11 { width: 91.66%; padding: 0 10px; }
        .col-10 { width: 83.33%; padding: 0 10px; }
        .col-9 { width: 75%; padding: 0 10px; }
        .col-8 { width: 66.66%; padding: 0 10px; }
        .col-7 { width: 58.33%; padding: 0 10px; }
        .col-6 { width: 50%; padding: 0 10px; }
        .col-5 { width: 41.66%; padding: 0 10px; }
        .col-4 { width: 33.33%; padding: 0 10px; }
        .col-3 { width: 25%; padding: 0 10px; }
        .col-2 { width: 16.66%; padding: 0 10px; }
        .col-1 { width: 8.33%; padding: 0 10px; }

        nav {
            background: white;
            padding: 15px 25px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }

        nav a {
            margin-right: 20px;
            text-decoration: none;
            color: #db6f30;
            font-weight: 500;
            padding: 8px 15px;
            border-radius: 5px;
            transition: all 0.3s ease;
            display: inline-block;
        }

        nav a:hover {
            background-color: #db6f30;
            color: white;
            text-decoration: none;
        }

        hr {
            border: none;
            border-top: 2px solid rgba(255,255,255,0.2);
            margin: 20px 0;
        }

        h1, h2, h3 {
            color: #916248;
            margin-bottom: 20px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        p {
            color: white;
            font-size: 16px;
            line-height: 1.6;
        }

        ul {
            background: white;
            padding: 25px 25px 25px 50px;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        ul li {
            color: #333;
            margin-bottom: 10px;
            font-size: 16px;
        }

        /* Card/Box */
        .card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .card h2, .card h3 {
            color: #333;
            text-shadow: none;
            margin-top: 0;
        }

        /* Formularios */
        form {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: inline-block;
            font-weight: 500;
            color: #333;
            margin-bottom: 8px;
            margin-right: 10px;
        }

        input, select {
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        input[type="text"],
        input[type="number"],
        input[type="email"],
        input[type="date"],
        select {
            min-width: 200px;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #db6f30;
        }

        button {
            padding: 10px 20px;
            background: #db6f30;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin-right: 10px;
        }

        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(219, 111, 48, 0.4);
        }

        button:active {
            transform: translateY(0);
        }

        /* Code blocks */
        code {
            background-color: #2d2d2d;
            color: #f8f8f2;
            padding: 15px;
            display: block;
            margin-top: 10px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            line-height: 1.5;
        }

        small {
            color: #666;
            font-size: 12px;
            display: block;
            margin-bottom: 5px;
        }

        /* Tablas */
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th {
            background: #db6f30;
            color: white;
            padding: 12px;
            text-align: left;
            font-weight: 500;
            white-space: nowrap;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            color: #333;
        }

        tr:hover {
            background-color: #f8f9fa;
        }

        tr:last-child td {
            border-bottom: none;
        }

        /* Utilidades */
        .text-center {
            text-align: center;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .mt-20 {
            margin-top: 20px;
        }

        pre {
            background: white;
            padding: 15px;
            border-radius: 8px;
            overflow-x: auto;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        section {
            margin-top: 30px;
        }

        select {
            min-width: 220px;
            padding: 8px;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }
        
        select:focus {
            outline: none;
            border-color: #db6f30;
        }

        select option {
            padding: 10px;
            font-size: 14px;
            color: #333;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .col-1, .col-2, .col-3, .col-4, .col-5, .col-6,
            .col-7, .col-8, .col-9, .col-10, .col-11, .col-12 {
                width: 100%;
            }
            
            nav a {
                display: block;
                margin-bottom: 10px;
            }
        }

    </style>
</head>
<body>

<div class="container">
    <nav>
        <a href="/panel/index.php">Home</a>
        <a href="/panel/migrate.php">Migraciones</a>
        <a href="/panel/create.php">Crear</a>
        <a href="/panel/query.php">Query</a>
    </nav>