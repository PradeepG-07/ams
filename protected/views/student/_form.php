<script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800">Student Information Form</h2>
    </div>
    
    <?php echo $this->renderPartial('/user/_form', array('model' => $model)); ?>
</div>
