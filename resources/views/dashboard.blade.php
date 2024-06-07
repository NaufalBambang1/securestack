<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Tabs -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div x-data="{ activeTab: 'users' }">
                        <!-- Tab Buttons -->
                        <div class="flex justify-center mb-6">
                            <button 
                                :class="{'bg-blue-500 text-white': activeTab === 'users', 'bg-gray-200': activeTab !== 'users'}"
                                class="px-4 py-2 rounded-l"
                                @click="activeTab = 'users'"
                            >
                                Users
                            </button>
                            <button 
                                :class="{'bg-blue-500 text-white': activeTab === 'rfid', 'bg-gray-200': activeTab !== 'rfid'}"
                                class="px-4 py-2"
                                @click="activeTab = 'rfid'"
                            >
                                Register RFID Tag
                            </button>
                            <button 
                                :class="{'bg-blue-500 text-white': activeTab === 'fingerprint', 'bg-gray-200': activeTab !== 'fingerprint'}"
                                class="px-4 py-2 rounded-r"
                                @click="activeTab = 'fingerprint'"
                            >
                                Register Fingerprint Tag
                            </button>
                        </div>

                        <!-- Tab Contents -->
                        <div x-show="activeTab === 'users'">
                            <a href="{{ route('register.user.form') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
                                Register New User
                            </a>
                        </div>
                        <div x-show="activeTab === 'rfid'">
                            <a href="{{ route('register.rfid.form') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
                                Register RFID Tag
                            </a>
                        </div>
                        <div x-show="activeTab === 'fingerprint'">
                            <a href="{{ route('register.fingerprint.form') }}" class="bg-blue-500 text-white px-4 py-2 rounded">
                                Register Fingerprint Tag
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 text-gray-900 dark:text-gray-100" style="text-align: -webkit-center;">
                    <table> 
                        <tr class="p-6 bg-gray-500">
                            <th class="w-20">Log ID</th>
                            <th class="w-30">Username </th>
                            <th>Locker Number </th>
                            <th>Status</th>
                            <th class="w-10">Access Time</th>
                            <th>Access Method</th>
                            <th>Access Result</th>
                            <th>Option</th>
                        </tr>
                        @foreach($data as $dataX)
                        <tr>
                            <td>{{$dataX->LogID}}</td>
                            <td>{{$dataX->username}}</td>
                            <td>{{$dataX->lockerNumber}}</td>
                            <td>{{$dataX->StatusLocker}}</td>
                            <td>{{$dataX->AccessTime}}</td>
                            <td>{{$dataX->AccessMethod}}</td>
                            <td>{{$dataX->AccessResult}}</td>
                            <td class="text-center">
                                <a href="/userprofile" class="p-3">       
                                    <i class="fa-solid fa-ellipsis-vertical cursor-pointer"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
