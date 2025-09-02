<div class="flex flex-col max-w-full">
    <span class="font-medium text-gray-900 dark:text-white break-words whitespace-normal">
        {{ $question->question_text }}
    </span>
    @if($question->parent_question)
    <span class="text-xs text-gray-500 dark:text-gray-400 break-words whitespace-normal">
        Sub-question of: {{ Str::words($question->parent_question, 10, '...') }}
    </span>
    @endif
</div>