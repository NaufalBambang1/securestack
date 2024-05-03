<x-app-layout>
    <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Add New Locker') }}
            </h2>
        </x-slot>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Locker  -->
                <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg">
                    <div class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                                {{ __('Locker Information') }}
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
            </div>
        </div>
</x-app-layout>