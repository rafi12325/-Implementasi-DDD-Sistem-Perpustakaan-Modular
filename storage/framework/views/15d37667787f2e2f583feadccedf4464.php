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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Peminjaman Baru</h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <?php if($errors->any()): ?>
                <div class="p-3 mb-4 bg-red-100 text-red-800 rounded">
                    <ul><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('loans.store')); ?>" class="bg-white rounded-lg shadow p-6 space-y-4">
                <?php echo csrf_field(); ?>

                <div>
                    <label class="block text-sm font-medium">Anggota</label>
                    <select name="member_id" required class="mt-1 w-full rounded-md border-gray-300">
                        <option value="">-- Pilih anggota aktif --</option>
                        <?php $__currentLoopData = $members; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $member): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($member->id); ?>"><?php echo e($member->member_number); ?> — <?php echo e($member->user?->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium">Buku (pilih satu atau lebih)</label>
                    <div class="mt-2 max-h-72 overflow-y-auto border rounded divide-y">
                        <?php $__currentLoopData = $books; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $book): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center gap-3 p-2">
                                <input type="checkbox" name="items[<?php echo e($book->id); ?>][checked]" value="1"
                                       onchange="this.closest('label').querySelector('[type=number]').disabled = !this.checked">
                                <span class="flex-1">
                                    <?php echo e($book->title); ?>

                                    <span class="text-xs text-gray-500">(<?php echo e($book->author?->name); ?>, stok: <?php echo e($book->stock); ?>)</span>
                                </span>
                                <input type="hidden" name="items[<?php echo e($book->id); ?>][book_id]" value="<?php echo e($book->id); ?>">
                                <input type="number" name="items[<?php echo e($book->id); ?>][quantity]" value="1" min="1" max="5"
                                       disabled class="w-16 rounded-md border-gray-300">
                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium">Lama pinjam (hari)</label>
                    <input type="number" name="loan_days" value="7" min="1" max="30" class="mt-1 w-32 rounded-md border-gray-300">
                </div>

                <button class="px-4 py-2 bg-blue-600 text-white rounded-md">Simpan Peminjaman</button>
            </form>
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
<?php /**PATH D:\11\auth-monolith1\app\Modules\Circulation\Providers/../Views/loans/create.blade.php ENDPATH**/ ?>