<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'PhysioNote')</title>

    <!-- Open Graph Meta Tags -->
    @yield('meta')

    <link href="{{ mix('css/app.css') }}" rel="stylesheet">

    <style>
        /* Max width container */
        .app-container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
            position: relative;
        }

        @media (min-width: 500px) {
            .app-container {
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
        }
        /* Custom scrollbar hide */
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        /* Smooth scrolling for carousel */
        .overflow-x-scroll {
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
        }

        /* Content body styling */
        .content-body {
            line-height: 1.8;
        }

        .content-body h1,
        .content-body h2,
        .content-body h3,
        .content-body h4,
        .content-body h5,
        .content-body h6 {
            margin-top: 1.5rem;
            margin-bottom: 1rem;
            font-weight: bold;
        }

        .content-body p {
            margin-bottom: 1rem;
        }

        .content-body ul,
        .content-body ol {
            margin-bottom: 1rem;
            padding-left: 2rem;
        }

        .content-body img {
            max-width: 100%;
            height: auto;
            margin: 1rem 0;
        }

        .content-body blockquote {
            border-left: 4px solid #e5e7eb;
            padding-left: 1rem;
            margin: 1rem 0;
            color: #6b7280;
        }

        .content-body table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
        }

        .content-body table th,
        .content-body table td {
            border: 1px solid #e5e7eb;
            padding: 0.5rem;
            text-align: left;
        }

        .content-body table th {
            background-color: #f9fafb;
            font-weight: bold;
        }

        /* Line clamp utility */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-gray-100">
    <div class="app-container">
        <!-- Header Component -->
        <x-header
            type="content"
            :title="View::getSection('header_title')"
            :backAction="View::getSection('back_action', 'history.back()')"
        />

        <!-- Main Content -->
        <main class="pb-20">
            @yield('content')
        </main>

    </div><!-- End app-container -->

    @stack('scripts')
</body>
</html>