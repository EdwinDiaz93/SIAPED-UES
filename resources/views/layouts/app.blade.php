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

<div
    x-data="confirmDialog()"
    x-on:confirm-action.window="open($event.detail)"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-100 flex items-center justify-center bg-black/50"
    style="display: none"
>
    <div
        x-show="show"
        x-transition:enter="transform ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        @click.outside="cancel()"
        class="bg-white dark:bg-zinc-800 rounded-xl shadow-xl w-full max-w-sm p-6"
    >
        <h3 class="text-lg font-bold mb-2 text-[#960000]">Confirmar acción</h3>
        <p class="text-sm text-zinc-600 dark:text-zinc-300 mb-6" x-text="message"></p>
        <div class="flex justify-end gap-3">
            <button type="button" @click="cancel()"
                class="px-4 py-2 border rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-700 text-sm">
                Cancelar
            </button>
            <button type="button" @click="confirm()"
                class="px-4 py-2 bg-[#960000] text-white rounded-lg cursor-pointer hover:opacity-90 text-sm font-medium">
                Confirmar
            </button>
        </div>
    </div>
</div>

<script>
function confirmDialog() {
    return {
        show: false,
        message: '',
        action: null,

        open(detail) {
            this.message = detail.message;
            this.action = detail.action;
            this.show = true;
        },

        confirm() {
            if (typeof this.action === 'function') this.action();
            this.show = false;
            this.action = null;
        },

        cancel() {
            this.action = null;
            this.show = false;
        }
    }
}
</script>
</x-layouts::app.sidebar>


