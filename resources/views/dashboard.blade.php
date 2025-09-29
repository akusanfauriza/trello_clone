@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <h2>My Boards</h2>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBoardModal">
                <i class="fas fa-plus"></i> Create Board
            </button>
        </div>
    </div>

    <div class="row mt-4">
        @foreach($ownedBoards as $board)
        <div class="col-md-4 mb-3">
            <div class="card board-card" style="border-left: 4px solid {{ $board->color }};">
                <div class="card-body">
                    <h5 class="card-title">{{ $board->name }}</h5>
                    <p class="card-text text-muted small">{{ Str::limit($board->description, 100) }}</p>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">Created by: {{ $board->user->name }}</small>
                        <a href="{{ route('boards.show', $board->id) }}" class="btn btn-sm btn-outline-primary">
                            Open
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        @if($ownedBoards->isEmpty() && $memberBoards->isEmpty())
        <div class="col-12">
            <div class="text-center py-5">
                <i class="fas fa-trello fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No boards yet</h4>
                <p class="text-muted">Create your first board to get started!</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBoardModal">
                    Create Your First Board
                </button>
            </div>
        </div>
        @endif
    </div>

    @if($memberBoards->isNotEmpty())
    <div class="row mt-5">
        <div class="col-12">
            <h3>Boards You're Member Of</h3>
            <div class="row">
                @foreach($memberBoards as $board)
                <div class="col-md-4 mb-3">
                    <div class="card board-card" style="border-left: 4px solid {{ $board->color }};">
                        <div class="card-body">
                            <h5 class="card-title">{{ $board->name }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($board->description, 100) }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Created by: {{ $board->user->name }}</small>
                                <a href="{{ route('boards.show', $board->id) }}" class="btn btn-sm btn-outline-primary">
                                    Open
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Create Board Modal -->
<div class="modal fade" id="createBoardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Board</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('boards.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Board Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Color</label>
                        <input type="color" class="form-control" name="color" value="#0079bf">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create Board</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection