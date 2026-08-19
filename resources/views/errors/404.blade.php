<!DOCTYPE html>
<html lang="en">
<head>
    @include('includes.head-assets')
    <title>404 — Page Not Found | CSMS</title>
    <style>
        [x-cloak] { display: none !important; }

        :root {
            --clsu-green:  #009639;
            --clsu-cobra:  #1a5f30;
            --clsu-yellow: #ffd700;
            --clsu-gold:   #e0a70d;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background: #F4F6F9;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        /* ── Subtle mesh background ── */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 10%, rgba(0,150,57,0.07) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 85%, rgba(0,150,57,0.05) 0%, transparent 60%);
            pointer-events: none;
        }

        /* ── Faint grid lines ── */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(0,150,57,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0,150,57,0.04) 1px, transparent 1px);
            background-size: 48px 48px;
            pointer-events: none;
        }

        .card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            background: #fff;
            border-radius: 20px;
            border: 1px solid #E3E8EB;
            box-shadow: 0 8px 48px rgba(0,0,0,0.10), 0 2px 8px rgba(0,0,0,0.04);
            overflow: hidden;
            animation: slideUp 0.4s cubic-bezier(.22,.68,0,1.2) both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px) scale(0.97); }
            to   { opacity: 1; transform: translateY(0)    scale(1); }
        }

        /* ── Top accent rail ── */
        .accent-rail {
            height: 3px;
            background: linear-gradient(90deg, #00D88B 0%, #009639 40%, rgba(0,150,57,0) 100%);
        }

        /* ── Error code ── */
        .error-code {
            font-family: 'Inter', sans-serif;
            font-size: clamp(5rem, 20vw, 7rem);
            font-weight: 900;
            letter-spacing: -0.04em;
            line-height: 1;
            background: linear-gradient(135deg, #E3E8EB 0%, #C5CDD6 50%, #A1AEBF 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            user-select: none;
        }

        /* ── Icon badge ── */
        .icon-badge {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            background: linear-gradient(145deg, #F1F3F5 0%, #E3E8EB 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            box-shadow: 0 4px 14px rgba(0,0,0,0.08);
        }

        .icon-badge i {
            font-size: 2rem;
            color: #72809E;
        }

        .body-wrap { padding: 2rem 2rem 1.5rem; text-align: center; }

        h1.title {
            font-size: 1.2rem;
            font-weight: 800;
            color: #1D2836;
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        p.desc {
            font-size: 0.8125rem;
            color: #72809E;
            line-height: 1.6;
            max-width: 340px;
            margin: 0 auto 1.75rem;
        }

        .actions {
            display: flex;
            flex-direction: column;
            gap: 0.625rem;
        }

        @media (min-width: 400px) {
            .actions { flex-direction: row; justify-content: center; }
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            padding: 0.6rem 1.25rem;
            border-radius: 9px;
            font-size: 0.8125rem;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.18s ease;
            border: none;
            text-decoration: none;
            white-space: nowrap;
        }

        .btn-primary {
            background: linear-gradient(180deg, #00C075 0%, #009639 100%);
            color: #fff;
            box-shadow: 0 2px 8px rgba(0,150,57,0.35);
        }
        .btn-primary:hover {
            background: linear-gradient(180deg, #009639 0%, #06754E 100%);
            box-shadow: 0 4px 14px rgba(0,150,57,0.40);
            transform: translateY(-1px);
        }

        .btn-ghost {
            background: #fff;
            color: #394056;
            border: 1.5px solid #D6DDE3;
        }
        .btn-ghost:hover {
            background: #F1F3F5;
            border-color: #C1C8D4;
            transform: translateY(-1px);
        }

        /* ── Footer strip ── */
        .card-footer {
            background: #FAFBFC;
            border-top: 1px solid #F1F3F5;
            padding: 0.75rem 1.5rem;
            text-align: center;
        }
        .card-footer p {
            font-size: 0.7rem;
            color: #A5B2BD;
        }
        .card-footer strong {
            color: #72809E;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="accent-rail"></div>

        <div class="body-wrap">
            <div class="icon-badge">
                <i class='bx bx-map-pin'></i>
            </div>

            <div class="error-code">404</div>

            <h1 class="title">Page Not Found</h1>
            <p class="desc">
                The page you're looking for doesn't exist or has been moved.
                Please check the URL or head back to the dashboard.
            </p>

            <div class="actions">
                <a href="{{ route('dashboard') }}" class="btn btn-primary">
                    <i class='bx bx-home-alt'></i>
                    Return to Dashboard
                </a>
                <button onclick="history.back()" class="btn btn-ghost">
                    <i class='bx bx-arrow-back'></i>
                    Go Back
                </button>
            </div>
        </div>

        <div class="card-footer">
            <p>If you believe this is an error, contact your <strong>system administrator</strong>.</p>
        </div>
    </div>

</body>
</html>