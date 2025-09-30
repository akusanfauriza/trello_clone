@extends('layouts.app')

@section('title', $board->name)

@section('content')
<div class="board-header" style="background-color: {{ $board->color }};">
    <div class="container py-3">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-1 text-white">{{ $board->name }}</h2>
                <p class="mb-0 text-white opacity-75">{{ $board->description }}</p>
            </div>
            <div class="col-md-4 text-end">
                <div class="btn-group">
                    @if($board->user_id === Auth::id())
                    <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#editBoardModal">
                        <i class="fas fa-cog"></i> Settings
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt-4">
    <div class="lists-container" id="listsContainer">
        @foreach($board->lists as $list)
        <div class="list-card" data-list-id="{{ $list->id }}">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">{{ $list->name }}</h6>
                    @if($board->user_id === Auth::id())
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-secondary edit-list-btn" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editListModal"
                                data-list-id="{{ $list->id }}"
                                data-list-name="{{ $list->name }}">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="{{ route('lists.destroy', $list->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" 
                                    onclick="return confirm('Delete this list?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
                <div class="card-body list-body" data-list-id="{{ $list->id }}">
                    @foreach($list->cards as $card)
                    <div class="card card-item mb-2" data-card-id="{{ $card->id }}">
                        <div class="card-body py-2">
                            <h6 class="card-title mb-1">{{ $card->title }}</h6>
                            @if($card->description)
                            <p class="card-text small text-muted mb-1">
                                {{ Str::limit($card->description, 50) }}
                            </p>
                            @endif
                            @if($card->due_date)
                            <div class="small text-warning">
                                <i class="fas fa-clock"></i>
                                {{ \Carbon\Carbon::parse($card->due_date)->format('M j') }}
                            </div>
                            @endif
                            @if($card->members->count() > 0)
                            <div class="mt-1">
                                @foreach($card->members as $member)
                                <span class="badge bg-primary" title="{{ $member->name }}">
                                    {{ Str::substr($member->name, 0, 1) }}
                                </span>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="card-footer">
                    <button class="btn btn-sm btn-outline-primary w-100" 
                            data-bs-toggle="modal" 
                            data-bs-target="#createCardModal"
                            data-list-id="{{ $list->id }}">
                        <i class="fas fa-plus"></i> Add Card
                    </button>
                </div>
            </div>
        </div>
        @endforeach

        @if($board->user_id === Auth::id() || $board->members->contains(Auth::id()))
        <div class="list-card">
            <div class="card">
                <div class="card-body text-center">
                    <button class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#createListModal">
                        <i class="fas fa-plus"></i> Add List
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Include Modals -->
@include('boards.modals.create-list')
@include('boards.modals.edit-list')
@include('boards.modals.create-card')

<!-- Activities Sidebar -->
@if(isset($board->activities) && $board->activities->count() > 0)
<div class="offcanvas offcanvas-end" tabindex="-1" id="activitiesOffcanvas">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title">Activity</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body">
        @foreach($board->activities as $activity)
        <div class="activity-item">
            <strong>{{ $activity->user->name }}</strong> {{ $activity->description }}
            <br>
            <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
        </div>
        @endforeach
    </div>
</div>

<!-- Floating Action Button -->
<div class="position-fixed bottom-0 end-0 p-3">
    <button class="btn btn-primary rounded-circle" 
            style="width: 60px; height: 60px;"
            data-bs-toggle="offcanvas" 
            data-bs-target="#activitiesOffcanvas">
        <i class="fas fa-history"></i>
    </button>
</div>
@endif

<!-- Scripts langsung di view -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Sortable for lists
    if (document.getElementById('listsContainer')) {
        new Sortable(document.getElementById('listsContainer'), {
            animation: 150,
            ghostClass: 'sortable-ghost',
            onEnd: function(evt) {
                console.log('List moved', evt);
            }
        });
    }

    // Initialize Sortable for each list
    document.querySelectorAll('.list-body').forEach(function(listBody) {
        new Sortable(listBody, {
            animation: 150,
            ghostClass: 'sortable-ghost',
            group: 'cards',
            onEnd: function(evt) {
                const cardId = evt.item.dataset.cardId;
                const newListId = evt.to.dataset.listId;
                
                // Send AJAX request to update card list
                fetch(`/cards/${cardId}/move`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        list_id: newListId,
                        position: evt.newIndex
                    })
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Card moved successfully');
                })
                .catch(error => {
                    console.error('Error moving card:', error);
                });
            }
        });
    });

    // Edit list modal handler
    document.querySelectorAll('.edit-list-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const listId = this.dataset.listId;
            const listName = this.dataset.listName;
            
            document.getElementById('editListId').value = listId;
            document.getElementById('editListName').value = listName;
            document.getElementById('editListForm').action = `/lists/${listId}`;
        });
    });

    // Create card modal handler
    const createCardModal = document.getElementById('createCardModal');
    if (createCardModal) {
        createCardModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const listId = button.dataset.listId;
            
            document.getElementById('cardListId').value = listId;
            document.getElementById('createCardForm').action = `/lists/${listId}/cards`;
        });
    }
});
</script>
@endsection