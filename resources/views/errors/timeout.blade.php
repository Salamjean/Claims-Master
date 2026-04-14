<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Délai de traitement dépassé - Claims Master</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #818cf8;
            --bg: #f8fafc;
            --text: #1e293b;
            --card-bg: rgba(255, 255, 255, 0.8);
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg: #0f172a;
                --text: #f1f5f9;
                --card-bg: rgba(30, 41, 59, 0.7);
            }
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .background-blobs {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            filter: blur(80px);
            opacity: 0.5;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            background: var(--primary);
            animation: move 20s infinite alternate;
        }

        .blob-1 {
            width: 400px;
            height: 400px;
            top: -100px;
            left: -100px;
            background: var(--primary-light);
            animation-duration: 25s;
        }

        .blob-2 {
            width: 300px;
            height: 300px;
            bottom: -50px;
            right: -50px;
            background: var(--primary);
            animation-duration: 30s;
        }

        @keyframes move {
            from { transform: translate(0, 0) scale(1); }
            to { transform: translate(100px, 50px) scale(1.1); }
        }

        .card {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 3rem;
            border-radius: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 500px;
            width: 90%;
            text-align: center;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .icon-container {
            margin-bottom: 2rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 80px;
            height: 80px;
            background: rgba(79, 70, 229, 0.1);
            border-radius: 1.5rem;
            color: var(--primary);
        }

        h1 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
            letter-spacing: -0.025em;
        }

        p {
            line-height: 1.6;
            opacity: 0.8;
            margin-bottom: 2.5rem;
        }

        .btn {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 1rem 2rem;
            border-radius: 1rem;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);
        }

        .btn:hover {
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(79, 70, 229, 0.4);
        }

        .btn:active {
            transform: translateY(0);
        }

        .footer {
            margin-top: 2rem;
            font-size: 0.8rem;
            opacity: 0.5;
        }
    </style>
</head>
<body>
    <div class="background-blobs">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
    </div>

    <div class="card">
        <div class="icon-container">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
        <h1>Temps de réponse trop long</h1>
        <p>{{ $message ?? "Désolé, l'opération a pris trop de temps. Veuillez réessayer ou contacter le support si le problème persiste." }}</p>
        
        <a href="javascript:history.back()" class="btn">
            Retourner en arrière
        </a>

        <div class="footer">
            Code d'erreur: TIMEOUT_EXCEEDED
        </div>
    </div>
</body>
</html>
