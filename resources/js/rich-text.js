const RICH_TEXT_MARKER = '<!--flowtrack-rich-text-->';
const MAX_IMAGE_BYTES = 10 * 1024 * 1024;

const state = {
    observer: null,
    observedBody: null,
    livewireHookBound: false,
    imageViewerController: null,
};

const richTextInputsWithin = (root) => {
    const inputs = [];
    if (root instanceof Element && root.matches('textarea[data-rich-text]')) inputs.push(root);
    root.querySelectorAll?.('textarea[data-rich-text]').forEach((input) => inputs.push(input));
    return inputs;
};

const escapeHtml = (value) => String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');

const editorHtmlFromStored = (stored) => {
    const value = String(stored ?? '');
    if (value.trimStart().startsWith(RICH_TEXT_MARKER)) {
        return value.trimStart().slice(RICH_TEXT_MARKER.length);
    }
    return escapeHtml(value).replace(/\r?\n/g, '<br>');
};

const editorHasContent = (editor) => {
    if (editor.querySelector('img')) return true;
    return String(editor.innerText || '').replace(/\u00a0/g, ' ').trim() !== '';
};

const storedFromEditor = (editor) => editorHasContent(editor)
    ? RICH_TEXT_MARKER + editor.innerHTML.trim()
    : '';

const moveCaretToEnd = (element) => {
    const selection = window.getSelection?.();
    if (!selection) return;
    const range = document.createRange();
    range.selectNodeContents(element);
    range.collapse(false);
    selection.removeAllRanges();
    selection.addRange(range);
};

const uploadImage = async (file) => {
    if (!file?.type?.startsWith('image/')) throw new Error('Only image files can be inserted here.');
    if (file.size > MAX_IMAGE_BYTES) throw new Error('Pasted images must be 10 MB or smaller.');

    const url = document.querySelector('meta[name="flowtrack-rich-text-upload-url"]')?.content;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!url || !csrf) throw new Error('Image upload is not available on this page.');

    const body = new FormData();
    body.append('image', file, file.name || 'pasted-image.png');

    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body,
    });

    if (!response.ok) {
        let message = 'Could not upload the image. Please try again.';
        try {
            const payload = await response.json();
            message = payload?.message || Object.values(payload?.errors || {})?.flat?.()?.[0] || message;
        } catch (_) {}
        throw new Error(message);
    }

    const payload = await response.json();
    if (!payload?.url) throw new Error('The image upload finished without a usable URL.');
    return String(payload.url);
};

const parseMentionUsers = (source) => {
    try {
        const users = JSON.parse(source.dataset.mentionUsers || '[]');
        return Array.isArray(users) ? users : [];
    } catch (_) {
        return [];
    }
};

const createButton = (label, title, command) => {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'ft-rich-text-tool';
    button.textContent = label;
    button.title = title;
    button.setAttribute('aria-label', title);
    button.dataset.command = command;
    return button;
};

const bootOne = (source) => {
    if (source.__flowtrackRichTextBound && source.__flowtrackRichTextShell?.isConnected) return;
    source.__flowtrackRichTextCleanup?.();

    const shell = document.createElement('div');
    shell.className = `ft-rich-text${source.dataset.richTextCompact !== undefined ? ' is-compact' : ''}`;

    const toolbar = document.createElement('div');
    toolbar.className = 'ft-rich-text-toolbar';
    toolbar.setAttribute('role', 'toolbar');
    toolbar.setAttribute('aria-label', 'Text formatting');

    const bold = createButton('B', 'Bold', 'bold');
    bold.classList.add('is-bold');
    const italic = createButton('I', 'Italic', 'italic');
    italic.classList.add('is-italic');
    const underline = createButton('U', 'Underline', 'underline');
    underline.classList.add('is-underline');
    const bullets = createButton('• List', 'Bulleted list', 'insertUnorderedList');
    const numbers = createButton('1. List', 'Numbered list', 'insertOrderedList');
    const imageButton = createButton('▧ Image', 'Insert image', 'image');
    imageButton.classList.add('ft-rich-text-image-button');
    toolbar.append(bold, italic, underline, bullets, numbers, imageButton);

    const editor = document.createElement('div');
    editor.className = 'ft-rich-text-editor';
    editor.contentEditable = source.disabled ? 'false' : 'true';
    editor.setAttribute('role', 'textbox');
    editor.setAttribute('aria-multiline', 'true');
    editor.dataset.placeholder = source.getAttribute('placeholder') || 'Write here…';
    editor.innerHTML = editorHtmlFromStored(source.value);

    const footer = document.createElement('div');
    footer.className = 'ft-rich-text-footer';
    const hint = document.createElement('span');
    hint.textContent = source.dataset.richTextCompact !== undefined
        ? 'Paste screenshots with Ctrl/⌘+V · Ctrl/⌘+Enter to comment'
        : 'Paste screenshots directly with Ctrl/⌘+V';
    const status = document.createElement('span');
    status.className = 'ft-rich-text-status';
    status.setAttribute('aria-live', 'polite');
    footer.append(hint, status);

    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = 'image/png,image/jpeg,image/webp,image/gif';
    fileInput.hidden = true;

    shell.append(toolbar, editor, footer, fileInput);
    source.insertAdjacentElement('beforebegin', shell);

    source.__flowtrackRichTextBound = true;
    source.__flowtrackRichTextShell = shell;
    const previousDisplay = source.style.display;
    source.style.display = 'none';

    let savedRange = null;
    let mentionMenu = null;
    let mentionMatches = [];
    let mentionIndex = 0;
    let mentionToken = null;
    const pendingUploads = new Set();

    const setStatus = (message = '', error = false) => {
        status.textContent = message;
        status.classList.toggle('is-error', error);
    };

    const rememberRange = () => {
        const selection = window.getSelection?.();
        if (!selection?.rangeCount) return;
        const range = selection.getRangeAt(0);
        if (editor.contains(range.startContainer)) savedRange = range.cloneRange();
    };

    const restoreRange = () => {
        editor.focus();
        const selection = window.getSelection?.();
        if (!selection) return;
        selection.removeAllRanges();
        if (savedRange && editor.contains(savedRange.startContainer)) selection.addRange(savedRange);
        else moveCaretToEnd(editor);
    };

    const syncToSource = () => {
        source.value = storedFromEditor(editor);
        source.dispatchEvent(new Event('input', { bubbles: true }));
    };

    const syncFromSource = () => {
        const expected = editorHtmlFromStored(source.value);
        if (editor.innerHTML !== expected) editor.innerHTML = expected;
    };

    source.__flowtrackRichTextSyncFromSource = syncFromSource;
    // Inline description editors intentionally keep the rich editor as the
    // single source of truth. Alpine x-model and async image uploads can race
    // with each other, so callers can explicitly reset, synchronously read, or
    // wait for all pending image uploads before reading the canonical value.
    source.__flowtrackRichTextSetValue = (value) => {
        source.value = String(value ?? '');
        syncFromSource();
    };
    source.__flowtrackRichTextValue = () => {
        syncToSource();
        return source.value;
    };
    source.__flowtrackRichTextValueAsync = async () => {
        if (pendingUploads.size) {
            await Promise.allSettled(Array.from(pendingUploads));
        }
        syncToSource();
        return source.value;
    };

    // Inline-edit code already focuses its textarea x-ref. Redirect that focus
    // into the visible rich editor while keeping the hidden textarea as the
    // Livewire/Alpine source of truth.
    const nativeFocus = source.focus.bind(source);
    source.focus = () => {
        syncFromSource();
        editor.focus();
        moveCaretToEnd(editor);
    };

    const removeMentionMenu = () => {
        mentionMenu?.remove();
        mentionMenu = null;
    };

    const closeMentions = () => {
        removeMentionMenu();
        mentionMatches = [];
        mentionIndex = 0;
        mentionToken = null;
    };

    const activeMentionToken = () => {
        const selection = window.getSelection?.();
        if (!selection?.rangeCount || !selection.isCollapsed) return null;
        const range = selection.getRangeAt(0);
        const node = range.startContainer;
        if (!editor.contains(node) || node.nodeType !== Node.TEXT_NODE) return null;

        const before = String(node.textContent || '').slice(0, range.startOffset);
        const match = before.match(/(^|[\s([{,:;])@([A-Za-z0-9._-]*)$/);
        if (!match) return null;
        const query = match[2] || '';
        return { node, start: range.startOffset - query.length - 1, end: range.startOffset, query };
    };

    const positionMentionMenu = () => {
        if (!mentionMenu) return;
        const selection = window.getSelection?.();
        let rect = editor.getBoundingClientRect();
        if (selection?.rangeCount) {
            const caretRect = selection.getRangeAt(0).getBoundingClientRect();
            if (caretRect.width || caretRect.height) rect = caretRect;
        }
        const width = Math.min(360, Math.max(260, editor.getBoundingClientRect().width));
        const left = Math.min(Math.max(12, rect.left), Math.max(12, window.innerWidth - width - 12));
        const estimated = Math.min(240, Math.max(48, mentionMatches.length * 40 + 10));
        const top = window.innerHeight - rect.bottom >= estimated + 12
            ? rect.bottom + 5
            : Math.max(12, rect.top - estimated - 5);
        mentionMenu.style.left = `${left}px`;
        mentionMenu.style.top = `${top}px`;
        mentionMenu.style.width = `${width}px`;
    };

    const selectMention = (user) => {
        if (!mentionToken?.node?.isConnected || !user) return;
        const { node, start, end } = mentionToken;
        const insertion = `@${user.handle} `;
        node.deleteData(start, end - start);
        node.insertData(start, insertion);
        const range = document.createRange();
        range.setStart(node, start + insertion.length);
        range.collapse(true);
        const selection = window.getSelection();
        selection.removeAllRanges();
        selection.addRange(range);
        savedRange = range.cloneRange();
        syncToSource();
        closeMentions();
        editor.focus();
    };

    const renderMentions = () => {
        removeMentionMenu();
        if (!mentionMatches.length) return;
        const menu = document.createElement('div');
        menu.className = 'ft-mention-menu ft-rich-text-mention-menu';
        menu.style.position = 'fixed';
        menu.setAttribute('role', 'listbox');

        mentionMatches.forEach((user, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `ft-mention-option${index === mentionIndex ? ' active' : ''}`;
            const avatar = document.createElement('span');
            avatar.className = 'ft-mention-avatar';
            avatar.textContent = String(user.name || '?').split(/\s+/).filter(Boolean).slice(0, 2).map((part) => part[0]).join('').toUpperCase();
            const copy = document.createElement('span');
            copy.className = 'ft-mention-copy';
            const name = document.createElement('strong');
            name.textContent = user.name || 'User';
            const handle = document.createElement('small');
            handle.textContent = `@${user.handle}`;
            copy.append(name, handle);
            button.append(avatar, copy);
            button.addEventListener('mousedown', (event) => {
                event.preventDefault();
                selectMention(user);
            });
            menu.appendChild(button);
        });

        mentionMenu = menu;
        document.body.appendChild(menu);
        positionMentionMenu();
    };

    const updateMentions = () => {
        const token = activeMentionToken();
        const users = parseMentionUsers(source);
        if (!token || users.length === 0) {
            closeMentions();
            return;
        }
        mentionToken = token;
        const query = token.query.toLowerCase().replace(/[._-]+/g, ' ').trim();
        mentionMatches = users.filter((user) => {
            const haystack = `${user.name || ''} ${user.handle || ''}`.toLowerCase().replace(/[._-]+/g, ' ');
            return query === '' || haystack.includes(query);
        }).slice(0, 8);
        mentionIndex = Math.min(mentionIndex, Math.max(0, mentionMatches.length - 1));
        renderMentions();
    };

    const insertImageAtRange = (url) => {
        restoreRange();
        const selection = window.getSelection();
        const range = selection?.rangeCount ? selection.getRangeAt(0) : null;
        if (!range) return;
        range.deleteContents();
        const image = document.createElement('img');
        image.src = url;
        image.alt = 'Pasted image';
        image.loading = 'lazy';
        const spacer = document.createElement('br');
        range.insertNode(spacer);
        range.insertNode(image);
        range.setStartAfter(spacer);
        range.collapse(true);
        selection.removeAllRanges();
        selection.addRange(range);
        savedRange = range.cloneRange();
        syncToSource();
    };

    const uploadAndInsert = async (files) => {
        const images = Array.from(files || []).filter((file) => file.type?.startsWith('image/'));
        if (!images.length) return;
        rememberRange();
        shell.classList.add('is-uploading');
        setStatus(images.length === 1 ? 'Uploading image…' : `Uploading ${images.length} images…`);
        try {
            for (const file of images) insertImageAtRange(await uploadImage(file));
            setStatus(images.length === 1 ? 'Image inserted' : 'Images inserted');
            window.setTimeout(() => setStatus(''), 1600);
        } catch (error) {
            setStatus(error?.message || 'Could not upload image.', true);
        } finally {
            shell.classList.remove('is-uploading');
        }
    };

    const queueUploadAndInsert = (files) => {
        const promise = uploadAndInsert(files);
        pendingUploads.add(promise);
        promise.finally(() => pendingUploads.delete(promise));
        return promise;
    };

    const onToolbar = (event) => {
        const button = event.target.closest('.ft-rich-text-tool');
        if (!button) return;
        event.preventDefault();
        if (button.dataset.command === 'image') {
            rememberRange();
            fileInput.click();
            return;
        }
        restoreRange();
        document.execCommand(button.dataset.command, false);
        syncToSource();
        rememberRange();
    };

    const onPaste = (event) => {
        const directFiles = Array.from(event.clipboardData?.files || [])
            .filter((file) => file.type?.startsWith('image/'));
        const itemFiles = Array.from(event.clipboardData?.items || [])
            .filter((item) => item.kind === 'file' && item.type?.startsWith('image/'))
            .map((item) => item.getAsFile?.())
            .filter(Boolean);

        // Browsers commonly expose the same pasted screenshot through BOTH
        // clipboardData.files and clipboardData.items. Combining those arrays
        // uploads/inserts the same screenshot twice because getAsFile() returns
        // a different File object. Prefer the canonical files collection and
        // only fall back to items when files is empty.
        const clipboardFiles = directFiles.length ? directFiles : itemFiles;
        const seenFiles = new Set();
        const files = clipboardFiles.filter((file) => {
            const fingerprint = [file.name || '', file.type || '', file.size || 0, file.lastModified || 0].join(':');
            if (seenFiles.has(fingerprint)) return false;
            seenFiles.add(fingerprint);
            return true;
        });

        if (files.length) {
            event.preventDefault();
            queueUploadAndInsert(files);
            return;
        }

        // Keep pasted web content safe and predictable. Formatting is available
        // through the toolbar; regular clipboard text is inserted as text only.
        const text = event.clipboardData?.getData('text/plain');
        if (typeof text === 'string' && text !== '') {
            event.preventDefault();
            document.execCommand('insertText', false, text);
            syncToSource();
            rememberRange();
        }
    };

    const onDrop = (event) => {
        const files = Array.from(event.dataTransfer?.files || []).filter((file) => file.type?.startsWith('image/'));
        if (!files.length) return;
        event.preventDefault();
        rememberRange();
        queueUploadAndInsert(files);
    };

    const onInput = () => {
        syncToSource();
        rememberRange();
        updateMentions();
    };

    const onKeydown = (event) => {
        if (mentionMenu && mentionMatches.length) {
            if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
                event.preventDefault();
                mentionIndex = (mentionIndex + (event.key === 'ArrowDown' ? 1 : -1) + mentionMatches.length) % mentionMatches.length;
                renderMentions();
                return;
            }
            if ((event.key === 'Enter' || event.key === 'Tab') && mentionMatches[mentionIndex]) {
                event.preventDefault();
                selectMention(mentionMatches[mentionIndex]);
                return;
            }
            if (event.key === 'Escape') {
                event.preventDefault();
                closeMentions();
                return;
            }
        }

        if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
            const submit = source.closest('.ft-comment-composer, .ft-inline-description-editor, .ft-inquiry-description-editor')
                ?.querySelector('[data-rich-text-submit]');
            if (submit) {
                event.preventDefault();
                syncToSource();
                submit.click();
            }
        }
    };

    const onFocus = () => {
        syncFromSource();
        rememberRange();
    };
    const onSelection = () => {
        if (document.activeElement === editor) rememberRange();
    };
    const onViewport = () => positionMentionMenu();

    toolbar.addEventListener('mousedown', onToolbar);
    editor.addEventListener('input', onInput);
    editor.addEventListener('paste', onPaste);
    editor.addEventListener('drop', onDrop);
    editor.addEventListener('keydown', onKeydown);
    editor.addEventListener('keyup', updateMentions);
    editor.addEventListener('click', updateMentions);
    editor.addEventListener('focus', onFocus);
    editor.addEventListener('blur', () => window.setTimeout(closeMentions, 120));
    fileInput.addEventListener('change', () => {
        queueUploadAndInsert(fileInput.files);
        fileInput.value = '';
    });
    document.addEventListener('selectionchange', onSelection);
    window.addEventListener('resize', onViewport);
    window.addEventListener('scroll', onViewport, true);

    source.__flowtrackRichTextCleanup = () => {
        closeMentions();
        toolbar.removeEventListener('mousedown', onToolbar);
        editor.removeEventListener('input', onInput);
        editor.removeEventListener('paste', onPaste);
        editor.removeEventListener('drop', onDrop);
        editor.removeEventListener('keydown', onKeydown);
        editor.removeEventListener('keyup', updateMentions);
        editor.removeEventListener('click', updateMentions);
        editor.removeEventListener('focus', onFocus);
        document.removeEventListener('selectionchange', onSelection);
        window.removeEventListener('resize', onViewport);
        window.removeEventListener('scroll', onViewport, true);
        shell.remove();
        source.style.display = previousDisplay;
        source.focus = nativeFocus;
        delete source.__flowtrackRichTextBound;
        delete source.__flowtrackRichTextShell;
        delete source.__flowtrackRichTextCleanup;
        delete source.__flowtrackRichTextSyncFromSource;
        delete source.__flowtrackRichTextSetValue;
        delete source.__flowtrackRichTextValue;
        delete source.__flowtrackRichTextValueAsync;
    };
};

export const bootRichTextEditors = (root = document) => {
    richTextInputsWithin(root).forEach(bootOne);
};

const refreshAll = (root = document) => {
    richTextInputsWithin(root).forEach((source) => {
        if (source.__flowtrackRichTextBound && !source.__flowtrackRichTextShell?.isConnected) {
            source.__flowtrackRichTextCleanup?.();
        }
        bootOne(source);
        // Always reconcile from the hidden source. During normal typing the
        // source already contains exactly what the editor contains, so this is
        // a no-op. After a Livewire action clears or replaces a value, it also
        // clears the visible editor even if the contenteditable still has focus.
        source.__flowtrackRichTextSyncFromSource?.();
    });
};

export const scheduleRichTextRefresh = (root = document) => {
    refreshAll(root);
    queueMicrotask(() => refreshAll(root?.isConnected === false ? document : root));
    window.requestAnimationFrame?.(() => refreshAll(root?.isConnected === false ? document : root));
};

export const observeRichTextEditors = () => {
    if (!document.body) return;

    // Livewire wire:navigate replaces the BODY contents. If a future Livewire
    // release replaces the body node itself, reconnect the observer instead of
    // leaving it attached to a detached page.
    if (state.observer && state.observedBody === document.body) return;
    state.observer?.disconnect?.();
    state.observer = new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {
                if (node instanceof Element) refreshAll(node);
            }
        }
    });
    state.observedBody = document.body;
    state.observer.observe(document.body, { childList: true, subtree: true });
};

export const bootLivewireRichTextHooks = () => {
    if (state.livewireHookBound || !window.Livewire?.hook) return;
    state.livewireHookBound = true;

    const bootElement = (payload = {}) => {
        const root = payload.el || payload.component?.el || document;
        scheduleRichTextRefresh(root);
    };

    // Livewire 4 can add a complete rich-text field without an existing node
    // receiving morph.updated. Cover component creation, newly-added elements,
    // and the final component morph so SPA navigation never leaves a raw
    // textarea on screen.
    for (const hook of ['component.init', 'morph.added', 'morph.updated', 'morphed']) {
        try {
            window.Livewire.hook(hook, bootElement);
        } catch (_) {}
    }
};

const richTextImageDownloadUrl = (src) => {
    try {
        const url = new URL(src, window.location.href);
        if (!/(?:^|\/)rich-text-images\/[^/]+$/i.test(url.pathname)) return url.href;
        url.pathname = `${url.pathname.replace(/\/$/, '')}/download`;
        return url.href;
    } catch (_) {
        return src;
    }
};

const createRichTextImageViewer = () => {
    const overlay = document.createElement('div');
    overlay.className = 'ft-rich-image-viewer';
    overlay.hidden = true;
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.setAttribute('aria-label', 'Image preview');
    overlay.innerHTML = `
        <div class="ft-rich-image-viewer-panel" data-rich-image-panel>
            <div class="ft-rich-image-viewer-toolbar">
                <div class="ft-rich-image-viewer-title" data-rich-image-title>Image preview</div>
                <div class="ft-rich-image-viewer-actions">
                    <button type="button" class="ft-rich-image-viewer-btn" data-rich-image-zoom-out aria-label="Zoom out" title="Zoom out">−</button>
                    <button type="button" class="ft-rich-image-viewer-zoom" data-rich-image-zoom-label aria-label="Current zoom">100%</button>
                    <button type="button" class="ft-rich-image-viewer-btn" data-rich-image-zoom-in aria-label="Zoom in" title="Zoom in">+</button>
                    <a class="ft-rich-image-viewer-download" data-rich-image-download href="#" download>Download</a>
                    <button type="button" class="ft-rich-image-viewer-close" data-rich-image-close aria-label="Close image preview" title="Close">×</button>
                </div>
            </div>
            <div class="ft-rich-image-viewer-stage" data-rich-image-stage>
                <img data-rich-image-preview alt="Image preview">
            </div>
        </div>
    `;
    document.body.appendChild(overlay);
    return overlay;
};

export const bootRichTextImageViewer = () => {
    if (!document.body) return;

    if (!state.imageViewerController) {
        let overlay = null;
        let preview = null;
        let download = null;
        let title = null;
        let zoomLabel = null;
        let stage = null;
        let closeButton = null;
        let zoom = 1;
        let lastFocused = null;

        const filenameFromSrc = (src) => {
            try {
                const url = new URL(src, window.location.href);
                return decodeURIComponent(url.pathname.split('/').filter(Boolean).pop() || 'image');
            } catch (_) {
                return 'image';
            }
        };

        const renderZoom = () => {
            if (!preview || !zoomLabel) return;
            zoom = Math.min(4, Math.max(0.25, zoom));
            preview.style.transform = `scale(${zoom})`;
            zoomLabel.textContent = `${Math.round(zoom * 100)}%`;
        };

        const setZoom = (next) => {
            zoom = next;
            renderZoom();
        };

        const bindOverlay = (nextOverlay) => {
            overlay = nextOverlay;
            preview = overlay.querySelector('[data-rich-image-preview]');
            download = overlay.querySelector('[data-rich-image-download]');
            title = overlay.querySelector('[data-rich-image-title]');
            zoomLabel = overlay.querySelector('[data-rich-image-zoom-label]');
            stage = overlay.querySelector('[data-rich-image-stage]');
            closeButton = overlay.querySelector('[data-rich-image-close]');

            stage.addEventListener('wheel', (event) => {
                if (overlay.hidden) return;
                event.preventDefault();
                setZoom(zoom + (event.deltaY < 0 ? 0.15 : -0.15));
            }, { passive: false });

            preview.addEventListener('dblclick', () => setZoom(zoom === 1 ? 2 : 1));
        };

        const ensureOverlay = () => {
            if (overlay?.isConnected) return overlay;
            // A wire:navigate body swap can detach an open viewer. Reset the
            // page lock before installing the fresh viewer in the new body.
            document.documentElement.classList.remove('ft-rich-image-viewer-open');
            lastFocused = null;
            bindOverlay(createRichTextImageViewer());
            return overlay;
        };

        const close = () => {
            if (!overlay?.isConnected || overlay.hidden) return;
            overlay.hidden = true;
            document.documentElement.classList.remove('ft-rich-image-viewer-open');
            preview?.removeAttribute('src');
            lastFocused?.focus?.({ preventScroll: true });
            lastFocused = null;
        };

        const open = (image) => {
            ensureOverlay();
            const src = image.currentSrc || image.src;
            if (!src) return;
            lastFocused = document.activeElement;
            preview.src = src;
            preview.alt = image.alt || 'Image preview';
            const filename = filenameFromSrc(src);
            title.textContent = filename;
            download.href = richTextImageDownloadUrl(src);
            download.setAttribute('download', filename);
            setZoom(1);
            overlay.hidden = false;
            document.documentElement.classList.add('ft-rich-image-viewer-open');
            closeButton.focus({ preventScroll: true });
        };

        document.addEventListener('click', (event) => {
            const image = event.target.closest?.('.ft-rich-text-content img');
            if (image) {
                event.preventDefault();
                open(image);
                return;
            }

            if (overlay?.isConnected && (event.target === overlay || event.target.closest?.('[data-rich-image-close]'))) {
                close();
                return;
            }

            if (event.target.closest?.('[data-rich-image-zoom-in]')) {
                setZoom(zoom + 0.25);
                return;
            }

            if (event.target.closest?.('[data-rich-image-zoom-out]')) {
                setZoom(zoom - 0.25);
                return;
            }

            if (event.target.closest?.('[data-rich-image-zoom-label]')) setZoom(1);
        });

        document.addEventListener('keydown', (event) => {
            if (!overlay?.isConnected || overlay.hidden) return;
            if (event.key === 'Escape') close();
            if (event.key === '+' || event.key === '=') setZoom(zoom + 0.25);
            if (event.key === '-') setZoom(zoom - 0.25);
            if (event.key === '0') setZoom(1);
        });

        state.imageViewerController = { ensureOverlay, close };
    }

    // wire:navigate replaces page markup and can remove a viewer appended to
    // the previous body. Recreate it every time the lifecycle boots if needed.
    state.imageViewerController.ensureOverlay();
};
