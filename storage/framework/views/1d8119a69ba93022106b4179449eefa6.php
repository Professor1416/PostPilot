<?php $__env->startSection('title', 'Post Queue'); ?>
<?php $__env->startSection('content'); ?>
<div class="pp-main">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;">
        <div>
            <h1 class="pp-page-title">Post Queue</h1>
            <p class="pp-page-sub"><?php echo e($posts->total()); ?> post<?php echo e($posts->total() !== 1 ? 's' : ''); ?> found</p>
        </div>
        <a href="<?php echo e(route('dashboard.calendar')); ?>" class="pp-btn pp-btn-primary">+ New Post</a>
    </div>

    
    <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px;">
        <div>
            <select name="status" class="pp-select" style="width:auto;margin-bottom:0;" onchange="this.form.submit()">
                <?php $__currentLoopData = ['all' => 'All Status', 'scheduled' => 'Scheduled', 'published' => 'Published', 'failed' => 'Failed', 'draft' => 'Draft']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($val); ?>" <?php echo e(request('status', 'all') === $val ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <select name="platform" class="pp-select" style="width:auto;margin-bottom:0;" onchange="this.form.submit()">
                <option value="">All Platforms</option>
                <option value="instagram" <?php echo e(request('platform') === 'instagram' ? 'selected' : ''); ?>>Instagram</option>
                <option value="facebook" <?php echo e(request('platform') === 'facebook' ? 'selected' : ''); ?>>Facebook</option>
            </select>
        </div>
    </form>

    
    <?php if($posts->isEmpty()): ?>
        <div style="display:flex;flex-direction:column;align-items:center;gap:12px;min-height:300px;justify-content:center;color:#444;">
            <span style="font-size:40px;">📋</span>
            <p style="font-size:15px;font-weight:600;color:#555;font-family:'Syne',sans-serif;">No posts found</p>
            <a href="<?php echo e(route('dashboard.calendar')); ?>" class="pp-btn pp-btn-primary">+ Create Post</a>
        </div>
    <?php else: ?>
        <div style="display:flex;flex-direction:column;gap:8px;">
            <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $statusClass = match($post->status) {
                        'scheduled'  => 'pp-badge-scheduled',
                        'published'  => 'pp-badge-published',
                        'failed'     => 'pp-badge-failed',
                        'publishing' => 'pp-badge-publishing',
                        default      => 'pp-badge-draft',
                    };
                    $borderColor = match($post->status) {
                        'published'  => '#27AE60',
                        'failed'     => '#C0392B',
                        'scheduled'  => '#FF6B00',
                        default      => '#555',
                    };
                ?>

                <div style="display:flex;align-items:center;gap:14px;background:#0E0E18;border:1px solid #18182A;border-radius:12px;padding:14px 16px;position:relative;overflow:hidden;">
                    <div style="position:absolute;left:0;top:0;bottom:0;width:3px;background:<?php echo e($borderColor); ?>;border-radius:12px 0 0 12px;"></div>

                    <span style="font-size:20px;flex-shrink:0;"><?php echo e(in_array('instagram', $post->platforms ?? []) ? '📸' : '📘'); ?></span>

                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;color:#BEBECE;overflow:hidden;white-space:nowrap;text-overflow:ellipsis;margin-bottom:4px;">
                            <?php echo e($post->captionSnippet()); ?>

                        </div>
                        <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                            <?php if($post->festival): ?>
                                <span style="font-size:11px;color:#FF6B00;">🎉 <?php echo e($post->festival); ?></span>
                            <?php endif; ?>
                            <?php if($post->ai_generated): ?>
                                <span style="font-size:10px;background:rgba(155,89,182,0.1);color:#9B59B6;padding:2px 6px;border-radius:4px;font-weight:700;">AI</span>
                            <?php endif; ?>
                            <span style="font-size:11px;color:#444;"><?php echo e($post->scheduledAtIst()); ?></span>
                        </div>
                    </div>

                    <span class="pp-badge <?php echo e($statusClass); ?>"><?php echo e($post->status); ?></span>
                    <?php if($post->image_url): ?> <span style="font-size:14px;">🖼</span> <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div style="margin-top:20px;">
            <?php echo e($posts->withQueryString()->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\postpilot-laravel\resources\views/dashboard/queue.blade.php ENDPATH**/ ?>