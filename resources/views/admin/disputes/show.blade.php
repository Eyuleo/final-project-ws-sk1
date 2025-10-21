<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dispute: {{ $order->order_number }}
            </h2>
            <a href="{{ route('admin.disputes.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Back to Disputes
            </a>
        </div>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Order Details -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Order Details</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Service</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $order->serviceListing->title }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Order Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $order->order_number }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Client</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $order->clientProfile->user->name }}
                                    <span class="text-gray-500">({{ $order->clientProfile->user->email }})</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Student Provider</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $order->studentProfile->user->name }}
                                    <span class="text-gray-500">({{ $order->studentProfile->user->email }})</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Order Amount</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <div>Subtotal: ${{ number_format($order->subtotal, 2) }}</div>
                                    <div>Platform Fee (15%): ${{ number_format($order->platform_fee, 2) }}</div>
                                    <div class="font-semibold">Total: ${{ number_format($order->total_amount, 2) }}</div>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Escrow Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
                                        {{ $order->escrow_status === 'held' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ str_replace('_', ' ', $order->escrow_status) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500">Requirements</dt>
                                <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ $order->requirements }}</dd>
                            </div>
                            @if($order->deliverable_files)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Deliverables</dt>
                                    <dd class="mt-1">
                                        @foreach($order->deliverable_files as $file)
                                            <a href="{{ Storage::url($file) }}" target="_blank" class="text-blue-600 hover:text-blue-500 text-sm block">
                                                {{ basename($file) }}
                                            </a>
                                        @endforeach
                                    </dd>
                                </div>
                            @endif
                            @if($order->dispute_reason)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Dispute Reason</dt>
                                    <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap bg-red-50 p-3 rounded">{{ $order->dispute_reason }}</dd>
                                </div>
                            @endif
                            @if($order->dispute_evidence_files)
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Evidence Files</dt>
                                    <dd class="mt-1">
                                        @foreach($order->dispute_evidence_files as $file)
                                            <a href="{{ Storage::url($file) }}" target="_blank" class="text-blue-600 hover:text-blue-500 text-sm block">
                                                {{ basename($file) }}
                                            </a>
                                        @endforeach
                                    </dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Conversation History -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Conversation History</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        @if($order->messages->count() > 0)
                            <div class="space-y-4 max-h-96 overflow-y-auto">
                                @foreach($order->messages as $message)
                                    <div class="flex {{ $message->sender_id === $order->clientProfile->user_id ? 'justify-start' : 'justify-end' }}">
                                        <div class="max-w-xs lg:max-w-md">
                                            <div class="text-xs text-gray-500 mb-1">
                                                {{ $message->sender->name }} - {{ $message->created_at->format('M d, Y H:i') }}
                                            </div>
                                            <div class="rounded-lg px-4 py-2 {{ $message->sender_id === $order->clientProfile->user_id ? 'bg-gray-100' : 'bg-blue-100' }}">
                                                <p class="text-sm text-gray-900">{{ $message->message }}</p>
                                                @if($message->attachment_path)
                                                    <a href="{{ Storage::url($message->attachment_path) }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-500 mt-1 block">
                                                        📎 {{ basename($message->attachment_path) }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No messages in this order</p>
                        @endif
                    </div>
                </div>

                <!-- Transaction History -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Transaction History</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        @if($order->transactions->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead>
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach($order->transactions as $transaction)
                                            <tr>
                                                <td class="px-3 py-2 text-sm text-gray-900">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                                <td class="px-3 py-2 text-sm text-gray-900 capitalize">{{ str_replace('_', ' ', $transaction->type) }}</td>
                                                <td class="px-3 py-2 text-sm text-gray-900">${{ number_format($transaction->amount, 2) }}</td>
                                                <td class="px-3 py-2 text-sm">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium capitalize
                                                        {{ $transaction->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                        {{ $transaction->status }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No transactions recorded</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Resolution Panel -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg sticky top-6">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Resolve Dispute</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        @if($order->dispute_resolved_at)
                            <div class="mb-4 p-4 bg-green-50 rounded-lg">
                                <p class="text-sm text-green-800">
                                    <strong>Dispute Resolved</strong><br>
                                    {{ $order->dispute_resolved_at->format('M d, Y H:i') }}
                                </p>
                                @if($order->dispute_resolution)
                                    <p class="text-sm text-green-700 mt-2">
                                        Resolution: <strong class="capitalize">{{ str_replace('_', ' to ', $order->dispute_resolution) }}</strong>
                                    </p>
                                @endif
                                @if($order->admin_notes)
                                    <p class="text-sm text-green-700 mt-2">
                                        Notes: {{ $order->admin_notes }}
                                    </p>
                                @endif
                            </div>
                        @else
                            <form method="POST" action="{{ route('admin.disputes.resolve', $order) }}" class="space-y-4">
                                @csrf

                                <!-- Resolution Options -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Resolution</label>
                                    <div class="space-y-2">
                                        <label class="flex items-center">
                                            <input type="radio" name="resolution" value="release_to_student" class="form-radio" required>
                                            <span class="ml-2 text-sm text-gray-700">Release to Student</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="resolution" value="refund_to_client" class="form-radio" required>
                                            <span class="ml-2 text-sm text-gray-700">Refund to Client</span>
                                        </label>
                                        <label class="flex items-center">
                                            <input type="radio" name="resolution" value="split" class="form-radio" required>
                                            <span class="ml-2 text-sm text-gray-700">Split Amount</span>
                                        </label>
                                    </div>
                                    @error('resolution')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Split Amount Fields (shown only if split is selected) -->
                                <div id="split-fields" class="hidden space-y-3">
                                    <div>
                                        <label for="student_amount" class="block text-sm font-medium text-gray-700">Student Amount</label>
                                        <input type="number" name="student_amount" id="student_amount" step="0.01" min="0" max="{{ $order->total_amount }}"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        @error('student_amount')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="client_amount" class="block text-sm font-medium text-gray-700">Client Amount</label>
                                        <input type="number" name="client_amount" id="client_amount" step="0.01" min="0" max="{{ $order->total_amount }}"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        @error('client_amount')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <p class="text-xs text-gray-500">Total must equal ${{ number_format($order->total_amount, 2) }}</p>
                                </div>

                                <!-- Admin Notes -->
                                <div>
                                    <label for="admin_notes" class="block text-sm font-medium text-gray-700">Admin Notes</label>
                                    <textarea name="admin_notes" id="admin_notes" rows="4" required
                                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                                              placeholder="Provide detailed reasoning for this resolution (minimum 50 characters)"></textarea>
                                    @error('admin_notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Submit Button -->
                                <div>
                                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Resolve Dispute
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Show/hide split amount fields based on resolution selection
        document.querySelectorAll('input[name="resolution"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const splitFields = document.getElementById('split-fields');
                if (this.value === 'split') {
                    splitFields.classList.remove('hidden');
                } else {
                    splitFields.classList.add('hidden');
                }
            });
        });
    </script>
    @endpush
</x-admin-layout>
