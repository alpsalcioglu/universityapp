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
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">Edit Course</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="text-red-500 text-center"><?= $_SESSION['error'];
                                                unset($_SESSION['error']); ?></p>
        <?php endif; ?>

        <form action="/admin/courses/edit/<?= $course['id'] ?>" method="POST" class="max-w-md mx-auto">
            <label for="course_name" class="block font-semibold">Course Name:</label>
            <input type="text" id="course_name" name="course_name" class="w-full p-2 border rounded mb-4" value="<?= htmlspecialchars($course['course_name']) ?>" required>

            <label for="course_code" class="block font-semibold">Course Code:</label>
            <input type="text" id="course_code" name="course_code" class="w-full p-2 border rounded mb-4" value="<?= htmlspecialchars($course['course_code']) ?>" required>

            <label for="teacher_id" class="block font-semibold">Teacher:</label>
            <select id="teacher_id" name="teacher_id" class="w-full p-2 border rounded mb-4" required>
                <?php foreach ($teachers as $teacher): ?>
                    <option value="<?= $teacher['id'] ?>" <?= ($teacher['id'] == $course['teacher_id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($teacher['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="semester" class="block font-semibold">Semester:</label>
            <select id="semester" name="semester" class="w-full p-2 border rounded mb-4" required>
                <option value="Fall" <?= ($course['semester'] == 'Fall') ? 'selected' : '' ?>>Fall</option>
                <option value="Spring" <?= ($course['semester'] == 'Spring') ? 'selected' : '' ?>>Spring</option>
                <option value="Summer" <?= ($course['semester'] == 'Summer') ? 'selected' : '' ?>>Summer</option>
            </select>

            <label for="credits" class="block font-semibold">Credits:</label>
            <input type="number" id="credits" name="credits" class="w-full p-2 border rounded mb-4" value="<?= $course['credits'] ?>" required>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update Course</button>
        </form>
    </main>

    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>
</body>

</html>