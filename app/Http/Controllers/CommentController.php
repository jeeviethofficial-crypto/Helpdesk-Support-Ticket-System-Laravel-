<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Http\Requests\StoreCommentRequest;
use App\Events\CommentPosted;
use Illuminate\Support\Facades\Gate;

class CommentController extends Controller
{
    public function store(StoreCommentRequest $request, Ticket $ticket)
    {
        Gate::authorize('view', $ticket); // Ensure user can view the ticket

        $comment = $ticket->comments()->create([
            'body' => $request->body,
            'user_id' => $request->user()->id,
        ]);

        // Dispatch queued event
        CommentPosted::dispatch($comment);

        return redirect()->route('tickets.show', $ticket)->with('status', 'Comment posted.');
    }
}
