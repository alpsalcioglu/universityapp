<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SESSION['user_type'] !== 'admin') {
    header("Location: /");
    exit();
}

$error = $_SESSION['auth_error'] ?? null;
unset($_SESSION['auth_error']);
?>

<!DOCTYPE html>
<html lang="tr">
<?= loadPartial('header') ?>

<body class="min-h-screen flex flex-col bg-gray-100">
    <?= loadPartial('top-banner') ?>
    <?= loadPartial('navbar') ?>

    <main class="container mx-auto my-10 p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">Admin Authentication</h2>

        <?php if ($error): ?>
            <p class="text-red-500 text-center font-bold"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form action="/admin/query/auth" method="POST" class="max-w-lg mx-auto">
            <label class="block mb-2 font-bold">Enter your password to access Query Executor:</label>
            <input type="password" name="password" class="w-full p-2 border rounded mb-4" required>

            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Verify</button>
        </form>
    </main>

    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>
</body>

</html>