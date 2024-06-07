<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            
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
                            <th>Access Method 1</th>
                            <th>Result Method 1</th>
                            <th>Access Method 2</th>
                            <th>Access Result 2</th>
                            <th>Option</th>
                        </tr>
                        @foreach($data as $dataX)
                        <tr>
                            <td>{{$dataX->LogID}}</td>
                            <td>{{$dataX->username}}</td>
                            <td>{{$dataX->lockerNumber}}</td>
                            <td>{{$dataX->StatusLocker}}</td>
                            <td>{{$dataX->AccessTime}}</td>
                            <td>{{$dataX->AccessMethodFingerprint}}</td>
                            <td>{{$dataX->AccessResultFingerprint}}</td>
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
