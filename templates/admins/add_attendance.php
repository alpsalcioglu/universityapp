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
        <h2 class="text-3xl font-bold text-gray-800 text-center">Add Attendance</h2>

        <form action="/admin/attendance/add" method="POST" class="mt-6">
            <label>Student:</label>
            <select id="studentSelect" name="student_id" class="w-full p-2 border rounded mb-4" required>
                <option value="">Select a student</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['full_name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label>Course:</label>
            <select id="courseSelect" name="course_id" class="w-full p-2 border rounded mb-4" required>
                <option value="">Select a student first</option>
            </select>

            <label>Date:</label>
            <input type="date" name="date" class="w-full p-2 border rounded mb-4" required>

            <label>Status:</label>
            <select name="status" class="w-full p-2 border rounded mb-4" required>
                <option value="Present">Present</option>
                <option value="Absent">Absent</option>
                <option value="Late">Late</option>
            </select>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Add Attendance</button>
        </form>
    </main>


    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>
</body>

<script>
    document.getElementById("studentSelect").addEventListener("change", function() {
        let studentId = this.value;
        let courseSelect = document.getElementById("courseSelect");

        if (!studentId) {
            courseSelect.innerHTML = "<option>Select a student first</option>";
            return;
        }

        fetch(`/admin/attendance/courses/${studentId}`)
            .then(response => response.json())
            .then(data => {
                courseSelect.innerHTML = "<option value=''>Select a course</option>";
                data.forEach(course => {
                    courseSelect.innerHTML += `<option value="${course.id}">${course.course_name}</option>`;
                });
            })
            .catch(error => console.error('Error fetching courses:', error));
    });
</script>

</html>