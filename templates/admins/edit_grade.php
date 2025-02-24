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

    <main class="flex-grow container mx-auto my-10 p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-3xl font-bold text-gray-800 text-center">Edit Grade</h2>

        <form action="/admin/grades/edit/<?= $grade['id'] ?>" method="POST" class="max-w-lg mx-auto mt-6">
            <label class="block mb-2 font-bold">Student:</label>
            <input type="text" value="<?= htmlspecialchars($grade['student_name']) ?>" class="w-full p-2 border rounded mb-4 bg-gray-200" readonly>

            <label class="block mb-2 font-bold">Course:</label>
            <input type="text" value="<?= htmlspecialchars($grade['course_name']) ?>" class="w-full p-2 border rounded mb-4 bg-gray-200" readonly>

            <label class="block mb-2 font-bold">Midterm:</label>
            <input type="number" name="midterm" value="<?= $grade['midterm'] ?>" step="0.01" min="0" max="100" class="w-full p-2 border rounded mb-4" required>

            <label class="block mb-2 font-bold">Final:</label>
            <input type="number" name="final" value="<?= $grade['final'] ?>" step="0.01" min="0" max="100" class="w-full p-2 border rounded mb-4" required>

            <label class="block mb-2 font-bold">Project:</label>
            <input type="number" name="project" value="<?= $grade['project'] ?>" step="0.01" min="0" max="100" class="w-full p-2 border rounded mb-4" required>

            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">Update Grade</button>
        </form>
    </main>

    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>
</body>

</html>