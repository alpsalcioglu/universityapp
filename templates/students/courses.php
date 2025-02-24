<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
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
        <h2 class="text-3xl font-bold text-gray-800 text-center">Your Taken Courses</h2>

        <?php if (!empty($courses)): ?>
            <div class="mt-6">
                <table class="table-auto w-full border border-gray-300">
                    <thead class="bg-gray-200 text-left">
                        <tr>
                            <th class="px-4 py-2 border">Course Name</th>
                            <th class="px-4 py-2 border">Course Code</th>
                            <th class="px-4 py-2 border">Semester</th>
                            <th class="px-4 py-2 border">Credits</th>
                            <th class="px-4 py-2 border">Instructor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($courses as $course): ?>
                            <tr class="hover:bg-gray-100">
                                <td class="px-4 py-2 border"><?= htmlspecialchars($course['course_name']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($course['course_code']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($course['semester']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($course['credits']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($course['instructor'] ?? 'Not Assigned') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-red-500 text-center mt-4">No courses found.</p>
        <?php endif; ?>
    </main>

    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>
</body>

</html>