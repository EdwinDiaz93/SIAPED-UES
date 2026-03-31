<x-layouts::app.sidebar :title="$title ?? null">

    <flux:main>
        {{ $slot }}
    </flux:main>
    <div
    x-data="toastSystem()"
    x-on:notify.window="add($event.detail)"
    class="fixed top-5 right-5 z-50 flex flex-col gap-3 w-80"
>
    <template x-for="n in notifications" :key="n.id">
        <div
            x-show="n.show"
            x-transition:enter="transform ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-8 scale-95"
            x-transition:enter-end="opacity-100 translate-x-0 scale-100"
            x-transition:leave="transform ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0 scale-100"
            x-transition:leave-end="opacity-0 translate-x-8 scale-95"
            @mouseenter="pause(n)"
            @mouseleave="resume(n)"
            :class="getClass(n.type)"
            class="relative overflow-hidden flex items-start gap-3 p-4 rounded-xl shadow-lg backdrop-blur"
        >
            <!-- Icon -->
            <div x-html="getIcon(n.type)" class="mt-0.5"></div>

            <!-- Content -->
            <div class="flex-1">
                <p x-text="n.message" class="text-sm font-medium"></p>
            </div>

            <!-- Close -->
            <button @click="remove(n.id)" class="text-white/70  hover:text-white">
                ✕
            </button>

            <!-- Progress bar -->
            <div
                class="absolute bottom-0 left-0 h-1 bg-white/40"
                :style="`width: ${n.progress}%`"
            ></div>
        </div>
    </template>
</div>

<script>
function toastSystem() {
    return {
        notifications: [],

        add(data) {
            const id = Date.now();

            const toast = {
                id,
                type: data.type || 'info',
                message: data.message,
                duration: data.duration || 3000,
                progress: 100,
                interval: null,
                timeout: null,
                show: true
            };

            this.notifications.push(toast);
            this.startTimer(toast);
        },

        startTimer(toast) {
            const step = 100 / (toast.duration / 50);

            toast.interval = setInterval(() => {
                toast.progress -= step;
                if (toast.progress <= 0) {
                    this.remove(toast.id);
                }
            }, 50);
        },

        pause(toast) {
            clearInterval(toast.interval);
        },

        resume(toast) {
            this.startTimer(toast);
        },

        remove(id) {
            const toast = this.notifications.find(t => t.id === id);
            if (!toast) return;

            toast.show = false;

            setTimeout(() => {
                this.notifications = this.notifications.filter(t => t.id !== id);
            }, 200);
        },

        getClass(type) {
            return {
                'bg-green-800 text-white': type === 'success',
                'bg-red-800 text-white': type === 'error',
                'bg-yellow-400 text-black': type === 'warning',
                'bg-blue-500 text-white': type === 'info',
            }
        },

        getIcon(type) {
            const icons = {
                success: '✅',
                error: '❌',
                warning: '⚠️',
                info: 'ℹ️'
            };
            return icons[type] || icons.info;
        }
    }
}
</script>
</x-layouts::app.sidebar>


