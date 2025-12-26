<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import {
    Menu, X, Home, Music, Users, ListMusic, Calendar, Camera,
    Search, Star, Heart, ChevronDown, Headphones, SlidersHorizontal
} from 'lucide-vue-next';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});

// Mobile menu state
const isMenuOpen = ref(false);

// Scroll snap state
const snapScrolling = ref(true);
const scrollContainer = ref(null);
const sections = ref([]);
const currentSectionIndex = ref(0);

// DJ Profiles
const djs = ref([
    {
        name: "DJ Pulse",
        specialty: "House & EDM Specialist",
        image: "/placeholder.svg?height=500&width=400",
        bio: "With over 10 years of experience, DJ Pulse brings energy and excitement to every event. Known for seamless transitions and reading the crowd perfectly.",
        genres: ["House", "EDM", "Progressive", "Electro"],
        experience: "10+ years",
        location: "Los Angeles, CA",
        price: "$300-$500 per event",
        rating: 5,
        reviews: 48
    },
    {
        name: "DJ Vinyl Queen",
        specialty: "Retro & Disco Master",
        image: "/placeholder.svg?height=500&width=400",
        bio: "A true vinyl enthusiast with a massive collection of disco and retro classics. DJ Vinyl Queen creates an authentic disco experience that gets everyone dancing.",
        genres: ["Disco", "Funk", "80s Classics", "Retro"],
        experience: "15+ years",
        location: "New York, NY",
        price: "$400-$700 per event",
        rating: 4,
        reviews: 36
    },
    {
        name: "DJ Rhythm",
        specialty: "Hip-Hop & R&B Expert",
        image: "/placeholder.svg?height=500&width=400",
        bio: "DJ Rhythm specializes in creating the perfect urban soundtrack for your event. From old school classics to the latest hits, he knows how to keep the floor packed.",
        genres: ["Hip-Hop", "R&B", "Trap", "Urban"],
        experience: "8+ years",
        location: "Miami, FL",
        price: "$350-$600 per event",
        rating: 4,
        reviews: 29
    }
]);

// Toggle scroll mode
const toggleScrollMode = () => {
    snapScrolling.value = !snapScrolling.value;
};

// Scroll to section
const scrollToSection = (sectionId) => {
    const section = document.getElementById(sectionId);
    if (section) {
        section.scrollIntoView({ behavior: 'smooth' });
    }
};

onMounted(() => {
    // Make all sections visible on mount for simpler implementation
    document.querySelectorAll('.section').forEach(section => {
        section.classList.add('section-visible');
    });
});
</script>

<template>
    <Head title="DJ Agency" />

    <div class="min-h-screen bg-black text-white">
        <!-- Mobile Navigation -->
        <header class="fixed top-0 left-0 right-0 z-50 bg-black/80 backdrop-blur-md border-b border-purple-500/20">
            <div class="container mx-auto px-4 h-16 flex items-center justify-between">
                <a href="/" class="flex items-center gap-2">
                    <!-- DJ Council Logo -->
                    <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <Headphones class="h-6 w-6 text-white" />
                    </div>
                    <span class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-purple-500">
            DJ Council
          </span>
                </a>

                <nav class="hidden md:flex items-center space-x-6">
                    <a
                        href="#home"
                        @click.prevent="scrollToSection('home')"
                        class="text-white hover:text-pink-400 transition-colors"
                    >
                        Home
                    </a>
                    <a
                        href="#djs"
                        @click.prevent="scrollToSection('djs')"
                        class="text-white hover:text-pink-400 transition-colors"
                    >
                        Find DJs
                    </a>
                    <a
                        href="#book"
                        @click.prevent="scrollToSection('book')"
                        class="text-white hover:text-pink-400 transition-colors"
                    >
                        Book
                    </a>

                    <div v-if="canLogin" class="ml-4">
                        <Link v-if="$page.props.auth && $page.props.auth.user" :href="route('dashboard')" class="px-4 py-2 rounded-lg bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white font-medium">
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link :href="route('login')" class="mr-2 text-white hover:text-pink-400 transition-colors">
                                Log in
                            </Link>
                            <Link v-if="canRegister" :href="route('register')" class="px-4 py-2 rounded-lg bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white font-medium">
                                Sign Up
                            </Link>
                        </template>
                    </div>
                </nav>

                <button
                    @click="isMenuOpen = !isMenuOpen"
                    class="md:hidden text-white p-2"
                >
                    <Menu v-if="!isMenuOpen" class="h-6 w-6" />
                    <X v-else class="h-6 w-6" />
                </button>
            </div>

            <!-- Mobile Menu -->
            <div
                v-if="isMenuOpen"
                class="md:hidden bg-black/95 border-b border-purple-500/30 overflow-hidden transition-all duration-300"
            >
                <nav class="flex flex-col gap-4 p-6">
                    <a
                        href="#home"
                        @click.prevent="scrollToSection('home'); isMenuOpen = false"
                        class="flex items-center gap-2 text-lg hover:text-pink-400 transition-colors"
                    >
                        <Home class="h-5 w-5" />
                        Home
                    </a>
                    <a
                        href="#djs"
                        @click.prevent="scrollToSection('djs'); isMenuOpen = false"
                        class="flex items-center gap-2 text-lg hover:text-pink-400 transition-colors"
                    >
                        <Users class="h-5 w-5" />
                        Find DJs
                    </a>
                    <a
                        href="#book"
                        @click.prevent="scrollToSection('book'); isMenuOpen = false"
                        class="flex items-center gap-2 text-lg hover:text-pink-400 transition-colors"
                    >
                        <Calendar class="h-5 w-5" />
                        Book
                    </a>

                    <div v-if="canLogin" class="mt-4">
                        <Link v-if="$page.props.auth && $page.props.auth.user" :href="route('dashboard')" class="px-4 py-2 rounded-lg bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white font-medium">
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link :href="route('login')" class="flex items-center gap-2 text-lg hover:text-pink-400 transition-colors mb-2">
                                Log in
                            </Link>
                            <Link v-if="canRegister" :href="route('register')" class="px-4 py-2 rounded-lg bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white font-medium">
                                Sign Up
                            </Link>
                        </template>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main>
            <!-- Hero Section -->
            <section id="home" class="section min-h-screen flex flex-col items-center justify-center pt-16 bg-gradient-to-b from-black to-purple-950/50">
                <div class="container mx-auto px-4 text-center">
                    <div class="flex items-center justify-center mb-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                            <Headphones class="h-10 w-10 text-white" />
                        </div>
                        <h1 class="text-5xl md:text-7xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500">
                            DJ Council
                        </h1>
                    </div>
                    <p class="text-xl md:text-2xl mb-8 max-w-2xl mx-auto">
                        Connect with top DJs, manage bookings, and build your reputation
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <button class="px-6 py-3 rounded-lg bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 font-medium">
                            Book a DJ
                        </button>
                        <button class="px-6 py-3 rounded-lg border border-pink-500 text-pink-500 hover:bg-pink-950">
                            Join as DJ
                        </button>
                    </div>

                </div>
            </section>

            <!-- DJ Profiles Section -->
            <section id="djs" class="section min-h-screen py-16 px-4 bg-gradient-to-b from-purple-950/50 to-black">
                <div class="max-w-6xl mx-auto pt-16">
                    <h2 class="text-3xl md:text-4xl font-bold mb-6 text-center">Find Your Perfect DJ</h2>
                    <p class="text-center text-gray-300 mb-12 max-w-3xl mx-auto">
                        Browse our network of professional DJs, filter by genre, price, and location.
                        All DJs are vetted and rated by our community.
                    </p>

                    <!-- Filter Controls -->
                    <div class="bg-black/50 backdrop-blur-sm p-6 rounded-xl border border-purple-500/30 mb-10">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Genre Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Genre</label>
                                <select class="w-full px-3 py-2 bg-purple-950/50 border border-purple-500/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 text-white">
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
                                <select class="w-full px-3 py-2 bg-purple-950/50 border border-purple-500/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 text-white">
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
                                <select class="w-full px-3 py-2 bg-purple-950/50 border border-purple-500/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 text-white">
                                    <option value="">All Locations</option>
                                    <option>Los Angeles</option>
                                    <option>New York</option>
                                    <option>Miami</option>
                                    <option>Chicago</option>
                                    <option>Las Vegas</option>
                                </select>
                            </div>

                            <!-- Reputation -->
                            <div>
                                <label class="block text-sm font-medium text-gray-400 mb-2">Reputation</label>
                                <select class="w-full px-3 py-2 bg-purple-950/50 border border-purple-500/30 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-500 text-white">
                                    <option value="">Any Rating</option>
                                    <option>5 Stars</option>
                                    <option>4+ Stars</option>
                                    <option>3+ Stars</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- DJ Profiles Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div v-for="(dj, index) in djs" :key="index" class="group">
                            <div class="relative overflow-hidden rounded-xl aspect-[3/4] mb-4">
                                <img
                                    :src="dj.image"
                                    :alt="dj.name"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-70"></div>
                                <div class="absolute bottom-0 left-0 right-0 p-6">
                                    <div class="flex items-center mb-2">
                                        <div class="flex">
                                            <Star v-for="i in dj.rating" :key="i" class="w-5 h-5 text-yellow-400 fill-yellow-400" />
                                            <Star v-for="i in 5-dj.rating" :key="i+5" class="w-5 h-5 text-gray-600" />
                                        </div>
                                        <span class="ml-2 text-sm text-gray-300">({{ dj.reviews }})</span>
                                    </div>
                                    <h3 class="text-2xl font-bold mb-1">{{ dj.name }}</h3>
                                    <p class="text-pink-400">{{ dj.specialty }}</p>
                                </div>
                            </div>

                            <div class="bg-black/50 backdrop-blur-sm p-6 rounded-xl border border-purple-500/30">
                                <div class="flex justify-between items-start mb-4">
                                    <div>
                                        <p class="text-gray-300">{{ dj.location }}</p>
                                        <p class="text-lg font-semibold text-white">{{ dj.price }}</p>
                                    </div>
                                    <button class="p-2 rounded-full bg-purple-900/50 hover:bg-purple-900/80 transition-colors">
                                        <Heart class="w-5 h-5 text-pink-400" />
                                    </button>
                                </div>

                                <p class="text-gray-300 mb-4">{{ dj.bio }}</p>

                                <h4 class="font-semibold mb-2 text-pink-400">Top Genres</h4>
                                <div class="flex flex-wrap gap-2 mb-4">
                  <span
                      v-for="(genre, i) in dj.genres"
                      :key="i"
                      class="px-3 py-1 bg-purple-900/50 rounded-full text-sm"
                  >
                    {{ genre }}
                  </span>
                                </div>

                                <div class="flex gap-2">
                                    <button class="flex-1 px-4 py-2 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 rounded-lg font-medium">
                                        Book Now
                                    </button>
                                    <button class="px-4 py-2 border border-pink-500 text-pink-500 rounded-lg hover:bg-pink-950 transition-colors">
                                        View Profile
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Book Section -->
            <section id="book" class="section min-h-screen py-16 px-4 bg-gradient-to-b from-purple-950/50 to-black">
                <div class="max-w-6xl mx-auto pt-16">
                    <h2 class="text-3xl md:text-4xl font-bold mb-6 text-center">Book Your DJ</h2>
                    <p class="text-center text-gray-300 mb-12 max-w-3xl mx-auto">
                        Book your favorite DJ in minutes. We handle the contracts, payments, and communication.
                        Only 10% booking fee, the lowest in the industry.
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
                        <div class="bg-black/50 backdrop-blur-sm p-6 rounded-xl border border-purple-500/30 text-center h-auto">
                            <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <Search class="w-8 h-8 text-white" />
                            </div>
                            <h3 class="text-xl font-bold mb-3">Find Your DJ</h3>
                            <p class="text-gray-300">
                                Browse profiles, read reviews, and find the perfect DJ for your event based on genre, price, and location.
                            </p>
                        </div>

                        <div class="bg-black/50 backdrop-blur-sm p-6 rounded-xl border border-purple-500/30 text-center h-auto">
                            <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <Calendar class="w-8 h-8 text-white" />
                            </div>
                            <h3 class="text-xl font-bold mb-3">Book & Pay</h3>
                            <p class="text-gray-300">
                                Select your date, venue, and customize your playlist. Secure your booking with our simple payment process.
                            </p>
                        </div>

                        <div class="bg-black/50 backdrop-blur-sm p-6 rounded-xl border border-purple-500/30 text-center h-auto">
                            <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <Music class="w-8 h-8 text-white" />
                            </div>
                            <h3 class="text-xl font-bold mb-3">Enjoy Your Event</h3>
                            <p class="text-gray-300">
                                Your DJ arrives on time, fully prepared with your playlist. After the event, leave a review to help the community.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-black border-t border-purple-500/30 pt-12 pb-6">
            <div class="container mx-auto px-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <Headphones class="h-6 w-6 text-white" />
                            </div>
                            <span class="text-xl font-bold text-white">DJ Council</span>
                        </div>
                        <p class="text-gray-400 mb-4">Connecting DJs and event organizers since 2023. The premier platform for DJ bookings and community.</p>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold mb-4 text-white">Quick Links</h4>
                        <ul class="space-y-2">
                            <li>
                                <a href="#home" @click.prevent="scrollToSection('home')" class="text-gray-400 hover:text-pink-500 transition-colors">
                                    Home
                                </a>
                            </li>
                            <li>
                                <a href="#djs" @click.prevent="scrollToSection('djs')" class="text-gray-400 hover:text-pink-500 transition-colors">
                                    Find DJs
                                </a>
                            </li>
                            <li>
                                <a href="#book" @click.prevent="scrollToSection('book')" class="text-gray-400 hover:text-pink-500 transition-colors">
                                    Book
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold mb-4 text-white">Contact Us</h4>
                        <ul class="space-y-3">
                            <li class="flex items-start">
                                <span class="text-gray-400">info@djcouncil.com</span>
                            </li>
                        </ul>
                    </div>

                    <div>
                        <h4 class="text-lg font-semibold mb-4 text-white">Newsletter</h4>
                        <p class="text-gray-400 mb-4">Subscribe to our newsletter for the latest DJ news and exclusive offers.</p>
                        <form class="flex">
                            <input
                                type="email"
                                placeholder="Your email"
                                class="px-4 py-2 bg-purple-950/50 border border-purple-500/30 rounded-l-lg focus:outline-none focus:ring-2 focus:ring-pink-500 w-full"
                            />
                            <button class="px-4 py-2 rounded-r-lg bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white font-medium">
                                Subscribe
                            </button>
                        </form>
                    </div>
                </div>

                <div class="border-t border-purple-500/20 pt-6 text-center text-gray-500 text-sm">
                    <p>&copy; {{ new Date().getFullYear() }} DJ Council. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style>
/* Section styles */
.section {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.8s ease, transform 0.8s ease;
}

.section-visible {
    opacity: 1;
    transform: translateY(0);
}

/* Handle the fill attribute for stars */
.fill-yellow-400 {
    fill: #facc15;
}
</style>
