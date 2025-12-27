<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Calendar, Clock, MapPin, User, ChevronRight, X, CheckCircle, XCircle, Headphones } from 'lucide-vue-next';

const props = defineProps({
    bookings: Object,
    activeStatus: String,
});

const statusTabs = [
    { value: 'upcoming', label: 'Upcoming', icon: Calendar },
    { value: 'pending', label: 'Pending', icon: Clock },
    { value: 'past', label: 'Past', icon: CheckCircle },
    { value: 'cancelled', label: 'Cancelled', icon: XCircle },
];

const getStatusColor = (status) => {
    const colors = {
        pending: 'text-yellow-400 bg-yellow-400/10',
        confirmed: 'text-green-400 bg-green-400/10',
        completed: 'text-blue-400 bg-blue-400/10',
        cancelled: 'text-red-400 bg-red-400/10',
    };
    return colors[status] || 'text-gray-400 bg-gray-400/10';
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-GB', {
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};

const formatTime = (timeString) => {
    if (!timeString) return 'TBD';
    return new Date('2000-01-01 ' + timeString).toLocaleTimeString('en-GB', {
        hour: '2-digit',
        minute: '2-digit'
    });
};

const formatCurrency = (amount) => {
    if (!amount) return 'TBD';
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: 'GBP'
    }).format(amount);
};
</script>

<template>
    <Head title="My Bookings" />

    <div class="min-h-screen bg-black text-white">
        <!-- Header -->
        <header class="fixed top-0 left-0 right-0 z-50 bg-black/80 backdrop-blur-md border-b border-purple-500/20">
            <div class="container mx-auto px-4 h-16 flex items-center justify-between">
                <Link href="/" class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <Headphones class="h-6 w-6 text-white" />
                    </div>
                    <span class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-purple-500">
                        DJ Council
                    </span>
                </Link>

                <nav class="hidden md:flex items-center space-x-6">
                    <Link href="/client/dashboard" class="text-white hover:text-pink-400 transition-colors">
                        Dashboard
                    </Link>
                    <Link href="/client/bookings" class="text-pink-400">
                        Bookings
                    </Link>
                    <Link href="/djs" class="text-white hover:text-pink-400 transition-colors">
                        Find DJs
                    </Link>

                    <div class="ml-4">
                        <Link href="/logout" method="post" as="button" class="text-white hover:text-pink-400 transition-colors">
                            Log Out
                        </Link>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="pt-16">
            <section class="min-h-screen py-8 bg-gradient-to-b from-purple-950/50 to-black">
                <div class="container mx-auto px-4">
                    <!-- Page Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">My Bookings</h1>
                        <p class="text-gray-400">Manage your DJ bookings and event requests</p>
                    </div>

                    <!-- Status Tabs -->
                    <div class="mb-6 flex gap-2 overflow-x-auto pb-2">
                        <Link
                            v-for="tab in statusTabs"
                            :key="tab.value"
                            :href="`/client/bookings?status=${tab.value}`"
                            :class="[
                                    'flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap',
                                    activeStatus === tab.value
                                        ? 'bg-gradient-to-r from-pink-600 to-purple-600 text-white'
                                        : 'bg-purple-950/30 border border-purple-500/30 text-gray-400 hover:text-white hover:border-purple-500/50'
                                ]"
                        >
                            <component :is="tab.icon" class="w-4 h-4" />
                            {{ tab.label }}
                        </Link>
                    </div>

                    <!-- Bookings List -->
                    <div v-if="bookings.data.length > 0" class="space-y-4">
                        <Link
                            v-for="booking in bookings.data"
                            :key="booking.id"
                            :href="`/client/bookings/${booking.id}`"
                            class="block bg-purple-950/30 backdrop-blur-sm border-2 border-purple-500/30 rounded-xl p-6 hover:border-pink-500/50 transition-all group"
                        >
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <!-- Event Type & Status -->
                                    <div class="flex items-center gap-3 mb-3">
                                            <span class="text-lg font-bold text-white capitalize">
                                                {{ booking.event_type }}
                                            </span>
                                        <span :class="['px-3 py-1 rounded-full text-xs font-medium capitalize', getStatusColor(booking.status)]">
                                                {{ booking.status }}
                                            </span>
                                    </div>

                                    <!-- Event Date & Time -->
                                    <div class="flex items-center gap-4 text-sm text-gray-400 mb-3">
                                        <div class="flex items-center gap-2">
                                            <Calendar class="w-4 h-4 text-pink-500" />
                                            <span>{{ formatDate(booking.event_date) }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <Clock class="w-4 h-4 text-pink-500" />
                                            <span>{{ booking.duration_hours }} hours</span>
                                        </div>
                                    </div>

                                    <!-- DJ & Venue Info -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                        <div v-if="booking.dj_profile" class="flex items-center gap-2">
                                            <User class="w-4 h-4 text-purple-400" />
                                            <span class="text-gray-300">
                                                    {{ booking.dj_profile?.user?.name || 'No DJ assigned' }}
                                                </span>
                                        </div>
                                        <div v-if="booking.venue" class="flex items-center gap-2">
                                            <MapPin class="w-4 h-4 text-purple-400" />
                                            <span class="text-gray-300">{{ booking.venue?.name }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Price & Arrow -->
                                <div class="flex flex-col items-end gap-2">
                                    <div class="text-right">
                                        <div class="text-xl font-bold text-pink-500">
                                            {{ formatCurrency(booking.total_price) }}
                                        </div>
                                    </div>
                                    <ChevronRight class="w-5 h-5 text-gray-500 group-hover:text-pink-500 transition-colors" />
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="bg-purple-950/30 backdrop-blur-sm border-2 border-purple-500/30 rounded-xl p-12 text-center">
                        <Calendar class="w-16 h-16 text-gray-600 mx-auto mb-4" />
                        <h3 class="text-xl font-bold text-white mb-2">No bookings found</h3>
                        <p class="text-gray-400 mb-6">
                            {{ activeStatus === 'upcoming' ? "You don't have any upcoming bookings yet." : `No ${activeStatus} bookings.` }}
                        </p>
                        <Link
                            href="/djs"
                            class="inline-block px-6 py-3 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 rounded-lg font-medium"
                        >
                            Find a DJ
                        </Link>
                    </div>

                    <!-- Pagination -->
                    <div v-if="bookings.last_page > 1" class="mt-6 flex justify-center gap-2">
                        <Link
                            v-if="bookings.prev_page_url"
                            :href="bookings.prev_page_url"
                            class="px-4 py-2 bg-purple-900/50 hover:bg-purple-900 text-white rounded-lg text-sm"
                        >
                            Previous
                        </Link>
                        <span class="px-4 py-2 text-gray-400 text-sm">
                                Page {{ bookings.current_page }} of {{ bookings.last_page }}
                            </span>
                        <Link
                            v-if="bookings.next_page_url"
                            :href="bookings.next_page_url"
                            class="px-4 py-2 bg-purple-900/50 hover:bg-purple-900 text-white rounded-lg text-sm"
                        >
                            Next
                        </Link>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-black border-t border-purple-500/30 py-8">
            <div class="container mx-auto px-4 text-center text-gray-500 text-sm">
                <p>&copy; {{ new Date().getFullYear() }} DJ Council. All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>
