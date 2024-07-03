<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            
            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 text-gray-900 dark:text-gray-100" style="text-align: -webkit-center;">
                    <table> 
                        <tr class="p-6 bg-gray-500">
                            <th class="w-20">Log ID</th>
                            <th class="w-10">Username </th>
                            <th>Locker Number </th>
                            <th>Status</th>
                            <th>Access Method</th>
                            <th>Result Method</th>
                            <th class="w-10">Access Time</th>
                            <th>Access Method</th>
                            <th>Access Result</th>
                            <th class="w-10">Access Time</th>
                            <th>Option</th>
                        </tr>
                        @foreach($data as $dataX)
                        <tr>
                            <td>{{$dataX->LogID}}</td>
                            <td>{{$dataX->username}}</td>
                            <td>{{$dataX->lockerNumber}}</td>
                            <td>{{$dataX->StatusLocker}}</td>
                            <td>{{$dataX->AccessMethodFingerprint}}</td>
                            <td>{{$dataX->AccessResultFingerprint}}</td>
                            <td>{{$dataX->AccessTimeFingerprint}}</td>
                            <td>{{$dataX->AccessMethod}}</td>
                            <td>{{$dataX->AccessResult}}</td>
                            <td>{{$dataX->AccessTime}}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <div class="dropdown">
                                        <button class="btn btn-primary dropdown-toggle mr-1 mb-1" type="button" data-toggle="dropdown">
                                            <i class="fa-solid fa-ellipsis-vertical cursor-pointer"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="/viewdetail/{{ $dataX->LogID }}">Lihat Detail</a>  
                                            <a class="dropdown-item text--red" href="#" onclick="resetLocker({{ $dataX->LockerID}})">Reset</a>                                        </div>
                                    </div>
                                </div>
                                <!-- <a href="/userprofile" class="p-3">       
                                    <i class="fa-solid fa-ellipsis-vertical cursor-pointer"></i>
                                </a> -->
                            </td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
function resetLocker(lockerID) {

    if (!confirm("Apakah Anda yakin ingin mereset locker ini?")) {
        return;
    }

    var url = '/resetButton?LockerID=' + lockerID;

    axios.get(url)
        .then(function(response) {
            if (response.status === 200) {
                alert('Locker berhasil direset.');
                location.reload(); // Refresh halaman setelah reset berhasil
            } else {
                console.error(response.data);
                alert('Gagal mereset locker.');
            }
        })
        .catch(function(error) {
            if (error.response) {
                console.error(error.response.data);
                alert('Gagal mereset locker: ' + error.response.data.error);
            } else {
                console.error(error.message);
                alert('Gagal mereset locker.');
            }
        });
}
</script>

    </script>
</x-app-layout>
