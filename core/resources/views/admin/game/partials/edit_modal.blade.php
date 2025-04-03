<div class="modal fade" id="editMarketModal" role="dialog" tabindex="-1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Edit Market')</h5>
                <button class="close" data-bs-dismiss="modal" type="button" aria-label="Close">
                    <i class="las la-times"></i>
                </button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label>@lang('Title')</label>
                                <input class="form-control" name="title" type="text" value="{{ old('title') }}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn--primary w-100 h-45" type="submit">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('script')
    <script>
        (function($) {
            "use strict";
            const modal = $('#editMarketModal');

            const handleEditMarket = (e) => {
                const market = JSON.parse(e.target.dataset.resource);
                let url = '{{ route("admin.market.update", ":id") }}';
                modal.find('form').attr('action', url.replace(":id", market.id));
                modal.find('[name=title]').val(market.title);
                modal.modal('show');
            }

            $('.editMarketBtn').on('click', (e) =>  handleEditMarket(e))
        })(jQuery);
    </script>
@endpush
