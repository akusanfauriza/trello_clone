<div class="modal fade" id="editListModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editListForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="list_id" id="editListId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">List Name</label>
                        <input type="text" class="form-control" id="editListName" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update List</button>
                </div>
            </form>
        </div>
    </div>
</div>