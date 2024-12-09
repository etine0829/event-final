<x-guest-layout>
    <div class="max-w-lg mx-auto bg-white p-8 rounded-lg shadow-lg mt-5">
        <h3 class="text-3xl font-semibold text-center text-gray-800 mb-6">REGISTER YOUR ACCOUNT</h3>
        <hr class="border-gray-300 mb-6">
        <form method="POST" action="{{ route('register') }}">
            @csrf   
            <div class="space-y-2">

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Name')" class="font-medium text-gray-700" />
                    <x-text-input id="name" class="block mt-1 w-full px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-500" />
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" class="font-medium text-gray-700" />
                    <x-text-input id="email" class="block mt-1 w-full px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" type="email" name="email" :value="old('email')" required autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-500" />
                </div>

                <!-- Role -->
                <div class="mt-4">
                    <x-input-label for="role" :value="__('Role')" class="font-medium text-gray-700" />
                    <select id="role" class="block mt-1 w-full px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" name="role" required>
                        @if (!App\Models\User::where('role', 'admin')->exists())
                            <option value="admin">Admin</option>
                        @endif
                        <option value="judge">Judge</option>
                        <option value="event_manager">Event Manager</option>
                        <option value="staff">Staff</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2 text-red-500" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" class="font-medium text-gray-700" />
                    <x-text-input id="password" class="block mt-1 w-full px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-500" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="font-medium text-gray-700" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full px-4 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-500" />
                </div>

                <div class="flex items-center justify-between mt-6">
                    <a class="text-sm text-indigo-600 hover:text-indigo-700" href="{{ route('login') }}">
                        {{ __('Already registered?') }}
                    </a>
                    <x-primary-button class="px-6 py-2 rounded-md bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500">
                        {{ __('Register') }}
                    </x-primary-button>
                </div>
            </div>
        </form>
    </div>
</x-guest-layout>
