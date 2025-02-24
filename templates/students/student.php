<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: /");
    exit();
}

$profileImage = $student['profile_image'] ?? '/images/default-avatar.png';

?>

<!DOCTYPE html>
<html lang="tr">
<?= loadPartial('header') ?>

<body class="min-h-screen flex flex-col bg-gray-100">
    <?= loadPartial('top-banner') ?>
    <?= loadPartial('navbar') ?>

    <main class="flex-grow container mx-auto my-10 bg-white shadow-md rounded-lg p-6">
        <div class="flex flex-col md:flex-row items-center">
            <div class="w-32 h-32 md:w-40 md:h-40 rounded-full border-4 border-green-400 overflow-hidden">
                <img src="<?= htmlspecialchars($profileImage) ?>" alt="Profile Photo" class="w-full h-full object-cover">
            </div>
            <div class="ml-6">
                <h1 class="text-3xl font-bold text-gray-900">Personal Information</h1>
                <p class="text-lg"><strong>Name:</strong> <?= htmlspecialchars($student['full_name']) ?></p>
                <p class="text-lg"><strong>E-mail:</strong> <?= htmlspecialchars($student['email']) ?></p>
                <p class="text-lg"><strong>Student Number:</strong> <?= htmlspecialchars($student['student_number']) ?></p>
                <p class="text-lg"><strong>Address:</strong> <?= htmlspecialchars($student['address']) ?></p>
                <p class="text-lg"><strong>Birth Date:</strong> <?= htmlspecialchars($student['birth_date']) ?></p>
                <p class="text-lg"><strong>Enrollment Date:</strong> <?= htmlspecialchars($student['created_at']) ?></p>
            </div>
        </div>

    </main>

    <?= loadPartial('footer') ?>
</body>

</html>