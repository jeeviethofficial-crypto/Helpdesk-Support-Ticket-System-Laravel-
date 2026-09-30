<?php

namespace App\Listeners;
use App\Events\CommentPosted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendCommentPostedNotification implements ShouldQueue
{
    public function handle(CommentPosted $event): void
    {
        $comment = $event->comment;
        $ticket = $comment->ticket;
        Log::info("New comment on Ticket #{$ticket->id} by User #{$comment->user_id}. Notify stakeholders.");
    }
}
