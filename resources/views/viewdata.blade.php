<x-app-layout>
<div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
            
                <div class="p-6 text-gray-900 dark:text-gray-100" style="text-align: -webkit-center;"> 
                <span class="fs-20px">View Detail Akses Log</span>  
                    <table> 
                        <tr class="p-6 bg-gray-500">
                            <th>LogID</th>
                            <th class="w-10">Username </th>
                            <th>Locker Number </th>
                            <th>Status</th>
                            <th>Access Method 1</th>
                            <th>Result Method 1</th>
                            <th class="w-10">Access Time Method 1</th>
                            <th>Failed Attemps Method 1</th>
                            <th>Access Method 2</th>
                            <th>Access Result 2</th>
                            <th class="w-10">Access Time Method 2</th>
                            <th>Failed Attemps Method 2</th>
                        </tr>
                        @foreach($dataView as $dataX)
                        <tr>
                            <td>{{$dataX->LogID}}</td>
                            <td>{{$dataX->username}}</td>
                            <td>{{$dataX->lockerNumber}}</td>
                            <td>{{$dataX->StatusLocker}}</td>
                            <td>{{$dataX->AccessMethodFingerprint}}</td>
                            <td>{{$dataX->AccessResultFingerprint}}</td>
                            <td>{{$dataX->AccessTimeFingerprint}}</td>
                            <td>{{$dataX->failed_attempts_fingerprint}}</td>
                            <td>{{$dataX->AccessMethod}}</td>
                            <td>{{$dataX->AccessResult}}</td>
                            <td>{{$dataX->AccessTime}}</td>
                            <td>{{$dataX->failed_attempts_rfid}}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>