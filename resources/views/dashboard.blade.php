<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Button Register new user locker -->
            <div class="mb-6 flex justify-end">
                <x-register-locker-button>{{ __('Register New User') }}</x-register-locker-button>
            </div>
    
            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
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
