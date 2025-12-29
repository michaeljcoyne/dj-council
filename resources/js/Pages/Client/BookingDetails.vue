<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { Calendar, Clock, MapPin, Music, User, FileText, Phone, Mail, MessageSquare, XCircle, Headphones } from 'lucide-vue-next';

const props = defineProps({
    booking: Object,
});

const getStatusColor = (status) => {
    const colors = {
        pending: 'text-yellow-400 bg-yellow-400/10 border-yellow-400/30',
        confirmed: 'text-green-400 bg-green-400/10 border-green-400/30',
        completed: 'text-blue-400 bg-blue-400/10 border-blue-400/30',
        cancelled: 'text-red-400 bg-red-400/10 border-red-400/30',
    };
    return colors[status] || 'text-gray-400 bg-gray-400/10 border-gray-400/30';
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-GB', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
};

const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString('en-GB', {
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

const cancelBooking = () => {
    if (!confirm('Are you sure you want to cancel this booking?')) return;

    router.delete(route('client.bookings.cancel', props.booking.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Booking #${booking.id}`" />

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
                    <!-- Back Link -->
                    <Link href="/client/bookings" class="inline-block text-gray-400 hover:text-pink-500 transition-colors mb-6 text-sm">
                        ← Back to Bookings
                    </Link>

                    <!-- Page Header -->
                    <div class="flex items-start justify-between mb-8">
                        <div>
                            <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                                Booking #{{ booking.id }}
                            </h1>
                            <p class="text-gray-400 capitalize">{{ booking.event_type }} Event</p>
                        </div>
                        <div :class="['px-4 py-2 rounded-lg border-2 font-medium capitalize', getStatusColor(booking.status)]">
                            {{ booking.status }}
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Main Details -->
                        <div class="lg:col-span-2 space-y-6">
                            <!-- Event Details -->
                            <div class="bg-purple-950/30 backdrop-blur-sm border-2 border-purple-500/30 rounded-xl p-6">
                                <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                    <Calendar class="w-5 h-5 text-pink-500" />
                                    Event Details
                                </h2>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <label class="block text-gray-400 mb-1">Date</label>
                                        <div class="text-white font-medium">{{ formatDate(booking.event_date) }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-gray-400 mb-1">Time</label>
                                        <div class="text-white font-medium">{{ formatTime(booking.event_date) }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-gray-400 mb-1">Duration</label>
                                        <div class="text-white font-medium">{{ booking.duration_hours }} hours</div>
                                    </div>
                                    <div>
                                        <label class="block text-gray-400 mb-1">Event Type</label>
                                        <div class="text-white font-medium capitalize">{{ booking.event_type }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Venue Details -->
                            <div v-if="booking.venue" class="bg-purple-950/30 backdrop-blur-sm border-2 border-purple-500/30 rounded-xl p-6">
                                <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                    <MapPin class="w-5 h-5 text-pink-500" />
                                    Venue
                                </h2>

                                <div class="space-y-1">
                                    <div class="text-white font-medium text-lg">{{ booking.venue.name }}</div>
                                    <div class="text-gray-400">{{ booking.venue.address }}</div>
                                    <div class="text-gray-400">{{ booking.venue.city }}, {{ booking.venue.postcode }}</div>
                                </div>
                            </div>

                            <!-- DJ Details -->
                            <div v-if="booking.dj_profile" class="bg-purple-950/30 backdrop-blur-sm border-2 border-purple-500/30 rounded-xl p-6">
                                <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                    <User class="w-5 h-5 text-pink-500" />
                                    DJ
                                </h2>

                                <div class="flex items-center gap-4 mb-4">
                                    <img
                                        v-if="booking.dj_profile.profile_image"
                                        :src="booking.dj_profile.profile_image"
                                        :alt="booking.dj_profile.stage_name"
                                        class="w-16 h-16 rounded-lg object-cover"
                                    />
                                    <div v-else class="w-16 h-16 bg-gradient-to-br from-pink-500/20 to-purple-600/20 rounded-lg flex items-center justify-center">
                                        <Music class="w-8 h-8 text-gray-600" />
                                    </div>
                                    <div>
                                        <div class="font-bold text-white text-lg">{{ booking.dj_profile.stage_name }}</div>
                                        <div class="text-sm text-pink-400">{{ booking.dj_profile.specialty }}</div>
                                    </div>
                                </div>

                                <div class="flex gap-2">
                                    <Link
                                        :href="`/djs/${booking.dj_profile.id}`"
                                        class="flex-1 px-4 py-2 bg-purple-900/50 hover:bg-purple-900 text-white rounded-lg text-sm text-center transition-colors"
                                    >
                                        View Profile
                                    </Link>
                                    <button
                                        v-if="booking.status === 'confirmed'"
                                        class="px-4 py-2 border border-pink-500 text-pink-500 rounded-lg hover:bg-pink-950 transition-colors text-sm"
                                    >
                                        <MessageSquare class="w-4 h-4 inline mr-1" />
                                        Message
                                    </button>
                                </div>
                            </div>

                            <!-- Playlist -->
                            <div v-if="booking.playlist" class="bg-purple-950/30 backdrop-blur-sm border-2 border-purple-500/30 rounded-xl p-6">
                                <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                    <Music class="w-5 h-5 text-pink-500" />
                                    Playlist
                                </h2>

                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="font-medium text-white">{{ booking.playlist.name }}</div>
                                        <div class="text-sm text-gray-400">Shared with DJ</div>
                                    </div>
                                    <Link
                                        :href="`/client/playlists/${booking.playlist.id}`"
                                        class="px-4 py-2 bg-purple-900/50 hover:bg-purple-900 text-white rounded-lg text-sm transition-colors"
                                    >
                                        View
                                    </Link>
                                </div>
                            </div>

                            <!-- Special Requests -->
                            <div v-if="booking.special_requests" class="bg-purple-950/30 backdrop-blur-sm border-2 border-purple-500/30 rounded-xl p-6">
                                <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                    <FileText class="w-5 h-5 text-pink-500" />
                                    Special Requests
                                </h2>

                                <p class="text-gray-300 whitespace-pre-wrap">{{ booking.special_requests }}</p>
                            </div>

                            <!-- Decline Reason (if declined) -->
                            <div v-if="booking.status === 'cancelled' && booking.decline_reason" class="bg-red-900/20 border-2 border-red-500/50 rounded-xl p-6">
                                <h2 class="text-xl font-bold text-red-400 mb-4 flex items-center gap-2">
                                    <XCircle class="w-5 h-5" />
                                    Booking Declined
                                </h2>

                                <div class="mb-2 text-sm text-gray-400">The DJ declined this booking with the following reason:</div>
                                <p class="text-gray-300 whitespace-pre-wrap">{{ booking.decline_reason }}</p>
                            </div>
                        </div>

                        <!-- Sidebar -->
                        <div class="lg:col-span-1">
                            <div class="sticky top-24 space-y-6">
                                <!-- Price Summary -->
                                <div class="bg-purple-950/30 backdrop-blur-sm border-2 border-purple-500/30 rounded-xl p-6">
                                    <h3 class="text-lg font-bold text-white mb-4">Price Summary</h3>

                                    <div class="space-y-3 text-sm mb-4">
                                        <div class="flex justify-between">
                                            <span class="text-gray-400">Duration:</span>
                                            <span class="text-white">{{ booking.duration_hours }} hours</span>
                                        </div>
                                        <div v-if="booking.dj_profile" class="flex justify-between">
                                            <span class="text-gray-400">Rate:</span>
                                            <span class="text-white">{{ formatCurrency(booking.dj_profile.hourly_rate) }}/hr</span>
                                        </div>
                                        <div class="border-t border-purple-500/30 pt-3">
                                            <div class="flex justify-between text-lg">
                                                <span class="text-white font-bold">Total:</span>
                                                <span class="text-pink-500 font-bold">{{ formatCurrency(booking.total_price) }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="booking.status === 'pending'" class="text-xs text-gray-500 text-center">
                                        Final price confirmed upon DJ acceptance
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="bg-purple-950/30 backdrop-blur-sm border-2 border-purple-500/30 rounded-xl p-6">
                                    <h3 class="text-lg font-bold text-white mb-4">Actions</h3>

                                    <div class="space-y-2">
                                        <button
                                            v-if="booking.status === 'confirmed'"
                                            class="w-full px-4 py-3 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 rounded-lg font-medium text-white transition-all"
                                        >
                                            <MessageSquare class="w-4 h-4 inline mr-2" />
                                            Message DJ
                                        </button>

                                        <button
                                            v-if="['pending', 'confirmed'].includes(booking.status)"
                                            @click="cancelBooking"
                                            class="w-full px-4 py-3 border-2 border-red-500 text-red-500 hover:bg-red-950 rounded-lg font-medium transition-colors"
                                        >
                                            <XCircle class="w-4 h-4 inline mr-2" />
                                            Cancel Booking
                                        </button>

                                        <Link
                                            href="/client/bookings"
                                            class="block w-full px-4 py-3 bg-purple-900/50 hover:bg-purple-900 text-white rounded-lg font-medium text-center transition-colors"
                                        >
                                            Back to Bookings
                                        </Link>
                                    </div>
                                </div>

                                <!-- Status Timeline -->
                                <div class="bg-purple-950/30 backdrop-blur-sm border-2 border-purple-500/30 rounded-xl p-6">
                                    <h3 class="text-lg font-bold text-white mb-4">Status</h3>

                                    <div class="space-y-3 text-sm">
                                        <div class="flex items-start gap-3">
                                            <div :class="['w-2 h-2 rounded-full mt-1.5', booking.status ? 'bg-green-400' : 'bg-gray-600']"></div>
                                            <div>
                                                <div class="text-white font-medium">Request Sent</div>
                                                <div class="text-gray-400 text-xs">{{ formatDate(booking.created_at) }}</div>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <div :class="['w-2 h-2 rounded-full mt-1.5', ['confirmed', 'completed'].includes(booking.status) ? 'bg-green-400' : booking.status === 'cancelled' ? 'bg-red-400' : 'bg-gray-600']"></div>
                                            <div>
                                                <div class="text-white font-medium">
                                                    {{ booking.status === 'confirmed' || booking.status === 'completed' ? 'DJ Accepted' : booking.status === 'cancelled' ? 'Declined by DJ' : 'Awaiting Response' }}
                                                </div>
                                                <div v-if="booking.status === 'pending'" class="text-gray-400 text-xs">Pending...</div>
                                                <div v-else-if="booking.status === 'cancelled'" class="text-gray-400 text-xs">See reason above</div>
                                            </div>
                                        </div>

                                        <div class="flex items-start gap-3">
                                            <div :class="['w-2 h-2 rounded-full mt-1.5', booking.status === 'completed' ? 'bg-green-400' : 'bg-gray-600']"></div>
                                            <div>
                                                <div class="text-white font-medium">Event Complete</div>
                                                <div class="text-gray-400 text-xs">
                                                    {{ booking.status === 'completed' ? 'Completed' : formatDate(booking.event_date) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
