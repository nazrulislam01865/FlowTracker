<div class="ft-profile-page" data-ft-feedback-scope="form">
    <?php if (isset($component)) { $__componentOriginal8f6938ac62d0a39f318af1c1674a1814 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8f6938ac62d0a39f318af1c1674a1814 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.page-head','data' => ['title' => 'Edit Profile','subtitle' => 'Update your account details, profile photo and security settings']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.page-head'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Edit Profile','subtitle' => 'Update your account details, profile photo and security settings']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8f6938ac62d0a39f318af1c1674a1814)): ?>
<?php $attributes = $__attributesOriginal8f6938ac62d0a39f318af1c1674a1814; ?>
<?php unset($__attributesOriginal8f6938ac62d0a39f318af1c1674a1814); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8f6938ac62d0a39f318af1c1674a1814)): ?>
<?php $component = $__componentOriginal8f6938ac62d0a39f318af1c1674a1814; ?>
<?php unset($__componentOriginal8f6938ac62d0a39f318af1c1674a1814); ?>
<?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
        <div class="flash ft-profile-flash"><?php echo e(session('success')); ?></div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="ft-profile-layout ft-profile-edit-layout">
        <div class="ft-profile-main-column">
            <section class="card ft-profile-main-card">
                <div class="ft-profile-hero ft-profile-edit-hero">
                    <div class="ft-profile-avatar-wrap">
                        <?php
                            $profilePreviewUrl = $profileImage && str_starts_with((string) $profileImage->getMimeType(), 'image/') ? $profileImage->temporaryUrl() : null;
                        ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profilePreviewUrl): ?>
                            <span class="avatar ft-profile-avatar ft-profile-avatar-preview">
                                <img src="<?php echo e($profilePreviewUrl); ?>" alt="Profile image preview">
                            </span>
                        <?php else: ?>
                            <?php if (isset($component)) { $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.avatar','data' => ['user' => $user,'name' => $user->name,'size' => 96,'class' => 'ft-profile-avatar']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['user' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user),'name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($user->name),'size' => 96,'class' => 'ft-profile-avatar']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $attributes = $__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__attributesOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2)): ?>
<?php $component = $__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2; ?>
<?php unset($__componentOriginald04dd79f9e235eb8e58dee4526a2f3c2); ?>
<?php endif; ?>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="ft-profile-identity-copy">
                        <div class="ft-profile-eyebrow">Profile photo</div>
                        <h2><?php echo e($user->name); ?></h2>

                        <div class="ft-profile-meta-line">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($position): ?><span><?php echo e($position); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php ($profileRoles = $user->assignedRoles()->pluck('name')); ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profileRoles->isNotEmpty()): ?><span><?php echo e($profileRoles->join(' · ')); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->department?->name): ?><span><?php echo e($user->department->name); ?></span><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="ft-profile-photo-actions">
                            <label class="ft-profile-upload-button">
                                <input type="file" wire:model="profileImage" accept="image/jpeg,image/png,image/webp">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 16V4m0 0 4 4m-4-4-4 4M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4" /></svg>
                                <span><?php echo e($user->profile_image_path ? 'Choose new photo' : 'Choose photo'); ?></span>
                            </label>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($profileImage): ?>
                                <button class="primary ft-profile-photo-save" type="button" wire:click="saveProfileImage" wire:loading.attr="disabled" wire:target="saveProfileImage,profileImage">
                                    <span wire:loading.remove wire:target="saveProfileImage">Use this photo</span>
                                    <span wire:loading wire:target="saveProfileImage">Saving…</span>
                                </button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user->profile_image_path): ?>
                                <button class="ft-profile-photo-remove" type="button" wire:click="removeProfileImage" wire:confirm="Remove your profile image?">Remove photo</button>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="ft-profile-photo-help">JPG, PNG or WebP · Maximum 250 KB · Square images work best.</div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['profileImage'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error ft-profile-inline-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <div wire:loading wire:target="profileImage" class="ft-profile-photo-help">Preparing preview…</div>
                    </div>
                </div>

                <div class="ft-profile-section ft-profile-edit-section">
                    <div class="ft-profile-section-heading">
                        <span class="ft-profile-section-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M20 21a8 8 0 0 0-16 0M12 13a5 5 0 1 0 0-10 5 5 0 0 0 0 10Z" /></svg>
                        </span>
                        <div>
                            <h3>Account information</h3>
                            <p>These details identify you across Jobs, Tasks, comments and notifications.</p>
                        </div>
                    </div>

                    <div class="ft-profile-form-grid">
                        <div class="field">
                            <label for="profile-name">Full name</label>
                            <input id="profile-name" wire:model="name" autocomplete="name" placeholder="Your full name">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="field">
                            <label for="profile-email">Email address</label>
                            <input id="profile-email" wire:model="email" type="email" autocomplete="email" placeholder="name@company.com">
                            <small class="ft-profile-field-help">Used for sign-in and account-related communication.</small>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        <div class="field ft-profile-language-field">
                            <label for="profile-language">Preferred language</label>
                            <select id="profile-language" wire:model="locale">
                                <option value="en">English</option>
                                <option value="zh">Simplified Chinese</option>
                            </select>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['locale'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="ft-profile-main-footer">
                    <div class="ft-profile-save-copy">
                        <strong>Personal changes</strong>
                        <span>Name, email and language are updated together.</span>
                    </div>
                    <button class="primary ft-profile-save-button" type="button" wire:click="saveProfile" wire:loading.attr="disabled" wire:target="saveProfile">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12.5 9.5 17 19 7.5" /></svg>
                        <span wire:loading.remove wire:target="saveProfile">Save profile</span>
                        <span wire:loading wire:target="saveProfile">Saving…</span>
                    </button>
                </div>
            </section>
        </div>

        <aside class="ft-profile-side-column">
            <section class="card ft-profile-side-card ft-profile-company-card">
                <div class="ft-profile-side-heading ft-profile-company-heading">
                    <span class="ft-profile-section-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 10h1m4 0h1m-6 4h1m4 0h1m-5 7v-3h4v3" /></svg>
                    </span>
                    <div>
                        <h3>Company details</h3>
                        <p>Managed by your administrator.</p>
                    </div>
                </div>

                <div class="ft-profile-company-grid ft-profile-company-grid-side">
                    <div class="ft-profile-company-item"><span>Position</span><strong><?php echo e($position ?: 'Not assigned'); ?></strong></div>
                    <div class="ft-profile-company-item"><span>Roles</span><strong><?php echo e($user->assignedRoles()->pluck('name')->join(', ') ?: 'Not assigned'); ?></strong></div>
                    <div class="ft-profile-company-item"><span>Department</span><strong><?php echo e($user->department?->name ?: 'Not assigned'); ?></strong></div>
                </div>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canManageBranding): ?>
                    <a class="ft-profile-admin-link" href="<?php echo e(route('administration', ['tab' => 'branding'])); ?>" wire:navigate>
                        <span>Manage system logo &amp; favicon</span>
                        <span aria-hidden="true">→</span>
                    </a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </section>

            <section class="card ft-profile-side-card">
                <div class="ft-profile-side-heading">
                    <span class="ft-profile-section-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M7 10V8a5 5 0 0 1 10 0v2m-11 0h12a2 2 0 0 1 2 2v7a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2Z" /></svg>
                    </span>
                    <div>
                        <h3>Change password</h3>
                        <p>Use at least 10 characters and avoid reusing another password.</p>
                    </div>
                </div>

                <div class="field">
                    <label for="current-password">Current password</label>
                    <input id="current-password" wire:model="currentPassword" type="password" autocomplete="current-password" placeholder="Enter current password">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['currentPassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="field">
                    <label for="new-password">New password</label>
                    <input id="new-password" wire:model="newPassword" type="password" autocomplete="new-password" minlength="10" placeholder="At least 10 characters">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newPassword'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <div class="field">
                    <label for="new-password-confirmation">Confirm new password</label>
                    <input id="new-password-confirmation" wire:model="newPasswordConfirmation" type="password" autocomplete="new-password" minlength="10" placeholder="Repeat new password">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['newPasswordConfirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="validation-error"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>

                <button class="secondary ft-profile-password-button" type="button" wire:click="changePassword" wire:loading.attr="disabled" wire:target="changePassword">
                    <span wire:loading.remove wire:target="changePassword">Update password</span>
                    <span wire:loading wire:target="changePassword">Updating…</span>
                </button>
            </section>

            <section class="card ft-profile-side-card ft-profile-notifications-card">
                <div class="ft-profile-side-heading">
                    <span class="ft-profile-section-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" /></svg>
                    </span>
                    <div>
                        <h3>Notification coverage</h3>
                        <p>Current FlowTrack events that can notify your account.</p>
                    </div>
                </div>

                <div class="ft-profile-notification-list ft-profile-notification-summary">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $notificationPreferences; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => [$title, $description]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="ft-profile-notification-row is-summary">
                            <span class="ft-profile-notification-check is-on" aria-hidden="true"></span>
                            <span class="ft-profile-notification-copy"><strong><?php echo e($title); ?></strong><small><?php echo e($description); ?></small></span>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
                <a class="ft-profile-notification-link" href="<?php echo e(route('notifications')); ?>" wire:navigate>Open notifications →</a>
            </section>

            <section class="card ft-profile-signout-card">
                <div><strong>Finished for now?</strong><span>Sign out securely from this device.</span></div>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button class="danger-btn ft-profile-signout-button" type="submit">Sign out</button>
                </form>
            </section>
        </aside>
    </div>
</div>
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/laravel/flowtrack/resources/views/livewire/profile/index.blade.php ENDPATH**/ ?>