<div class="ft-admin-reference ft-workflow-form-page">
    <div class="ft-admin-form-top">
        <div>
            <div class="ft-admin-breadcrumb">{{ $workflowId ? 'Edit Workflow' : 'New Workflow' }}</div>
            <h1>{{ $workflowId ? 'Edit Workflow' : 'Create New Workflow' }}</h1>
            <p>Configure workflow identity on a dedicated page.</p>
        </div>
        <a href="{{ route('workflow.setup') }}" wire:navigate class="ft-admin-back">← Back to Workflow Setup</a>
    </div>

    <form wire:submit="save" class="ft-admin-form-card ft-workflow-create-card">
        <div class="ft-admin-form-card-head">
            <h2>{{ $workflowId ? 'Edit Workflow' : 'Create New Workflow' }}</h2>
            <p>Configure the workflow identity without changing the template layout.</p>
        </div>
        <div class="ft-admin-form-body ft-workflow-form-body">
            <div class="ft-admin-field">
                <label>Workflow name *</label>
                <input type="text" wire:model="workflowName" placeholder="e.g. Fast Track Order">
                @error('workflowName')<div class="validation-error">{{ $message }}</div>@enderror
            </div>
            <div class="ft-admin-field">
                <label>Workflow code *</label>
                <input type="text" wire:model="workflowCode" placeholder="e.g. FAST_TRACK">
                @error('workflowCode')<div class="validation-error">{{ $message }}</div>@enderror
            </div>
            <div class="ft-admin-field">
                <label>Description</label>
                <textarea wire:model="workflowDescription" rows="3" placeholder="Describe when this workflow should be used..."></textarea>
            </div>

            @unless($workflowId)
                <div class="ft-admin-field">
                    <label>Start from</label>
                    <select wire:model="sourceWorkflowId">
                        <option value="">Blank workflow</option>
                        @foreach($workflows as $workflow)<option value="{{ $workflow->id }}">{{ $workflow->name }}</option>@endforeach
                    </select>
                    <small>Duplicating copies the phase sequence and configuration, but not Job history.</small>
                    @error('sourceWorkflowId')<div class="validation-error">{{ $message }}</div>@enderror
                </div>
            @endunless
        </div>
        <div class="ft-admin-form-footer">
            <button type="button" class="ft-admin-cancel" wire:click="cancel">Cancel</button>
            <button type="submit" class="ft-admin-primary">{{ $workflowId ? 'Save Workflow' : 'Create Workflow' }}</button>
        </div>
    </form>
</div>
