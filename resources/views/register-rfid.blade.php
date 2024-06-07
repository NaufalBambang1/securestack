<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-center mb-4">Register RFID Tag</h3>
                    <form method="POST" action="{{ route('register.rfid') }}">
                        @csrf
                        <div class="mb-4">
                            <label for="rfid" class="block text-sm font-medium text-gray-700">RFID Tag</label>
                            <input type="text" name="rfid" id="rfid" class="mt-1 block w-full" required>
                        </div>
                        <div class="mb-4">
                            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                            <select name="username" id="username" class="mt-1 block w-full" required>
                                @foreach($users as $user)
                                    <option value="{{ $user->UserID }}">{{ $user->Username }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex justify-center">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                                Register RFID Tag
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
