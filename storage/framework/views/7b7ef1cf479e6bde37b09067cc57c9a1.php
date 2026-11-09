<?php
    $orderId = (int) data_get($row, 'order_id', 0);
    $taskId = (int) data_get($row, 'next_task_id', 0);
    $label = (string) data_get($row, 'next_action', 'Open order');
    $useInlineListAction = (bool) ($inlineListActions ?? true);
    $isEmailHandoff = in_array((string) data_get($row, 'next_action_key', ''), ['NEW_SEND_PO_ARTWORK', 'ART_SEND_ORDER_TEAM'], true);
?>


<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($useInlineListAction && $orderId > 0 && $taskId > 0): ?>
    <button
        type="button"
        class="stage-action stage-action-button"
        wire:click="openListWorkflowAction(<?php echo e($orderId); ?>, <?php echo e($taskId); ?>)"
        wire:loading.attr="disabled"
        x-on:click.stop
    >
        <?php echo e($label); ?>

    </button>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($isEmailHandoff && ! (bool) data_get($row, 'is_on_hold', false)): ?>
        <button
            type="button"
            class="stage-action stage-action-button"
            wire:click="completeListWorkflowHandoffWithoutEmail(<?php echo e($orderId); ?>, <?php echo e($taskId); ?>)"
            wire:loading.attr="disabled"
            x-on:click.stop
        >Complete without email</button>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <span class="stage-table-note">Perform action here</span>
<?php else: ?>
    <a class="stage-action" href="<?php echo e($detailUrl); ?>" wire:navigate x-on:click.stop><?php echo e($label); ?></a>
    <span class="stage-table-note">Open order to continue</span>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/FlowTracker/resources/views/components/orders/prototype-next-action.blade.php ENDPATH**/ ?>