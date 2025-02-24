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
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">Edit Teacher</h2>

        <form action="/admin/teachers/edit/<?= $teacher['id'] ?>" method="POST" class="max-w-lg mx-auto mt-6">
            <label class="block mb-2 font-bold">Username:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($teacher['username']) ?>" class="w-full p-2 border rounded mb-4 bg-gray-200" readonly>

            <label class="block mb-2 font-bold">Full Name:</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($teacher['full_name']) ?>" class="w-full p-2 border rounded mb-4" required>

            <label class="block mb-2 font-bold">Department:</label>
            <input type="text" name="department" value="<?= htmlspecialchars($teacher['department']) ?>" class="w-full p-2 border rounded mb-4" required>

            <label class="block mb-2 font-bold">Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($teacher['email']) ?>" class="w-full p-2 border rounded mb-4" required>

            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Update Teacher</button>
        </form>
    </main>

    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>
</body>

</html>