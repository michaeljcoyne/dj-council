<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Star, MapPin, Heart, Music, Instagram, Globe, Headphones } from 'lucide-vue-next';

const props = defineProps({
    dj: Object,
    canLogin: Boolean,
    canRegister: Boolean,
});

const formatRate = (rate) => {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0
    }).format(rate);
};
</script>

<template>
    <Head :title="dj.stage_name" />

    <div class="min-h-screen bg-black text-white">
        <!-- Header - EXACT match to Welcome page -->
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

                    <div v-if="canLogin" class="ml-4">
                        <Link href="/login" class="mr-2 text-white hover:text-pink-400 transition-colors">
                            Log in
                        </Link>
                        <Link v-if="canRegister" href="/register" class="px-4 py-2 rounded-lg bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white font-medium">
                            Sign Up
                        </Link>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Main Content - NO GAP, starts right after header -->
        <main class="pt-16">
            <section class="min-h-screen py-8 px-4 bg-gradient-to-b from-purple-950/50 to-black">
                <div class="max-w-6xl mx-auto">
                    <!-- Back Link -->
                    <Link href="/djs" class="inline-block text-gray-400 hover:text-pink-500 transition-colors mb-6 text-sm">
                        ← Back to DJs
                    </Link>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Left: DJ Image & Info -->
                        <div class="lg:col-span-2">
                            <div class="relative overflow-hidden rounded-xl aspect-video mb-6">
                                <img
                                    v-if="dj.profile_image"
                                    :src="dj.profile_image"
                                    :alt="dj.stage_name"
                                    class="w-full h-full object-cover"
                                />
                                <div v-else class="flex items-center justify-center h-full bg-gradient-to-br from-pink-500/20 to-purple-600/20">
                                    <Music class="w-24 h-24 text-gray-600" />
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-70"></div>
                                <div v-if="dj.is_featured" class="absolute top-4 right-4 px-3 py-1.5 bg-gradient-to-r from-pink-600 to-purple-600 text-white text-sm font-medium rounded-full">
                                    Featured DJ
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 p-8">
                                    <div class="flex items-center mb-3">
                                        <Star v-for="i in 5" :key="i" class="w-5 h-5 text-yellow-400" :class="i <= Math.round(dj.average_rating || 0) ? 'fill-yellow-400' : 'text-gray-600'" />
                                        <span class="ml-2 text-sm text-gray-300">({{ dj.review_count || 0 }})</span>
                                    </div>
                                    <h1 class="text-4xl font-bold mb-2">{{ dj.stage_name }}</h1>
                                    <p class="text-xl text-pink-400">{{ dj.specialty }}</p>
                                </div>
                            </div>

                            <!-- Bio Section -->
                            <div class="bg-black/50 backdrop-blur-sm p-6 rounded-xl border border-purple-500/30 mb-6">
                                <h2 class="text-xl font-bold mb-4">About</h2>
                                <p class="text-gray-300 mb-6 leading-relaxed">{{ dj.bio || 'Professional DJ ready to make your event unforgettable.' }}</p>

                                <h3 class="font-semibold mb-3 text-pink-400">Top Genres</h3>
                                <div class="flex flex-wrap gap-2 mb-6">
                                    <span
                                        v-for="genre in dj.genres"
                                        :key="genre.id"
                                        class="px-3 py-1 bg-purple-900/50 rounded-full text-sm"
                                    >
                                        {{ genre.name }}
                                    </span>
                                </div>

                                <!-- Social Links -->
                                <div v-if="dj.instagram_url || dj.soundcloud_url || dj.website_url" class="flex gap-3">
                                    <a v-if="dj.instagram_url" :href="dj.instagram_url" target="_blank" class="p-2 rounded-full bg-purple-900/50 hover:bg-purple-900/80 transition-colors">
                                        <Instagram class="w-5 h-5 text-pink-400" />
                                    </a>
                                    <a v-if="dj.soundcloud_url" :href="dj.soundcloud_url" target="_blank" class="p-2 rounded-full bg-purple-900/50 hover:bg-purple-900/80 transition-colors">
                                        <Music class="w-5 h-5 text-pink-400" />
                                    </a>
                                    <a v-if="dj.website_url" :href="dj.website_url" target="_blank" class="p-2 rounded-full bg-purple-900/50 hover:bg-purple-900/80 transition-colors">
                                        <Globe class="w-5 h-5 text-pink-400" />
                                    </a>
                                </div>
                            </div>

                            <!-- Reviews Section -->
                            <div v-if="dj.reviews && dj.reviews.length > 0" class="bg-black/50 backdrop-blur-sm p-6 rounded-xl border border-purple-500/30">
                                <h2 class="text-xl font-bold mb-6">Reviews</h2>
                                <div class="space-y-4">
                                    <div v-for="review in dj.reviews.slice(0, 5)" :key="review.id" class="border-b border-purple-500/20 pb-4 last:border-0">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <div class="font-medium">{{ review.user?.name || 'Anonymous' }}</div>
                                                <div class="flex mt-1">
                                                    <Star v-for="i in 5" :key="i" class="w-4 h-4" :class="i <= review.rating ? 'text-yellow-400 fill-yellow-400' : 'text-gray-600'" />
                                                </div>
                                            </div>
                                            <div class="text-sm text-gray-500">{{ new Date(review.created_at).toLocaleDateString() }}</div>
                                        </div>
                                        <p class="text-gray-300">{{ review.comment }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Booking Card - EXACT match to Welcome page -->
                        <div class="lg:col-span-1">
                            <div class="bg-black/50 backdrop-blur-sm p-6 rounded-xl border border-purple-500/30 sticky top-24">
                                <h2 class="text-xl font-bold mb-4">Book This DJ</h2>

                                <div class="bg-purple-900/50 p-4 rounded-lg mb-6">
                                    <div class="flex justify-between items-start mb-4">
                                        <div>
                                            <p class="text-gray-300">{{ dj.location || 'Location TBD' }}</p>
                                            <p class="text-lg font-semibold text-white">{{ formatRate(dj.hourly_rate) }} per event</p>
                                        </div>
                                        <button class="p-2 rounded-full bg-purple-900/50 hover:bg-purple-900/80 transition-colors">
                                            <Heart class="w-5 h-5 text-pink-400" />
                                        </button>
                                    </div>

                                    <div class="space-y-2 text-sm text-gray-300 mb-4">
                                        <div class="flex items-center gap-2">
                                            <MapPin class="w-4 h-4" />
                                            {{ dj.location || 'Available nationwide' }}
                                        </div>
                                        <div>Minimum {{ dj.minimum_booking_hours }} hours</div>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <Link
                                        v-if="canLogin || canRegister"
                                        href="/register"
                                        class="block w-full px-4 py-3 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 rounded-lg font-medium text-center text-white"
                                    >
                                        Sign Up to Book
                                    </Link>
                                    <Link
                                        v-else
                                        :href="`/client/djs/${dj.id}/book`"
                                        class="block w-full px-4 py-3 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 rounded-lg font-medium text-center text-white"
                                    >
                                        Book Now
                                    </Link>
                                    <button class="w-full px-4 py-2 border border-pink-500 text-pink-500 rounded-lg hover:bg-pink-950 transition-colors">
                                        Message DJ
                                    </button>
                                </div>

                                <p class="text-xs text-gray-500 text-center mt-4">
                                    Free to request • Pay only when booking confirmed
                                </p>
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

<style>
.fill-yellow-400 {
    fill: #facc15;
}
</style>
