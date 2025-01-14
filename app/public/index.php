<!DOCTYPE html>
<html lang="tr" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Okey 101</title>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js CDN -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .tile {
            width: 35px;
            height: 50px;
            perspective: 1000px;
            cursor: grab;
            user-select: none;
            transition: all 0.2s ease;
        }

        .tile-content {
            width: 100%;
            height: 100%;
            transition: all 0.2s ease;
            transform-style: preserve-3d;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: bold;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
            border-width: 1px;
            user-select: none;
        }

        .tile.dragging {
            opacity: 0.5;
            cursor: grabbing;
        }

        .tile-red {
            color: #dc2626;
        }

        .tile-blue {
            color: #2563eb;
        }

        .tile-yellow {
            color: #ca8a04;
        }

        .tile-black {
            color: #1f2937;
        }

        .tile-green {
            color: #16a34a;
        }

        .istaka {
            background: rgba(139, 69, 19, 0.9);
            padding: 1rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: fit-content;
        }

        .istaka-row {
            display: flex;
            gap: 2px;
            margin-bottom: 2px;
            min-height: 55px;
            padding: 3px;
            background: rgba(0, 0, 0, 0.05);
            border-radius: 4px;
            width: fit-content;
        }

        .istaka-row:last-child {
            margin-bottom: 0;
        }

        .tile-slot {
            width: 35px;
            height: 50px;
            border: 1px dashed rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            transition: all 0.2s ease;
            flex-shrink: 0;
        }

        .tile-slot.empty {
            background: rgba(255, 255, 255, 0.02);
        }

        .tile-slot:hover {
            border-color: rgba(255, 255, 255, 0.2);
        }

        .draw-area {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            background: rgba(111, 78, 55, 0.9);
        }

        .draw-pile,
        .discard-pile {
            border: 1px dashed rgba(255, 255, 255, 0.1) !important;
            position: relative;
        }

        .draw-pile:hover {
            transform: translateY(-1px);
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .discard-pile .tile {
            transform: rotate(5deg);
        }

        .tile-back {
            width: 35px;
            height: 50px;
            transform: rotate(-5deg);
            transition: all 0.2s ease;
        }

        .tile-back-content {
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, #2a1810, #3c2317);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            position: relative;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .tile-back-content::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 70%;
            height: 70%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), transparent);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 2px;
        }

        .draw-pile:hover .tile-back {
            transform: rotate(-5deg) translateY(-2px);
        }

        .remaining-count {
            backdrop-filter: blur(4px);
            font-weight: 500;
            min-width: 20px;
            text-align: center;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            font-size: 0.7rem;
        }

        .player-avatar {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 9999px;
            background: rgba(0, 0, 0, 0.2);
            color: white;
            transition: all 0.3s ease;
        }

        .player-name {
            font-weight: 500;
            color: rgba(255, 255, 255, 0.9);
            font-size: 1rem;
        }

        .active-player {
            background: rgba(59, 130, 246, 0.3);
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0.4);
            }

            70% {
                box-shadow: 0 0 0 6px rgba(59, 130, 246, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(59, 130, 246, 0);
            }
        }

        .left-player,
        .right-player {
            padding: 1rem;
        }

        .top-player {
            margin-bottom: 2rem;
        }

        .bottom-player {
            margin-top: 2rem;
        }

        .indicator-tile {
            padding: 4px;
            border-radius: 4px;
            background: rgba(255, 255, 255, 0.1);
        }

        @keyframes scoreUpdate {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }

            100% {
                transform: scale(1);
            }
        }

        .score-updated {
            animation: scoreUpdate 0.3s ease;
        }

        .per-area {
            width: 100%;
            max-width: 900px;
        }

        .per-player-area {
            transition: all 0.3s ease;
            min-height: 120px;
        }

        .per-player-area:hover {
            background-color: rgba(111, 78, 55, 0.7);
        }

        .per-row {
            background: rgba(0, 0, 0, 0.1);
            padding: 2px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ring-offset-2 {
            --tw-ring-offset-width: 2px;
        }

        /* Seçili olmayan taşları soluklaştır */
        .opacity-75 {
            opacity: 0.75;
        }

        /* Transition efekti için */
        [x-transition] {
            transition: all 0.2s ease-out;
        }

        [x-transition\:enter] {
            opacity: 0;
            transform: scale(0.95);
        }

        [x-transition\:enter-end] {
            opacity: 1;
            transform: scale(1);
        }

        .relative {
            position: relative;
        }

        .absolute {
            position: absolute;
        }

        .z-10 {
            z-index: 10;
        }

        /* Buton hover efekti */
        .hover\:scale-105:hover {
            transform: scale(1.05);
        }
    </style>
</head>

<body class="h-full m-0 p-0 overflow-hidden bg-gray-900" x-data="game" x-init="startGame">
    <div class="absolute top-4 right-4 flex flex-col gap-2">
        <div class="bg-[#6F4E37] rounded-lg p-4 shadow-lg">
            <div class="text-white mb-3 text-center font-semibold">Puan Tablosu</div>
            <div class="space-y-2">
                <template x-for="(player, index) in players" :key="index">
                    <div class="flex items-center justify-between gap-4"
                        :class="{ 'text-blue-300': currentPlayer === index }">
                        <div class="flex items-center gap-2">
                            <span class="text-white/90" x-text="player.name"></span>
                            <span class="text-xs text-white/60" x-text="'(' + calculatePerTotal(index) + ')'"></span>
                        </div>
                        <span class="text-white/90 font-mono" x-text="player.score || 0"></span>
                    </div>
                </template>
            </div>
        </div>

        <div class="absolute -top-2 right-0 transform translate-y-[-100%] z-10 flex gap-2">
            <button @click="autoOpenPer"
                class="px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-all shadow-md text-xs font-medium flex items-center gap-1 hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Per Aç
            </button>

            <button @click="autoOpenPairs"
                class="px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 transition-all shadow-md text-xs font-medium flex items-center gap-1 hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Çift Aç
            </button>

            <button @click="autoOpenHand"
                class="px-3 py-1.5 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-all shadow-md text-xs font-medium flex items-center gap-1 hover:scale-105">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
                El Aç
            </button>
        </div>
    </div>

    <div class="w-full h-full flex items-center justify-center"
        @keydown.window="handleKeydown($event)"
        x-cloak>

        <!-- Oyun Alanı -->
        <div class="w-full h-full flex flex-col items-center justify-center gap-4">
            <!-- Üst Oyuncu -->
            <div class="player-avatar top-player" :class="{ 'active-player': currentPlayer === 2 }">
                <span class="player-name">Oyuncu 2</span>
            </div>

            <!-- Per Açma Alanı -->
            <div class="per-area mb-4">
                <div class="grid grid-cols-3 gap-4">
                    <!-- Her oyuncunun per alanı -->
                    <template x-for="(player, index) in players" :key="index">
                        <div :class="{ 'border-blue-500': currentPlayer === index }"
                            class="per-player-area bg-[#6F4E37]/50 rounded-lg p-2 border-2 border-transparent">
                            <div class="flex flex justify-center items-center gap-1">
                                <div class="text-white/60 text-xs text-center" x-text="player.name"></div>
                                <div class="text-white/50 text-[10px] font-mono" x-text="'(' + calculatePerTotal(index) + ')'"></div>
                            </div>
                            <div class="flex flex-col gap-2 mt-2">
                                <!-- Per sıraları -->
                                <template x-for="(per, perIndex) in (player.openPers || [])" :key="perIndex">
                                    <div class="flex gap-0.5 justify-center per-row">
                                        <template x-for="(tile, tileIndex) in per" :key="tileIndex">
                                            <div class="tile scale-75 -ml-1">
                                                <div class="tile-content bg-white border-2 border-gray-300"
                                                    :class="`tile-${tile.color}`">
                                                    <span x-text="tile.number"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Orta Alan (Sol Oyuncu, İstaka, Sağ Oyuncu) -->
            <div class="flex items-center justify-center gap-8 w-full">
                <!-- Sol Oyuncu -->
                <div class="player-avatar left-player" :class="{ 'active-player': currentPlayer === 1 }">
                    <span class="player-name">Oyuncu 1</span>
                </div>

                <!-- Oyun Alanı (Mevcut İstaka ve Çekme/Atma Alanı) -->
                <div class="flex flex-col gap-4">
                    <!-- Çekme/Atma Alanı -->
                    <div class="draw-area flex justify-center items-center gap-2 p-3 bg-[#6F4E37] rounded-lg w-fit mx-auto">
                        <!-- Gösterge taşı -->
                        <div class="indicator-area flex flex-col items-center gap-2">
                            <div class="indicator-tile relative flex items-center justify-center">
                                <template x-if="indicatorTile">
                                    <div class="tile scale-75">
                                        <div class="tile-content bg-white border-2 border-gray-300"
                                            :class="`tile-${indicatorTile.color}`">
                                            <span x-text="indicatorTile.number"></span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Çekme alanı -->
                        <div class="draw-pile relative flex items-center justify-center w-[80px] h-[60px] border border-dashed border-white/20 rounded-lg hover:bg-black/10 transition-colors cursor-pointer"
                            @click="drawTile">
                            <!-- Ters çevrilmiş taş -->
                            <div class="tile-back absolute scale-75">
                                <div class="tile-back-content">
                                    <div class="remaining-count absolute -top-2 -right-2 bg-white/10 text-white/70 text-xs px-2 py-1 rounded-full"
                                        x-text="drawPileCount">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Atma alanı -->
                        <div class="discard-pile relative flex items-center justify-center w-[80px] h-[60px] border border-dashed border-white/20 rounded-lg"
                            @dragover.prevent
                            @drop.prevent="discardTile">
                            <template x-if="discardedTile">
                                <div class="tile absolute scale-75">
                                    <div class="tile-content bg-white border-2 border-gray-300"
                                        :class="`tile-${discardedTile.color}`">
                                        <span x-text="discardedTile.number"></span>
                                    </div>
                                </div>
                            </template>
                            <div x-show="!discardedTile" class="text-white/70 text-center">
                                <div class="text-xl">🗑️</div>
                            </div>
                        </div>
                    </div>

                    <!-- İstaka -->
                    <div class="relative">
                        <!-- İstaka üstündeki butonları güncelle -->
                        <div class="absolute -top-2 right-0 transform translate-y-[-100%] z-10 flex gap-2">
                            <button @click="autoOpenPer"
                                class="px-3 py-1.5 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-all shadow-md text-xs font-medium flex items-center gap-1 hover:scale-105">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Per Aç
                            </button>

                            <button @click="autoOpenPairs"
                                class="px-3 py-1.5 bg-green-600 text-white rounded-md hover:bg-green-700 transition-all shadow-md text-xs font-medium flex items-center gap-1 hover:scale-105">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                                Çift Aç
                            </button>

                            <button @click="autoOpenHand"
                                class="px-3 py-1.5 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 transition-all shadow-md text-xs font-medium flex items-center gap-1 hover:scale-105">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                                </svg>
                                El Aç
                            </button>
                        </div>

                        <!-- İstaka -->
                        <div class="istaka">
                            <!-- Üst Sıra -->
                            <div class="istaka-row">
                                <template x-for="i in 18" :key="'slot-top-'+i">
                                    <div class="tile-slot"
                                        :class="{ 'empty': !playerTiles[i-1] }"
                                        @dragover.prevent
                                        @drop.prevent="drop($event, i-1)">
                                        <template x-if="playerTiles[i-1]">
                                            <div class="tile"
                                                draggable="true"
                                                @dragstart="dragStart($event, i-1)"
                                                @dragend="dragEnd($event)"
                                                @dblclick="toggleTileVisibility(i-1)"
                                                @click.shift="toggleTileSelection(i-1)">
                                                <div class="tile-content bg-white border-2 border-gray-300"
                                                    :class="{
                                                        [`tile-${playerTiles[i-1].color}`]: true,
                                                        'ring-2 ring-blue-500 ring-offset-2': selectedTiles.includes(i-1),
                                                        'opacity-75': selectedTiles.length > 0 && !selectedTiles.includes(i-1)
                                                    }">
                                                    <span x-show="!isTileHidden(playerTiles[i-1])"
                                                        x-text="playerTiles[i-1].number"
                                                        class="transition-opacity duration-200">
                                                    </span>
                                                    <span x-show="isTileHidden(playerTiles[i-1])"
                                                        class="opacity-0">
                                                        *
                                                    </span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <!-- Alt Sıra -->
                            <div class="istaka-row">
                                <template x-for="i in 18" :key="'slot-bottom-'+i">
                                    <div class="tile-slot"
                                        :class="{ 'empty': !playerTiles[i+17] }"
                                        @dragover.prevent
                                        @drop.prevent="drop($event, i+17)">
                                        <template x-if="playerTiles[i+17]">
                                            <div class="tile"
                                                draggable="true"
                                                @dragstart="dragStart($event, i+17)"
                                                @dragend="dragEnd($event)"
                                                @dblclick="toggleTileVisibility(i+17)"
                                                @click.shift="toggleTileSelection(i+17)">
                                                <div class="tile-content bg-white border-2 border-gray-300"
                                                    :class="{
                                                        [`tile-${playerTiles[i+17].color}`]: true,
                                                        'ring-2 ring-blue-500 ring-offset-2': selectedTiles.includes(i+17),
                                                        'opacity-75': selectedTiles.length > 0 && !selectedTiles.includes(i+17)
                                                    }">
                                                    <span x-show="!isTileHidden(playerTiles[i+17])"
                                                        x-text="playerTiles[i+17].number"
                                                        class="transition-opacity duration-200">
                                                    </span>
                                                    <span x-show="isTileHidden(playerTiles[i+17])"
                                                        class="opacity-0">
                                                        *
                                                    </span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sağ Oyuncu -->
                <div class="player-avatar right-player" :class="{ 'active-player': currentPlayer === 3 }">
                    <span class="player-name">Oyuncu 3</span>
                </div>
            </div>

            <!-- Alt Oyuncu (Siz) -->
            <div class="player-avatar bottom-player" :class="{ 'active-player': currentPlayer === 0 }">
                <span class="player-name">Siz</span>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('game', () => ({
                playerTiles: Array(36).fill(null),
                draggingTile: null,
                discardedTile: null,
                drawPileCount: 0,
                currentPlayer: 0,
                indicatorTile: null,
                players: [{
                        name: 'Siz',
                        tileCount: 21,
                        score: 0,
                        openPers: [],
                        hasOpenedDouble: false
                    },
                    {
                        name: 'Oyuncu 1',
                        tileCount: 21,
                        score: 0,
                        openPers: []
                    },
                    {
                        name: 'Oyuncu 2',
                        tileCount: 21,
                        score: 0,
                        openPers: []
                    },
                    {
                        name: 'Oyuncu 3',
                        tileCount: 21,
                        score: 0,
                        openPers: []
                    }
                ],
                hiddenTiles: new Map(),
                selectedTiles: [],

                startGame() {
                    this.initializeTiles();
                    this.players[this.currentPlayer].tileCount = 22;
                    this.autoArrangeTiles();
                },

                initializeTiles() {
                    const colors = ['red', 'blue', 'yellow', 'green'];
                    let tiles = [];
                    let tileId = 1;

                    colors.forEach(color => {
                        for (let num = 1; num <= 13; num++) {
                            tiles.push({
                                id: tileId++,
                                color,
                                number: num
                            });
                            tiles.push({
                                id: tileId++,
                                color,
                                number: num
                            });
                        }
                    });

                    tiles.push({
                        id: tileId++,
                        color: 'black',
                        number: '★'
                    });
                    tiles.push({
                        id: tileId++,
                        color: 'black',
                        number: '★'
                    });

                    tiles = this.shuffleArray(tiles);

                    do {
                        this.indicatorTile = tiles[Math.floor(Math.random() * tiles.length)];
                    } while (this.indicatorTile.number === '★');

                    const firstPlayerTiles = tiles.slice(0, 22);
                    const secondPlayerTiles = tiles.slice(22, 43);
                    const thirdPlayerTiles = tiles.slice(43, 64);
                    const fourthPlayerTiles = tiles.slice(64, 85);

                    this.drawPileCount = tiles.length - 85;

                    firstPlayerTiles.forEach((tile, index) => {
                        this.playerTiles[index] = tile;
                    });

                    const currentDealer = Math.floor(Math.random() * 4); // Rastgele dağıtıcı seç
                    this.players.forEach((player, index) => {
                        player.tileCount = index === currentDealer ? 22 : 21;
                    });
                },

                shuffleArray(array) {
                    for (let i = array.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [array[i], array[j]] = [array[j], array[i]];
                    }
                    return array;
                },

                dragStart(event, index) {
                    this.draggingTile = index;
                    event.target.classList.add('dragging');
                },

                dragEnd(event) {
                    event.target.classList.remove('dragging');
                },

                drop(event, index) {
                    if (this.draggingTile !== null) {
                        const tiles = [...this.playerTiles];

                        // Seçili taşların indekslerini güncelle
                        if (this.selectedTiles.length > 0) {
                            // Sürüklenen taş seçili mi?
                            const isDraggingSelected = this.selectedTiles.includes(this.draggingTile);
                            // Hedef konum seçili mi?
                            const isTargetSelected = this.selectedTiles.includes(index);

                            this.selectedTiles = this.selectedTiles.map(selectedIndex => {
                                if (selectedIndex === this.draggingTile) {
                                    return index; // Sürüklenen taşın yeni konumu
                                } else if (selectedIndex === index) {
                                    return this.draggingTile; // Hedef konumdaki taşın yeni konumu
                                }
                                return selectedIndex;
                            });
                        }

                        // Eğer taş gizliyse, yeni konumunda da gizli kalmasını sağla
                        if (this.hiddenTiles.has(this.draggingTile)) {
                            this.hiddenTiles.delete(this.draggingTile);
                            this.hiddenTiles.add(index);
                        }

                        // Hedef konumda gizli bir taş varsa, onun gizliliğini koru
                        if (this.hiddenTiles.has(index)) {
                            this.hiddenTiles.delete(index);
                            this.hiddenTiles.add(this.draggingTile);
                        }

                        // Taşları yer değiştir
                        const temp = tiles[this.draggingTile];
                        tiles[this.draggingTile] = tiles[index];
                        tiles[index] = temp;
                        this.playerTiles = tiles;
                        this.draggingTile = null;
                    }
                },

                handleKeydown(event) {
                    console.log('Tuş basıldı:', event.key);
                },

                drawTile() {
                    if (this.drawPileCount <= 0) {
                        alert('Çekilecek taş kalmadı!');
                        return;
                    }

                    const emptySlotIndex = this.playerTiles.findIndex(tile => tile === null);
                    if (emptySlotIndex === -1) {
                        alert('İstakada boş yer yok!');
                        return;
                    }

                    const colors = ['red', 'blue', 'yellow', 'black'];
                    const newTile = {
                        color: colors[Math.floor(Math.random() * colors.length)],
                        number: Math.floor(Math.random() * 13) + 1
                    };

                    this.playerTiles[emptySlotIndex] = newTile;
                    this.drawPileCount--;
                    this.nextTurn();
                },

                discardTile(event) {
                    if (!this.draggingTile !== null) {
                        const discardedTile = this.playerTiles[this.draggingTile];
                        this.playerTiles[this.draggingTile] = null;
                        this.discardedTile = discardedTile;
                        this.draggingTile = null;
                    }
                    this.nextTurn();
                },

                nextTurn() {
                    this.currentPlayer = (this.currentPlayer + 1) % 4;
                },

                toggleTileVisibility(index) {
                    const tile = this.playerTiles[index];
                    if (!tile || !tile.id) return;

                    const tileKey = this.getTileKey(tile);
                    if (this.hiddenTiles.has(tileKey)) {
                        this.hiddenTiles.delete(tileKey);
                    } else {
                        this.hiddenTiles.set(tileKey, true);
                    }
                },

                getTileKey(tile) {
                    if (!tile || !tile.id) return null;
                    return `${tile.id}`;
                },

                isTileHidden(tile) {
                    if (!tile) return false;
                    return this.hiddenTiles.has(this.getTileKey(tile));
                },

                updateScore(playerIndex, points) {
                    this.players[playerIndex].score += points;
                },

                openPer(playerIndex, tiles) {
                    // Per açma kontrolü (sıralı sayılar, aynı renk vb.)
                    if (this.isValidPer(tiles)) {
                        this.players[playerIndex].openPers.push(tiles);
                        // Açılan per için puan ekle
                        this.updateScore(playerIndex, tiles.length * 10);
                        return true;
                    }
                    return false;
                },

                isValidPer(tiles) {
                    if (tiles.length < 3) return false;

                    // Aynı renk ve ardışık sayı kontrolü
                    const color = tiles[0].color;
                    const numbers = tiles.map(t => t.number).sort((a, b) => a - b);

                    // Hepsi aynı renk mi?
                    if (!tiles.every(t => t.color === color)) return false;

                    // Ardışık sayılar mı?
                    for (let i = 1; i < numbers.length; i++) {
                        if (numbers[i] !== numbers[i - 1] + 1) return false;
                    }

                    return true;
                },

                toggleTileSelection(index) {
                    const tile = this.playerTiles[index];
                    if (!tile) return;

                    if (this.selectedTiles.includes(index)) {
                        // Seçili taşı kaldır
                        this.selectedTiles = this.selectedTiles.filter(i => i !== index);
                    } else if (this.selectedTiles.length < 4) { // Max 4 taş
                        // Yeni taş seç
                        const selectedTile = this.playerTiles[index];
                        const okeyTile = this.getOkeyTile();

                        // İlk seçilen taş değilse kontrol yap
                        if (this.selectedTiles.length > 0) {
                            const firstTile = this.playerTiles[this.selectedTiles[0]];
                            const isOkey = selectedTile.color === okeyTile.color && selectedTile.number === okeyTile.number;
                            const firstIsOkey = firstTile.color === okeyTile.color && firstTile.number === okeyTile.number;

                            // Aynı sayı kontrolü
                            const allSelectedTiles = [...this.selectedTiles.map(i => this.playerTiles[i]), selectedTile];
                            const allSameNumber = allSelectedTiles.every(t =>
                                t.number === firstTile.number ||
                                t.number === '★' ||
                                (t.color === okeyTile.color && t.number === okeyTile.number)
                            );

                            // Aynı renk kontrolü
                            const allSameColor = allSelectedTiles.every(t =>
                                t.color === firstTile.color ||
                                t.number === '★' ||
                                (t.color === okeyTile.color && t.number === okeyTile.number)
                            );

                            // Farklı renk aynı sayı VEYA aynı renk farklı sayı olmalı
                            if (!allSameNumber && !allSameColor) {
                                return; // Seçilemez
                            }

                            // Aynı sayılı perler için renk tekrarı kontrolü
                            if (allSameNumber) {
                                const colors = new Set(this.selectedTiles.map(i => this.playerTiles[i].color));
                                if (colors.has(selectedTile.color)) {
                                    return; // Aynı renk tekrar seçilemez
                                }
                            }
                        }

                        this.selectedTiles.push(index);
                    }
                },

                tryOpenPer() {
                    if (this.selectedTiles.length < 3 || this.selectedTiles.length > 5) {
                        alert('Per için 3-5 arası taş seçmelisiniz!');
                        return;
                    }

                    // İstakadaki pozisyona göre sırala
                    const sortedIndices = [...this.selectedTiles].sort((a, b) => a - b);
                    const selectedTilesData = sortedIndices.map(index => ({
                        ...this.playerTiles[index],
                        index
                    }));

                    if (this.isValidPerWithJoker(selectedTilesData)) {
                        // Per geçerliyse taşları per alanına ekle
                        const perTiles = selectedTilesData.map(t => ({
                            color: t.color,
                            number: t.number
                        }));

                        this.players[this.currentPlayer].openPers.push(perTiles);

                        // Kullanılan taşları istakadan kaldır (sıralı indeksleri kullan)
                        sortedIndices.forEach(index => {
                            this.playerTiles[index] = null;
                        });

                        // Seçili taşları temizle
                        this.selectedTiles = [];

                        // Puan ekle (her taş 10 puan)
                        this.updateScore(this.currentPlayer, perTiles.length * 10);
                    } else {
                        alert('Geçersiz per! Aynı renk ve sıralı sayılar olmalı.');
                    }
                },

                isValidPerWithJoker(per) {
                    const okeyTile = this.getOkeyTile();
                    const jokerValue = {
                        color: this.indicatorTile.color,
                        number: this.indicatorTile.number === 13 ? 1 : this.indicatorTile.number + 1
                    };

                    // Önce okey ve joker taşlarını işaretle
                    const processedPer = per.map(tile => {
                        // Joker kontrolü (sadece gösterge renginde ve bir büyük sayı olarak kullanılabilir)
                        if (tile.number === '★') {
                            return {
                                ...tile,
                                isJoker: true,
                                actualColor: jokerValue.color, // Joker sadece gösterge renginde kullanılır
                                actualNumber: jokerValue.number
                            };
                        }
                        // Okey kontrolü (her yerde kullanılabilir)
                        if (tile.color === okeyTile.color && tile.number === okeyTile.number) {
                            return {
                                ...tile,
                                isOkey: true,
                                actualColor: null, // Okey her renkte kullanılabilir
                                actualNumber: null // Okey her sayı olabilir
                            };
                        }
                        return tile;
                    });

                    // Aynı sayılı per kontrolü
                    const isSameNumber = processedPer.every((tile, _, arr) => {
                        const normalTiles = arr.filter(t => !t.isJoker && !t.isOkey);
                        if (normalTiles.length === 0) return true;

                        const targetNumber = normalTiles[0].number;

                        if (tile.isJoker) {
                            // Joker sadece kendi değerinde kullanılabilir
                            return jokerValue.number === targetNumber;
                        }
                        if (tile.isOkey) {
                            // Okey her sayı yerine kullanılabilir
                            return true;
                        }
                        return tile.number === targetNumber;
                    });

                    if (isSameNumber) {
                        // Renk kontrolü
                        const colors = new Set();
                        let lastNormalTile = null;

                        for (const tile of processedPer) {
                            if (tile.isJoker) {
                                // Joker sadece gösterge renginde kullanılabilir
                                colors.add(jokerValue.color);
                            } else if (tile.isOkey) {
                                // Okey için farklı bir renk bul
                                for (const color of ['red', 'blue', 'yellow', 'green']) {
                                    if (!colors.has(color)) {
                                        colors.add(color);
                                        break;
                                    }
                                }
                            } else {
                                if (colors.has(tile.color)) return false; // Aynı renk tekrarı olamaz
                                colors.add(tile.color);
                                lastNormalTile = tile;
                            }
                        }
                        return processedPer.length <= 4; // Aynı sayılı per max 4'lü olabilir
                    }

                    // Sıralı per kontrolü
                    let baseColor = null;
                    let expectedNumber = null;
                    let lastNormalTile = null;

                    return processedPer.every((tile, index) => {
                        if (index === 0) {
                            if (tile.isJoker) {
                                // Joker sadece kendi değerinde ve renginde kullanılabilir
                                baseColor = jokerValue.color;
                                expectedNumber = jokerValue.number;
                            } else if (tile.isOkey) {
                                // İlk taş okey ise sonraki taşa göre değer alacak
                                return true;
                            } else {
                                baseColor = tile.color;
                                expectedNumber = tile.number;
                            }
                            lastNormalTile = tile;
                            return true;
                        }

                        if (tile.isJoker) {
                            // Joker sadece kendi değerinde ve renginde kullanılabilir
                            return jokerValue.color === baseColor && jokerValue.number === expectedNumber + 1;
                        }

                        if (tile.isOkey) {
                            // Okey taşı, solundaki taşın renginde ve bir büyük sayı olarak kullanılır
                            if (lastNormalTile) {
                                expectedNumber++;
                                return true;
                            }
                        }

                        if (baseColor === null) {
                            baseColor = tile.color;
                        }

                        if (tile.color !== baseColor) return false;
                        if (tile.number !== expectedNumber + 1) return false;

                        expectedNumber = tile.number;
                        lastNormalTile = tile;
                        return true;
                    }) && processedPer.length <= 5; // Sıralı per max 5'li olabilir
                },

                getOkeyTile() {
                    if (!this.indicatorTile) return null;

                    return {
                        color: this.indicatorTile.color,
                        number: this.indicatorTile.number === 13 ? 1 : this.indicatorTile.number + 1
                    };
                },

                calculatePerTotal(playerIndex) {
                    const player = this.players[playerIndex];
                    if (!player.openPers) return 0;

                    return player.openPers.reduce((total, per) => {
                        return total + per.reduce((perTotal, tile) => {
                            // Joker ve okey taşları için gösterge+1 değerini kullan
                            if (tile.number === '★') {
                                return perTotal + (this.indicatorTile.number === 13 ? 1 : this.indicatorTile.number + 1);
                            }
                            const okeyTile = this.getOkeyTile();
                            if (tile.color === okeyTile.color && tile.number === okeyTile.number) {
                                // Okey taşı için per içindeki diğer taşların ortalamasını al
                                const otherTiles = per.filter(t => t !== tile);
                                if (otherTiles.length === 0) return perTotal + tile.number;
                                const avg = otherTiles.reduce((sum, t) => sum + (t.number === '★' ?
                                    (this.indicatorTile.number === 13 ? 1 : this.indicatorTile.number + 1) :
                                    t.number), 0) / otherTiles.length;
                                return perTotal + Math.round(avg);
                            }
                            return perTotal + tile.number;
                        }, 0);
                    }, 0);
                },

                isValidDouble(tiles) {
                    if (tiles.length < 5) return false; // En az 5 çift olmalı

                    // Çiftleri kontrol et
                    const pairs = [];
                    for (let i = 0; i < tiles.length; i += 2) {
                        const tile1 = tiles[i];
                        const tile2 = tiles[i + 1];

                        if (!tile1 || !tile2) return false;
                        if (tile1.color !== tile2.color || tile1.number !== tile2.number) return false;

                        pairs.push([tile1, tile2]);
                    }

                    return pairs.length >= 5;
                },

                calculateGameScore(winningPlayer, withJoker = false) {
                    this.players.forEach((player, index) => {
                        if (index === winningPlayer) {
                            // Kazanan oyuncu
                            const baseScore = withJoker ? -202 : -101;
                            player.score += player.hasOpenedDouble ? baseScore * 2 : baseScore;
                        } else {
                            // Kaybeden oyuncular
                            let score = 0;
                            if (!player.openPers.length) {
                                // Hiç per açmamış
                                score = withJoker ? 404 : 202;
                            } else {
                                // Elinde kalan taşların toplamı
                                score = this.calculateRemainingScore(player);
                                if (withJoker) score *= 2;
                            }

                            if (player.hasOpenedDouble) score *= 2;
                            player.score += score;
                        }
                    });
                },

                applyPenalty(playerIndex, reason) {
                    const PENALTY = 101;

                    switch (reason) {
                        case 'invalid_per_total':
                            // Per açtı ama 101'e ulaşmadı
                            this.players[playerIndex].score += PENALTY;
                            break;

                        case 'missed_addition':
                            // Eklenebilecek taşı eklemedi
                            this.players[playerIndex].score += PENALTY;
                            break;

                        case 'multiple_take_back':
                            // Birden fazla taş geri aldı
                            this.players[playerIndex].score += PENALTY;
                            break;
                    }
                },

                canOpenHand() {
                    // El açma koşullarını kontrol et
                    const totalPerPoints = this.calculatePerTotal(this.currentPlayer);
                    return totalPerPoints >= 101;
                },

                tryOpenDouble() {
                    if (this.selectedTiles.length < 10) {
                        alert('Çift açmak için en az 5 çift (10 taş) seçmelisiniz!');
                        return;
                    }

                    // İstakadaki pozisyona göre sırala
                    const sortedIndices = [...this.selectedTiles].sort((a, b) => a - b);
                    const selectedTilesData = sortedIndices.map(index => ({
                        ...this.playerTiles[index],
                        index
                    }));

                    if (this.isValidDouble(selectedTilesData)) {
                        // Çiftleri per alanına ekle
                        const perTiles = selectedTilesData.map(t => ({
                            color: t.color,
                            number: t.number
                        }));

                        this.players[this.currentPlayer].openPers.push(perTiles);
                        this.players[this.currentPlayer].hasOpenedDouble = true;

                        // Kullanılan taşları istakadan kaldır
                        sortedIndices.forEach(index => {
                            this.playerTiles[index] = null;
                        });

                        // Seçili taşları temizle
                        this.selectedTiles = [];

                        // Puan ekle (her çift 20 puan)
                        this.updateScore(this.currentPlayer, perTiles.length * 10);
                    } else {
                        alert('Geçersiz çift! Her çift aynı renk ve sayıda olmalı.');
                    }
                },

                tryOpenHand() {
                    const pers = this.findArrangedPers();
                    const totalPoints = pers.reduce((total, per) => {
                        // Her per için puan hesapla
                        const perPoints = per.reduce((perTotal, tile) => {
                            // Joker kontrolü
                            if (tile.number === '★') {
                                return perTotal + (this.indicatorTile.number === 13 ? 1 : this.indicatorTile.number + 1);
                            }

                            // Okey kontrolü
                            const okeyTile = this.getOkeyTile();
                            if (tile.color === okeyTile.color && tile.number === okeyTile.number) {
                                // Okey taşı için per içindeki diğer taşların ortalamasını al
                                const otherTiles = per.filter(t => t !== tile);
                                if (otherTiles.length === 0) return perTotal + tile.number;
                                const avg = otherTiles.reduce((sum, t) => sum + (t.number === '★' ?
                                    (this.indicatorTile.number === 13 ? 1 : this.indicatorTile.number + 1) :
                                    t.number), 0) / otherTiles.length;
                                return perTotal + Math.round(avg);
                            }

                            // Normal taş
                            return perTotal + tile.number;
                        }, 0);

                        // Her per için bonus puan (opsiyonel)
                        const bonusPoints = per.length > 3 ? (per.length - 3) * 5 : 0;

                        return total + perPoints + bonusPoints;
                    }, 0);

                    console.log('Toplam Puanlar:', totalPoints); // Debug için

                    if (totalPoints < 101) {
                        alert(`Dizili perler toplamı 101 sayısına ulaşmıyor! (Toplam: ${totalPoints})`);
                        return;
                    }

                    pers.forEach(per => {
                        this.players[this.currentPlayer].openPers.push(per);
                        per.forEach(tile => {
                            const index = this.playerTiles.findIndex(t => t && t.id === tile.id);
                            if (index !== -1) this.playerTiles[index] = null;
                        });
                    });

                    this.players[this.currentPlayer].handOpened = true;
                    alert(`El açıldı! Toplam ${totalPoints} puan ile. Artık diğer oyuncuların perlerine taş ekleyebilirsiniz.`);
                },

                // İstakadaki dizili perleri tespit et
                findArrangedPers() {
                    // Önce okey taşını belirle
                    const okeyNumber = this.indicatorTile.number === 13 ? 1 : this.indicatorTile.number + 1;
                    const okeyColor = this.indicatorTile.color;

                    // İstaka haritasını oluştur ve okey taşlarını işaretle
                    const topRow = this.playerTiles.slice(0, 18).map(tile => {
                        if (!tile) return '_';
                        // Okey taşı kontrolü
                        if (tile.color === okeyColor && tile.number === okeyNumber) {
                            return {
                                display: '**',
                                tile: {
                                    ...tile,
                                    isOkey: true
                                }
                            };
                        }
                        return {
                            display: tile.number === '★' ? '*' : tile.number,
                            tile: tile
                        };
                    });

                    const bottomRow = this.playerTiles.slice(18).map(tile => {
                        if (!tile) return '_';
                        // Okey taşı kontrolü
                        if (tile.color === okeyColor && tile.number === okeyNumber) {
                            return {
                                display: '**',
                                tile: {
                                    ...tile,
                                    isOkey: true
                                }
                            };
                        }
                        return {
                            display: tile.number === '★' ? '*' : tile.number,
                            tile: tile
                        };
                    });

                    // İstaka haritasını göster
                    const istakaMap = `
Üst Sıra:   ${topRow.map(t => typeof t === 'string' ? t : t.display).join('-')}
Alt Sıra:   ${bottomRow.map(t => typeof t === 'string' ? t : t.display).join('-')}`;
                    alert(istakaMap);

                    // Per kontrolü için grupları bul
                    const findGroups = row => {
                        const groups = [];
                        let currentGroup = [];

                        row.forEach(item => {
                            if (item === '_') {
                                if (currentGroup.length > 0) {
                                    groups.push(currentGroup);
                                    currentGroup = [];
                                }
                            } else {
                                currentGroup.push(item.tile);
                            }
                        });

                        if (currentGroup.length > 0) {
                            groups.push(currentGroup);
                        }

                        return groups;
                    };

                    const topGroups = findGroups(topRow);
                    const bottomGroups = findGroups(bottomRow);
                    const allGroups = [...topGroups, ...bottomGroups];

                    // Her grubu per kurallarına göre kontrol et
                    const validPers = allGroups.filter(group => {
                        if (group.length < 3) return false;

                        // Aynı sayı kontrolü
                        const isSameNumber = group.every(tile => {
                            if (tile.isOkey) {
                                // Okey taşı, gruptaki diğer taşların sayısını alır
                                return true;
                            }
                            return tile.number === group[0].number;
                        });

                        if (isSameNumber) {
                            // Farklı renk kontrolü
                            const colors = new Set();
                            let lastNormalTile = null;

                            group.forEach(tile => {
                                if (tile.isOkey) {
                                    // Okey taşı için farklı bir renk varsay
                                    if (lastNormalTile) {
                                        // Kullanılmayan bir renk bul
                                        ['red', 'blue', 'green', 'yellow'].forEach(color => {
                                            if (!colors.has(color)) {
                                                colors.add(color);
                                            }
                                        });
                                    }
                                } else {
                                    colors.add(tile.color);
                                    lastNormalTile = tile;
                                }
                            });

                            return colors.size === group.length && group.length <= 4;
                        }

                        // Sıralı sayı kontrolü
                        let baseColor = null;
                        let expectedNumber = null;

                        return group.every((tile, index) => {
                            if (index === 0) {
                                baseColor = tile.isOkey ? null : tile.color;
                                expectedNumber = tile.isOkey ? null : tile.number;
                                return true;
                            }

                            if (tile.isOkey) {
                                // Okey taşı, beklenen sayı olarak kabul edilir
                                if (expectedNumber === null) {
                                    expectedNumber = tile.number;
                                } else {
                                    expectedNumber++;
                                }
                                return true;
                            }

                            if (baseColor === null) {
                                baseColor = tile.color;
                            }

                            if (tile.color !== baseColor) return false;

                            if (expectedNumber === null) {
                                expectedNumber = tile.number;
                            } else {
                                if (tile.number !== expectedNumber + 1) return false;
                                expectedNumber = tile.number;
                            }

                            return true;
                        }) && group.length <= 5;
                    });

                    return validPers;
                },

                // autoOpenPer fonksiyonunu güncelle
                autoOpenPer() {
                    const pers = this.findArrangedPers();

                    if (pers.length === 0) {
                        alert('Dizilmiş per bulunamadı!');
                        return;
                    }

                    let openedPers = 0;
                    pers.forEach(per => {
                        // Per açma işlemi
                        this.players[this.currentPlayer].openPers.push([...per]);

                        // Kullanılan taşları istakadan kaldır
                        per.forEach(tile => {
                            const index = this.playerTiles.findIndex(t =>
                                t && t.id === tile.id
                            );
                            if (index !== -1) {
                                this.playerTiles[index] = null;
                            }
                        });

                        openedPers++;
                    });

                    alert(`${openedPers} adet per açıldı!`);
                },

                // Otomatik çift açma
                autoOpenPairs() {
                    const pairs = this.findArrangedPairs();
                    if (pairs.length < 5) {
                        alert('En az 5 çift dizilmiş olmalı!');
                        return;
                    }

                    const allPairTiles = pairs.flat();
                    this.players[this.currentPlayer].openPers.push(allPairTiles);
                    this.players[this.currentPlayer].hasOpenedDouble = true;

                    // Kullanılan taşları kaldır
                    allPairTiles.forEach(tile => {
                        const index = this.playerTiles.findIndex(t => t && t.id === tile.id);
                        if (index !== -1) this.playerTiles[index] = null;
                    });

                    alert(`${pairs.length} çift açıldı!`);
                },

                // Otomatik el açma
                autoOpenHand() {
                    const pers = this.findArrangedPers();
                    const totalPoints = pers.reduce((total, per) => {
                        // Her per için puan hesapla
                        const perPoints = per.reduce((perTotal, tile) => {
                            // Joker kontrolü
                            if (tile.number === '★') {
                                return perTotal + (this.indicatorTile.number === 13 ? 1 : this.indicatorTile.number + 1);
                            }

                            // Okey kontrolü
                            const okeyTile = this.getOkeyTile();
                            if (tile.color === okeyTile.color && tile.number === okeyTile.number) {
                                // Okey taşı için per içindeki diğer taşların ortalamasını al
                                const otherTiles = per.filter(t => t !== tile);
                                if (otherTiles.length === 0) return perTotal + tile.number;
                                const avg = otherTiles.reduce((sum, t) => sum + (t.number === '★' ?
                                    (this.indicatorTile.number === 13 ? 1 : this.indicatorTile.number + 1) :
                                    t.number), 0) / otherTiles.length;
                                return perTotal + Math.round(avg);
                            }

                            // Normal taş
                            return perTotal + tile.number;
                        }, 0);

                        // Her per için bonus puan (opsiyonel)
                        const bonusPoints = per.length > 3 ? (per.length - 3) * 5 : 0;

                        return total + perPoints + bonusPoints;
                    }, 0);

                    console.log('Toplam Puanlar:', totalPoints); // Debug için

                    if (totalPoints < 101) {
                        alert(`Dizili perler toplamı 101 sayısına ulaşmıyor! (Toplam: ${totalPoints})`);
                        return;
                    }

                    pers.forEach(per => {
                        this.players[this.currentPlayer].openPers.push(per);
                        per.forEach(tile => {
                            const index = this.playerTiles.findIndex(t => t && t.id === tile.id);
                            if (index !== -1) this.playerTiles[index] = null;
                        });
                    });

                    this.players[this.currentPlayer].handOpened = true;
                    alert(`El açıldı! Toplam ${totalPoints} puan ile. Artık diğer oyuncuların perlerine taş ekleyebilirsiniz.`);
                },

                // Taşları otomatik düzenle
                autoArrangeTiles() {
                    // Tüm taşları topla
                    let allTiles = this.playerTiles.filter(tile => tile !== null);

                    // Olası tüm perleri bul
                    let possiblePers = this.findAllPossiblePers(allTiles);

                    // Perleri puanlarına göre sırala (en yüksek puan üstte)
                    possiblePers.sort((a, b) => {
                        const scoreA = a.reduce((sum, tile) => sum + (tile.number === '★' ?
                            (this.indicatorTile.number === 13 ? 1 : this.indicatorTile.number + 1) :
                            tile.number), 0);
                        const scoreB = b.reduce((sum, tile) => sum + (tile.number === '★' ?
                            (this.indicatorTile.number === 13 ? 1 : this.indicatorTile.number + 1) :
                            tile.number), 0);
                        return scoreB - scoreA;
                    });

                    // En iyi perleri seç
                    let selectedPers = [];
                    let usedTiles = new Set();

                    possiblePers.forEach(per => {
                        // Per içindeki taşlar daha önce kullanılmış mı kontrol et
                        const isPerAvailable = per.every(tile => !usedTiles.has(tile.id));

                        if (isPerAvailable) {
                            selectedPers.push(per);
                            per.forEach(tile => usedTiles.add(tile.id));
                        }
                    });

                    // İstakayı temizle
                    this.playerTiles = Array(36).fill(null);

                    // Seçilen perleri üst sıraya yerleştir
                    let topRowIndex = 0;
                    selectedPers.forEach(per => {
                        if (topRowIndex + per.length <= 18) { // Üst sırada yer varsa
                            per.forEach(tile => {
                                this.playerTiles[topRowIndex] = tile;
                                topRowIndex++;
                            });
                            // Per arası boşluk bırak
                            topRowIndex++;
                        }
                    });

                    // Kullanılmayan taşları alt sıranın sonuna yerleştir
                    let unusedTiles = allTiles.filter(tile => !usedTiles.has(tile.id));
                    let bottomRowIndex = 35;

                    unusedTiles.forEach(tile => {
                        while (bottomRowIndex >= 18 && this.playerTiles[bottomRowIndex] !== null) {
                            bottomRowIndex--;
                        }
                        if (bottomRowIndex >= 18) {
                            this.playerTiles[bottomRowIndex] = tile;
                            bottomRowIndex--;
                        }
                    });
                },

                // Tüm olası perleri bul
                findAllPossiblePers(tiles) {
                    let possiblePers = [];
                    const okeyTile = this.getOkeyTile();

                    // Aynı sayılı per kombinasyonlarını bul
                    for (let i = 1; i <= 13; i++) {
                        let sameTiles = tiles.filter(t => t.number === i ||
                            (t.number === '★') ||
                            (t.color === okeyTile.color && t.number === okeyTile.number));

                        if (sameTiles.length >= 3) {
                            // Tüm olası 3'lü ve 4'lü kombinasyonları oluştur
                            this.getCombinations(sameTiles, 3).forEach(combo => {
                                if (this.isValidPerWithJoker(combo)) {
                                    possiblePers.push(combo);
                                }
                            });
                            if (sameTiles.length >= 4) {
                                this.getCombinations(sameTiles, 4).forEach(combo => {
                                    if (this.isValidPerWithJoker(combo)) {
                                        possiblePers.push(combo);
                                    }
                                });
                            }
                        }
                    }

                    // Sıralı per kombinasyonlarını bul
                    ['red', 'blue', 'yellow', 'green'].forEach(color => {
                        let colorTiles = tiles.filter(t => t.color === color ||
                            t.number === '★' ||
                            (t.color === okeyTile.color && t.number === okeyTile.number));

                        for (let start = 1; start <= 11; start++) {
                            let sequentialTiles = colorTiles.filter(t =>
                                t.number >= start && t.number <= start + 4 ||
                                t.number === '★' ||
                                (t.color === okeyTile.color && t.number === okeyTile.number)
                            );

                            if (sequentialTiles.length >= 3) {
                                // 3'lü, 4'lü ve 5'li kombinasyonları kontrol et
                                [3, 4, 5].forEach(size => {
                                    if (sequentialTiles.length >= size) {
                                        this.getCombinations(sequentialTiles, size).forEach(combo => {
                                            if (this.isValidPerWithJoker(combo)) {
                                                possiblePers.push(combo);
                                            }
                                        });
                                    }
                                });
                            }
                        }
                    });

                    return possiblePers;
                },

                // Kombinasyonları oluştur
                getCombinations(arr, size) {
                    const result = [];

                    function backtrack(start, current) {
                        if (current.length === size) {
                            result.push([...current]);
                            return;
                        }

                        for (let i = start; i < arr.length; i++) {
                            current.push(arr[i]);
                            backtrack(i + 1, current);
                            current.pop();
                        }
                    }

                    backtrack(0, []);
                    return result;
                }
            }));
        });
    </script>
</body>

</html>