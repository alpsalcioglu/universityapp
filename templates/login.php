<?php if (session_status() === PHP_SESSION_NONE) {
    session_start();
} ?>

<!DOCTYPE html>
<html lang="tr">
<?= loadPartial('header') ?>

<body class="min-h-screen flex flex-col justify-between bg-gray-100">

    <?= loadPartial('top-banner') ?>


    <div class="flex flex-grow items-center justify-center bg-green-100" style="background-image: url('/images/abc.jpg')">
        <div class="bg-white shadow-md rounded p-8 w-96 border">
            <h2 class="text-xl font-bold mb-4 text-center">University Login System</h2>

            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="bg-red-500 text-white p-2 mb-4 rounded">
                    <?= $_SESSION['login_error'];
                    unset($_SESSION['login_error']); ?>
                </div>
            <?php endif; ?>

            <form action="/auth/login" method="POST">
                <label class="block mb-2">User Type:</label>
                <select name="user_type" class="w-full p-2 mb-4 border rounded">
                    <option value="student">Student</option>
                    <option value="teacher">Teacher</option>
                    <option value="admin">Admin</option>
                </select>

                <label class="block mb-2">Username (Student Number or Email):</label>
                <input type="text" name="username" class="w-full p-2 mb-4 border rounded" required>

                <label class="block mb-2">Password:</label>
                <input type="password" name="password" class="w-full p-2 mb-4 border rounded" required>

                <button type="submit" class="w-full bg-blue-500 text-white p-2 rounded hover:bg-blue-600">
                    Login
                </button>
            </form>
        </div>
    </div>


    <!-- <?= loadPartial('bottom-banner') ?> -->
    <?= loadPartial('footer') ?>
</body>

</html>