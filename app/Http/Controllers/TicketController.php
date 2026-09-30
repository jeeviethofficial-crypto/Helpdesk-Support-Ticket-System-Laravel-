<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\User;
use App\Enums\UserRole;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Events\TicketUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Ticket::with(['category', 'user', 'assignee'])->latest();

        if ($user->isCustomer()) $query->where('user_id', $user->id);
        elseif ($user->isAgent()) $query->where('assigned_to', $user->id)->orWhereNull('assigned_to');

        $tickets = $query->paginate(10);
        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('tickets.create', compact('categories'));
    }

    public function store(StoreTicketRequest $request)
    {
        $ticket = $request->user()->tickets()->create($request->validated());
        return redirect()->route('tickets.show', $ticket)->with('status', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket)
    {
        Gate::authorize('view', $ticket);
        
        $ticket->load(['user', 'category', 'assignee', 'comments.user']);
        $agents = User::where('role', UserRole::Agent)->get();

        return view('tickets.show', compact('ticket', 'agents'));
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        Gate::authorize('update', $ticket);
        if ($request->has('assigned_to')) Gate::authorize('assign', $ticket);

        // Check if status actually changed
        $statusChanged = $request->has('status') && $ticket->status->value !== $request->status;

        $ticket->update($request->validated());

        if ($statusChanged) {
            TicketUpdated::dispatch($ticket);
        }

        return redirect()->route('tickets.show', $ticket)->with('status', 'Ticket updated successfully.');
    }
}
