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
        <h2 class="text-3xl font-bold text-gray-800 text-center">Students Grades</h2>


        <div class="flex justify-end mb-4">
            <a href="/teacher/grades/add" class="bg-green-500 px-4 py-2 rounded text-white hover:bg-green-600">Add Grade</a>
        </div>

        <?php if (!empty($grades)): ?>
            <div class="mt-6">
                <table class="table-auto w-full border border-gray-300">
                    <thead class="bg-gray-200 text-left">
                        <tr>
                            <th class="px-4 py-2 border">Student Name</th>
                            <th class="px-4 py-2 border">Student Number</th>
                            <th class="px-4 py-2 border">Course</th>
                            <th class="px-4 py-2 border">Midterm</th>
                            <th class="px-4 py-2 border">Final</th>
                            <th class="px-4 py-2 border">Project</th>
                            <th class="px-4 py-2 border">Total Grade</th>
                            <th class="px-4 py-2 border">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($grades as $grade): ?>
                            <tr class="hover:bg-gray-100">
                                <td class="px-4 py-2 border"><?= htmlspecialchars($grade['full_name']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($grade['student_number']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($grade['course_name']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($grade['midterm']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($grade['final']) ?></td>
                                <td class="px-4 py-2 border"><?= htmlspecialchars($grade['project']) ?></td>
                                <td class="px-4 py-2 border font-bold text-lg text-blue-600"><?= htmlspecialchars($grade['total_grade']) ?></td>
                                <td class="px-4 py-2 border flex space-x-2">
                                    <button
                                        class="edit-grade bg-yellow-500 px-2 py-1 rounded text-white hover:bg-black"
                                        data-id="<?= $grade['id'] ?>"
                                        data-midterm="<?= $grade['midterm'] ?>"
                                        data-final="<?= $grade['final'] ?>"
                                        data-project="<?= $grade['project'] ?>">
                                        Edit
                                    </button>
                                    <button
                                        class="delete-grade bg-red-500 px-2 py-1 rounded text-white hover:bg-black"
                                        data-id="<?= $grade['id'] ?>">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>


            <div id="editModal" class="hidden fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50">
                <div class="bg-white p-6 rounded shadow-md w-96">
                    <h2 class="text-xl font-bold mb-4 text-center">Edit Student Grade</h2>
                    <form id="editGradeForm" action="/teacher/grades/update" method="POST">
                        <input type="hidden" id="grade_id" name="grade_id">

                        <label>Midterm:</label>
                        <input type="number" id="editMidterm" name="midterm" class="w-full p-2 border rounded mb-4" required>

                        <label>Final:</label>
                        <input type="number" id="editFinal" name="final" class="w-full p-2 border rounded mb-4" required>

                        <label>Project:</label>
                        <input type="number" id="editProject" name="project" class="w-full p-2 border rounded mb-4" required>

                        <div class="flex justify-end space-x-2">
                            <button type="button" id="closeModal" class="bg-gray-400 text-white px-4 py-2 rounded">Cancel</button>
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Update</button>
                        </div>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <p class="text-red-500 text-center mt-4">No grades available.</p>
        <?php endif; ?>
    </main>

    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>
</body>

<script>
    document.querySelectorAll(".edit-grade").forEach(button => {
        button.addEventListener("click", function() {
            document.getElementById("grade_id").value = this.dataset.id;
            document.getElementById("editMidterm").value = this.dataset.midterm;
            document.getElementById("editFinal").value = this.dataset.final;
            document.getElementById("editProject").value = this.dataset.project;
            document.getElementById("editModal").classList.remove("hidden");
        });
    });

    document.getElementById("closeModal").addEventListener("click", function() {
        document.getElementById("editModal").classList.add("hidden");
    });

    document.querySelectorAll(".delete-grade").forEach(button => {
        button.addEventListener("click", function() {
            const gradeId = this.dataset.id;
            if (confirm("Are you sure you want to delete this grade?")) {
                fetch(`/teacher/grades/delete/${gradeId}`, {
                        method: "POST",
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert("Grade deleted successfully.");
                            location.reload();
                        } else {
                            alert("Failed to delete grade.");
                        }
                    })
                    .catch(error => console.error("Error:", error));
            }
        });
    });
</script>

</html>