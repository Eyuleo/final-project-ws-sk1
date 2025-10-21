<div class="flex items-start justify-between">
    <div class="flex items-center flex-1">
        <div class="flex-shrink-0">
            @if($user->profile_picture)
                <img class="h-12 w-12 rounded-full" src="{{ Storage::url($user->profile_picture) }}" alt="">
            @else
                <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                    <span class="text-gray-600 font-medium text-lg">{{ substr($user->name, 0, 2) }}</span>
                </div>
            @endif
        </div>
        
        <div class="ml-4 flex-1">
            <div class="flex items-center space-x-3">
                <h3 class="text-lg font-medium text-gray-900">{{ $user->name }}</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                    {{ $user->role === 'student' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                    {{ $user->role }}
                </span>
                @if(!$user->is_active)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        Suspended
                    </span>
                @endif
            </div>
            
            <div class="mt-1 text-sm text-gray-600">
                <p>{{ $user->email }}</p>
            </div>

            @if($user->suspension_reason)
                <div class="mt-2 text-sm text-gray-600">
                    <span class="font-medium">Suspension Reason:</span> {{ $user->suspension_reason }}
                </div>
            @endif

            <div class="mt-3 flex items-center space-x-4 text-sm text-gray-500">
                <div class="flex items-center">
                    <svg class="mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Joined: {{ $user->created_at->format('M d, Y') }}
                </div>
                @if($user->role === 'student' && $user->studentProfile)
                    <div class="flex items-center">
                        <svg class="mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        {{ $user->studentProfile->serviceListings()->count() }} Services
                    </div>
                    <div class="flex items-center">
                        <svg class="mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        {{ number_format($user->studentProfile->average_rating ?? 0, 1) }} Rating
                    </div>
                @elseif($user->role === 'client' && $user->clientProfile)
                    <div class="flex items-center">
                        <svg class="mr-1.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        {{ $user->clientProfile->orders()->count() }} Orders
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="ml-4 flex-shrink-0 flex space-x-2">
        @if($user->is_active)
            <button type="button" 
                    onclick="openSuspendModal({{ $user->id }})" 
                    class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Suspend
            </button>
        @else
            <form method="POST" action="{{ route('admin.moderation.users.activate', $user) }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Activate
                </button>
            </form>
        @endif

        <a href="{{ route('admin.users.show', $user) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
            View Details
        </a>
    </div>
</div>

<!-- Suspend Modal -->
<div id="suspendModal-{{ $user->id }}" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
            <form method="POST" action="{{ route('admin.moderation.users.suspend', $user) }}">
                @csrf
                <div>
                    <div class="mt-3 text-center sm:mt-0 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Suspend User
                        </h3>
                        <div class="mt-2">
                            <label for="reason-{{ $user->id }}" class="block text-sm font-medium text-gray-700">Suspension Reason</label>
                            <textarea name="reason" id="reason-{{ $user->id }}" rows="4" required
                                      class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                      placeholder="Explain why this user is being suspended..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Suspend
                    </button>
                    <button type="button" onclick="closeSuspendModal({{ $user->id }})" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openSuspendModal(id) {
        document.getElementById('suspendModal-' + id).classList.remove('hidden');
    }
    function closeSuspendModal(id) {
        document.getElementById('suspendModal-' + id).classList.add('hidden');
    }
</script>
@endpush
