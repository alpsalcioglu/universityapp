<nav class="bg-blue-600 p-4 shadow-md">
  <div class="container mx-auto flex justify-between items-center">
    <?php if ($_SESSION['user_type'] == 'teacher'): ?>
      <a href="/teacher" class="text-black text-xl font-semibold">U-App</a>
      <ul class="flex space-x-4">
        <li><a href="/teacher" class="text-black hover:underline">Home</a></li>
        <li><a href="/teacher/info" class="text-white hover:underline">Your Profile</a></li>
        <li><a href="/teacher/courses" class="text-white hover:underline">Your Courses</a></li>
        <li><a href="/teacher/grades" class="text-white hover:underline">Students Grades</a></li>
        <li><a href="/teacher/attendance" class="text-white hover:underline">Students Attendance</a></li>
      <?php elseif ($_SESSION['user_type'] == 'student'): ?>
        <a href="/student" class="text-black text-xl font-semibold">U-App</a>
        <ul class="flex space-x-4">
          <li><a href="/student" class="text-white hover:underline">Home</a></li>
          <li><a href="/student/info" class="text-white hover:underline">Your Profile</a></li>
          <li><a href="/student/courses" class="text-white hover:underline">Taken Courses</a></li>
          <li><a href="/student/grades" class="text-white hover:underline">Grades</a></li>
          <li><a href="/student/attendance" class="text-white hover:underline">Attendance</a></li>
        <?php elseif ($_SESSION['user_type'] == 'admin'): ?>
          <a href="/admin" class="text-black text-xl font-semibold">U-App</a>
          <ul class="flex space-x-4">
            <li><a href="/admin" class="text-white hover:underline">Home</a></li>
            <li><a href="/admin/info" class="text-white hover:underline">Your Profile</a></li>
            <li><a href="/admin/students" class="text-white hover:underline">Students</a></li>
            <li><a href="/admin/teachers" class="text-white hover:underline">Teachers</a></li>
            <li><a href="/admin/enrollments" class="text-white hover:underline">Enrollments</a></li>
            <li><a href="/admin/courses" class="text-white hover:underline">Courses</a></li>
            <li><a href="/admin/grades" class="text-white hover:underline">Grades</a></li>
            <li><a href="/admin/attendance" class="text-white hover:underline">Attendance</a></li>
            <li><a href="/admin/query/auth" class="text-black hover:underline">QUERY</a></li>
          <?php endif; ?>

          <li> <a href="/auth/logout" class="bg-red-500 text-white px-6 py-2 rounded-lg hover:bg-red-600 transition"> Logout </a>
          </li>
          </ul>
  </div>
</nav>