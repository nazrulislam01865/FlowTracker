<div class="ft-document-rows-placeholder" role="status" aria-live="polite" aria-busy="true">
    @for($group = 0; $group < 3; $group++)
        <section>
            <div class="ft-document-placeholder-heading"><span></span><span></span></div>
            @for($row = 0; $row < 3; $row++)
                <div class="ft-document-placeholder-row"><span></span><span></span><span></span><span></span></div>
            @endfor
        </section>
    @endfor
</div>
