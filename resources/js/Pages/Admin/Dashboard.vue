<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import { Menu, X, Users, Music, DollarSign, Star, CalendarDays, BookOpen } from 'lucide-vue-next';

// Sidebar state for mobile
const isMobileSidebarOpen = ref(false);
const windowWidth = ref(1024); // Default value for server-side rendering

// Check if we're in the browser environment
onMounted(() => {
    windowWidth.value = window.innerWidth;
    window.addEventListener('resize', () => {
        windowWidth.value = window.innerWidth;
    });
});

// Compute whether the sidebar should be shown
const isMobile = computed(() => {
    return windowWidth.value < 1024;
});

// Platform statistics
const stats = {
    total_users: 423,
    total_djs: 158,
    total_venues: 65,
    total_bookings: 876,
    completed_bookings: 698,
    pending_bookings: 42,
    pending_reviews: 15,
    total_revenue: 86400,
    total_earnings_month: 12650
};

// Recent bookings
const recentBookings = [
    {
        id: 1,
        client: 'John Smith',
        client_id: 342,
        dj_name: 'DJ Pulse',
        dj_id: 23,
        date: 'Jun.15, 2025',
        venue: 'Skyline Rooftop',
        venue_id: 12,
        type: 'Corporate Event',
        status: 'confirmed',
        price: 500
    },
    {
        id: 2,
        client: 'Sarah Johnson',
        client_id: 201,
        dj_name: 'DJ Vinyl Queen',
        dj_id: 45,
        date: 'Jun.22, 2025',
        venue: 'The Grand Hall',
        venue_id: 8,
        type: 'Wedding Reception',
        status: 'confirmed',
        price: 600
    },
    {
        id: 3,
        client: 'Tech Innovations Inc.',
        client_id: 187,
        dj_name: 'DJ Rhythm',
        dj_id: 37,
        date: 'Jul.05, 2025',
        venue: 'Convention Center',
        venue_id: 22,
        type: 'Product Launch',
        status: 'pending',
        price: 700
    },
    {
        id: 4,
        client: 'Emma Davis',
        client_id: 156,
        dj_name: 'DJ Electro',
        dj_id: 19,
        date: 'Jul.10, 2025',
        venue: 'Beachside Resort',
        venue_id: 31,
        type: 'Birthday Party',
        status: 'pending',
        price: 450
    },
    {
        id: 5,
        client: 'Robert Wilson',
        client_id: 278,
        dj_name: 'DJ Pulse',
        dj_id: 23,
        date: 'Jul.18, 2025',
        venue: 'Downtown Club',
        venue_id: 5,
        type: 'Club Night',
        status: 'confirmed',
        price: 550
    }
];

// Recent users
const newUsers = [
    {
        id: 1001,
        name: 'Michael Brown',
        email: 'michael@example.com',
        user_type: 'client',
        date_joined: 'May 28, 2025'
    },
    {
        id: 1002,
        name: 'DJ StarLight',
        email: 'starlight@example.com',
        user_type: 'dj',
        date_joined: 'May 27, 2025'
    },
    {
        id: 1003,
        name: 'Luxury Events',
        email: 'info@luxuryevents.com',
        user_type: 'venue',
        date_joined: 'May 26, 2025'
    },
    {
        id: 1004,
        name: 'Jessica Miller',
        email: 'jessica@example.com',
        user_type: 'client',
        date_joined: 'May 25, 2025'
    },
    {
        id: 1005,
        name: 'DJ BeatMaster',
        email: 'beatmaster@example.com',
        user_type: 'dj',
        date_joined: 'May 24, 2025'
    }
];

// Pending reviews
const pendingReviews = [
    {
        id: 201,
        client: 'Mark Thompson',
        client_id: 245,
        dj_name: 'DJ Pulse',
        dj_id: 23,
        rating: 5,
        date: 'May 27, 2025',
        content: 'Amazing performance! DJ Pulse kept everyone dancing all night long and was very professional.'
    },
    {
        id: 202,
        client: 'Angela White',
        client_id: 187,
        dj_name: 'DJ Rhythm',
        dj_id: 37,
        rating: 4,
        date: 'May 26, 2025',
        content: 'Great music selection, very responsive to requests. Would book again for future events.'
    },
    {
        id: 203,
        client: 'Carlos Rodriguez',
        client_id: 301,
        dj_name: 'DJ Vinyl Queen',
        dj_id: 45,
        rating: 3,
        date: 'May 25, 2025',
        content: 'Good overall performance but arrived a bit late. Music selection was on point.'
    }
];

// Monthly booking data
const monthlyBookings = [
    { month: 'Jan', count: 62 },
    { month: 'Feb', count: 58 },
    { month: 'Mar', count: 73 },
    { month: 'Apr', count: 81 },
    { month: 'May', count: 76 },
];
</script>

<template>
    <Head title="Admin Dashboard" />

    <div class="min-h-screen bg-gray-900">
        <!-- Mobile Sidebar Toggle -->
        <div class="fixed top-0 left-0 z-50 p-4 lg:hidden">
            <button @click="isMobileSidebarOpen = !isMobileSidebarOpen" class="p-2 text-white bg-purple-800 rounded-lg">
                <Menu v-if="!isMobileSidebarOpen" class="w-6 h-6" />
                <X v-else class="w-6 h-6" />
            </button>
        </div>

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-40 w-64 transition-transform duration-300 transform bg-gray-800 lg:transform-none"
            :class="{ '-translate-x-full': !isMobileSidebarOpen && isMobile }"
        >
            <div class="flex items-center justify-center h-16 px-4 bg-gray-900">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold">DJ</span>
                    </div>
                    <span class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-purple-500">
            DJ Council
          </span>
                </div>
            </div>

            <nav class="px-4 py-6 space-y-1">
                <Link
                    href="/admin/dashboard"
                    class="flex items-center px-4 py-2 text-white bg-purple-700 rounded-lg"
                >
                    <span class="ml-3">Dashboard</span>
                </Link>
                <Link
                    href="/admin/users"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg"
                >
                    <span class="ml-3">Users</span>
                </Link>
                <Link
                    href="/admin/djs"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg"
                >
                    <span class="ml-3">DJs</span>
                </Link>
                <Link
                    href="/admin/venues"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg"
                >
                    <span class="ml-3">Venues</span>
                </Link>
                <Link
                    href="/admin/bookings"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg"
                >
                    <span class="ml-3">Bookings</span>
                </Link>
                <Link
                    href="/admin/reviews"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg"
                >
                    <span class="ml-3">Reviews</span>
                </Link>
                <Link
                    href="/admin/genres"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg"
                >
                    <span class="ml-3">Genres</span>
                </Link>
                <Link
                    href="/admin/analytics"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg"
                >
                    <span class="ml-3">Analytics</span>
                </Link>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="lg:pl-64">
            <!-- Top Navigation -->
            <div class="bg-gray-800 shadow">
                <div class="flex items-center justify-end px-4 py-3">
                    <div class="flex items-center space-x-3">
                        <button class="p-1 text-gray-400 rounded-full hover:bg-gray-700">
                            <span class="sr-only">Notifications</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </button>
                        <div class="relative ml-3">
                            <div>
                                <button class="flex text-sm bg-gray-800 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
                                    <span class="sr-only">Open user menu</span>
                                    <div class="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold">
                                        A
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-white">Admin Dashboard</h1>
                <p class="mt-2 text-gray-400">Welcome to the DJ Council Admin Dashboard</p>

                <!-- Stats Overview -->
                <div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="p-5 bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-blue-600 rounded-lg">
                                <Users class="w-6 h-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-white">Total Users</h2>
                                <p class="text-2xl font-bold text-white">{{ stats.total_users }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-pink-600 rounded-lg">
                                <Music class="w-6 h-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-white">DJs</h2>
                                <p class="text-2xl font-bold text-white">{{ stats.total_djs }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-600 rounded-lg">
                                <DollarSign class="w-6 h-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-white">Revenue (Month)</h2>
                                <p class="text-2xl font-bold text-white">${{ stats.total_earnings_month }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-600 rounded-lg">
                                <CalendarDays class="w-6 h-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-white">Bookings</h2>
                                <p class="text-2xl font-bold text-white">{{ stats.total_bookings }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity and Action Items -->
                <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Recent Bookings -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-white">Recent Bookings</h2>
                            <Link href="/admin/bookings" class="text-sm text-purple-400 hover:text-purple-300">View All</Link>
                        </div>

                        <div class="bg-gray-800 rounded-lg shadow overflow-hidden">
                            <ul class="divide-y divide-gray-700">
                                <li v-for="booking in recentBookings" :key="booking.id" class="p-4">
                                    <div class="flex justify-between">
                                        <div>
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 rounded-full mr-2" :class="booking.status === 'confirmed' ? 'bg-green-500' : 'bg-yellow-500'"></div>
                                                <p class="font-medium text-white">{{ booking.type }}</p>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-400">{{ booking.date }}</p>
                                        </div>

                                        <div class="text-right">
                                            <p class="text-sm text-white">
                                                <Link :href="`/admin/djs/${booking.dj_id}`" class="text-purple-400 hover:text-purple-300">{{ booking.dj_name }}</Link>
                                                •
                                                <Link :href="`/admin/users/${booking.client_id}`" class="text-purple-400 hover:text-purple-300">{{ booking.client }}</Link>
                                            </p>
                                            <p class="text-sm text-gray-400">${{ booking.price }}</p>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Action Items -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-white">Action Items</h2>
                        </div>

                        <div class="bg-gray-800 rounded-lg shadow p-4">
                            <div class="flex items-center mb-3 pb-3 border-b border-gray-700">
                                <div class="p-2 bg-yellow-600 rounded-lg mr-3">
                                    <BookOpen class="w-5 h-5 text-white" />
                                </div>
                                <div>
                                    <p class="font-medium text-white">{{ stats.pending_bookings }} Pending Bookings</p>
                                    <p class="text-sm text-gray-400">Require review and confirmation</p>
                                </div>
                                <Link href="/admin/bookings?status=pending" class="ml-auto text-sm text-purple-400 hover:text-purple-300">
                                    Review
                                </Link>
                            </div>

                            <div class="flex items-center mb-3 pb-3 border-b border-gray-700">
                                <div class="p-2 bg-purple-600 rounded-lg mr-3">
                                    <Star class="w-5 h-5 text-white" />
                                </div>
                                <div>
                                    <p class="font-medium text-white">{{ stats.pending_reviews }} Pending Reviews</p>
                                    <p class="text-sm text-gray-400">Need moderation before publication</p>
                                </div>
                                <Link href="/admin/reviews?status=pending" class="ml-auto text-sm text-purple-400 hover:text-purple-300">
                                    Moderate
                                </Link>
                            </div>

                            <div class="flex items-center">
                                <div class="p-2 bg-blue-600 rounded-lg mr-3">
                                    <Users class="w-5 h-5 text-white" />
                                </div>
                                <div>
                                    <p class="font-medium text-white">5 New DJ Applications</p>
                                    <p class="text-sm text-gray-400">Waiting for approval</p>
                                </div>
                                <Link href="/admin/djs?status=pending" class="ml-auto text-sm text-purple-400 hover:text-purple-300">
                                    Review
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- New Users and Pending Reviews -->
                <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- New Users -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-white">New Users</h2>
                            <Link href="/admin/users" class="text-sm text-purple-400 hover:text-purple-300">View All</Link>
                        </div>

                        <div class="bg-gray-800 rounded-lg shadow overflow-hidden">
                            <ul class="divide-y divide-gray-700">
                                <li v-for="user in newUsers" :key="user.id" class="p-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center text-white font-bold">
                                                {{ user.name.charAt(0) }}
                                            </div>
                                            <div class="ml-3">
                                                <p class="font-medium text-white">{{ user.name }}</p>
                                                <p class="text-xs text-gray-400">{{ user.email }}</p>
                                            </div>
                                        </div>

                                        <div class="flex items-center">
                      <span class="px-2 py-1 text-xs font-medium rounded-full mr-2"
                            :class="{
                          'bg-blue-900 text-blue-400': user.user_type === 'client',
                          'bg-pink-900 text-pink-400': user.user_type === 'dj',
                          'bg-green-900 text-green-400': user.user_type === 'venue',
                          'bg-purple-900 text-purple-400': user.user_type === 'admin'
                        }"
                      >
                        {{ user.user_type }}
                      </span>
                                            <span class="text-xs text-gray-400">{{ user.date_joined }}</span>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Pending Reviews -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-white">Pending Reviews</h2>
                            <Link href="/admin/reviews?status=pending" class="text-sm text-purple-400 hover:text-purple-300">View All</Link>
                        </div>

                        <div class="bg-gray-800 rounded-lg shadow overflow-hidden">
                            <ul class="divide-y divide-gray-700">
                                <li v-for="review in pendingReviews" :key="review.id" class="p-4">
                                    <div class="flex justify-between mb-2">
                                        <div class="flex items-center">
                                            <p class="font-medium text-white">
                                                <Link :href="`/admin/users/${review.client_id}`" class="hover:text-purple-300">{{ review.client }}</Link>
                                                <span class="text-gray-400">→</span>
                                                <Link :href="`/admin/djs/${review.dj_id}`" class="hover:text-purple-300">{{ review.dj_name }}</Link>
                                            </p>
                                        </div>

                                        <div class="flex">
                                            <Star v-for="i in review.rating" :key="i" class="w-4 h-4 text-yellow-400" />
                                        </div>
                                    </div>

                                    <p class="text-sm text-gray-300 mb-2">{{ review.content }}</p>

                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-400">{{ review.date }}</span>
                                        <div>
                                            <button class="px-2 py-1 text-xs font-medium text-green-400 bg-green-900 rounded-full mr-1">Approve</button>
                                            <button class="px-2 py-1 text-xs font-medium text-red-400 bg-red-900 rounded-full">Reject</button>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Monthly Bookings Chart -->
                <div class="mt-8">
                    <h2 class="text-xl font-bold text-white mb-4">Monthly Bookings</h2>

                    <div class="p-5 bg-gray-800 rounded-lg shadow">
                        <div class="h-64">
                            <div class="relative h-full">
                                <div class="absolute bottom-0 left-0 right-0 flex items-end justify-around h-56">
                                    <div v-for="(data, index) in monthlyBookings" :key="index" class="flex flex-col items-center w-1/5">
                                        <div class="w-full mx-1">
                                            <div
                                                class="w-full bg-gradient-to-t from-purple-600 to-pink-500 rounded-t-sm"
                                                :style="`height: ${(data.count / 100) * 100}%`"
                                            ></div>
                                        </div>
                                        <span class="mt-2 text-xs text-gray-400">{{ data.month }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-4 text-sm text-gray-400">
                            <p>Total Bookings: {{ stats.total_bookings }}</p>
                            <Link href="/admin/analytics" class="text-purple-400 hover:text-purple-300">
                                View Detailed Analytics
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
