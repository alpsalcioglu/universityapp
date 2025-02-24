<!DOCTYPE html>
<html lang="tr">
<?= loadPartial('header') ?>

<body class="min-h-screen flex flex-col bg-gray-100">
    <?= loadPartial('top-banner') ?>
    <?= loadPartial('navbar') ?>

    <main class="flex-grow container mx-auto my-10 p-6 bg-white shadow-lg rounded-lg">
        <h2 class="text-3xl font-bold text-gray-800 text-center">Add Grade</h2>

        <form action="/admin/grades/add" method="POST" class="max-w-lg mx-auto mt-6">
            <label class="block mb-2 font-bold">Student:</label>
            <select name="student_id" id="studentSelect" class="w-full p-2 border rounded mb-4" required>
                <option value="">Select a student</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['full_name']) ?></option>
                <?php endforeach; ?>
            </select>

            <label class="block mb-2 font-bold">Course:</label>
            <select name="course_id" id="courseSelect" class="w-full p-2 border rounded mb-4" required>
                <option value="">Select a student first</option>
            </select>

            <label class="block mb-2 font-bold">Midterm:</label>
            <input type="number" name="midterm" step="0.01" min="0" max="100" class="w-full p-2 border rounded mb-4" required>

            <label class="block mb-2 font-bold">Final:</label>
            <input type="number" name="final" step="0.01" min="0" max="100" class="w-full p-2 border rounded mb-4" required>

            <label class="block mb-2 font-bold">Project:</label>
            <input type="number" name="project" step="0.01" min="0" max="100" class="w-full p-2 border rounded mb-4" required>

            <button type="submit" class="w-full bg-green-500 text-white p-2 rounded hover:bg-green-600">Add Grade</button>
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
            courseSelect.innerHTML = "<option value=''>Select a student first</option>";
            return;
        }

        fetch(`/admin/grades/students/${studentId}`)
            .then(response => response.json())
            .then(data => {
                courseSelect.innerHTML = "<option value=''>Select a course</option>";
                data.forEach(course => {
                    courseSelect.innerHTML += `<option value="${course.id}">${course.course_name}</option>`;
                });
            })
            .catch(error => console.error("Error fetching courses:", error));
    });
</script>

</html>