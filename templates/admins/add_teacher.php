<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SESSION['user_type'] !== 'admin') {
    header("Location: /");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<?= loadPartial('header') ?>

<body class="min-h-screen flex flex-col bg-gray-100">
    <?= loadPartial('top-banner') ?>
    <?= loadPartial('navbar') ?>

    <main class="container mx-auto my-10 p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">Add Teacher</h2>

        <form action="/admin/teachers/add" method="POST" class="max-w-lg mx-auto mt-6">
            <label class="block mb-2 font-bold">Username:</label>
            <input type="text" name="username" class="w-full p-2 border rounded mb-4" required>

            <label class="block mb-2 font-bold">Password:</label>
            <input type="password" name="password" class="w-full p-2 border rounded mb-4" required>

            <label class="block mb-2 font-bold">Full Name:</label>
            <input type="text" name="full_name" class="w-full p-2 border rounded mb-4" required>

            <label class="block mb-2 font-bold">Department:</label>
            <input type="text" name="department" class="w-full p-2 border rounded mb-4" required>

            <label class="block mb-2 font-bold">Email:</label>
            <input type="email" name="email" class="w-full p-2 border rounded mb-4" required>

            <button type="submit" class="w-full bg-green-500 text-white p-2 rounded hover:bg-green-600">Add Teacher</button>
        </form>
    </main>

    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>
</body>

</html>