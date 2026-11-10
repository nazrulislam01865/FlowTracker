<article class="ft-mgmt-panel" style="min-height:{{ $height }}px" aria-busy="true" aria-label="{{ $heading }}">
    <div class="ft-mgmt-panel-head"><div><h2>{{ $heading }}</h2></div></div>
    <div class="ft-mgmt-panel-body" style="min-height:{{ max(80, $height - 80) }}px"></div>
    <span class="sr-only">Loading {{ $heading }}</span>
</article>
