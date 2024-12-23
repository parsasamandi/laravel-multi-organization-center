<li class="nav-item has-treeview menu-open">
    {{-- Has sub menus --}}
    <a class="nav-link">
        <i class="{{ $fontAwesome }}"></i>
        <p class="mr-1">
            {{ $text }}
        </p>
    </a>

    <ul class="nav nav-treeview">
        {{-- Content --}}
        @if(isset($content))
            {{ $content }}
        @endif
    </ul>
</li>