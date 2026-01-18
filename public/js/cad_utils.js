window.CadUtils = {
    /**
     * Processa input de texto para coordenadas.
     * Regra: Se existe lastPoint, coordenadas X,Y são somadas (Relativas).
     */
    parseInput: function(input, lastPoint, currentMouse) {
        input = input.trim();
        if (!input) return null;

        // 1. Polar (Distância < Ângulo) ex: 100<45
        // Sempre relativo ao último ponto
        if (input.includes('<')) {
            const parts = input.split('<');
            const dist = parseFloat(parts[0]);
            const angleDeg = parseFloat(parts[1]);

            if (isNaN(dist) || isNaN(angleDeg) || !lastPoint) return null;

            // Converte para Radianos (CAD: 0=Direita, 90=Cima)
            const angleRad = angleDeg * (Math.PI / 180);
            return {
                x: lastPoint.x + dist * Math.cos(angleRad),
                y: lastPoint.y - dist * Math.sin(angleRad) // Y Canvas é invertido (negativo sobe)
            };
        }

        // 2. Coordenada Cartesiana (X,Y) ex: 10,20
        if (input.includes(',')) {
            const parts = input.split(',');
            const val1 = parseFloat(parts[0]);
            const val2 = parseFloat(parts[1]);
            
            if (isNaN(val1) || isNaN(val2)) return null;

            if (lastPoint) {
                // SEGUNDO PONTO: RELATIVO (Soma)
                // Ex: Se estava em 100,100 e digita 50,0 -> Vai para 150,100
                return { x: lastPoint.x + val1, y: lastPoint.y + val2 };
            } else {
                // PRIMEIRO PONTO: ABSOLUTO
                return { x: val1, y: val2 };
            }
        }

        // 3. Comprimento Puro (Número) ex: 50
        // Usa a direção do mouse como vetor
        if (/^-?\d+(\.\d+)?$/.test(input)) {
            const dist = parseFloat(input);
            if (!lastPoint || !currentMouse) return null;

            const dx = currentMouse.x - lastPoint.x;
            const dy = currentMouse.y - lastPoint.y;
            const currentDist = Math.sqrt(dx*dx + dy*dy);

            if (currentDist === 0) return lastPoint;

            // Vetor unitário * distância desejada
            return {
                x: lastPoint.x + (dx / currentDist) * dist,
                y: lastPoint.y + (dy / currentDist) * dist
            };
        }

        return null;
    }
};