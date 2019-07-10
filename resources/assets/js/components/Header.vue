<style>

</style>

<template>
    <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <div class="container">
            <router-link class="navbar-brand" to="/">My Events App</router-link>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <li>
                        <router-link to="/events" class="nav-link">Events</router-link>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</template>

<script>
    export default {
        name: 'app-header',
        methods: {
        },
        computed: {
        }
    }
</script>
