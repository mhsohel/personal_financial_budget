<script setup>
import { ref, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, onMessage } from 'firebase/messaging';

const showingNavigationDropdown = ref(false);

const page = usePage();
const firebaseConfig = page.props.firebase;
const notificationPermission = ref(typeof Notification !== 'undefined' ? Notification.permission : 'denied');
const isSubscribing = ref(false);

const requestNotificationPermission = async () => {
    if (typeof Notification === 'undefined') return;
    
    isSubscribing.value = true;
    try {
        const permission = await Notification.requestPermission();
        notificationPermission.value = permission;
        
        if (permission === 'granted') {
            await registerPush();
        }
    } catch (error) {
        console.error('Error requesting notification permission:', error);
    } finally {
        isSubscribing.value = false;
    }
};

const registerPush = async () => {
    if (!firebaseConfig || !firebaseConfig.apiKey) {
        console.warn('Firebase configuration is not set.');
        return;
    }
    
    try {
        // Register the service worker with Firebase config parameters as query arguments
        const queryString = new URLSearchParams(firebaseConfig).toString();
        const registration = await navigator.serviceWorker.register(
            `/firebase-messaging-sw.js?${queryString}`,
            { scope: '/' }
        );
        
        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);
        
        const token = await getToken(messaging, {
            serviceWorkerRegistration: registration,
            vapidKey: firebaseConfig.vapidKey
        });
        
        if (token) {
            // Save the token to the backend
            await window.axios.post(route('profile.fcm-token'), { fcm_token: token });
            console.log('FCM Token registered successfully.');
        } else {
            console.warn('No registration token available. Request permission to generate one.');
        }
        
        // Listen for foreground messages
        onMessage(messaging, (payload) => {
            console.log('Message received in foreground: ', payload);
            if (Notification.permission === 'granted' && registration && payload) {
                const title = payload.notification?.title || payload.data?.title || 'Reminder Alert';
                const body = payload.notification?.body || payload.data?.body || 'You have an upcoming reminder.';
                registration.showNotification(title, {
                    body: body,
                    icon: '/favicon.ico',
                    badge: '/favicon.ico',
                    data: payload.data
                });
            }
        });
        
    } catch (err) {
        console.error('An error occurred while retrieving token:', err);
    }
};

onMounted(() => {
    if (typeof Notification !== 'undefined') {
        if (Notification.permission === 'granted') {
            registerPush();
        } else if (Notification.permission === 'default') {
            requestNotificationPermission();
        }
    }
});
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100 dark:bg-slate-950">
            <nav
                class="border-b border-gray-100 bg-white dark:border-slate-800 dark:bg-slate-900"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 to-violet-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <span class="text-xl font-black bg-gradient-to-r from-white via-slate-100 to-indigo-400 bg-clip-text text-transparent tracking-tight">FinFlow</span>
                                    </div>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    Dashboard
                                </NavLink>
                                <NavLink
                                    :href="route('reports.index')"
                                    :active="route().current('reports.index')"
                                >
                                    Reports & Projections
                                </NavLink>
                                <NavLink
                                    :href="route('reports.forecast')"
                                    :active="route().current('reports.forecast')"
                                >
                                    Forecast
                                </NavLink>
                                <NavLink
                                    :href="route('licenses.index')"
                                    :active="route().current('licenses.index')"
                                    
                                >
                                    SaaS Licenses
                                </NavLink>
                                <NavLink
                                    :href="route('loans.index')"
                                    :active="route().current('loans.index')"
                                >
                                    Loans & Debts
                                </NavLink>
                                <NavLink
                                    :href="route('recurring.index')"
                                    :active="route().current('recurring.index')"
                                >
                                    Recurring Items
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center gap-3">
                            <!-- Push Notification Toggle -->
                            <button
                                v-if="firebaseConfig && firebaseConfig.apiKey"
                                @click="requestNotificationPermission"
                                :disabled="isSubscribing"
                                class="p-2 text-gray-500 hover:text-indigo-650 dark:hover:text-indigo-400 focus:outline-none transition relative group rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800"
                                :class="{
                                    'text-indigo-600 dark:text-indigo-400 border-indigo-200 dark:border-indigo-900/50 bg-indigo-50/30 dark:bg-indigo-950/20': notificationPermission === 'granted',
                                    'text-amber-500 border-amber-200 dark:border-amber-900/50 bg-amber-50/30 dark:bg-amber-950/20': notificationPermission === 'default',
                                    'text-gray-400 opacity-60 cursor-not-allowed': notificationPermission === 'denied'
                                }"
                                :title="
                                    notificationPermission === 'granted'
                                        ? 'Reminders push notifications active'
                                        : notificationPermission === 'default'
                                            ? 'Click to enable reminders push notifications'
                                            : 'Notifications are blocked in your browser settings'
                                "
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    :class="{ 'animate-bounce': notificationPermission === 'default' && !isSubscribing }"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                    />
                                    <path
                                        v-if="notificationPermission === 'denied'"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 3l18 18"
                                        class="stroke-red-500"
                                    />
                                </svg>
                                
                                <span class="absolute top-full mt-2 hidden group-hover:block bg-slate-900 dark:bg-slate-800 text-white text-[10px] py-1 px-2 rounded whitespace-nowrap z-50 shadow-md">
                                    {{ 
                                        notificationPermission === 'granted' 
                                            ? 'Push Notifications: Active' 
                                            : notificationPermission === 'default' 
                                                ? 'Enable Push Notifications' 
                                                : 'Notifications Blocked' 
                                    }}
                                </span>
                            </button>

                            <!-- Settings Dropdown -->
                            <div class="relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-700 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none dark:bg-slate-900 dark:text-gray-600 dark:hover:text-gray-300"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            Log Out
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-600 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-700 focus:bg-gray-100 focus:text-gray-700 focus:outline-none dark:text-gray-700 dark:hover:bg-gray-900 dark:hover:text-gray-600 dark:focus:bg-gray-900 dark:focus:text-gray-600"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('reports.index')"
                            :active="route().current('reports.index')"
                        >
                            Reports & Projections
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('reports.forecast')"
                            :active="route().current('reports.forecast')"
                        >
                            Forecast
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('licenses.index')"
                            :active="route().current('licenses.index')"
                        >
                            SaaS Licenses
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('loans.index')"
                            :active="route().current('loans.index')"
                        >
                            Loans & Debts
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('recurring.index')"
                            :active="route().current('recurring.index')"
                        >
                            Recurring Items
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 pb-1 pt-4 dark:border-gray-600"
                    >
                        <div class="px-4 flex items-center justify-between">
                            <div>
                                <div
                                    class="text-base font-medium text-gray-800 dark:text-gray-200"
                                >
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="text-sm font-medium text-gray-500">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>
                            
                            <!-- Mobile Push Notification toggle button -->
                            <button
                                v-if="firebaseConfig && firebaseConfig.apiKey"
                                @click="requestNotificationPermission"
                                :disabled="isSubscribing"
                                class="p-2 bg-slate-100 dark:bg-slate-900 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-950/20 transition flex items-center gap-1.5 border border-slate-200 dark:border-slate-800"
                                :class="{
                                    'text-indigo-650 dark:text-indigo-400 border-indigo-200 dark:border-indigo-900/50 bg-indigo-50/30 dark:bg-indigo-950/20': notificationPermission === 'granted',
                                    'text-amber-500 border-amber-200 dark:border-amber-900/50 bg-amber-50/30 dark:bg-amber-950/20': notificationPermission === 'default',
                                    'text-gray-400 opacity-60': notificationPermission === 'denied'
                                }"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4.5 w-4.5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                    />
                                    <path
                                        v-if="notificationPermission === 'denied'"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 3l18 18"
                                        class="stroke-red-500"
                                    />
                                </svg>
                                <span class="text-[10px] font-bold uppercase tracking-wider">
                                    {{ 
                                        notificationPermission === 'granted' 
                                            ? 'Active' 
                                            : notificationPermission === 'default' 
                                                ? 'Enable' 
                                                : 'Blocked' 
                                    }}
                                </span>
                            </button>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow dark:bg-slate-900"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
