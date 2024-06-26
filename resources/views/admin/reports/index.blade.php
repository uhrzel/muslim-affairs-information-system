    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>

    </style>
    <x-app-layout>
        <x-slot name="header">
            <div class="flex items-center">
                <h2 class="font-semibold text-xl text-white leading-tight w-full">
                    @if(auth()->user()->type === 'admin')
                    {{ __('Complains from Client') }}
                    @endif
                    @if(auth()->user()->type === 'user')
                    {{ __('My Complains') }}
                    @endif
                </h2>

                <div class="relative pr-4"> <!-- Adjust the padding as needed -->
                    <input type="text" id="searchInput" class="w-full border rounded-full px-4 py-2 pl-10 focus:outline-none focus:ring focus:border-blue-300" placeholder="Search...">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                </div>
                @if(auth()->user()->type === 'user')
                <div class="mr-4">
                    <a href="{{ route('admin.reportCreate') }}" class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white rounded-full px-4 py-2 leading-none text-sm">
                        <i class="fas fa-plus"></i>
                        <span class="ml-1">Create</span>
                    </a>
                </div>
                @endif
                <div class="mr-10 flex items-center justify-end">
                    @if(auth()->user()->type === 'admin')
                    <x-dropdown align="left" width="32">
                        <x-slot name="trigger">
                            <button type="button" class="text-white dark:text-white bg-transparent hover:bg-green-600 px-2 py-1 rounded-md mr-1 text-xs border border-green-500 transition duration-300">
                                Export <i class="fa fa-caret-down"></i>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <form method="POST" class="form-inline" id="exportForm">
                                <button type="submit" id="exportExcel" class="text-white  hover:bg-green-600 px-2 py-1 rounded-md mr-1 text-xs border border-green-500 transition duration-300 ">
                                    <i class="fa fa-file-excel-o"></i> Excel
                                </button>
                                <button type="submit" id="exportPdf" class="text-white hover:bg-red-600 px-2 py-1 rounded-md mr-1 text-xs border border-red-500 transition duration-300">
                                    <i class="fa fa-file-pdf-o"></i> PDF
                                </button>
                                <button type="submit" id="exportWord" class="text-white hover:bg-blue-600 px-2 py-1 rounded-md text-xs border border-blue-500 transition duration-300">
                                    <i class="fa fa-file-word-o"></i> Word
                                </button>
                            </form>
                        </x-slot>
                    </x-dropdown>
                    <button type="button" id="printButton" class="text-white hover:bg-green-600 px-2 py-1 rounded-md text-xs border border-green-500 transition duration-300" onclick="printReport()">
                        <i class="fa fa-print"></i> Print
                    </button>
                    @endif
                </div>
            </div>
        </x-slot>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            function printReport() {
                // Fetch the content of print.blade.php
                fetch("{{ route('reports.print') }}")
                    .then(response => response.text())
                    .then(data => {
                        // Create a hidden iframe
                        const iframe = document.createElement('iframe');
                        iframe.style.display = 'none';
                        document.body.appendChild(iframe);

                        // Write the fetched content to the iframe
                        iframe.contentDocument.write(data);
                        iframe.contentDocument.close();

                        // Print the content
                        iframe.contentWindow.print();

                        // Remove the iframe from the DOM
                        setTimeout(() => {
                            document.body.removeChild(iframe);
                        }, 1000); // Adjust the delay as needed
                    })
                    .catch(error => console.error('Error fetching print content:', error));
            }
        </script>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class=" overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs uppercase bg-indigo-700 text-white">
                                <tr>
                                    @if(auth()->user()->type === 'admin')
                                    <th scope="col" class="px-6 py-3">Report From</th>
                                    <th scope="col" class="px-6 py-3">User Email</th>
                                    <th scope="col" class="px-6 py-3">Report Title </th>
                                    @endif
                                    @if(auth()->user()->type === 'user')
                                    <th scope="col" class="px-6 py-3">Reported Title</th>
                                    @endif
                                    <th scope="col" class="px-6 py-3">Report Description</th>
                                    <th scope="col" class="px-6 py-3">Status</th>
                                    <th scope="col" class="px-6 py-3">Created At</th>
                                    <!-- Show Status and Action columns only for admin users -->
                                    @if(auth()->user()->type === 'admin')
                                    <th scope="col" class="px-6 py-3">Action</th>
                                    @endif
                                    <th scope="col" class="px-6 py-3">FeedBack</th>
                                </tr>
                            </thead>

                            <tbody id="searchResults">
                                @foreach($reports as $user)
                                <tr class="border-b border-gray-500 bg-gray-200 hover:bg-gray-300" style="font-family: 'Arial', sans-serif;">
                                    @if(auth()->user()->type === 'admin')
                                    <td class="px-6 py-4 user-name">{{ $user->user->name }}</td>
                                    <td class="px-6 py-4 user-email">{{ $user->user->email }}</td>
                                    @endif

                                    <td class="px-6 py-4 user-reportTitle">{{ $user->report_title}}</td>
                                    <td class="px-6 py-4 user-reportDescription">{{ $user->report_description }}</td>
                                    <td class="px-6 py-4 user-status">
                                        @if($user->status === 'pending')
                                        <span class="inline-flex items-center bg-green-600 text-white px-2 py-1 rounded-full">
                                            <i class="fas fa-clock mr-1"></i>
                                            Pending
                                        </span>
                                        @elseif($user->status === 'settled')
                                        <span class="inline-flex items-center bg-blue-600 text-white px-2 py-1 rounded-full">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Settled
                                        </span>
                                        @elseif($user->status === 'cancelled')
                                        <span class="inline-flex items-center bg-red-600 text-white px-2 py-1 rounded-full">
                                            <i class="fas fa-times-circle mr-1"></i>
                                            Cancelled
                                        </span>
                                        @else
                                        <span class="inline-flex items-center bg-gray-300 text-gray-700 px-2 py-1 rounded-full">
                                            <i class="fas fa-exclamation-circle mr-1"></i>
                                            {{ ucfirst($user->status) }}
                                        </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 user-created">
                                        {{ \Carbon\Carbon::parse($user->created_at)->format('F j, Y') }} <br>
                                        {{ \Carbon\Carbon::parse($user->created_at)->format('h:i A') }}
                                    </td>

                                    <!-- Show Status and Action columns only for admin users -->
                                    @if(auth()->user()->type === 'admin')
                                    <td class="px-6 py-4 flex items-center">
                                        @if($user->status === 'pending')
                                        <div class="flex space-x-2">
                                            <button class="feedback-btn border border-blue hover:bg-blue-500 border-blue-600 transition-colors text-black font-xl rounded-full px-4 py-2 leading-none dark:hover:text-white " data-report-id="{{ $user->id }}" data-status="settled">
                                                Settled
                                            </button>
                                            <button class="feedback-btn border border-red hover:bg-red-500 border-red-600 transition-colors text-black font-xl rounded-full px-4 py-2 leading-none dark:hover:text-white" data-report-id="{{ $user->id }}" data-status="cancelled">
                                                Cancel
                                            </button>

                                        </div>
                                        @else
                                        <span class="inline-flex items-center font-semibold text-gray-600 px-2 py-1 rounded-full">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Completed
                                        </span>
                                        @endif
                                    </td>
                                    @endif
                                    <td class="px-6 py-4 user-reportDescription">{{ $user->feedback_message }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(auth()->user()->type === 'admin')
                {{ $reports->links() }}
                @endif

            </div>
            <!-- Updated Modal Code -->
            <div id="feedbackModal" class="fixed z-10 inset-0 overflow-y-auto hidden">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <button class="absolute top-0 right-0 m-4 p-2 rounded-full bg-gray-200 hover:bg-gray-300 focus:outline-none" onclick="closeModal()">
                                    <svg class="h-4 w-4 text-gray-500 hover:text-gray-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>

                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <img src="img/mess.png" alt="Icon" class="h-10 w-12">

                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Submit Feedback
                                    </h3>
                                    <div class="mt-2">
                                        <form id="feedbackForm" action="{{ route('admin.submitFeedback') }}" method="POST">
                                            @csrf
                                            <input type="hidden" id="reportId" name="report_id">
                                            <input type="hidden" id="status" name="status">
                                            <textarea id="feedbackMessage" name="feedback_message" rows="4" cols="50" placeholder="Enter feedback message" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"></textarea>
                                            <div class="mt-3 flex justify-center">
                                                <button type="submit" class="inline-flex justify-center w-40 py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    Submit Feedback
                                                </button>
                                                <button type="button" class="inline-flex justify-center w-40 ml-4 py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-gray-700 bg-gray-200 hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500" onclick="closeModal()">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="confirmationModalExcel" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="document">
                        <!-- Modal content -->
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                    <img src="img/excel.png" alt="Icon" class="h-6 w-12">
                                </div>

                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Export Confirmation
                                    </h3>
                                    <div class="mt-2">
                                        <label for="dateRangeExcell" class="block text-sm font-medium text-gray-700">Select Date Range:</label>
                                        <input type="text" id="dateRangeExcell" name="dateRangeExcell" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 text-gray-700 rounded-md">
                                    </div>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Do you want to export this report as Excel?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button id="exportButton3" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Export
                            </button>
                            <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal3()">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="confirmationModalPdf" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="document">
                        <!-- Modal content -->
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                    <img src="img/pdf.png" alt="Icon" class="h-6 w-12">
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Export Confirmation
                                    </h3>
                                    <div class="mt-2">
                                        <label for="dateRangePdf" class="block text-sm font-medium text-gray-700">Select Date Range:</label>
                                        <input type="text" id="dateRangePdf" name="dateRangePdf" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 text-gray-700 rounded-md">
                                    </div>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Do you want to export this report as PDF?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button id="exportButton1" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-500 text-base font-medium text-white hover:bg-red-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Export
                            </button>
                            <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal1()">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="confirmationModalWord" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="document">
                        <!-- Modal content -->
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                                    <img src="img/word.png" alt="Icon" class="h-6 w-12">
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                        Export Confirmation
                                    </h3>
                                    <div class="mt-2">
                                        <label for="dateRangeWord" class="block text-sm font-medium text-gray-700">Select Date Range:</label>
                                        <input type="text" id="dateRangeWord" name="dateRangeWord" class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 text-gray-700 rounded-md">
                                    </div>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500">
                                            Do you want to export this report as Word?
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button id="exportButton2" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Export
                            </button>
                            <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal2()">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Bootstrap JS -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
            var userType = "{{ auth()->user()->type }}";
        </script>

        <script>
            // Real-time search functionality
            // Real-time search functionality
            document.getElementById('searchInput').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const tableRows = document.querySelectorAll('#searchResults tr');

                tableRows.forEach(row => {
                    const userReportTitle = row.querySelector('.user-reportTitle').textContent.toLowerCase();
                    const userDescription = row.querySelector('.user-reportDescription').textContent.toLowerCase();
                    const userStatus = row.querySelector('.user-status').textContent.toLowerCase();
                    const userCreated = row.querySelector('.user-created').textContent.toLowerCase();

                    // Check if the user type is admin
                    if (userType === 'admin') {
                        const userName = row.querySelector('.user-name').textContent.toLowerCase();
                        const userEmail = row.querySelector('.user-email').textContent.toLowerCase();

                        // Search in all fields available for admins
                        if (userName.includes(searchTerm) || userEmail.includes(searchTerm) || userReportTitle.includes(searchTerm) || userDescription.includes(searchTerm) || userStatus.includes(searchTerm) || userCreated.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    } else {
                        // Search in common fields available for both admin and user
                        if (userReportTitle.includes(searchTerm) || userDescription.includes(searchTerm) || userStatus.includes(searchTerm) || userCreated.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            });



            flatpickr("#dateRangeExcell", {
                mode: "range",
                dateFormat: "Y-m-d",
            });
            flatpickr("#dateRangePdf", {
                mode: "range",
                dateFormat: "Y-m-d",
            });
            flatpickr("#dateRangeWord", {
                mode: "range",
                dateFormat: "Y-m-d",
            });


            //excel

            // Handle click event on the "Export PDF" button to show the modal
            document.getElementById('exportExcel').addEventListener('click', function(e) {
                e.preventDefault();
                showModal3();
            });

            // Handle click event on the "Export" button in the modal to proceed with export
            document.getElementById('exportButton3').addEventListener('click', function() {
                // Retrieve the selected date range
                const dateRangeExcell = document.getElementById('dateRangeExcell').value;

                // Close the modal
                closeModal3();

                // Redirect to the export route with the selected date range as a query parameter
                window.location.href = "{{ route('export.excel') }}" + "?dateRangeExcell=" + encodeURIComponent(dateRangeExcell);
            });

            function showModal3() {
                document.getElementById('confirmationModalExcel').classList.remove('hidden');
                document.getElementsByTagName('html')[0].classList.add('overflow-y-hidden');
            }

            function closeModal3() {
                document.getElementById('confirmationModalExcel').classList.add('hidden');
                document.getElementsByTagName('html')[0].classList.remove('overflow-y-hidden');
            }


            //pdf

            function showModal1() {
                document.getElementById('confirmationModalPdf').classList.remove('hidden');
                document.getElementsByTagName('html')[0].classList.add('overflow-y-hidden');
            }

            // Function to close the modal
            function closeModal1() {
                document.getElementById('confirmationModalPdf').classList.add('hidden');
                document.getElementsByTagName('html')[0].classList.remove('overflow-y-hidden');
            }

            // Handle click event on the "Export PDF" button to show the modal
            document.getElementById('exportPdf').addEventListener('click', function(e) {
                e.preventDefault();
                showModal1();
            });

            // Handle click event on the "Export" button in the modal to proceed with export
            document.getElementById('exportButton1').addEventListener('click', function() {

                const dateRangePdf = document.getElementById('dateRangePdf').value;
                closeModal1();
                window.location.href = "{{ route('export.pdf') }}" + "?dateRangePdf=" + encodeURIComponent(dateRangePdf);

            });

            //word

            function showModal2() {
                document.getElementById('confirmationModalWord').classList.remove('hidden');
                document.getElementsByTagName('html')[0].classList.add('overflow-y-hidden');
            }

            // Function to close the modal
            function closeModal2() {
                document.getElementById('confirmationModalWord').classList.add('hidden');
                document.getElementsByTagName('html')[0].classList.remove('overflow-y-hidden');
            }

            // Handle click event on the "Export PDF" button to show the modal
            document.getElementById('exportWord').addEventListener('click', function(e) {
                e.preventDefault();
                showModal2();
            });

            // Handle click event on the "Export" button in the modal to proceed with export
            document.getElementById('exportButton2').addEventListener('click', function() {
                const dateRangeWord = document.getElementById('dateRangeWord').value;

                window.location.href = "{{ route('export.word') }}" + "?dateRangeWord=" + encodeURIComponent(dateRangeWord);
                closeModal2();
            });




            // JavaScript function to open the modal with the correct report id and status
            function openModal(reportId, status) {
                var modal = document.getElementById("feedbackModal");
                var reportIdInput = document.getElementById("reportId");
                var statusInput = document.getElementById("status");
                reportIdInput.value = reportId;
                statusInput.value = status;
                modal.style.display = "block";
            }

            // Get the buttons that open the modal
            var feedbackButtons = document.querySelectorAll(".feedback-btn");

            // Add event listeners to each button to open the modal with the correct report id and status
            feedbackButtons.forEach(function(button) {
                button.addEventListener("click", function() {
                    var reportId = button.getAttribute("data-report-id");
                    var status = button.getAttribute("data-status");
                    openModal(reportId, status);
                });
            });

            function closeModal() {
                var modal = document.getElementById("feedbackModal");
                modal.style.display = "none";
            }
        </script>
    </x-app-layout>