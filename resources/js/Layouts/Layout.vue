<script setup>
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <div class="m-4">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
            <div class="container">
                <Link :href="route('freesound.index')" class="navbar-brand">
                    Random Soundboard
                </Link>
            </div>
        </nav>

        <main class="container">
            <slot />
        </main>

        <footer class="text-center text-muted mt-5 py-3 border-top">
            &copy; {{ new Date().getFullYear() }} — Powered by
            <a href="https://freesound.org" target="_blank">Freesound API</a>
        </footer>
    </div>
</template>
