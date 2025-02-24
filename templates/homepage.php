<?php
session_start();

if (!isset($_SESSION['user_id'])) {

    header("Location: /login");
    exit();
}

$user_type = $_SESSION['user_type'];
?>

<!DOCTYPE html>
<html lang="tr">
<?= loadPartial('header') ?>

<body class="min-h-screen flex flex-col bg-gray-100">
    <?= loadPartial('top-banner') ?>
    <?= loadPartial('navbar') ?>

    <section class="relative bg-cover bg-center h-[400px] flex items-center justify-center text-white text-center"
        style="background-image: url('/images/university-bg.jpg'); background-attachment: fixed;">
        <div class="bg-black bg-opacity-50 p-10 rounded-lg animate-fadeIn">
            <h1 class="text-4xl font-bold">
                <?php
                if ($user_type === 'student') {
                    echo "Welcome Student!";
                } elseif ($user_type === 'teacher') {
                    echo "Welcome Teacher!";
                } elseif ($user_type === 'admin') {
                    echo "Welcome Admin!";
                }
                ?>
            </h1>
            <p class="text-xl mt-4">
                <span id="changingText" class="text-yellow-400"></span>
            </p>
        </div>
    </section>

    <main class="flex-grow container mx-auto my-10 p-6 bg-white shadow-lg rounded-lg animate-slideUp">
        <h2 class="text-3xl font-bold text-gray-800 text-center">
            <?php
            if ($user_type === 'student') {
                echo "Your Dashboard";
            } elseif ($user_type === 'teacher') {
                echo "Teacher Panel";
            } elseif ($user_type === 'admin') {
                echo "Admin Control Panel";
            }
            ?>
        </h2>

        <p class="text-lg text-gray-600 text-center mt-4">
            <?php
            if ($user_type === 'student') {
                echo "Here you can view your grades, attendance, and courses.";
            } elseif ($user_type === 'teacher') {
                echo "Manage student grades, attendance, and courses.";
            } elseif ($user_type === 'admin') {
                echo "Manage the entire university system.";
            }
            ?>
        </p>


        <?php if ($user_type === 'student'): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 text-center">
                <div class="p-4 bg-blue-500 text-white rounded-lg hover:shadow-lg transition transform hover:scale-105">
                    <h3 class="text-2xl font-bold">Your Grades</h3>
                    <p class="text-lg">Check your academic performance.</p>
                </div>
                <div class="p-4 bg-green-500 text-white rounded-lg hover:shadow-lg transition transform hover:scale-105">
                    <h3 class="text-2xl font-bold">Your Courses</h3>
                    <p class="text-lg">View the courses you're enrolled in.</p>
                </div>
                <div class="p-4 bg-red-500 text-white rounded-lg hover:shadow-lg transition transform hover:scale-105">
                    <h3 class="text-2xl font-bold">Attendance</h3>
                    <p class="text-lg">See your attendance records.</p>
                </div>
            </div>
        <?php endif; ?>


        <?php if ($user_type === 'teacher'): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 text-center">
                <div class="p-4 bg-purple-500 text-white rounded-lg hover:shadow-lg transition transform hover:scale-105">
                    <h3 class="text-2xl font-bold">Manage Grades</h3>
                    <p class="text-lg">Assign and edit student grades.</p>
                </div>
                <div class="p-4 bg-teal-500 text-white rounded-lg hover:shadow-lg transition transform hover:scale-105">
                    <h3 class="text-2xl font-bold">Manage Attendance</h3>
                    <p class="text-lg">Take attendance for your classes.</p>
                </div>
                <div class="p-4 bg-yellow-500 text-white rounded-lg hover:shadow-lg transition transform hover:scale-105">
                    <h3 class="text-2xl font-bold">Your Courses</h3>
                    <p class="text-lg">Manage the courses you are teaching.</p>
                </div>
            </div>
        <?php endif; ?>


        <?php if ($user_type === 'admin'): ?>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8 text-center">
                <div class="p-4 bg-indigo-500 text-white rounded-lg hover:shadow-lg transition transform hover:scale-105">
                    <h3 class="text-2xl font-bold">Manage Users</h3>
                    <p class="text-lg">Add, edit, or remove students and teachers.</p>
                </div>
                <div class="p-4 bg-orange-500 text-white rounded-lg hover:shadow-lg transition transform hover:scale-105">
                    <h3 class="text-2xl font-bold">Manage Courses</h3>
                    <p class="text-lg">Edit course information and enrollments.</p>
                </div>
                <div class="p-4 bg-gray-500 text-white rounded-lg hover:shadow-lg transition transform hover:scale-105">
                    <h3 class="text-2xl font-bold">System Settings</h3>
                    <p class="text-lg">Configure system-wide settings.</p>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <?= loadPartial('bottom-banner') ?>
    <?= loadPartial('footer') ?>

    <script>
        const messages = ["Explore Your Future", "Shape Your Career", "Learn with the Best"];
        let index = 0;

        function changeText() {
            document.getElementById("changingText").innerText = messages[index];
            index = (index + 1) % messages.length;
        }

        setInterval(changeText, 2000);
        changeText();
    </script>
</body>

</html>