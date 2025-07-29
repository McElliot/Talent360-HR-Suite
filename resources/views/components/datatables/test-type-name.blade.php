<div class="flex items-center space-x-2">
    <span class="font-medium @if(!$active) line-through text-gray-500 @endif">{{ $name }}</span>
    @if(!$active)
    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">Inactive</span>
    @endif
</div>