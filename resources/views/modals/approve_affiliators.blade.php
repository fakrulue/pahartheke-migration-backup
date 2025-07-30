<!-- delete Modal -->
<div id="approve-modal" class="modal fade">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{translate('Approve Affiliator')}}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <form id="approve-affiliators" action="{{route('affiliates.affiliators.approve')}}" method="POST">
                    @csrf
                    <label for="commission">{{translate('Commission')}}</label>
                    <input type="number" name="commission" class="form-control">
                    <input type="hidden" name="id" id="id">
                    <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <button type="submit" class="btn btn-primary mt-2">{{translate('Approve')}}</button>
                </form>
            </div>
        </div>
    </div>
</div><!-- /.modal -->
