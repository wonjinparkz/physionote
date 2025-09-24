<x-filament-panels::page>
    @php
        $categories = $this->getCategories();
        $selectedCategory = $this->getSelectedCategory();
    @endphp

    @if($categories->isNotEmpty())
        <div class="fi-tabs flex overflow-x-auto border-b border-gray-200 dark:border-white/10">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                @foreach($categories as $category)
                    <button
                        wire:click="selectCategory({{ $category->id }})"
                        type="button"
                        @class([
                            'group inline-flex items-center px-1 py-4 text-sm font-medium transition',
                            'border-b-2 border-primary-600 text-primary-600' => $selectedCategoryId == $category->id,
                            'border-b-2 border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' => $selectedCategoryId != $category->id,
                        ])
                    >
                        <span>{{ $category->name }}</span>
                        @if($category->subCategories_count ?? $category->subCategories()->count())
                            <span @class([
                                'ml-2 rounded-full px-2 py-0.5 text-xs',
                                'bg-primary-100 text-primary-600 dark:bg-primary-900 dark:text-primary-400' => $selectedCategoryId == $category->id,
                                'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' => $selectedCategoryId != $category->id,
                            ])>
                                {{ $category->subCategories_count ?? $category->subCategories()->count() }}
                            </span>
                        @endif
                    </button>
                @endforeach
            </nav>
        </div>

        @if($selectedCategory)
            <div class="mt-4">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ $selectedCategory->name }} - 서브 카테고리 목록
                    </h3>
                </div>

                {{ $this->table }}
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">카테고리가 없습니다</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">상단의 "카테고리 생성" 버튼을 클릭하여 시작하세요.</p>
        </div>
    @endif
</x-filament-panels::page>