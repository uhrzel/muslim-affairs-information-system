<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center ">
            <h2 class="font-semibold text-xl text-white leading-tight w-full">
                {{ __('Advertisement List') }}
            </h2>
            <div class="relative pr-4"> <!-- Adjust the padding as needed -->
                <input type="text" id="searchInput" class="w-full border rounded-full px-4 py-2 pl-10 focus:outline-none focus:ring focus:border-blue-300" placeholder="Search...">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
            </div>
            <div class="mr-4">
                <a href="{{ route('admin.advertisementCreate') }}" class="inline-flex items-center bg-blue-500 hover:bg-blue-600 text-white rounded-full px-4 py-2 leading-none text-sm ">
                    <i class="fas fa-plus"></i>
                    <span class="ml-1">Create</span>
                </a>
            </div>

        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="overflow-hidden shadow-sm sm:rounded-lg">
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <table class="w-full text-sm text-left text-black">
                        <thead class="text-xs uppercase bg-blue-700 text-white ">
                            <tr>
                                <th scope="col" class="px-6 py-3">Advertisement Title</th>
                                <th scope="col" class="px-6 py-3">Advertisement Description</th>
                                <th scope="col" class="px-6 py-3">Advertisement Image</th>
                                <th scope="col" class="px-6 py-3">Advertisement Video</th>

                                <th scope="col" class="px-6 py-3">

                                    Action

                                </th>
                            </tr>
                        </thead>
                        <tbody id="searchResults">
                            @foreach($advertisements as $Ads)
                            <tr class="border-b border-gray-500 bg-gray-200 hover:bg-gray-300" style="font-family: 'Arial', sans-serif;">
                                <td scope="row" class="px-6 py-4 font-medium whitespace-nowrap text-black ads-title">{{ $Ads->ads_title }}</td>
                                <td class="px-6 py-4 ads-description">{{ $Ads->ads_description}}</td>
                                <td class="px-6 py-4">
                                    <img src="{{ asset('storage/ads_images/' . basename($Ads->ads_images)) }}" class="max-w-full h-20 w-20">
                                </td>

                                <td class="px-6 py-4">
                                    <video width="200" height="200" controls>
                                        <source src="{{ asset('storage/ads_videos/' . basename($Ads->ads_video)) }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.advertisementShow', $Ads->id) }}" class="w-24 inline-block bg-blue-600 text-white rounded-full px-4 py-2 leading-none hover:bg-blue-800 ">
                                        <i class="fas fa-eye mr-1"></i>
                                        View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>
            </div>
            {{ $advertisements->links() }}
        </div>
    </div>
    <script>
        // Real-time search functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('#searchResults tr');

            tableRows.forEach(row => {
                const adstitle = row.querySelector('.ads-title').textContent.toLowerCase();
                const adsDescription = row.querySelector('.ads-description').textContent.toLowerCase();

                if (adstitle.includes(searchTerm) || adsDescription.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>
</x-app-layout>