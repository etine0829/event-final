@if (Auth::user()->hasRole('admin'))
    <div>
        @if (session('success'))
            <x-sweetalert type="success" :message="session('success')" />
        @endif

        @if (session('info'))
            <x-sweetalert type="info" :message="session('info')" />
        @endif

        @if (session('error'))
            <x-sweetalert type="error" :message="session('error')" />
        @endif
        <div class="flex justify-between mb-4 sm:-mt-4">
            <div class="font-bold text-md tracking-wide text-black  mt-2 uppercase">Admin / Add User</div>
            <div x-data="{ open: false }">
                <button @click="open = true" class="bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700">
                    <i class="fa-solid fa-plus fa-xs" style="color: #ffffff;"></i> Add User
                </button>
                <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50">
                    <div @click.away="open = true" class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto">
                        <div class="flex justify-between items-center pb-3">
                            <p class="text-xl font-bold">Add User</p>
                            <button @click="open = false" class=" text-black text-sm px-3 py-2 rounded hover:text-red-500">X</button>
                        </div>
                        <div class="mb-4">
                            <form action="{{ route('admin.user.store') }}" method="POST" class="">
                                @csrf
                                
                                <div class="mb-4">
                                    <label for="name" class="block text-gray-700 text-md font-bold mb-2">Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="shadow appearance-none rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') is-invalid @enderror" required>
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <label for="email" class="block text-gray-700 text-md font-bold mb-2">Email</label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="shadow appearance-none rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') is-invalid @enderror" required>
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="block text-gray-700 text-md font-bold mb-2">Password</label>
                                    <input type="password" name="password" id="password" class="shadow appearance-none rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password') is-invalid @enderror" required>
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <div class="mb-4">
                                    <label for="password_confirmation" class="block text-gray-700 text-md font-bold mb-2">Confirm Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="shadow appearance-none rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div> 

                                <div class="mb-2">
                                        <label for="role" class="block text-gray-700 text-md font-bold mb-2">User Type: </label>
                                        <select id="role" name="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('role') is-invalid @enderror" required>
                                                <option value="">Select Option</option>
                                                <option value="event_manager">Event Manager</option>
                                                <option value="staff">Staff</option>
                                                <option value="judge">Judge</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                                </div>



                                <div class="flex mb-4 mt-5 justify-center">
                                    <button type="submit" class="w-80 bg-blue-500 text-white px-4 py-2 rounded-md">
                                        Save
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr class="border-gray-200 my-4">
        <div class="flex items-center mb-4 justify-end">
            <div class="flex w-16 sm:w-auto mt-2 sm:mt-0 sm:ml-2">
                <input wire:model.live="search" type="text" class="border text-black border-gray-300 rounded-md p-2 w-full" placeholder="Search..." autofocus>
            </div>
        </div>

        @if($search && $users->isEmpty())
            <p class="text-black mt-8 text-center">No events found for matching "{{ $search }}"</p>
        @elseif(!$search && $users->isEmpty())
            <p class="text-black mt-8 text-center">No data available in table</p>
        @else
            <table class="table-auto border-collapse border border-gray-400 w-full text-sm text-center mb-4">
                <thead class="bg-gray-200 text-black">
                    <tr>
                                <th class="border border-gray-400 px-3 py-2">
                                    <button wire:click="sortBy('name')" class="w-full h-full flex items-center justify-center">
                                        Name
                                        @if ($sortField == 'name')
                                            @if ($sortDirection == 'asc')
                                                &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                            @else
                                                &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                            @endif
                                        @endif
                                    </button>
                                </th>

                                <th class="border border-gray-400 px-3 py-2">
                                    <button wire:click="sortBy('email')" class="w-full h-full flex items-center justify-center">
                                        Email
                                        @if ($sortField == 'email')
                                            @if ($sortDirection == 'asc')
                                                &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                            @else
                                                &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                            @endif
                                        @endif
                                    </button>
                                </th>

                                <th class="border border-gray-400 px-3 py-2">
                                    <button wire:click="sortBy('role')" class="w-full h-full flex items-center justify-center">
                                        Role
                                        @if ($sortField == 'role')
                                            @if ($sortDirection == 'asc')
                                                &nbsp;<i class="fa-solid fa-down-long fa-xs"></i>
                                            @else
                                                &nbsp;<i class="fa-solid fa-up-long fa-xs"></i>
                                            @endif
                                        @endif
                                    </button>
                                </th>

                                <th class="border border-gray-400 px-3 py-2">Action</th>     
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td class="text-black border border-gray-400">{{ $user->name}}</td>
                            <td class="text-black border border-gray-400">{{ $user->email}}</td>
                            <td class="text-black border border-gray-400 px-3 py-3">
                                @if($user->role == 'admin')
                                    Admin
                                @elseif($user->role == 'event_manager')
                                    Event Manager
                                @elseif($user->role == 'staff')
                                    Staff
                                @elseif($user->role == 'judge')
                                    Judge
                                @else
                                    No Role
                                @endif
                            </td>
                            <td class="text-center text-black border border-gray-400 px-2 py-.5 ">
                            <div class="flex justify-center items-center space-x-2">
                                <!-- Edit Button -->
                                <div x-data="{ open: false, 
                                        Id: '{{ $user->id }}', 
                                        name: {{ json_encode($user->name) }},
                                        email: {{ json_encode($user->email) }},
                                        password: {{ json_encode($user->password) }},  
                                        role: {{ json_encode($user->role) }},
                                        }">
                                    <a @click="open = true" class="cursor-pointer bg-blue-500 text-white text-sm px-3 py-2 rounded hover:bg-blue-700 ">
                                        <i class="fa-solid fa-pen fa-xs" style="color: #ffffff;"></i>
                                    </a>
                                    <!-- Edit Modal -->
                                    <div x-cloak x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 space-x-3">
                                        <div @click.away="open = false" class="bg-white p-6 rounded-lg shadow-lg max-w-md mx-auto">
                                            <div class="flex justify-between items-start pb-3">
                                                <p class="text-xl font-bold">Edit User</p>
                                                <a @click="open = false" class="cursor-pointer text-black text-sm px-3 py-2 rounded hover:text-red-500">X</a>
                                            </div>
                                            <div class="mb-4">
                                                <form id="updateUserForm" action="{{ route('admin.user.update', $user->id )}}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="mb-2">
                                                        <div class="mb-4">
                                                            <label for="name" class="block text-gray-700 text-md font-bold mb-2 text-left">Name</label>
                                                            <input type="text" name="name" id="name" x-model="name" value="{{ $user->name }}"  class="shadow appearance-none rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('name') is-invalid @enderror" required autofocus>
                                                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                                        </div>
                                                        <div class="mb-4">
                                                            <label for="email" class="block text-gray-700 text-md font-bold mb-2 text-left">Email</label>
                                                            <input type="text" name="email" id="email" x-model="email" value="{{ $user->email }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('email') is-invalid @enderror" required>
                                                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                                        </div>
                                                        <div class="mb-2">
                                                            <label for="role" class="block text-gray-700 text-md font-bold mb-2 text-left">Roles: </label>
                                                            <select id="role" name="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-black leading-tight focus:outline-none focus:shadow-outline @error('role') is-invalid @enderror" required>
                                                                <option value="{{ $user->role }}">
                                                                    @if ($user->role == 'event_manager')
                                                                        Event Manager
                                                                    @elseif ($user->role == 'staff')
                                                                        Staff
                                                                    @else
                                                                        Judge
                                                                    @endif
                                                                </option>
                                                                <option value="event_manager">Event Manager</option>
                                                                <option value="staff">Staff</option>
                                                                <option value="judge">Judge</option>
                                                            </select>
                                                            <x-input-error :messages="$errors->get('role')" class="mt-2" />
                                                        </div>
                                                        <div class="flex mb-4 mt-5 justify-center">
                                                            <button type="submit" class="w-80 bg-blue-500 text-white px-4 py-2 rounded-md">
                                                                Save
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Button -->
                                <form id="deleteSelected" action="{{ route('admin.user.destroy', $user->id ) }}" method="POST" onsubmit="return ConfirmDeleteSelected(event, '{{ $user->id }}', '{{ $user->name }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="bg-red-500 text-white text-sm px-3 py-1.5 rounded hover:bg-red-700">
                                        <i class="fa-solid fa-trash fa-xs" style="color: #ffffff;"></i>
                                    </button>
                                </form>
                            </div>


                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $users->links() }}
        @endif
    </div>

    <script>

        function confirmDeleteAll(event) {
            event.preventDefault(); // Prevent form submission initially

            Swal.fire({
                title: 'Are you sure to delete all records?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete all!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // If confirmed, submit the form programmatically
                    document.getElementById('deleteAll').submit();
                }
            });
        }

        function ConfirmDeleteSelected(user, userID, Name) {
        event.preventDefault(); // Prevent form submission initially

        Swal.fire({
            title: `Are you sure you want to delete the event ${Name}?`,
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('deleteSelected');
                // Replace the placeholder with the actual event ID
                form.action = `{{ route('admin.user.destroy', ':Id') }}`.replace(':Id', userID);
                form.submit();
            }
        });

        return false; 
    }

    </script>

@endif