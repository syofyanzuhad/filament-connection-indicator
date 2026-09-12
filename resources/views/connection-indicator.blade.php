@props([
    'style' => config('connection-indicator.style', 'dot'),
])

<div
    x-data="{
        status: 'checking',
        connectionType: '',
        rtt: null,
        barLevel: 4,
        labels: {{ \Illuminate\Support\Js::from(config('connection-indicator.labels', [
            'checking' => 'Checking...',
            'online' => 'Online',
            'moderate' => 'Slow Connection',
            'slow' => 'Very Slow',
            'offline' => 'Offline',
        ])) }},
        init() {
            this.checkConnection()
            window.addEventListener('online',  () => this.checkConnection())
            window.addEventListener('offline', () => {
                this.status = 'offline'
                this.barLevel = 0
            })
            if (navigator.connection) {
                navigator.connection.addEventListener('change', () => this.checkConnection())
            }
            setInterval(() => this.checkConnection(), {{ (int) config('connection-indicator.poll_interval', 30000) }})
        },
        checkConnection() {
            if (!navigator.onLine) {
                this.status = 'offline'
                this.barLevel = 0
                return
            }
            if (navigator.connection) {
                const conn = navigator.connection
                this.connectionType = conn.effectiveType || ''
                this.rtt = conn.rtt || null
                if (conn.effectiveType === 'slow-2g') {
                    this.status = 'slow'
                    this.barLevel = 1
                } else if (conn.effectiveType === '2g') {
                    this.status = 'slow'
                    this.barLevel = 2
                } else if (conn.effectiveType === '3g') {
                    this.status = 'moderate'
                    this.barLevel = 3
                } else {
                    this.status = 'online'
                    this.barLevel = 4
                }
            } else {
                this.status = 'online'
                this.barLevel = 4
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
    style="position: relative; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;"
    :title="getTooltip()"
    @click="checkConnection()"
>
    @if ($style === 'bars')
        {{-- Vertical Signal Bars Style --}}
        <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 16 16"
            style="width: 16px; height: 16px; overflow: visible;"
        >
            {{-- Bar 1 --}}
            <rect
                x="1.5"
                y="11.5"
                width="2.5"
                height="4"
                rx="0.75"
                :fill="barLevel >= 1 ? getColor() : 'currentColor'"
                :style="'transition: fill 0.2s, opacity 0.2s; opacity: ' + (barLevel >= 1 ? (status === 'offline' ? 0.3 : 1) : 0.2)"
            />
            {{-- Bar 2 --}}
            <rect
                x="5.5"
                y="8"
                width="2.5"
                height="7.5"
                rx="0.75"
                :fill="barLevel >= 2 ? getColor() : 'currentColor'"
                :style="'transition: fill 0.2s, opacity 0.2s; opacity: ' + (barLevel >= 2 ? (status === 'offline' ? 0.3 : 1) : 0.2)"
            />
            {{-- Bar 3 --}}
            <rect
                x="9.5"
                y="4.5"
                width="2.5"
                height="11"
                rx="0.75"
                :fill="barLevel >= 3 ? getColor() : 'currentColor'"
                :style="'transition: fill 0.2s, opacity 0.2s; opacity: ' + (barLevel >= 3 ? (status === 'offline' ? 0.3 : 1) : 0.2)"
            />
            {{-- Bar 4 --}}
            <rect
                x="13.5"
                y="1"
                width="2.5"
                height="14.5"
                rx="0.75"
                :fill="barLevel >= 4 ? getColor() : 'currentColor'"
                :style="'transition: fill 0.2s, opacity 0.2s; opacity: ' + (barLevel >= 4 ? (status === 'offline' ? 0.3 : 1) : 0.2)"
            />
            {{-- Offline Strike Line --}}
            <line
                x-show="status === 'offline'"
                x1="1"
                y1="15"
                x2="15"
                y2="1"
                stroke="#ef4444"
                stroke-width="1.75"
                stroke-linecap="round"
                style="display: none;"
            />
        </svg>
    @else
        {{-- Default Pulsing Dot Style --}}
        <div style="position: relative; width: 12px; height: 12px; display: inline-block;">
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
    @endif
</div>

<style>
    @keyframes ci-ping {
        75%, 100% {
            transform: scale(2);
            opacity: 0;
        }
    }
</style>
