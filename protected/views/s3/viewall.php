<!-- filepath: /home/dhanunjaya.s/Projects/yii/AMS/protected/views/s3/viewall.php -->
<?php
$this->pageTitle = Yii::app()->name . ' - View Uploaded Files';
?>

<div class="container mx-auto p-6 bg-white shadow-md rounded-lg">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Uploaded Files</h1>

    <?php if (!empty($files)): ?>
        <ul class="list-disc pl-6 space-y-2">
            <?php foreach ($files as $file): ?>
                <li>
                    <a href="<?php echo CHtml::encode($file['url']); ?>" target="_blank" 
                       class="text-blue-600 hover:underline">
                        <?php echo CHtml::encode(basename($file['key'])); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p class="text-gray-500">No files found.</p>
    <?php endif; ?>
</div>