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
        <h2 class="text-3xl font-bold text-gray-800 text-center">Your Attendance</h2>

        <?php if (!empty($attendanceRecords)): ?>
            <div class="mt-6">
                <table class="table-auto w-full border border-gray-300">
                    <thead class="bg-gray-200 text-left">
                        <tr>
                            <th class="px-4 py-2 border">Course</th>
                            <th class="px-4 py-2 border">Date</th>
                            <th class="px-4 py-2 border">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendanceRecords as $record): ?>
                            <tr class="hover:bg-gray-100">
                                <td class="px-4 py-2 border"><?= htmlspecialchars($record['course_name']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($record['date']) ?></td>
                                <td class="px-4 py-2 border font-semibold text-lg 
                                    <?php
                                    if ($record['status'] == 'Present') echo 'text-green-600';
                                    elseif ($record['status'] == 'Absent') echo 'text-red-600';
                                    else echo 'text-yellow-600';
                                    ?>">
                                    <?= htmlspecialchars($record['status']) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-red-500 text-center mt-4">No attendance records found.</p>
        <?php endif; ?>
    </main>

    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>
</body>

</html>