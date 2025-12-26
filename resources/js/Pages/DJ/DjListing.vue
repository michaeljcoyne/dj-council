<template>
    <div class="min-h-screen bg-gray-900">
        <!-- Top Navigation -->
        <header class="bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold">DJ</span>
                            </div>
                            <span class="ml-2 text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-purple-500">
                DJ Council
              </span>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="ml-3 relative">
                            <div>
                                <button class="flex text-sm rounded-full focus:outline-none">
                                    <span class="sr-only">Open user menu</span>
                                    <div class="w-8 h-8 rounded-full bg-purple-600 flex items-center justify-center text-white font-bold">
                                        C
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex">
            <!-- Sidebar -->
            <aside class="hidden md:block w-64 bg-gray-800 min-h-screen">
                <div class="px-4 py-6">
                    <nav class="space-y-1">
                        <a href="/client/dashboard" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                            Dashboard
                        </a>
                        <a href="/client/djs" class="flex items-center px-4 py-2 text-white bg-purple-700 rounded-lg">
                            Find DJs
                        </a>
                        <a href="/client/bookings" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                            My Bookings
                        </a>
                        <a href="/client/playlists" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                            My Playlists
                        </a>
                    </nav>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 p-6">
                <div class="max-w-7xl mx-auto">
                    <h1 class="text-3xl font-bold text-white mb-6">Find Your Perfect DJ</h1>

                    <!-- Filters -->
                    <div class="bg-gray-800 p-6 rounded-xl mb-8">
                        <h2 class="text-xl font-bold text-white mb-4">Filter DJs</h2>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Genre Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Genre</label>
                                <select class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 text-white">
                                    <option value="">All Genres</option>
                                    <option>House</option>
                                    <option>EDM</option>
                                    <option>Hip-Hop</option>
                                    <option>R&B</option>
                                    <option>Disco</option>
                                    <option>Techno</option>
                                </select>
                            </div>

                            <!-- Price Range -->
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Price Range</label>
                                <select class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 text-white">
                                    <option value="">Any Price</option>
                                    <option>$100 - $300</option>
                                    <option>$300 - $500</option>
                                    <option>$500 - $1000</option>
                                    <option>$1000+</option>
                                </select>
                            </div>

                            <!-- Location -->
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Location</label>
                                <select class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 text-white">
                                    <option value="">All Locations</option>
                                    <option>Los Angeles</option>
                                    <option>New York</option>
                                    <option>Miami</option>
                                    <option>Chicago</option>
                                    <option>Las Vegas</option>
                                </select>
                            </div>

                            <!-- Rating -->
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Rating</label>
                                <select class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 text-white">
                                    <option value="">Any Rating</option>
                                    <option>5 Stars</option>
                                    <option>4+ Stars</option>
                                    <option>3+ Stars</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4 flex justify-end">
                            <button class="px-4 py-2 bg-pink-600 hover:bg-pink-700 text-white rounded-lg">
                                Apply Filters
                            </button>
                        </div>
                    </div>

                    <!-- DJ Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- DJ Card 1 -->
                        <div class="bg-gray-800 rounded-xl overflow-hidden">
                            <div class="h-48 bg-gray-700 relative">
                                <img src="/placeholder.svg?height=200&width=400" alt="DJ Pulse" class="w-full h-full object-cover" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-70"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-4">
                                    <div class="flex items-center mb-2">
                                        <div class="flex">
                                            <svg v-for="i in 5" :key="i" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="i <= 5 ? 'text-yellow-400' : 'text-gray-600'" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-300">(48 reviews)</span>
                                    </div>
                                    <h3 class="text-2xl font-bold text-white mb-1">DJ Pulse</h3>
                                    <p class="text-pink-400">House & EDM Specialist</p>
                                </div>
                            </div>

                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <p class="text-gray-300">Los Angeles, CA</p>
                                        <p class="text-lg font-semibold text-white">$300-$500 per event</p>
                                    </div>
                                    <button class="p-2 rounded-full bg-gray-700 hover:bg-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-pink-400" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>

                                <p class="text-gray-300 mb-4 line-clamp-3">With over 10 years of experience, DJ Pulse brings energy and excitement to every event. Known for seamless transitions and reading the crowd perfectly.</p>

                                <h4 class="font-semibold mb-2 text-pink-400">Top Genres</h4>
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <span class="px-3 py-1 bg-gray-700 rounded-full text-sm">House</span>
                                    <span class="px-3 py-1 bg-gray-700 rounded-full text-sm">EDM</span>
                                    <span class="px-3 py-1 bg-gray-700 rounded-full text-sm">Progressive</span>
                                </div>

                                <div class="flex gap-2">
                                    <a href="/client/djs/1/book" class="flex-1 px-4 py-2 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 rounded-lg font-medium text-center text-white">
                                        Book Now
                                    </a>
                                    <a href="/client/djs/1" class="px-4 py-2 border border-pink-500 text-pink-500 rounded-lg hover:bg-pink-900/30 text-center">
                                        View Profile
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- DJ Card 2 -->
                        <div class="bg-gray-800 rounded-xl overflow-hidden">
                            <div class="h-48 bg-gray-700 relative">
                                <img src="/placeholder.svg?height=200&width=400" alt="DJ Vinyl Queen" class="w-full h-full object-cover" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-70"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-4">
                                    <div class="flex items-center mb-2">
                                        <div class="flex">
                                            <svg v-for="i in 5" :key="i" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="i <= 4 ? 'text-yellow-400' : 'text-gray-600'" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-300">(36 reviews)</span>
                                    </div>
                                    <h3 class="text-2xl font-bold text-white mb-1">DJ Vinyl Queen</h3>
                                    <p class="text-pink-400">Retro & Disco Master</p>
                                </div>
                            </div>

                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <p class="text-gray-300">New York, NY</p>
                                        <p class="text-lg font-semibold text-white">$400-$700 per event</p>
                                    </div>
                                    <button class="p-2 rounded-full bg-gray-700 hover:bg-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-pink-400" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>

                                <p class="text-gray-300 mb-4 line-clamp-3">A true vinyl enthusiast with a massive collection of disco and retro classics. DJ Vinyl Queen creates an authentic disco experience that gets everyone dancing.</p>

                                <h4 class="font-semibold mb-2 text-pink-400">Top Genres</h4>
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <span class="px-3 py-1 bg-gray-700 rounded-full text-sm">Disco</span>
                                    <span class="px-3 py-1 bg-gray-700 rounded-full text-sm">Funk</span>
                                    <span class="px-3 py-1 bg-gray-700 rounded-full text-sm">80s Classics</span>
                                </div>

                                <div class="flex gap-2">
                                    <a href="/client/djs/2/book" class="flex-1 px-4 py-2 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 rounded-lg font-medium text-center text-white">
                                        Book Now
                                    </a>
                                    <a href="/client/djs/2" class="px-4 py-2 border border-pink-500 text-pink-500 rounded-lg hover:bg-pink-900/30 text-center">
                                        View Profile
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- DJ Card 3 -->
                        <div class="bg-gray-800 rounded-xl overflow-hidden">
                            <div class="h-48 bg-gray-700 relative">
                                <img src="/placeholder.svg?height=200&width=400" alt="DJ Rhythm" class="w-full h-full object-cover" />
                                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-70"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-4">
                                    <div class="flex items-center mb-2">
                                        <div class="flex">
                                            <svg v-for="i in 5" :key="i" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" :class="i <= 4 ? 'text-yellow-400' : 'text-gray-600'" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        </div>
                                        <span class="ml-2 text-sm text-gray-300">(29 reviews)</span>
                                    </div>
                                    <h3 class="text-2xl font-bold text-white mb-1">DJ Rhythm</h3>
                                    <p class="text-pink-400">Hip-Hop & R&B Expert</p>
                                </div>
                            </div>

                            <div class="p-6">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <p class="text-gray-300">Miami, FL</p>
                                        <p class="text-lg font-semibold text-white">$350-$600 per event</p>
                                    </div>
                                    <button class="p-2 rounded-full bg-gray-700 hover:bg-gray-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-pink-400" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>

                                <p class="text-gray-300 mb-4 line-clamp-3">DJ Rhythm specializes in creating the perfect urban soundtrack for your event. From old school classics to the latest hits, he knows how to keep the floor packed.</p>

                                <h4 class="font-semibold mb-2 text-pink-400">Top Genres</h4>
                                <div class="flex flex-wrap gap-2 mb-4">
                                    <span class="px-3 py-1 bg-gray-700 rounded-full text-sm">Hip-Hop</span>
                                    <span class="px-3 py-1 bg-gray-700 rounded-full text-sm">R&B</span>
                                    <span class="px-3 py-1 bg-gray-700 rounded-full text-sm">Trap</span>
                                </div>

                                <div class="flex gap-2">
                                    <a href="/client/djs/3/book" class="flex-1 px-4 py-2 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 rounded-lg font-medium text-center text-white">
                                        Book Now
                                    </a>
                                    <a href="/client/djs/3" class="px-4 py-2 border border-pink-500 text-pink-500 rounded-lg hover:bg-pink-900/30 text-center">
                                        View Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8 flex justify-center">
                        <nav class="flex items-center space-x-2">
                            <a href="#" class="px-3 py-1 rounded-md bg-gray-800 text-gray-400">
                                Previous
                            </a>
                            <a href="#" class="px-3 py-1 rounded-md bg-purple-700 text-white">
                                1
                            </a>
                            <a href="#" class="px-3 py-1 rounded-md bg-gray-800 text-gray-400 hover:bg-gray-700">
                                2
                            </a>
                            <a href="#" class="px-3 py-1 rounded-md bg-gray-800 text-gray-400 hover:bg-gray-700">
                                3
                            </a>
                            <span class="px-2 text-gray-400">...</span>
                            <a href="#" class="px-3 py-1 rounded-md bg-gray-800 text-gray-400 hover:bg-gray-700">
                                8
                            </a>
                            <a href="#" class="px-3 py-1 rounded-md bg-gray-800 text-gray-400 hover:bg-gray-700">
                                Next
                            </a>
                        </nav>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script>
export default {
    // Add component logic here if needed
}
</script>
