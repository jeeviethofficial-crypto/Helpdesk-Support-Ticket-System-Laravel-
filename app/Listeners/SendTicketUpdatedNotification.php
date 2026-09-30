<?php

namespace App\Listeners;
use App\Events\TicketUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendTicketUpdatedNotification implements ShouldQueue
{
    public function handle(TicketUpdated $event): void
    {
        $ticket = $event->ticket;
        Log::info("Ticket #{$ticket->id} status changed to {$ticket->status->value}. Notify User #{$ticket->user_id}.");
    }
}
