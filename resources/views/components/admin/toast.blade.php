<div x-data="{ show: false, message: '', type: 'success' }"
     @toast.window="
        show = true;
        message = $event.detail.message;
        type = $event.detail.type || 'success';
        setTimeout(() => show = false, 3000);
     "
     x-show="show"
     x-transition
     class="fixed bottom-4 right-4 z-50"
     style="display: none">

    <div :class="{
        'bg-green-50 border-green-200 text-green-800': type === 'success',
        'bg-red-50 border-red-200 text-red-800': type === 'error',
        'bg-blue-50 border-blue-200 text-blue-800': type === 'info',
        'bg-yellow-50 border-yellow-200 text-yellow-800': type === 'warning'
    }" class="border rounded-lg px-4 py-3 shadow-lg max-w-sm">
        <p class="text-sm font-medium" x-text="message"></p>
    </div>
</div>
