<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

http_response_code(404);
?>

<!DOCTYPE html>
<html lang="en">
<?= loadPartial('header') ?>

<body class="min-h-screen flex flex-col bg-gray-100">
    <?= loadPartial('top-banner') ?>


    <main class="flex-grow container mx-auto my-10 p-6 bg-white shadow-lg rounded-lg text-center">
        <h2 class="text-5xl font-bold text-gray-800">404</h2>
        <p class="text-lg text-gray-600 mt-4">Oops! The page you are looking for does not exist.</p>
        <p class="text-gray-500">It may have been moved, deleted, or you might have mistyped the URL.</p>
        <a href="/" class="mt-6 inline-block px-6 py-3 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
            Go Back to Home
        </a>
    </main>

    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>
</body>

</html>