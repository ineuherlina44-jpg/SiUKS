<nav class="siuks-breadcrumbs" aria-label="Breadcrumb">
    <ol>
        <li><a href="{{ route('home') }}">Beranda</a></li>
        @foreach ($breadcrumbItems as $item)
            <li aria-hidden="true">/</li>
            <li @if ($loop->last) aria-current="page" @endif>
                @if (isset($item['url']))
                    <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                @else
                    <span>{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
