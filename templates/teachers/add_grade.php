<?php if (session_status() === PHP_SESSION_NONE) {
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
        <h2 class="text-3xl font-bold text-gray-800 text-center">Add Student Grade</h2>

        <form action="/teacher/grades/add" method="POST" class="mt-6">
            <label class="block mb-2">Course:</label>
            <select id="courseSelect" name="course_id" class="w-full p-2 mb-4 border rounded">
                <option value="">Select Course</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= $course['id'] ?>"><?= htmlspecialchars($course['course_name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label class="block mb-2">Student:</label>
            <select id="studentSelect" name="student_id" class="w-full p-2 mb-4 border rounded">
                <option value="">Select Student</option>
            </select>

            <label class="block mb-2">Midterm:</label>
            <input type="number" name="midterm" class="w-full p-2 mb-4 border rounded" required>

            <label class="block mb-2">Final:</label>
            <input type="number" name="final" class="w-full p-2 mb-4 border rounded" required>

            <label class="block mb-2">Project:</label>
            <input type="number" name="project" class="w-full p-2 mb-4 border rounded" required>

            <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">
                Add Grade
            </button>
        </form>
    </main>

    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>

    <script>
        document.getElementById('courseSelect').addEventListener('change', function() {
            let courseId = this.value;
            let studentSelect = document.getElementById('studentSelect');

            studentSelect.innerHTML = '<option value="">Loading...</option>';

            fetch(`/teacher/grades/students/${courseId}`)
                .then(response => response.json())
                .then(data => {
                    studentSelect.innerHTML = '<option value="">Select Student</option>';
                    data.forEach(student => {
                        studentSelect.innerHTML += `<option value="${student.id}">${student.full_name} (${student.student_number})</option>`;
                    });
                });
        });
    </script>
</body>

</html>