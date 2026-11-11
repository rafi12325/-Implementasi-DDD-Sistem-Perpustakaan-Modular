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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Peminjaman Buku</h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <?php if(session('status')): ?>
                <div class="p-3 bg-green-100 text-green-800 rounded"><?php echo e(session('status')); ?></div>
            <?php endif; ?>

            <div class="flex gap-2">
                <a href="<?php echo e(route('loans.create')); ?>" class="px-4 py-2 bg-blue-600 text-white rounded-md">+ Peminjaman Baru</a>
                <a href="<?php echo e(route('loans.trashed')); ?>" class="px-4 py-2 bg-gray-200 rounded-md">Sampah</a>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-left">
                        <tr>
                            <th class="p-3">No. Peminjaman</th>
                            <th class="p-3">Anggota</th>
                            <th class="p-3">Buku</th>
                            <th class="p-3">Jatuh Tempo</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Denda</th>
                            <th class="p-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="border-t">
                                <td class="p-3"><?php echo e($loan->loan_number); ?></td>
                                <td class="p-3"><?php echo e($loan->member?->user?->name); ?> (<?php echo e($loan->member?->member_number); ?>)</td>
                                <td class="p-3">
                                    <?php $__currentLoopData = $loan->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php echo e($item->book_title); ?> x<?php echo e($item->quantity); ?><br>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </td>
                                <td class="p-3"><?php echo e($loan->due_at?->format('d M Y')); ?></td>
                                <td class="p-3">
                                    <span class="px-2 py-0.5 rounded text-xs <?php echo e($loan->status === 'borrowed' ? 'bg-yellow-100 text-yellow-800' : 'bg-green-100 text-green-800'); ?>">
                                        <?php echo e($loan->status); ?>

                                    </span>
                                </td>
                                <td class="p-3">Rp <?php echo e(number_format($loan->fines->sum('amount'), 0, ',', '.')); ?></td>
                                <td class="p-3 space-x-2">
                                    <a href="<?php echo e(route('loans.show', $loan)); ?>" class="text-blue-600 hover:underline">Detail</a>
                                    <?php if($loan->status === 'borrowed'): ?>
                                        <form method="POST" action="<?php echo e(route('loans.return', $loan)); ?>" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <button class="text-green-600 hover:underline">Kembalikan</button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" action="<?php echo e(route('loans.destroy', $loan)); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button class="text-red-600 hover:underline">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>

            <?php echo e($loans->links()); ?>

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
<?php /**PATH D:\11\auth-monolith1\app\Modules\Circulation\Providers/../Views/loans/index.blade.php ENDPATH**/ ?>