<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('tickets.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-3">
                <span class="text-indigo-500">#{{ $ticket->id }}</span>
                {{ $ticket->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Main Content: Ticket Details & Comments -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Ticket Description -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-5">
                        <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/></svg>
                    </div>
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 text-white flex items-center justify-center font-bold text-lg shadow-md">
                            {{ substr($ticket->user->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-900">{{ $ticket->user->name }}</div>
                            <div class="text-xs text-gray-500">Opened {{ $ticket->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    <div class="prose max-w-none text-gray-700 leading-relaxed">
                        {!! nl2br(e($ticket->description)) !!}
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                            Activity Timeline
                        </h3>
                        <span class="bg-indigo-100 text-indigo-700 py-1 px-3 rounded-full text-xs font-bold">{{ $ticket->comments->count() }} Responses</span>
                    </div>
                    
                    <div class="p-8">
                        <div class="space-y-8 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-transparent before:via-gray-200 before:to-transparent">
                            @forelse($ticket->comments as $comment)
                                <div class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full border-4 border-white bg-indigo-100 text-indigo-600 shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10 font-bold text-sm">
                                        {{ substr($comment->user->name, 0, 1) }}
                                    </div>
                                    <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] bg-white p-5 rounded-xl border border-gray-100 shadow-sm transition-all duration-300 hover:shadow-md">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="font-bold text-gray-900 text-sm">
                                                {{ $comment->user->name }}
                                                @if($comment->user->isAgent() || $comment->user->isAdmin())
                                                    <span class="ml-1 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-blue-100 text-blue-800">Support</span>
                                                @endif
                                            </span>
                                            <span class="text-xs text-gray-400 font-medium">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-gray-600 text-sm leading-relaxed">{!! nl2br(e($comment->body)) !!}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10 text-gray-500 italic">No activity yet. Be the first to reply!</div>
                            @endforelse
                        </div>

                        <!-- Add Comment Form -->
                        <div class="mt-10 pt-8 border-t border-gray-100">
                            <form action="{{ route('comments.store', $ticket) }}" method="POST">
                                @csrf
                                <div class="relative">
                                    <textarea name="body" rows="4" class="block w-full rounded-xl border-gray-200 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors sm:text-sm p-4" placeholder="Type your reply here..." required></textarea>
                                </div>
                                <div class="mt-4 flex justify-end">
                                    <button type="submit" class="inline-flex items-center px-6 py-2.5 border border-transparent text-sm font-bold rounded-full shadow-sm text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all hover:scale-105">
                                        Post Reply
                                        <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar: Metadata & Actions -->
            <div class="space-y-6">
                <!-- Status Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-8">
                    <div class="p-6 bg-gray-50/50 border-b border-gray-100">
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Ticket Details</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Status</span>
                                @php
                                    $statusColors = [
                                        'open' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        'in_progress' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'resolved' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'closed' => 'bg-gray-100 text-gray-600 border-gray-200',
                                    ];
                                    $colorClass = $statusColors[$ticket->status->value] ?? 'bg-gray-100 text-gray-600 border-gray-200';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $colorClass }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status->value)) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Priority</span>
                                <span class="text-sm font-bold capitalize {{ $ticket->priority->value === 'high' ? 'text-rose-600' : ($ticket->priority->value === 'medium' ? 'text-orange-600' : 'text-gray-600') }}">
                                    {{ $ticket->priority->value }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Category</span>
                                <span class="text-sm font-medium text-gray-900">{{ $ticket->category?->name ?? 'None' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Agent / Admin Actions -->
                    @can('update', $ticket)
                        <div class="p-6 bg-white">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Management</h3>
                            <form action="{{ route('tickets.update', $ticket) }}" method="POST" class="space-y-5">
                                @csrf
                                @method('PATCH')
                                
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                                    <select name="status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-700">
                                        @foreach(App\Enums\TicketStatus::cases() as $status)
                                            <option value="{{ $status->value }}" @selected($ticket->status === $status)>
                                                {{ ucfirst(str_replace('_', ' ', $status->value)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                @can('assign', $ticket)
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Assigned Agent</label>
                                    <div class="relative">
                                        <select name="assigned_to" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm text-gray-700 appearance-none">
                                            <option value="">Unassigned</option>
                                            @foreach($agents as $agent)
                                                <option value="{{ $agent->id }}" @selected($ticket->assigned_to === $agent->id)>
                                                    {{ $agent->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @else
                                <div class="pt-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Assigned Agent</label>
                                    <div class="flex items-center gap-2 p-2 bg-gray-50 rounded-lg border border-gray-100">
                                        @if($ticket->assignee)
                                            <div class="h-6 w-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-bold">{{ substr($ticket->assignee->name, 0, 1) }}</div>
                                            <span class="text-sm font-medium text-gray-900">{{ $ticket->assignee->name }}</span>
                                        @else
                                            <span class="text-sm text-gray-500 italic px-1">Currently unassigned</span>
                                        @endif
                                    </div>
                                </div>
                                @endcan

                                <button type="submit" class="w-full flex justify-center py-2.5 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    Save Changes
                                </button>
                            </form>
                        </div>
                    @endcan
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
