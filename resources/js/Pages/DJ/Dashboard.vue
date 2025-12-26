<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import { Menu, X, CalendarDays, DollarSign, Star, Users, Music } from 'lucide-vue-next';

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

// Example data for the dashboard
const profile = {
    stage_name: 'DJ Pulse',
    specialty: 'House & EDM Specialist',
    hourly_rate: 100,
    average_rating: 4.9,
    review_count: 48,
    upcoming_bookings_count: 5,
    total_bookings: 63,
    completed_bookings: 58,
    total_earnings: 15800,
};

// Upcoming bookings
const upcomingBookings = [
    {
        id: 1,
        client: 'John Smith',
        date: 'Jun.15, 2025',
        time: '8:00 PM - 12:00 AM',
        venue: 'Skyline Rooftop',
        venue_address: '123 Main St, Los Angeles, CA',
        type: 'Corporate Event',
        status: 'confirmed',
        price: 500
    },
    {
        id: 2,
        client: 'Sarah Johnson',
        date: 'Jun.22, 2025',
        time: '9:00 PM - 1:00 AM',
        venue: 'The Grand Hall',
        venue_address: '456 High St, Los Angeles, CA',
        type: 'Wedding Reception',
        status: 'confirmed',
        price: 600
    },
    {
        id: 3,
        client: 'Tech Innovations Inc.',
        date: 'Jul.05, 2025',
        time: '7:00 PM - 11:00 PM',
        venue: 'Convention Center',
        venue_address: '789 Convention Blvd, Los Angeles, CA',
        type: 'Product Launch',
        status: 'pending',
        price: 700
    }
];

// Recent reviews
const recentReviews = [
    {
        id: 1,
        client: 'Jennifer L.',
        event_type: 'Wedding',
        date: 'May 20, 2025',
        rating: 5,
        comment: 'DJ Pulse was amazing at our wedding! Everyone was dancing all night long and several guests asked for his contact information. Would highly recommend!'
    },
    {
        id: 2,
        client: 'Michael T.',
        event_type: 'Corporate Party',
        date: 'May 12, 2025',
        rating: 5,
        comment: 'Great selection of music that appealed to all age groups at our company event. Very professional and reliable.'
    },
    {
        id: 3,
        client: 'Robert K.',
        event_type: 'Birthday Party',
        date: 'Apr 30, 2025',
        rating: 4,
        comment: 'Good DJ, read the crowd well and maintained a great energy throughout the night.'
    }
];

// Monthly earnings data
const monthlyEarnings = [
    { month: 'Jan', amount: 1200 },
    { month: 'Feb', amount: 1500 },
    { month: 'Mar', amount: 1800 },
    { month: 'Apr', amount: 2000 },
    { month: 'May', amount: 1700 },
];
</script>

<template>
    <Head title="DJ Dashboard" />

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
                    href="/dj/dashboard"
                    class="flex items-center px-4 py-2 text-white bg-purple-700 rounded-lg"
                >
                    <span class="ml-3">Dashboard</span>
                </Link>
                <Link
                    href="/dj/profile/edit"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg"
                >
                    <span class="ml-3">Edit Profile</span>
                </Link>
                <Link
                    href="/dj/bookings"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg"
                >
                    <span class="ml-3">Bookings</span>
                </Link>
                <Link
                    href="/dj/reviews"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg"
                >
                    <span class="ml-3">Reviews</span>
                </Link>
                <Link
                    href="/dj/earnings"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg"
                >
                    <span class="ml-3">Earnings</span>
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
                                    <div class="w-8 h-8 rounded-full bg-pink-600 flex items-center justify-center text-white font-bold">
                                        DJ
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="px-4 py-6 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white">{{ profile.stage_name }}</h1>
                        <p class="mt-1 text-gray-400">{{ profile.specialty }}</p>
                    </div>
                    <Link
                        href="/dj/profile/edit"
                        class="mt-4 px-4 py-2 text-sm font-medium text-white bg-pink-600 rounded-lg hover:bg-pink-700 md:mt-0"
                    >
                        Edit Profile
                    </Link>
                </div>

                <!-- Stats Overview -->
                <div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="p-5 bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-600 rounded-lg">
                                <CalendarDays class="w-6 h-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-white">Upcoming Bookings</h2>
                                <p class="text-2xl font-bold text-white">{{ profile.upcoming_bookings_count }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-600 rounded-lg">
                                <DollarSign class="w-6 h-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-white">Monthly Earnings</h2>
                                <p class="text-2xl font-bold text-white">${{ monthlyEarnings[monthlyEarnings.length - 1].amount }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-600 rounded-lg">
                                <Star class="w-6 h-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-white">Rating</h2>
                                <p class="text-2xl font-bold text-white">{{ profile.average_rating }} <span class="text-sm text-gray-400">({{ profile.review_count }})</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-pink-600 rounded-lg">
                                <Users class="w-6 h-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-white">Total Events</h2>
                                <p class="text-2xl font-bold text-white">{{ profile.total_bookings }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Bookings -->
                <div class="mt-8">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-white">Upcoming Bookings</h2>
                        <Link href="/dj/bookings" class="text-sm text-purple-400 hover:text-purple-300">View All</Link>
                    </div>

                    <div class="mt-4 overflow-hidden bg-gray-800 rounded-lg shadow">
                        <ul class="divide-y divide-gray-700">
                            <li v-for="booking in upcomingBookings" :key="booking.id" class="flex flex-col p-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="mb-2 sm:mb-0">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-2 h-2" :class="booking.status === 'confirmed' ? 'bg-green-500' : 'bg-yellow-500'"></div>
                                        <p class="ml-2 font-medium text-white">{{ booking.type }}</p>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-400">{{ booking.date }} • {{ booking.time }}</p>
                                </div>

                                <div class="mb-2 sm:mb-0">
                                    <p class="text-sm font-medium text-white">{{ booking.client }}</p>
                                    <p class="text-sm text-gray-400">{{ booking.venue }}</p>
                                </div>

                                <div class="flex items-center">
                  <span class="px-2 py-1 text-xs font-medium rounded-full"
                        :class="booking.status === 'confirmed' ? 'text-green-400 bg-green-900' : 'text-yellow-400 bg-yellow-900'">
                    {{ booking.status }}
                  </span>
                                    <Link :href="`/dj/bookings/${booking.id}`" class="ml-4 text-purple-400 hover:text-purple-300">
                                        Details
                                    </Link>
                                </div>
                            </li>

                            <li v-if="upcomingBookings.length === 0" class="p-4 text-center text-gray-400">
                                No upcoming bookings
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Recent Reviews -->
                <div class="mt-8">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-white">Recent Reviews</h2>
                        <Link href="/dj/reviews" class="text-sm text-purple-400 hover:text-purple-300">View All</Link>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div v-for="review in recentReviews" :key="review.id" class="p-5 bg-gray-800 rounded-lg shadow">
                            <div class="flex items-center justify-between mb-3">
                <span class="px-2 py-1 text-xs font-medium text-gray-300 bg-gray-700 rounded-full">
                  {{ review.event_type }}
                </span>
                                <div class="flex">
                                    <Star v-for="i in review.rating" :key="i" class="w-4 h-4 text-yellow-400" />
                                </div>
                            </div>
                            <p class="text-sm text-gray-300 line-clamp-3">{{ review.comment }}</p>
                            <div class="flex items-center justify-between mt-4">
                                <p class="text-xs text-gray-400">{{ review.date }}</p>
                                <p class="text-xs font-medium text-gray-300">{{ review.client }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Monthly Earnings Chart -->
                <div class="mt-8">
                    <h2 class="text-xl font-bold text-white">Monthly Earnings</h2>

                    <div class="p-5 mt-4 bg-gray-800 rounded-lg shadow">
                        <div class="h-64">
                            <div class="relative h-full">
                                <div class="absolute bottom-0 left-0 right-0 flex items-end justify-around h-56">
                                    <div v-for="(data, index) in monthlyEarnings" :key="index" class="flex flex-col items-center w-1/5">
                                        <div class="w-full mx-1">
                                            <div
                                                class="w-full bg-gradient-to-t from-purple-600 to-pink-500 rounded-t-sm"
                                                :style="`height: ${(data.amount / 2500) * 100}%`"
                                            ></div>
                                        </div>
                                        <span class="mt-2 text-xs text-gray-400">{{ data.month }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between mt-4 text-sm text-gray-400">
                            <p>Total Earnings: ${{ profile.total_earnings }}</p>
                            <Link href="/dj/earnings" class="text-purple-400 hover:text-purple-300">
                                View Detailed Report
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
