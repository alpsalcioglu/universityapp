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
        <h2 class="text-3xl font-bold text-gray-800 text-center">Your Grades</h2>

        <?php if (!empty($grades)): ?>
            <div class="mt-6">
                <table class="table-auto w-full border border-gray-300">
                    <thead class="bg-gray-200 text-left">
                        <tr>
                            <th class="px-4 py-2 border">Course</th>
                            <th class="px-4 py-2 border">Midterm</th>
                            <th class="px-4 py-2 border">Final</th>
                            <th class="px-4 py-2 border">Project</th>
                            <th class="px-4 py-2 border">Total Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grades as $grade): ?>
                            <tr class="hover:bg-gray-100">
                                <td class="px-4 py-2 border"><?= htmlspecialchars($grade['course_name']) ?></td>
                                <td class="px-4 py-2 border font-semibold text-lg"><?= htmlspecialchars($grade['midterm']) ?></td>
                                <td class="px-4 py-2 border font-semibold text-lg"><?= htmlspecialchars($grade['final']) ?></td>
                                <td class="px-4 py-2 border font-semibold text-lg"><?= htmlspecialchars($grade['project']) ?></td>
                                <td class="px-4 py-2 border font-bold text-lg text-blue-600"><?= htmlspecialchars($grade['total_grade']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-red-500 text-center mt-4">No grades available.</p>
        <?php endif; ?>
    </main>

    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>
</body>

</html>