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
        <h2 class="text-3xl font-bold text-gray-800 text-center">Edit Attendance</h2>

        <form action="/admin/attendance/update/<?= $attendance['id'] ?>" method="POST" class="max-w-lg mx-auto mt-6">
            <label class="block mb-2 font-bold">Date:</label>
            <input type="date" name="date" class="w-full p-2 border rounded mb-4" value="<?= htmlspecialchars($attendance['date']) ?>" required>

            <label class="block mb-2 font-bold">Status:</label>
            <select name="status" class="w-full p-2 border rounded mb-4" required>
                <option value="Present" <?= $attendance['status'] === 'Present' ? 'selected' : '' ?>>Present</option>
                <option value="Absent" <?= $attendance['status'] === 'Absent' ? 'selected' : '' ?>>Absent</option>
                <option value="Late" <?= $attendance['status'] === 'Late' ? 'selected' : '' ?>>Late</option>
            </select>

            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Update Attendance</button>
        </form>
    </main>

    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>
</body>

</html>