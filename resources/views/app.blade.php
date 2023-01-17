<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap');

        /* from up */
        .from-up-enter-active {
            transition: all 300ms ease-out;
        }

        .from-up-leave-active {
            transition: all 200ms cubic-bezier(1, 0.5, 0.8, 1);
        }

        .from-up-enter-from,
        .from-up-leave-to {
            transform: translateY(-20px);
            opacity: 0;
        }

        /* from right */
        .from-right-enter-active {
            transition: all 220ms ease-out;
        }

        .from-right-leave-active {
            transition: all 150ms cubic-bezier(1, 0.5, 0.8, 1);
        }

        .from-right-enter-from,
        .from-right-leave-to {
            transform: translateX(20px);
            opacity: 0;
        }

        /* list */
        /* .list-move, */
        .list-enter-active {
            transition: all 0.2s ease;
        }

        .list-leave-active {
            transition: all 0.2s ease;
        }

        .list-enter-from,
        .list-leave-to {
            opacity: 0;
            transform: translateX(20px);
        }

        /* .list-leave-active {
            position: absolute;
        } */

        /* fade */
        .fade-enter-active,
        .fade-leave-active {
            transition: opacity 0.5s ease;
        }

        .fade-enter-from,
        .fade-leave-to {
            opacity: 0;
        }

        /* image */
        .image-enter-active,
        .image-leave-active {
            transition: opacity 0.250s ease;
        }

        .image-enter-from,
        .image-leave-to {
            opacity: 0;
        }

        /* width */
        ::-webkit-scrollbar {
            width: 7px;
        }

        /* Track */
        ::-webkit-scrollbar-track {
            background-color: transparent;
        }

        /* Handle */
        ::-webkit-scrollbar-thumb {
            border-radius: 8px;
            background: rgb(63 63 70);
        }

        /* Handle on hover */
        ::-webkit-scrollbar-thumb:hover {
            background: rgb(82 82 91);
        }
    </style>
</head>

<body class="font-sans antialiased" style="font-family: 'Roboto', sans-serif;">
    @inertia
</body>

</html>