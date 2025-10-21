@props(['earnings'])

<div class="bg-white overflow-hidden shadow-sm rounded-lg">
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Earnings Overview</h3>
        
        <div class="space-y-4">
            <!-- Total Earnings -->
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                <span class="text-sm text-gray-600">Total Earnings</span>
                <span class="text-lg font-semibold text-gray-900">
                    ETB {{ number_format($earnings['total'] ?? 0, 2) }}
                </span>
            </div>
            
            <!-- Available Balance -->
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                <span class="text-sm text-gray-600">Available Balance</span>
                <span class="text-lg font-semibold text-green-600">
                    ETB {{ number_format($earnings['available'] ?? 0, 2) }}
                </span>
            </div>
            
            <!-- Pending Balance -->
            <div class="flex justify-between items-center pb-3 border-b border-gray-200">
                <span class="text-sm text-gray-600">Pending (In Escrow)</span>
                <span class="text-lg font-semibold text-yellow-600">
                    ETB {{ number_format($earnings['pending'] ?? 0, 2) }}
                </span>
            </div>
            
            <!-- Withdrawn -->
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Total Withdrawn</span>
                <span class="text-lg font-semibold text-gray-700">
                    ETB {{ number_format($earnings['withdrawn'] ?? 0, 2) }}
                </span>
            </div>
        </div>
        
        @if(($earnings['available'] ?? 0) > 0)
            <div class="mt-6">
                <a href="{{ route('student.earnings.index') }}" class="block w-full text-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Withdraw Funds
                </a>
            </div>
        @endif
    </div>
</div>
