<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<!DOCTYPE html>
<html lang="tr">
<?= loadPartial('header') ?>

<body class="min-h-screen flex flex-col bg-gray-100">
    <?= loadPartial('top-banner') ?>
    <?= loadPartial('navbar') ?>

    <main class="flex-grow container mx-auto my-10 p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-3xl font-bold text-gray-800 text-center">Add Attendance</h2>


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

        <form action="/teacher/attendance/add" method="POST" class="mt-6">
            <label>Course:</label>
            <select id="courseSelect" name="course_id" class="w-full p-2 border rounded mb-4" required>
                <option value="">Select a course</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['id'] ?>"><?= $course['course_name'] ?></option>
                <?php endforeach; ?>
            </select>

            <label>Date:</label>
            <input type="date" name="date" class="w-full p-2 border rounded mb-4" required>

            <label>Student:</label>
            <select id="studentSelect" name="student_id" class="w-full p-2 border rounded mb-4" required>
                <option value="">Select a student</option>
            </select>

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
    document.getElementById("courseSelect").addEventListener("change", function() {
        let courseId = this.value;
        let date = document.querySelector("input[name='date']").value;
        let studentSelect = document.getElementById("studentSelect");
        studentSelect.innerHTML = "<option>Loading...</option>";

        fetch(`/teacher/attendance/students/${courseId}?date=${date}`)
            .then(response => response.json())
            .then(data => {
                studentSelect.innerHTML = "<option value=''>Select a student</option>";
                data.forEach(student => {
                    let disabled = student.attendance_status === 'exists' ? "disabled" : "";
                    let statusText = student.attendance_status === 'exists' ? " (Already Added)" : "";
                    studentSelect.innerHTML += `<option value="${student.id}" ${disabled}>${student.full_name} (${student.student_number})${statusText}</option>`;
                });
            });
    });


    document.querySelector("input[name='date']").addEventListener("change", function() {
        document.getElementById("courseSelect").dispatchEvent(new Event("change"));
    });
</script>

</html>