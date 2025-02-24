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
        <h2 class="text-3xl font-bold text-gray-800 text-center">Edit Enrollment</h2>

        <form action="/admin/enrollments/edit/<?= $enrollment['id'] ?>" method="POST" class="mt-6">
            <label class="block mb-2 font-bold">Student:</label>
            <select name="student_id" class="w-full p-2 border rounded mb-4" required>
                <?php foreach ($students as $student): ?>
                    <option value="<?= $student['id'] ?>" <?= ($student['id'] == $enrollment['student_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($student['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label class="block mb-2 font-bold">Course:</label>
            <select name="course_id" class="w-full p-2 border rounded mb-4" required>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['id'] ?>" <?= ($course['id'] == $enrollment['course_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($course['course_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">Update Enrollment</button>
        </form>
    </main>

    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>
</body>

</html>