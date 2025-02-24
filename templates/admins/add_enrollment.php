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
        <h2 class="text-3xl font-bold text-gray-800 text-center">Add Enrollment</h2>


        <?php if (!empty($_SESSION['error'])): ?>
            <div class="bg-red-500 text-white text-center p-3 rounded mt-4">
                <?= $_SESSION['error']; ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>


        <?php if (!empty($_SESSION['success'])): ?>
            <div class="bg-green-500 text-white text-center p-3 rounded mt-4">
                <?= $_SESSION['success']; ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <form action="/admin/enrollments/add" method="POST" class="max-w-lg mx-auto mt-6">
            <label class="block mb-2 font-bold">Student:</label>
            <select name="student_id" class="w-full p-2 border rounded mb-4" required>
                <option value="">Select a student</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['full_name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label class="block mb-2 font-bold">Course:</label>
            <select name="course_id" class="w-full p-2 border rounded mb-4" required>
                <option value="">Select a course</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['course_name']) ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Add Enrollment</button>
        </form>
    </main>

    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>
</body>

</html>