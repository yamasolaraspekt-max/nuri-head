<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Nibe Devices</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen p-8">

    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">My Nibe Devices</h1>
            <a href="/nibe/auth" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded shadow">
                Refresh Token
            </a>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @if(empty($devices))
            <div class="bg-white p-8 rounded-lg shadow text-center">
                <p class="text-gray-500 text-lg">No devices were found on this account.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($devices as $device)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                        
                        <h2 class="text-xl font-bold text-gray-800 mb-2">
                            {{ $device['product']['name'] ?? $device['name'] ?? 'Unknown Model' }}
                        </h2>
                        
                        <div class="text-sm text-gray-500 mb-4">
                            System: <span class="font-medium text-gray-700">{{ $device['system_name'] }}</span>
                        </div>

                        <div class="space-y-2 mb-6">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-500">Device ID:</span>
                                <span class="font-mono text-gray-800">{{ $device['id'] ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="flex justify-between text-sm items-center">
                                <span class="text-gray-500">Status:</span>
                                @php
                                    $status = $device['connectionState'] ?? 'Unknown';
                                    $statusColor = $status === 'Connected' ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50';
                                @endphp
                                <span class="px-2 py-1 rounded-md text-xs font-semibold {{ $statusColor }}">
                                    {{ $status }}
                                </span>
                            </div>
                        </div>

                        <div class="border-t pt-4">
                            <button class="w-full text-center text-blue-600 font-medium hover:text-blue-800 text-sm">
                                View Sensor Data &rarr;
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</body>
</html>