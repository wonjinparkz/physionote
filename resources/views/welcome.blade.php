@extends('layouts.app')

@section('title', 'PhysioNote - 홈')

@section('content')
    <!-- Banner Section -->
    <div class="bg-[#4056FF] px-5 py-8 text-white flex items-center justify-between mb-6">
        <div class="flex-1">
            <h2 class="text-2xl font-bold mb-2">물리치료사・운동지도자를 위한<br>통합 플랫폼</h2>
            <p class="text-sm opacity-90 mb-4">문제 풀이, 임상 정보, 커뮤니티를 한 곳에서</p>
            <a href="/problems" class="inline-block px-5 py-2.5 bg-white text-[#4056FF] rounded-lg font-semibold text-sm transition-transform hover:-translate-y-0.5">문제 풀어보기</a>
        </div>
        <div class="w-30 h-20 opacity-80">
            <svg viewBox="0 0 300 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect x="50" y="40" width="200" height="120" rx="10" fill="#E3F2FD"/>
                <circle cx="150" cy="80" r="20" fill="#4A90E2"/>
                <rect x="80" y="110" width="140" height="8" rx="4" fill="#BBDEFB"/>
                <rect x="80" y="125" width="100" height="8" rx="4" fill="#BBDEFB"/>
            </svg>
        </div>
    </div>

    <!-- Card News Section -->
    <div class="mb-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 px-5">카드뉴스</h2>

        <!-- Content Carousel -->
        @if($contents->isNotEmpty())
            <div class="overflow-x-scroll overflow-y-hidden scrollbar-hide" style="cursor: grab; user-select: none; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none;">
                <div class="flex gap-3 pl-5 pr-5" style="width: max-content; padding-bottom: 2px;">
                    @foreach($contents as $content)
                        <div class="cursor-pointer flex-shrink-0 card-item"
                             style="width: calc(100vw - 60px); max-width: 320px;"
                             data-url="/content/{{ $content->id }}">
                            @if($content->thumbnail)
                                <img src="{{ Storage::url($content->thumbnail) }}"
                                     alt="{{ $content->title }}"
                                     class="w-full h-36 object-cover rounded-lg">
                            @else
                                <div class="w-full h-36 bg-gray-200 flex items-center justify-center rounded-lg">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif

                            <div class="mt-3">
                                <div class="mb-2">
                                    <h3 class="text-sm font-semibold text-gray-900 line-clamp-2">
                                        {{ $content->title }}
                                    </h3>
                                    @if($content->badge)
                                        @php
                                            $badges = [
                                                'popular' => ['text' => '인기', 'color' => 'bg-red-100 text-red-800'],
                                                'essential' => ['text' => '필수', 'color' => 'bg-yellow-100 text-yellow-800'],
                                                'basic' => ['text' => '기본', 'color' => 'bg-gray-100 text-gray-800'],
                                                'new' => ['text' => '신규', 'color' => 'bg-green-100 text-green-800'],
                                                'updated' => ['text' => '업데이트', 'color' => 'bg-blue-100 text-blue-800'],
                                                'premium' => ['text' => '프리미엄', 'color' => 'bg-purple-100 text-purple-800'],
                                            ];
                                            $badge = $badges[$content->badge] ?? ['text' => '', 'color' => ''];
                                        @endphp
                                        @if($badge['text'])
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $badge['color'] }} mt-1">
                                                {{ $badge['text'] }}
                                            </span>
                                        @endif
                                    @endif
                                </div>

                                <div class="flex items-center justify-between text-xs">
                                    @if($content->subCategory)
                                        <div class="text-gray-500">
                                            {{ $content->subCategory->name }}
                                        </div>
                                    @endif
                                    <div class="text-gray-400">
                                        {{ $content->created_at->format('Y.m.d') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            @if($contents->hasPages())
                <div class="flex justify-center mb-6">
                    {{ $contents->links('pagination::tailwind') }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">콘텐츠가 없습니다</h3>
                <p class="mt-1 text-sm text-gray-500">아직 등록된 카드뉴스가 없습니다.</p>
            </div>
        @endif
    </div>
    <script>
    // Enable mouse drag scrolling for desktop - wait for DOM
    window.addEventListener('DOMContentLoaded', function() {
        const scrollContainer = document.querySelector('.overflow-x-scroll');
        if (!scrollContainer) {
            console.log('No scroll container found');
            return;
        }

        let isDown = false;
        let startX;
        let scrollLeft;
        let moved = false;

        // Mouse wheel scrolling for desktop
        scrollContainer.addEventListener('wheel', (e) => {
            e.preventDefault();
            scrollContainer.scrollLeft += e.deltaY;
        });

        // Mouse drag events
        scrollContainer.addEventListener('mousedown', (e) => {
            isDown = true;
            moved = false;
            scrollContainer.classList.add('active');
            scrollContainer.style.cursor = 'grabbing';
            startX = e.pageX - scrollContainer.offsetLeft;
            scrollLeft = scrollContainer.scrollLeft;
        });

        document.addEventListener('mouseleave', () => {
            isDown = false;
            scrollContainer.style.cursor = 'grab';
            scrollContainer.classList.remove('active');
        });

        document.addEventListener('mouseup', () => {
            isDown = false;
            scrollContainer.style.cursor = 'grab';
            scrollContainer.classList.remove('active');
        });

        scrollContainer.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - scrollContainer.offsetLeft;
            const walk = (x - startX) * 2;
            if (Math.abs(walk) > 5) {
                moved = true;
            }
            scrollContainer.scrollLeft = scrollLeft - walk;
        });

        // Handle card clicks with delay
        const cards = scrollContainer.querySelectorAll('.card-item');
        cards.forEach(card => {
            card.addEventListener('click', (e) => {
                if (!moved) {
                    const url = card.dataset.url;
                    if (url) {
                        window.location.href = url;
                    }
                }
            });
        });
    });
    </script>

    <style>
    @media (max-width: 400px) {
        .grid-cols-4 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
    }

    .scrollbar-hide::-webkit-scrollbar {
        display: none;  /* Chrome, Safari and Opera */
    }

    /* Smooth scrolling */
    .overflow-x-scroll {
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
        overflow-x: scroll !important;
        overflow-y: hidden !important;
    }

    /* Prevent text selection while dragging */
    .overflow-x-scroll.active {
        scroll-behavior: auto;
    }

    /* Only apply snap scrolling on mobile */
    @media (max-width: 768px) {
        .overflow-x-scroll {
            scroll-snap-type: x mandatory;
        }

        .card-item {
            scroll-snap-align: start;
        }
    }
</style>
@endsection