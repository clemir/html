<ul class="{{ $class }}">
@foreach ($items as $item)
    @if (empty ($item->items))
        <li{!! Html::classes(['nav-item', $item->class, 'active' => $item->active]) !!}>
            <a href="{{ $item->url() }}" class="nav-link">
                {{ $item->text }}
            </a>
        </li>
    @else
        <li{!! Html::classes(['nav-item', 'dropdown', $item->class, 'active' => $item->active]) !!}>
            <a href="{{ $item->url() }}" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                {{ $item->text }}
            </a>
            <div class="dropdown-menu">
                @foreach ($item->items as $subitem)
                    <a href="{{ $subitem->url() }}" class="dropdown-item">{{ $subitem->text }}</a>
                @endforeach
            </div>
        </li>
    @endif
@endforeach
</ul>
