<?php /* @var $this Controller */?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="language" content="en">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>

<body class="flex flex-col min-h-screen bg-gray-100">
    <!-- Black Header -->
    <header class="bg-black text-white p-4">
        <div class="container mx-auto">
            <h1 class="text-xl font-bold"><?php echo CHtml::encode(Yii::app()->name); ?></h1>
        </div>
    </header>
	<!-- Navigation Bar -->
	<nav class="bg-gray-800 text-white p-4">
		<div class="container mx-auto flex justify-between items-center">
			<!-- Left side navigation links -->
			<div>
				<?php if (Yii::app()->user->isGuest): ?>
				<!-- Guest Navigation -->
				<ul class="flex space-x-4">
					<li><a href="<?php echo Yii::app()->createUrl('site/login'); ?>" class="hover:text-gray-400">Login</a></li>
				</ul>
				<?php elseif (Yii::app()->user->isAdmin()): ?>
				<!-- Admin Navigation -->
				<ul class="flex space-x-4">
					<li><a href="<?php echo Yii::app()->createUrl('student/index'); ?>" class="hover:text-gray-400">Manage Students</a></li>
					<li><a href="<?php echo Yii::app()->createUrl('teacher/index'); ?>" class="hover:text-gray-400">Manage Teachers</a></li>
					<li><a href="<?php echo Yii::app()->createUrl('classes/index'); ?>" class="hover:text-gray-400">Manage Classes</a></li>
					<li><a href="<?php echo Yii::app()->createUrl('user/create'); ?>" class="hover:text-gray-400">Create User</a></li>
				</ul>
				<?php elseif (Yii::app()->user->isTeacher()): ?>
				<!-- Teacher Navigation -->
				<ul class="flex space-x-4">
					<li><a href="<?php echo Yii::app()->createUrl('teacher/classes'); ?>" class="hover:text-gray-400">Classes</a></li>
					<li><a href="<?php echo Yii::app()->createUrl('teacher/attendance'); ?>" class="hover:text-gray-400">Attendance</a></li>
				</ul>
				<?php elseif (Yii::app()->user->isStudent()): ?>
				<!-- Student Navigation -->
				<ul class="flex space-x-4">
					<li><a href="<?php echo Yii::app()->createUrl('student/dashboard'); ?>" class="hover:text-gray-400">Dashboard</a></li>
					<li><a href="<?php echo Yii::app()->createUrl('student/daywise'); ?>" class="hover:text-gray-400">Day Wise Attendance</a></li>
				</ul>
				<?php endif; ?>
			</div>
			
			<!-- Right side profile dropdown - Only shown for logged in users -->
			<?php if (!Yii::app()->user->isGuest): ?>
			<div class="relative" x-data="{ open: false }">
				
				<button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
					<?php
					// Get current user's profile picture if they're a student
					$profilePicture = null;
					if (Yii::app()->user->isStudent()) {
						$studentId = Yii::app()->user->id;
						$student = StudentHelper::getStudentById($studentId);
						if ($student && !empty($student->profile_picture)) {
							$profilePicture = $student->profile_picture;
						}
					}
					?>
					<?php if ($profilePicture): ?>
						<img src="<?php echo S3Helper::generateGETObjectUrl($profilePicture); ?>" alt="Profile" class="w-8 h-8 rounded-full object-cover">
					<?php else: ?>
						<img src="https://ui-avatars.com/api/?name=<?php echo urlencode(Yii::app()->user->getName()); ?>&background=random" alt="Profile" class="w-8 h-8 rounded-full">
					<?php endif; ?>
					<div class="flex flex-col items-start">

						<span><?php echo CHtml::encode(Yii::app()->user->getName()); ?></span>
					</div>
					<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
						<path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
					</svg>
				</button>
				
				<div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10">
					<?php if (Yii::app()->user->isStudent()): ?>
						<a href="<?php echo Yii::app()->createUrl('student/profile'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Profile</a>
					<?php endif; ?>
					<div class="border-t border-gray-100"></div>
					<a href="<?php echo Yii::app()->createUrl('site/logout'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Logout</a>
				</div>
			</div>
			<?php endif; ?>
		</div>
	</nav>
    <!-- Main Content -->
    <main class="container mx-auto flex-grow p-4">
        <?php echo $content; ?>
    </main>

    <!-- Simple Footer -->
    <footer class="bg-black text-white p-4 mt-auto">
        
    </footer>
    
    <!-- Alpine.js for dropdown functionality -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>