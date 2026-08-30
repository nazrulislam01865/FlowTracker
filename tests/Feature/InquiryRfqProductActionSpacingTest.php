<?php

test('rfq product supplier actions keep a safe inset from the table edge', function () {
    $css = file_get_contents(resource_path('css/modules/application/24-inquiry-rfq-product-workspace.css'));

    expect($css)
        ->toContain('.ft-rfq-px-table th:last-child,')
        ->toContain('width: 156px;')
        ->toContain('padding-right: var(--ft-space-3);')
        ->toContain('.ft-rfq-px-action-col .ft-rfq-px-row-action')
        ->toContain('width: max-content;')
        ->toContain('width: fit-content;');
});
