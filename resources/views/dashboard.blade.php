<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Table -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 text-gray-900 dark:text-gray-100" style="text-align: -webkit-center;">
                    <table id="data-table">
                        <thead>
                            <tr class="p-6 bg-gray-500">
                                <th class="w-10">Username</th>
                                <th>Locker Number</th>
                                <th>Status</th>
                                <th>Access Method </th>
                                <th>Result Method </th>
                                <th class="w-10">Access Time</th>
                                <th>Access Method</th>
                                <th>Access Result</th>
                                <th class="w-10">Access Time</th>
                                <th>Option</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Fungsi untuk mereset locker
    function resetLocker(UserLockerID) {
        if (!confirm("Apakah Anda yakin ingin mereset locker ini?")) {
            return;
        }

        var url = '/resetButton?LockerID=' + UserLockerID;
        console.log('lockerid',UserLockerID);
        axios.get(url)
            .then(function(response) {
                if (response.status === 200) {
                    alert('Locker berhasil direset.');
                    fetchData();
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

    // Fungsi untuk mengambil data dari server
    function fetchData() {
        axios.get('http://127.0.0.1:8000/dashboard/data')
            .then(function(response) {
                var data = response.data;
                var tableBody = $('#data-table tbody');
                tableBody.empty();

                data.forEach(function(item) {
                    var row = `<tr>
                        <td>${item.Username}</td>
                        <td>${item.lockerNumber}</td>
                        <td>${item.StatusLocker}</td>
                        <td>${item.AccessMethodFingerprint || ''}</td>
                        <td>${item.AccessResultFingerprint || ''}</td>
                        <td>${item.AccessTimeFingerprint || ''}</td>
                        <td>${item.AccessMethod || ''}</td>
                        <td>${item.AccessResult || ''}</td>
                        <td>${item.AccessTime || ''}</td>
                        <td class="text-center">
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle mr-1 mb-1" type="button" data-toggle="dropdown">
                                        <i class="fa-solid fa-ellipsis-vertical cursor-pointer"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="/viewdetail/${item.UserLockerID}">Lihat Detail</a>
                                        <a class="dropdown-item text--red" href="#" onclick="resetLocker(${item.UserLockerID})">Reset</a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>`;
                    tableBody.append(row);
                });
            })
            .catch(function(error) {
                console.error('Error fetching data:', error);
            });
    }

    // Panggil fetchData untuk memuat data pertama kali halaman dimuat
    fetchData();

    // Atur interval untuk memperbarui data setiap 10 detik
    setInterval(fetchData, 10000); // 10 detik
</script>
