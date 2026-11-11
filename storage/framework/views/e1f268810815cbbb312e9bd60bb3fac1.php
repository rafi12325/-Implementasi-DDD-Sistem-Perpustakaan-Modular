<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Katalog Buku</h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            
            <form method="GET" action="<?php echo e(route('books.index')); ?>" class="flex gap-2">
                <input type="text" name="q" value="<?php echo e(request('q')); ?>" placeholder="Cari judul buku..."
                       class="flex-1 rounded-md border-gray-300 shadow-sm">
                <button class="px-4 py-2 bg-gray-800 text-white rounded-md">Cari</button>
            </form>

            <?php if(session('status')): ?>
                <div class="p-3 bg-green-100 text-green-800 rounded"><?php echo e(session('status')); ?></div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php $__empty_1 = true; $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="font-semibold text-gray-900"><?php echo e($book->title); ?></h3>
                        <p class="text-sm text-gray-600"><?php echo e($book->author?->name); ?> — <?php echo e($book->publisher?->name); ?></p>
                        <p class="text-xs text-gray-500">ISBN: <?php echo e($book->isbn->getFormatted()); ?></p>
                        <div class="mt-2 flex flex-wrap gap-1">
                            <?php $__currentLoopData = $book->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="text-xs bg-blue-100 text-blue-800 px-2 py-0.5 rounded"><?php echo e($category->name); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <p class="mt-2 text-sm">Stok: <strong><?php echo e($book->stock); ?></strong></p>
                        @role('admin')
                            <form method="POST" action="<?php echo e(route('books.destroy', $book)); ?>" class="mt-2">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button class="text-xs text-red-600 hover:underline">Soft delete</button>
                            </form>
                        @endrole
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500">Tidak ada buku.</p>
                <?php endif; ?>
            </div>

            <?php echo e($books->links()); ?>

        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH D:\11\auth-monolith1\app\Modules\Catalog\Providers/../Views/books/index.blade.php ENDPATH**/ ?>