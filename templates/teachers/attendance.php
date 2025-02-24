<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if ($_SESSION['user_type'] !== 'teacher') {
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
        <h2 class="text-3xl font-bold text-gray-800 text-center">Students Attendance</h2>


        <div class="flex justify-end mb-4">
            <a href="/teacher/attendance/add" class="bg-green-500 px-4 py-2 rounded text-white hover:bg-green-600">
                Add Attendance
            </a>
        </div>

        <?php if (!empty($attendanceRecords)): ?>
            <div class="mt-6">
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
                                    <?php
                                    if ($record['status'] == 'Present') echo 'text-green-600';
                                    elseif ($record['status'] == 'Absent') echo 'text-red-600';
                                    else echo 'text-yellow-600';
                                    ?>">
                                    <?= htmlspecialchars($record['status']) ?>
                                </td>
                                <td class="px-4 py-2 border flex space-x-2">

                                    <button class="edit-attendance bg-yellow-500 px-2 py-1 rounded text-white"
                                        data-id="<?= $record['id'] ?>"
                                        data-date="<?= $record['date'] ?>"
                                        data-status="<?= $record['status'] ?>"
                                        data-student_id="<?= isset($record['student_id']) ? $record['student_id'] : '' ?>"
                                        data-course_id="<?= isset($record['course_id']) ? $record['course_id'] : '' ?>">
                                        Edit
                                    </button>


                                    <form action="/teacher/attendance/delete/<?= $record['id'] ?>" method="POST">
                                        <button type="submit" class="bg-red-500 px-2 py-1 rounded text-white">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>


            <div id="editModal" class="hidden fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
                <div class="bg-white p-6 rounded shadow-md w-96">
                    <h2 class="text-xl font-bold mb-4 text-center">Edit Attendance</h2>
                    <form id="editAttendanceForm" action="/teacher/attendance/update" method="POST">
                        <input type="hidden" id="attendance_id" name="attendance_id">
                        <input type="hidden" id="studentId" name="student_id">
                        <input type="hidden" id="courseId" name="course_id">

                        <label>Date:</label>
                        <input type="date" id="editDate" name="date" class="w-full p-2 border rounded mb-4" required>

                        <label>Status:</label>
                        <select id="editStatus" name="status" class="w-full p-2 border rounded mb-4" required>
                            <option value="Present">Present</option>
                            <option value="Absent">Absent</option>
                            <option value="Late">Late</option>
                        </select>

                        <div class="flex justify-end space-x-2">
                            <button type="button" id="closeModal" class="bg-gray-400 text-white px-4 py-2 rounded">Cancel</button>
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        <?php else: ?>
            <p class="text-red-500 text-center mt-4">No attendance records found.</p>
        <?php endif; ?>
    </main>

    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>
</body>

<script>
    document.querySelectorAll(".edit-attendance").forEach(button => {
        button.addEventListener("click", function() {
            document.getElementById("attendance_id").value = this.dataset.id;
            document.getElementById("editDate").value = this.dataset.date;
            document.getElementById("editStatus").value = this.dataset.status;
            document.getElementById("studentId").value = this.dataset.student_id;
            document.getElementById("courseId").value = this.dataset.course_id;
            document.getElementById("editModal").classList.remove("hidden");
        });
    });


    document.getElementById("closeModal").addEventListener("click", function() {
        document.getElementById("editModal").classList.add("hidden");
    });

    document.getElementById("editDate").addEventListener("change", function() {
        let selectedDate = this.value;
        let attendanceId = document.getElementById("attendance_id").value;
        let studentId = document.getElementById("studentId").value;
        let courseId = document.getElementById("courseId").value;
        let updateButton = document.querySelector("#editAttendanceForm button[type='submit']");

        fetch(`/teacher/attendance/check-existing-date?date=${selectedDate}&attendance_id=${attendanceId}&student_id=${studentId}&course_id=${courseId}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    alert("This student already has attendance for this date! Please select another date.");
                    updateButton.disabled = true;
                    updateButton.classList.add("bg-gray-400", "cursor-not-allowed");
                    updateButton.classList.remove("bg-blue-500", "hover:bg-blue-600");
                } else {
                    updateButton.disabled = false;
                    updateButton.classList.remove("bg-gray-400", "cursor-not-allowed");
                    updateButton.classList.add("bg-blue-500", "hover:bg-blue-600");
                }
            });
    });


    document.getElementById("editAttendanceForm").addEventListener("submit", function(event) {
        let updateButton = document.querySelector("#editAttendanceForm button[type='submit']");

        if (updateButton.disabled) {
            event.preventDefault();
            alert("You cannot update to this date. Please choose another date.");
        }
    });
</script>

</html>