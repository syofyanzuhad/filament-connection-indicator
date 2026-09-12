<div
    x-data="{
        status: 'checking',
        connectionType: '',
        rtt: null,
        labels: {{ \Illuminate\Support\Js::from(config('connection-indicator.labels', [
            'checking' => 'Checking...',
            'online'   => 'Online',
            'moderate' => 'Slow Connection',
            'slow'     => 'Very Slow',
            'offline'  => 'Offline',
        ])) }},
        init() {
            this.checkConnection()
            window.addEventListener('online',  () => this.checkConnection())
            window.addEventListener('offline', () => this.status = 'offline')
            if (navigator.connection) {
                navigator.connection.addEventListener('change', () => this.checkConnection())
            }
            setInterval(() => this.checkConnection(), {{ (int) config('connection-indicator.poll_interval', 30000) }})
        },
        checkConnection() {
            if (!navigator.onLine) {
                this.status = 'offline'
                return
            }
            if (navigator.connection) {
                const conn = navigator.connection
                this.connectionType = conn.effectiveType || ''
                this.rtt = conn.rtt || null
                this.status = (conn.effectiveType === 'slow-2g' || conn.effectiveType === '2g')
                    ? 'slow'
                    : conn.effectiveType === '3g' ? 'moderate' : 'online'
            } else {
                this.status = 'online'
            }
        },
        getColor() {
            return {
                checking: '#9ca3af',
                online:   '#22c55e',
                moderate: '#eab308',
                slow:     '#f97316',
                offline:  '#ef4444',
            }[this.status]
        },
        getTooltip() {
            let tip = this.labels[this.status] || this.status
            if (this.connectionType && this.status !== 'offline') {
                tip += ` (${this.connectionType.toUpperCase()})`
            }
            if (this.rtt) tip += ` • ${this.rtt}ms`
            return tip
        },
    }"
    style="position: relative; width: 12px; height: 12px; cursor: pointer; display: inline-block;"
    :title="getTooltip()"
    @click="checkConnection()"
>
    {{-- Pulse ring (hidden when offline) --}}
    <span
        x-show="status !== 'offline'"
        :style="'position: absolute; width: 12px; height: 12px; border-radius: 50%; opacity: 0.75; animation: ci-ping 1s cubic-bezier(0, 0, 0.2, 1) infinite; background-color: ' + getColor()"
    ></span>

    {{-- Solid dot --}}
    <span
        :style="'position: absolute; width: 12px; height: 12px; border-radius: 50%; background-color: ' + getColor()"
    ></span>
</div>

<style>
    @keyframes ci-ping {
        75%, 100% {
            transform: scale(2);
            opacity: 0;
        }
    }
</style>
