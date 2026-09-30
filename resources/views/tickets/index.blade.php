<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-2xl text-gray-900 tracking-tight flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                {{ __('Support Tickets') }}
            </h2>
            <a href="{{ route('tickets.create') }}" class="group relative inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold text-white transition-all duration-200 bg-indigo-600 border border-transparent rounded-full hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                <span>Create Ticket</span>
                <svg class="w-4 h-4 ml-2 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            </a>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/80 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th class="p-4 pl-6">Ticket</th>
                                <th class="p-4">Status</th>
                                <th class="p-4">Priority</th>
                                <th class="p-4">Category</th>
                                <th class="p-4">Assignee</th>
                                <th class="p-4 pr-6 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse ($tickets as $ticket)
                                <tr class="hover:bg-indigo-50/30 transition-colors duration-150 group">
                                    <td class="p-4 pl-6">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 flex items-center justify-center rounded-full bg-indigo-100 text-indigo-600 font-bold text-sm">
                                                #{{ $ticket->id }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-gray-900 group-hover:text-indigo-600 transition-colors">{{ $ticket->title }}</div>
                                                <div class="text-xs text-gray-500 mt-0.5">Requested by {{ $ticket->user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4">
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
                                            <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ str_replace(['bg-', 'text-', 'border-'], ['bg-', 'bg-', 'bg-'], explode(' ', $colorClass)[1]) }}"></span>
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status->value)) }}
                                        </span>
                                    </td>
                                    <td class="p-4">
                                        @php
                                            $priorityColors = [
                                                'low' => 'text-gray-500 bg-gray-50 ring-gray-200',
                                                'medium' => 'text-orange-600 bg-orange-50 ring-orange-200',
                                                'high' => 'text-rose-600 bg-rose-50 ring-rose-200',
                                            ];
                                            $pColor = $priorityColors[$ticket->priority->value] ?? 'text-gray-500 bg-gray-50 ring-gray-200';
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium ring-1 ring-inset {{ $pColor }}">
                                            {{ ucfirst($ticket->priority->value) }}
                                        </span>
                                    </td>
                                    <td class="p-4 text-sm text-gray-600 font-medium">
                                        {{ $ticket->category?->name ?? 'N/A' }}
                                    </td>
                                    <td class="p-4">
                                        @if($ticket->assignee)
                                            <div class="flex items-center">
                                                <div class="h-6 w-6 rounded-full bg-indigo-600 text-white flex items-center justify-center text-xs font-bold ring-2 ring-white">
                                                    {{ substr($ticket->assignee->name, 0, 1) }}
                                                </div>
                                                <span class="ml-2 text-sm text-gray-700">{{ $ticket->assignee->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-sm text-gray-400 italic">Unassigned</span>
                                        @endif
                                    </td>
                                    <td class="p-4 pr-6 text-right text-sm font-medium">
                                        <a href="{{ route('tickets.show', $ticket) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 px-3 py-1.5 rounded-lg transition-colors">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        No tickets found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    @if($tickets->hasPages())
                        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                            {{ $tickets->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
