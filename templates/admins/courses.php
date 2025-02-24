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
        <h2 class="text-3xl font-bold text-gray-800 text-center mb-6">Courses</h2>


        <div class="flex justify-end mb-4">
            <a href="/admin/courses/add" class="bg-green-500 px-4 py-2 rounded text-white hover:bg-green-600">
                Add Course
            </a>
        </div>


        <?php if (!empty($courses)): ?>
            <table class="table-auto w-full border border-gray-300">
                <thead class="bg-gray-200 text-left">
                    <tr>
                        <th class="px-4 py-2 border">ID</th>
                        <th class="px-4 py-2 border">Course Code</th>
                        <th class="px-4 py-2 border">Course Name</th>
                        <th class="px-4 py-2 border">Teacher</th>
                        <th class="px-4 py-2 border">Semester</th>
                        <th class="px-4 py-2 border">Credits</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $course): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="px-4 py-2 border"><?= htmlspecialchars($course['id']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($course['course_code']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($course['course_name']) ?></td>
                            <td class="px-4 py-2 border">
                                <?= !empty($course['teacher_name']) ? htmlspecialchars($course['teacher_name']) : '<span class="text-gray-500">Not Assigned</span>' ?>
                            </td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($course['semester']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($course['credits']) ?></td>
                            <td class="px-4 py-2 border flex space-x-2">

                                <a href="/admin/courses/edit/<?= $course['id'] ?>" class="bg-yellow-500 px-2 py-1 rounded text-white">
                                    Edit
                                </a>

                                <form action="/admin/courses/delete/<?= $course['id'] ?>" method="POST">
                                    <button type="submit" class="bg-red-500 px-2 py-1 rounded text-white">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-red-500 text-center mt-4">No courses found.</p>
        <?php endif; ?>
    </main>

    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>
</body>

</html>