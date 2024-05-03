<x-app-layout>
    <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Register User Locker') }}
            </h2>
        </x-slot>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- User  -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('User Information') }}
                            </h2>
                        </header>
                        <!-- Name -->
                        <div>
                            <x-input-label for="username" :value="__('Username')" />
                            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
                            <x-input-error :messages="$errors->get('username')" class="mt-2" />
                        </div>
                        <!-- Role -->
                        <div>
                            <x-input-label for="Role" :value="__('Role')" />
                            <x-text-input id="Role" class="block mt-1 w-full" type="text" name="Role" :value="old('Role')" required autofocus autocomplete="Role" />
                            <x-input-error :messages="$errors->get('Role')" class="mt-2" />
                        </div>
                    </div>
                </div>
                <!-- Fingerprint -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('Fingerprint') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __("Tambahkan data fingerprint!") }}
                        </p>
                    </header>
                    </div>
                </div>
                <!-- Keypad -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('keypad password') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __("Tambahkan data keypad!") }}
                        </p>
                    </header>
                    </div>
                </div>
                <!-- RFIDAuth -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                            {{ __('RFID') }}
                        </h2>

                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __("Tambahkan data RFID!") }}
                        </p>
                    </header>
                    </div>
                </div>
            </div>
        </div>
</x-app-layout>