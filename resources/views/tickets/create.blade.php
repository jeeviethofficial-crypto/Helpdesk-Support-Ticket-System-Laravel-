<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('tickets.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-900 tracking-tight">
                {{ __('Create New Ticket') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 bg-gray-50/50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden relative">
                
                <!-- Decorative element -->
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full opacity-50 blur-xl pointer-events-none"></div>

                <div class="p-8 sm:p-10 text-gray-900 relative z-10">
                    <div class="mb-8">
                        <h3 class="text-lg font-bold text-gray-900">How can we help?</h3>
                        <p class="text-sm text-gray-500 mt-1">Please provide as much detail as possible so we can best assist you.</p>
                    </div>

                    <form action="{{ route('tickets.store') }}" method="POST" class="space-y-6">
                        @csrf
                        
                        <!-- Title -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1" for="title">Subject</label>
                            <input type="text" name="title" id="title" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors py-2.5 px-3" placeholder="Brief summary of your issue" required>
                            <x-input-error :messages="$errors->get('title')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Category -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1" for="category_id">Category</label>
                                <div class="relative">
                                    <select name="category_id" id="category_id" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors py-2.5 px-3 appearance-none" required>
                                        <option value="" disabled selected>Select a Category...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                            </div>

                            <!-- Priority -->
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1" for="priority">Priority Level</label>
                                <div class="relative">
                                    <select name="priority" id="priority" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors py-2.5 px-3 appearance-none" required>
                                        <option value="low">Low - General inquiry</option>
                                        <option value="medium" selected>Medium - Issue affecting work</option>
                                        <option value="high">High - System down / Critical</option>
                                    </select>
                                </div>
                                <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1" for="description">Detailed Description</label>
                            <textarea name="description" id="description" rows="6" class="block w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm transition-colors p-4" placeholder="Describe the steps to reproduce the issue, expected results, and actual results..." required></textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                            <a href="{{ route('tickets.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-6 py-2.5 border border-transparent text-sm font-bold rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600 transition-all hover:-translate-y-0.5 hover:shadow-md">
                                Submit Ticket
                                <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
