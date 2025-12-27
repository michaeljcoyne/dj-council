<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Calendar, Clock, MapPin, Music, User, FileText, Headphones } from 'lucide-vue-next';

const props = defineProps({
    dj: Object, // If booking specific DJ, otherwise null for generic request
    playlists: Array, // User's Spotify playlists
    canLogin: Boolean,
    canRegister: Boolean,
});

const form = useForm({
    dj_profile_id: props.dj?.id || null,
    event_date: '',
    event_time: '',
    duration_hours: 4,
    venue_name: '',
    venue_address: '',
    venue_city: '',
    venue_state: '',
    venue_postal_code: '',
    expected_guests: 50,
    event_type: 'wedding',
    playlist_id: '',
    special_requests: '',
});

const eventTypes = [
    { value: 'wedding', label: 'Wedding' },
    { value: 'birthday', label: 'Birthday Party' },
    { value: 'corporate', label: 'Corporate Event' },
    { value: 'club', label: 'Club Night' },
    { value: 'festival', label: 'Festival' },
    { value: 'private', label: 'Private Party' },
    { value: 'other', label: 'Other' },
];

const submitBooking = () => {
    if (props.dj) {
        form.post(route('client.bookings.store'), {
            preserveScroll: true,
            onSuccess: () => {
                // Redirect to booking confirmation or dashboard
            },
        });
    } else {
        // Generic request to DJ pool
        form.post(route('client.booking-requests.store'), {
            preserveScroll: true,
            onSuccess: () => {
                // Redirect to requests page
            },
        });
    }
};

const calculateTotal = () => {
    if (!props.dj) return null;
    const hours = form.duration_hours;
    const rate = props.dj.hourly_rate;
    const total = hours * rate;
    const fee = total * 0.10; // 10% booking fee
    return {
        subtotal: total,
        fee: fee,
        total: total + fee
    };
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: 'GBP'
    }).format(amount);
};
</script>

<template>
    <Head :title="dj ? `Book ${dj.stage_name}` : 'Request a DJ'" />

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
                    <Link href="/" class="text-white hover:text-pink-400 transition-colors">
                        Home
                    </Link>
                    <Link href="/djs" class="text-white hover:text-pink-400 transition-colors">
                        Find DJs
                    </Link>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="pt-16">
            <section class="min-h-screen py-8 bg-gradient-to-b from-purple-950/50 to-black">
                <div class="container mx-auto px-4">
                    <!-- Back Link -->
                    <Link
                        :href="dj ? `/djs/${dj.id}` : '/djs'"
                        class="inline-block text-gray-400 hover:text-pink-500 transition-colors mb-6 text-sm"
                    >
                        ← Back to {{ dj ? 'DJ Profile' : 'DJ Listing' }}
                    </Link>

                    <!-- Page Header -->
                    <div class="mb-8">
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">
                            {{ dj ? `Book ${dj.stage_name}` : 'Request a DJ' }}
                        </h1>
                        <p class="text-gray-400">
                            {{ dj ? 'Fill out the form below to send a booking request' : 'DJs will contact you with proposals' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Booking Form -->
                        <div class="lg:col-span-2">
                            <form @submit.prevent="submitBooking" class="space-y-6">
                                <!-- Event Details -->
                                <div class="bg-purple-950/30 backdrop-blur-sm border-2 border-purple-500/30 rounded-xl p-6">
                                    <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                        <Calendar class="w-5 h-5 text-pink-500" />
                                        Event Details
                                    </h2>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-400 mb-2">Event Type *</label>
                                            <select
                                                v-model="form.event_type"
                                                required
                                                class="w-full px-4 py-3 bg-purple-950/50 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-pink-500 appearance-none cursor-pointer"
                                                style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2724%27 height=%2724%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%23a855f7%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3E%3Cpath d=%27m6 9 6 6 6-6%27/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1.5em 1.5em; padding-right: 2.5rem;"
                                            >
                                                <option v-for="type in eventTypes" :key="type.value" :value="type.value">
                                                    {{ type.label }}
                                                </option>
                                            </select>
                                            <span v-if="form.errors.event_type" class="text-red-400 text-sm mt-1">{{ form.errors.event_type }}</span>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-400 mb-2">Expected Guests *</label>
                                            <input
                                                v-model="form.expected_guests"
                                                type="number"
                                                min="1"
                                                required
                                                placeholder="50"
                                                class="w-full px-4 py-3 bg-purple-950/50 border border-purple-500/30 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-pink-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                            />
                                            <span v-if="form.errors.expected_guests" class="text-red-400 text-sm mt-1">{{ form.errors.expected_guests }}</span>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-400 mb-2">Event Date *</label>
                                            <input
                                                v-model="form.event_date"
                                                type="date"
                                                required
                                                :min="new Date().toISOString().split('T')[0]"
                                                class="w-full px-4 py-3 bg-purple-950/50 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-pink-500"
                                            />
                                            <span v-if="form.errors.event_date" class="text-red-400 text-sm mt-1">{{ form.errors.event_date }}</span>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-400 mb-2">Start Time *</label>
                                            <input
                                                v-model="form.event_time"
                                                type="time"
                                                required
                                                class="w-full px-4 py-3 bg-purple-950/50 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-pink-500"
                                            />
                                            <span v-if="form.errors.event_time" class="text-red-400 text-sm mt-1">{{ form.errors.event_time }}</span>
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-400 mb-2">
                                                Duration (hours) *
                                                <span v-if="dj" class="text-gray-500">Min: {{ dj.minimum_booking_hours }}hr</span>
                                            </label>
                                            <input
                                                v-model="form.duration_hours"
                                                type="number"
                                                :min="dj?.minimum_booking_hours || 1"
                                                step="1"
                                                required
                                                class="w-full px-4 py-3 bg-purple-950/50 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-pink-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                            />
                                            <span v-if="form.errors.duration_hours" class="text-red-400 text-sm mt-1">{{ form.errors.duration_hours }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Venue Details -->
                                <div class="bg-purple-950/30 backdrop-blur-sm border-2 border-purple-500/30 rounded-xl p-6">
                                    <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                        <MapPin class="w-5 h-5 text-pink-500" />
                                        Venue Details
                                    </h2>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-400 mb-2">Venue Name *</label>
                                            <input
                                                v-model="form.venue_name"
                                                type="text"
                                                required
                                                placeholder="e.g., The Grand Hotel"
                                                class="w-full px-4 py-3 bg-purple-950/50 border border-purple-500/30 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-pink-500"
                                            />
                                            <span v-if="form.errors.venue_name" class="text-red-400 text-sm mt-1">{{ form.errors.venue_name }}</span>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-400 mb-2">Address *</label>
                                            <input
                                                v-model="form.venue_address"
                                                type="text"
                                                required
                                                placeholder="123 Main Street"
                                                class="w-full px-4 py-3 bg-purple-950/50 border border-purple-500/30 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-pink-500"
                                            />
                                            <span v-if="form.errors.venue_address" class="text-red-400 text-sm mt-1">{{ form.errors.venue_address }}</span>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-400 mb-2">City *</label>
                                                <input
                                                    v-model="form.venue_city"
                                                    type="text"
                                                    required
                                                    placeholder="London"
                                                    class="w-full px-4 py-3 bg-purple-950/50 border border-purple-500/30 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-pink-500"
                                                />
                                                <span v-if="form.errors.venue_city" class="text-red-400 text-sm mt-1">{{ form.errors.venue_city }}</span>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-400 mb-2">County/State *</label>
                                                <input
                                                    v-model="form.venue_state"
                                                    type="text"
                                                    required
                                                    placeholder="Greater London"
                                                    class="w-full px-4 py-3 bg-purple-950/50 border border-purple-500/30 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-pink-500"
                                                />
                                                <span v-if="form.errors.venue_state" class="text-red-400 text-sm mt-1">{{ form.errors.venue_state }}</span>
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-400 mb-2">Postcode *</label>
                                            <input
                                                v-model="form.venue_postal_code"
                                                type="text"
                                                required
                                                placeholder="SW1A 1AA"
                                                class="w-full px-4 py-3 bg-purple-950/50 border border-purple-500/30 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-pink-500"
                                            />
                                            <span v-if="form.errors.venue_postal_code" class="text-red-400 text-sm mt-1">{{ form.errors.venue_postal_code }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Playlist Selection -->
                                <div v-if="playlists?.length" class="bg-purple-950/30 backdrop-blur-sm border-2 border-purple-500/30 rounded-xl p-6">
                                    <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                        <Music class="w-5 h-5 text-pink-500" />
                                        Your Playlist (Optional)
                                    </h2>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">Select a playlist to share with the DJ</label>
                                        <select
                                            v-model="form.playlist_id"
                                            class="w-full px-4 py-3 bg-purple-950/50 border border-purple-500/30 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-pink-500 appearance-none cursor-pointer"
                                            style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2724%27 height=%2724%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%23a855f7%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3E%3Cpath d=%27m6 9 6 6 6-6%27/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1.5em 1.5em; padding-right: 2.5rem;"
                                        >
                                            <option value="">No playlist</option>
                                            <option v-for="playlist in playlists" :key="playlist.id" :value="playlist.id">
                                                {{ playlist.name }} ({{ playlist.tracks_count }} tracks)
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Special Requests -->
                                <div class="bg-purple-950/30 backdrop-blur-sm border-2 border-purple-500/30 rounded-xl p-6">
                                    <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                        <FileText class="w-5 h-5 text-pink-500" />
                                        Special Requests
                                    </h2>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-400 mb-2">Additional notes for the DJ</label>
                                        <textarea
                                            v-model="form.special_requests"
                                            rows="4"
                                            placeholder="Any specific requirements, music preferences, or special requests..."
                                            class="w-full px-4 py-3 bg-purple-950/50 border border-purple-500/30 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-pink-500 resize-none"
                                        ></textarea>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="w-full px-6 py-4 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg font-medium text-white text-lg transition-all"
                                >
                                    {{ form.processing ? 'Sending Request...' : (dj ? 'Send Booking Request' : 'Request DJs') }}
                                </button>
                            </form>
                        </div>

                        <!-- Sidebar -->
                        <div class="lg:col-span-1">
                            <div class="sticky top-20">
                                <!-- DJ Card (if booking specific DJ) -->
                                <div v-if="dj" class="bg-purple-950/30 backdrop-blur-sm border-2 border-purple-500/30 rounded-xl p-6 mb-6">
                                    <h3 class="text-lg font-bold text-white mb-4">Booking Summary</h3>

                                    <div class="flex items-center gap-3 mb-4">
                                        <img
                                            v-if="dj.profile_image"
                                            :src="dj.profile_image"
                                            :alt="dj.stage_name"
                                            class="w-16 h-16 rounded-lg object-cover"
                                        />
                                        <div v-else class="w-16 h-16 bg-gradient-to-br from-pink-500/20 to-purple-600/20 rounded-lg flex items-center justify-center">
                                            <Music class="w-8 h-8 text-gray-600" />
                                        </div>
                                        <div>
                                            <div class="font-bold text-white">{{ dj.stage_name }}</div>
                                            <div class="text-sm text-pink-400">{{ dj.specialty }}</div>
                                        </div>
                                    </div>

                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-400">Hourly Rate:</span>
                                            <span class="text-white font-medium">{{ formatCurrency(dj.hourly_rate) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-400">Duration:</span>
                                            <span class="text-white font-medium">{{ form.duration_hours }} hours</span>
                                        </div>
                                        <div class="border-t border-purple-500/30 pt-2 mt-2">
                                            <div class="flex justify-between">
                                                <span class="text-gray-400">Subtotal:</span>
                                                <span class="text-white font-medium">{{ formatCurrency(calculateTotal()?.subtotal || 0) }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-gray-400">Booking Fee (10%):</span>
                                                <span class="text-white font-medium">{{ formatCurrency(calculateTotal()?.fee || 0) }}</span>
                                            </div>
                                        </div>
                                        <div class="border-t border-purple-500/30 pt-2 mt-2">
                                            <div class="flex justify-between text-lg">
                                                <span class="text-white font-bold">Total:</span>
                                                <span class="text-pink-500 font-bold">{{ formatCurrency(calculateTotal()?.total || 0) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Info Box -->
                                <div class="bg-black/50 backdrop-blur-sm border border-purple-500/30 rounded-xl p-6">
                                    <h3 class="text-lg font-bold text-white mb-3">What Happens Next?</h3>
                                    <ul class="space-y-3 text-sm text-gray-400">
                                        <li class="flex gap-2">
                                            <span class="text-pink-500">1.</span>
                                            <span>{{ dj ? 'Your request is sent to the DJ' : 'DJs receive your request' }}</span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span class="text-pink-500">2.</span>
                                            <span>{{ dj ? 'DJ reviews and accepts/declines' : 'Interested DJs send proposals' }}</span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span class="text-pink-500">3.</span>
                                            <span>{{ dj ? 'You receive confirmation via email' : 'You review proposals and choose' }}</span>
                                        </li>
                                        <li class="flex gap-2">
                                            <span class="text-pink-500">4.</span>
                                            <span>Payment is secured through our platform</span>
                                        </li>
                                    </ul>
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
