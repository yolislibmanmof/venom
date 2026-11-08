<?php
defined('INDEX_AUTH') OR die('Direct access not allowed!');
require_once __DIR__ . '/helpers/core.php';

// Jika sudah login, redirect ke dashboard anggota
if (isset($_SESSION['m_id'])) {
    echo '<script>window.location.href = "index.php?p=member";</script>';
    exit;
}

$error = isset($_GET['error']) ? 'ID Anggota atau Kata Sandi salah!' : '';
?>

<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= venom_e(venom_library_name()) ?></title>
    <link rel="stylesheet" href="<?= venom_e(venom_asset('css/venom-core.css')) ?>">
    <style>
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--v-bg) 0%, var(--v-bg2) 100%);
            font-family: var(--v-fs);
        }
        .login-container {
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }
        .login-card {
            background: var(--v-glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--v-gline);
            border-radius: var(--v-r);
            padding: 40px;
            box-shadow: var(--v-sh2);
        }
        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }
        .login-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            background: linear-gradient(135deg, var(--v-p), var(--v-a));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            box-shadow: 0 8px 24px color-mix(in srgb, var(--v-p) 40%, transparent);
        }
        .login-header h1 {
            margin: 0 0 8px 0;
            font-size: 1.5rem;
            font-weight: 800;
        }
        .login-header p {
            margin: 0;
            color: var(--v-mut);
            font-size: 0.95rem;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--v-tx);
        }
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--v-line);
            border-radius: 10px;
            background: var(--v-surf);
            color: var(--v-tx);
            font-size: 0.95rem;
            transition: all 0.2s;
            box-sizing: border-box;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--v-p);
            box-shadow: 0 0 0 3px color-mix(in srgb, var(--v-p) 15%, transparent);
        }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(120deg, var(--v-p), var(--v-a));
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
            margin-top: 8px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px color-mix(in srgb, var(--v-p) 40%, transparent);
        }
        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.9rem;
            margin-bottom: 20px;
            border-left: 4px solid #ef4444;
        }
        .login-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid var(--v-line);
        }
        .login-footer a {
            color: var(--v-p);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .login-footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <h1>Selamat Datang</h1>
                <p>Masuk ke akun anggota Anda</p>
            </div>

            <?php if ($error): ?>
                <div class="alert-error"><?= venom_e($error) ?></div>
            <?php endif; ?>

            <form action="index.php?p=member" method="POST">
                <input type="hidden" name="csrf_token" value="<?= venom_e(Venom_Sec::token('login')) ?>">
                
                <div class="form-group">
                    <label for="member_id">ID Anggota</label>
                    <input type="text" id="member_id" name="member_id" class="form-control" required autocomplete="off" autofocus>
                </div>

                <div class="form-group">
                    <label for="member_password">Kata Sandi</label>
                    <input type="password" id="member_password" name="member_password" class="form-control" required>
                </div>

                <button type="submit" class="btn-login">Masuk</button>
            </form>

            <div class="login-footer">
                <a href="index.php">← Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    <script src="<?= venom_e(venom_asset('js/venom-app.js')) ?>"></script>
</body>
</html>