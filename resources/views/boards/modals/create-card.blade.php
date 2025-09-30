<div class="modal fade" id="createCardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Card</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('cards.store') }}" method="POST">
                @csrf
                <input type="hidden" name="list_id" id="cardListId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Card Title</label>
                        <input type="text" class="form-control" name="title" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Card</button>
                </div>
            </form>
        </div>
    </div>
</div>