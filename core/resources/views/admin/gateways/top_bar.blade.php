<ul class="nav nav-tabs mb-4 topTap breadcrumb-nav" role="tablist">
    <button class="breadcrumb-nav-close"><i class="las la-times"></i></button>
    <li class="nav-item {{ menuActive(['admin.gateway.automatic.index','admin.gateway.automatic.edit']) }}" role="presentation">
        <a href="{{ route('admin.gateway.automatic.index') }}" class="nav-link text-dark" type="button">
            <i class="las la-credit-card"></i> @lang('Automatic Gateway')
        </a>
    </li>
    <li class="nav-item {{ menuActive(['admin.gateway.manual.index','admin.gateway.manual.edit','admin.gateway.manual.create']) }}" role="presentation">
        <a href="{{ route('admin.gateway.manual.index') }}" class="nav-link text-dark" type="button">
            <i class="las la-wallet"></i> @lang('Manual Gateway')
        </a>
    </li>
</ul>
