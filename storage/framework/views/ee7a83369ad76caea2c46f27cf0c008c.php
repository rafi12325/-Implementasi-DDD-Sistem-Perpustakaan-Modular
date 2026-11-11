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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Detail Peminjaman</h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <?php if(session('status')): ?>
                <div class="p-3 bg-green-100 text-green-800 rounded"><?php echo e(session('status')); ?></div>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow p-6 space-y-2 text-sm">
                <p><strong>No. Peminjaman:</strong> <?php echo e($loan->loan_number); ?></p>
                <p><strong>Anggota:</strong> <?php echo e($loan->member?->user?->name); ?> (<?php echo e($loan->member?->member_number); ?>)</p>
                <p><strong>Status:</strong> <?php echo e($loan->status); ?></p>
                <p><strong>Dipinjam:</strong> <?php echo e($loan->borrowed_at?->format('d M Y H:i')); ?></p>
                <p><strong>Jatuh Tempo:</strong> <?php echo e($loan->due_at?->format('d M Y H:i')); ?></p>
                <p><strong>Dikembalikan:</strong> <?php echo e($loan->returned_at?->format('d M Y H:i') ?? '-'); ?></p>

                <h3 class="font-semibold pt-4">Detail Buku (snapshot)</h3>
                <ul class="list-disc pl-5">
                    <?php $__currentLoopData = $loan->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($item->book_title); ?> — ISBN: <?php echo e($item->book_isbn); ?> x<?php echo e($item->quantity); ?>

                            <?php if($item->returned_at): ?> <span class="text-green-600">(dikembalikan)</span> <?php endif; ?>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>

                <h3 class="font-semibold pt-4">Denda</h3>
                <?php $__empty_1 = true; $__currentLoopData = $loan->fines; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fine): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <p>Rp <?php echo e(number_format($fine->amount, 0, ',', '.')); ?> (<?php echo e($fine->days_late); ?> hari x Rp <?php echo e(number_format($fine->daily_rate, 0, ',', '.')); ?>)</p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500">Tidak ada denda.</p>
                <?php endif; ?>

                <?php if($loan->status === 'borrowed'): ?>
                    <form method="POST" action="<?php echo e(route('loans.return', $loan)); ?>" class="pt-4">
                        <?php echo csrf_field(); ?>
                        <button class="px-4 py-2 bg-green-600 text-white rounded-md">Kembalikan Buku</button>
                    </form>
                <?php endif; ?>
            </div>
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
<?php /**PATH D:\11\auth-monolith1\app\Modules\Circulation\Providers/../Views/loans/show.blade.php ENDPATH**/ ?>