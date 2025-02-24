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
        <h2 class="text-3xl font-bold text-gray-800 text-center">All Students Attendance</h2>

        <div class="flex justify-end mb-4">
            <a href="/admin/attendance/add" class="bg-green-500 px-4 py-2 rounded text-white hover:bg-green-600">
                Add Attendance
            </a>
        </div>

        <?php if (!empty($attendanceRecords)): ?>
            <table class="table-auto w-full border border-gray-300">
                <thead class="bg-gray-200 text-left">
                    <tr>
                        <th class="px-4 py-2 border">Student Name</th>
                        <th class="px-4 py-2 border">Student Number</th>
                        <th class="px-4 py-2 border">Course</th>
                        <th class="px-4 py-2 border">Date</th>
                        <th class="px-4 py-2 border">Status</th>
                        <th class="px-4 py-2 border">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($attendanceRecords as $record): ?>
                        <tr class="hover:bg-gray-100">
                            <td class="px-4 py-2 border"><?= htmlspecialchars($record['full_name']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($record['student_number']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($record['course_name']) ?></td>
                            <td class="px-4 py-2 border"><?= htmlspecialchars($record['date']) ?></td>
                            <td class="px-4 py-2 border font-semibold text-lg 
                                <?= ($record['status'] == 'Present') ? 'text-green-600' : (($record['status'] == 'Absent') ? 'text-red-600' : 'text-yellow-600'); ?>">
                                <?= htmlspecialchars($record['status']) ?>
                            </td>
                            <td class="px-4 py-2 border flex space-x-2">
                                <a href="/admin/attendance/edit/<?= $record['id'] ?>" class="bg-yellow-500 px-2 py-1 rounded text-white">
                                    Edit
                                </a>
                                <form action="/admin/attendance/delete/<?= $record['id'] ?>" method="POST">
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
            <p class="text-red-500 text-center mt-4">No attendance records found.</p>
        <?php endif; ?>
    </main>

    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>
</body>

</html>