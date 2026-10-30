<template>
  <div class="cinema-seat-container" :class="[`seat-state-${status}`, { 'is-hoverable': status !== 'bought' && status !== 'held' }]">
    <svg viewBox="0 0 44 42" fill="none" xmlns="http://www.w3.org/2000/svg" class="seat-svg">
      <defs>
        <!-- Filtro de sombra y resplandor -->
        <filter :id="'glow-' + compId" x="-20%" y="-20%" width="140%" height="140%">
          <feDropShadow dx="0" dy="2" stdDeviation="2" flood-opacity="0.5" />
        </filter>

        <!-- Gradientes para cada estado -->
        <!-- Normal disponible -->
        <linearGradient :id="'grad-normal-' + compId" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#3b485d" />
          <stop offset="50%" stop-color="#222b3b" />
          <stop offset="100%" stop-color="#181f2b" />
        </linearGradient>

        <!-- VIP disponible (Dorado lujoso) -->
        <linearGradient :id="'grad-vip-' + compId" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#fbbf24" />
          <stop offset="45%" stop-color="#f59e0b" />
          <stop offset="100%" stop-color="#b45309" />
        </linearGradient>

        <!-- Seleccionado por el usuario (Verde Esmeralda Neón) -->
        <linearGradient :id="'grad-selected-' + compId" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#34d399" />
          <stop offset="40%" stop-color="#10b981" />
          <stop offset="100%" stop-color="#047857" />
        </linearGradient>

        <!-- Retenido temporalmente por otro (Naranja Alerta) -->
        <linearGradient :id="'grad-held-' + compId" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#fb923c" />
          <stop offset="50%" stop-color="#f97316" />
          <stop offset="100%" stop-color="#c2410c" />
        </linearGradient>

        <!-- Comprado / Ocupado (Gris oscuro apagado) -->
        <linearGradient :id="'grad-bought-' + compId" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#2a2e37" />
          <stop offset="100%" stop-color="#181a20" />
        </linearGradient>
      </defs>

      <!-- Sombra inferior de la butaca -->
      <ellipse cx="22" cy="40" rx="16" ry="2" fill="rgba(0, 0, 0, 0.45)" />

      <!-- Respaldo de la butaca (Backrest) con curvas ergonómicas -->
      <path
        d="M10 6.5C10 3.8 12 1.5 15 1.5H29C32 1.5 34 3.8 34 6.5V23C34 25.5 32 27 29.5 27H14.5C12 27 10 25.5 10 23V6.5Z"
        :fill="colors.body"
        :stroke="colors.stroke"
        stroke-width="1.3"
      />

      <!-- Cabecero acolchado superior (Headrest) -->
      <path
        d="M12.5 5.5C12.5 3.8 13.8 2.5 15.5 2.5H28.5C30.2 2.5 31.5 3.8 31.5 5.5V11.5C31.5 12.8 30.5 13.5 29 13.5H15C13.5 13.5 12.5 12.8 12.5 11.5V5.5Z"
        :fill="colors.headrest"
        :stroke="colors.strokeLight"
        stroke-width="0.8"
      />
      <!-- Pespunte/Línea de costura en el cabecero -->
      <line x1="16" y1="7" x2="28" y2="7" :stroke="colors.stitch" stroke-width="1" stroke-linecap="round" stroke-dasharray="1.5 2" opacity="0.65" />

      <!-- Cojín inferior de asiento (Seat cushion) -->
      <rect
        x="7.5"
        y="22.5"
        width="29"
        height="14"
        rx="4.5"
        :fill="colors.cushion"
        :stroke="colors.stroke"
        stroke-width="1.3"
      />
      <!-- Relieve del cojín -->
      <line x1="13" y1="27.5" x2="31" y2="27.5" :stroke="colors.stitch" stroke-width="0.9" stroke-linecap="round" opacity="0.35" />

      <!-- Reposabrazos izquierdo (Left Armrest) -->
      <rect
        x="3"
        y="12"
        width="6"
        height="21"
        rx="3"
        :fill="colors.armrest"
        :stroke="colors.stroke"
        stroke-width="1.2"
      />
      <!-- Brillo del reposabrazos izq -->
      <line x1="6" y1="15" x2="6" y2="28" stroke="white" stroke-width="0.8" stroke-linecap="round" opacity="0.2" />

      <!-- Reposabrazos derecho (Right Armrest) -->
      <rect
        x="35"
        y="12"
        width="6"
        height="21"
        rx="3"
        :fill="colors.armrest"
        :stroke="colors.stroke"
        stroke-width="1.2"
      />
      <!-- Brillo del reposabrazos der -->
      <line x1="38" y1="15" x2="38" y2="28" stroke="white" stroke-width="0.8" stroke-linecap="round" opacity="0.2" />

      <!-- Corona/Estrella distintiva para butaca VIP -->
      <g v-if="status === 'vip'" transform="translate(16.5, 4.5) scale(0.7)">
        <path d="M1 9L4 2L8 6L12 2L15 9H1Z" fill="#fef08a" stroke="#d97706" stroke-width="0.8" />
        <circle cx="4" cy="2" r="1.2" fill="#fff" />
        <circle cx="8" cy="6" r="1.2" fill="#fff" />
        <circle cx="12" cy="2" r="1.2" fill="#fff" />
      </g>

      <!-- Checkmark de confirmación para butaca Seleccionada -->
      <g v-if="status === 'selected'" transform="translate(14.5, 3.5)">
        <circle cx="7.5" cy="6.5" r="5.5" fill="#047857" stroke="#a7f3d0" stroke-width="1.2" />
        <path d="M5 6.5L7 8.5L10.5 4.5" stroke="#ffffff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
      </g>

      <!-- Reloj/Candado de alerta para Retenido por otro -->
      <g v-if="status === 'held'" transform="translate(15, 4)">
        <circle cx="7" cy="6" r="5" fill="#9a3412" stroke="#ffedd5" stroke-width="1.1" />
        <path d="M7 3.5V6.2L8.8 7.5" stroke="#ffffff" stroke-width="1.4" stroke-linecap="round" />
      </g>

      <!-- Diagonal suave para Comprado / Ocupado -->
      <g v-if="status === 'bought'">
        <line x1="17" y1="16" x2="27" y2="26" stroke="#52525b" stroke-width="2" stroke-linecap="round" opacity="0.6" />
        <line x1="27" y1="16" x2="17" y2="26" stroke="#52525b" stroke-width="2" stroke-linecap="round" opacity="0.6" />
      </g>

      <!-- Número del asiento impreso en el cojín -->
      <text
        v-if="number && status !== 'bought'"
        x="22"
        y="34.5"
        text-anchor="middle"
        font-size="8.8"
        font-weight="800"
        :fill="colors.text"
        font-family="system-ui, -apple-system, sans-serif"
      >
        {{ number }}
      </text>
    </svg>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  status: {
    type: String,
    default: 'normal' // 'normal', 'vip', 'selected', 'held', 'bought'
  },
  number: {
    type: [String, Number],
    default: ''
  },
  seatId: {
    type: [String, Number],
    default: () => Math.random().toString(36).substring(2, 7)
  }
})

const compId = computed(() => `${props.seatId}_${props.status}`)

const colors = computed(() => {
  switch (props.status) {
    case 'selected':
      return {
        body: `url(#grad-selected-${compId.value})`,
        cushion: `url(#grad-selected-${compId.value})`,
        headrest: '#10b981',
        armrest: '#065f46',
        stroke: '#6ee7b7',
        strokeLight: '#a7f3d0',
        stitch: '#ecfdf5',
        text: '#ffffff'
      }
    case 'vip':
      return {
        body: `url(#grad-vip-${compId.value})`,
        cushion: `url(#grad-vip-${compId.value})`,
        headrest: '#d97706',
        armrest: '#92400e',
        stroke: '#fde047',
        strokeLight: '#fef08a',
        stitch: '#fef3c7',
        text: '#ffffff'
      }
    case 'held':
      return {
        body: `url(#grad-held-${compId.value})`,
        cushion: `url(#grad-held-${compId.value})`,
        headrest: '#ea580c',
        armrest: '#9a3412',
        stroke: '#fed7aa',
        strokeLight: '#ffedd5',
        stitch: '#fff7ed',
        text: '#ffffff'
      }
    case 'bought':
      return {
        body: `url(#grad-bought-${compId.value})`,
        cushion: `url(#grad-bought-${compId.value})`,
        headrest: '#27272a',
        armrest: '#18181b',
        stroke: '#3f3f46',
        strokeLight: '#52525b',
        stitch: '#52525b',
        text: '#71717a'
      }
    case 'normal':
    default:
      return {
        body: `url(#grad-normal-${compId.value})`,
        cushion: `url(#grad-normal-${compId.value})`,
        headrest: '#334155',
        armrest: '#1e293b',
        stroke: '#475569',
        strokeLight: '#64748b',
        stitch: '#94a3b8',
        text: '#cbd5e1'
      }
  }
})
</script>

<style scoped>
.cinema-seat-container {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), filter 0.25s ease;
  user-select: none;
}

.seat-svg {
  width: 100%;
  height: 100%;
  overflow: visible;
  display: block;
}

/* Efectos visuales por estado */
.seat-state-selected {
  filter: drop-shadow(0 0 10px rgba(16, 185, 129, 0.75));
  animation: seatPop 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
}

.seat-state-vip {
  filter: drop-shadow(0 0 6px rgba(245, 158, 11, 0.45));
}

.seat-state-held {
  filter: drop-shadow(0 0 8px rgba(249, 115, 22, 0.65));
  animation: heldPulse 2s infinite ease-in-out;
}

.seat-state-bought {
  opacity: 0.42;
  filter: grayscale(85%);
  cursor: not-allowed;
}

.is-hoverable:hover {
  transform: translateY(-3px) scale(1.1);
  filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.4)) brightness(1.15);
}

@keyframes seatPop {
  0% {
    transform: scale(0.85);
  }
  60% {
    transform: scale(1.18);
  }
  100% {
    transform: scale(1);
  }
}

@keyframes heldPulse {
  0%, 100% {
    opacity: 1;
    transform: scale(1);
  }
  50% {
    opacity: 0.75;
    transform: scale(0.97);
  }
}
</style>
