@extends('layouts.content')

@section('title', $content->title . ' - PhysioNote')

@section('meta')
    <meta property="og:title" content="{{ $content->title }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($content->body), 160) }}">
    @if($content->thumbnail)
    <meta property="og:image" content="{{ url(Storage::url($content->thumbnail)) }}">
    @endif
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="article">
@endsection

@section('header_title', $content->title)

@section('header_action')
    <!-- Share Button -->
    <button onclick="shareContent()" class="p-2 -mr-2">
        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m9.032 4.024a3 3 0 10-4.056-4.414 3 3 0 004.056 4.414zm0 0a3 3 0 10-4.056 4.414 3 3 0 004.056-4.414zm-9.032 0a3 3 0 110-2.684m9.032-4.024a3 3 0 004.056-4.414 3 3 0 00-4.056 4.414z"></path>
        </svg>
    </button>
@endsection

@section('content')
    <!-- Thumbnail -->
    @if($content->thumbnail)
        <div class="w-full">
            <img src="{{ Storage::url($content->thumbnail) }}"
                 alt="{{ $content->title }}"
                 class="w-full h-auto">
        </div>
    @endif

    <!-- Content Info -->
    <div class="px-5 py-4 bg-white">
        <!-- Badge and Category -->
        <div class="flex items-center gap-2 mb-3">
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
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge['color'] }}">
                        {{ $badge['text'] }}
                    </span>
                @endif
            @endif

            @if($content->subCategory)
                <span class="text-xs text-gray-500">
                    {{ $content->subCategory->category->name }} > {{ $content->subCategory->name }}
                </span>
            @endif
        </div>

        <!-- Title -->
        <h1 class="text-xl font-bold text-gray-900 mb-2">{{ $content->title }}</h1>

        <!-- Date -->
        <div class="text-xs text-gray-400 mb-4">
            {{ $content->created_at->format('Y년 m월 d일') }}
        </div>

        <!-- Content Body -->
        <div class="content-body text-gray-700">
            {!! $content->body !!}
        </div>

        <!-- Custom Data if exists -->
        @if($content->custom_data && count($content->custom_data) > 0)
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">추가 정보</h3>
                <div class="space-y-2">
                    @foreach($content->custom_data as $key => $value)
                        <div class="flex items-start">
                            <span class="text-sm text-gray-500 min-w-[80px]">{{ $key }}:</span>
                            <span class="text-sm text-gray-700 flex-1">{{ $value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Related Contents -->
    @if($relatedContents->isNotEmpty())
        <div class="mt-6 bg-white">
            <h2 class="text-base font-bold text-gray-900 px-5 pt-4 pb-3">관련 콘텐츠</h2>
            <div class="overflow-x-scroll scrollbar-hide pb-4">
                <div class="flex gap-3 px-5" style="width: max-content;">
                    @foreach($relatedContents as $related)
                        <a href="/content/{{ $related->id }}"
                           class="block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden flex-shrink-0"
                           style="width: 200px;">
                            @if($related->thumbnail)
                                <img src="{{ Storage::url($related->thumbnail) }}"
                                     alt="{{ $related->title }}"
                                     class="w-full h-28 object-cover">
                            @else
                                <div class="w-full h-28 bg-gray-200 flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="p-3">
                                <h3 class="text-xs font-medium text-gray-900 line-clamp-2 mb-1">
                                    {{ $related->title }}
                                </h3>
                                <div class="text-xs text-gray-400">
                                    {{ $related->created_at->format('Y.m.d') }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endsection

<script>
    // Share functionality
    function shareContent() {
        const title = "{{ $content->title }}";
        const url = window.location.href;

        if (navigator.share) {
            // Use native share API if available (mobile)
            navigator.share({
                title: title,
                url: url
            }).catch((error) => console.log('Error sharing:', error));
        } else {
            // Fallback: copy URL to clipboard
            navigator.clipboard.writeText(url).then(() => {
                alert('링크가 복사되었습니다.');
            }).catch((error) => {
                console.error('Error copying to clipboard:', error);
                // Fallback for older browsers
                const textArea = document.createElement("textarea");
                textArea.value = url;
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                alert('링크가 복사되었습니다.');
            });
        }
    }
</script>