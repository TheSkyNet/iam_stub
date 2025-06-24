
// ScientistLogo.js
const ScientistLogo = {
    oninit: function() {
        // Get viewport width to distribute items
        const availableWidth = window.innerWidth - 300; // Subtracting navbar width

        this.state = {
            scientistX: Math.random() * availableWidth,
            direction: 1,
            walkPhase: 0,
            catVisible: false,
            catX: Math.random() * (window.innerWidth - 300), // Initial random position
            currentAction: 'walk',
            targetX: null,
            beakers: [
                { id: 1, x: Math.random() * availableWidth, color: '#ffb3b3', filled: true },
                { id: 2, x: Math.random() * availableWidth, color: '#b8e6b8', filled: true },
                { id: 3, x: Math.random() * availableWidth, color: '#b3d9ff', filled: true },
                { id: 4, x: Math.random() * availableWidth, color: null, filled: false }
            ],
            heldBeaker: null,
            bunsenX: Math.random() * availableWidth,
            lastInteractionTime: 0,
            labWidth: availableWidth
        };
    },

    updateWidth: function() {
        const newWidth = window.innerWidth - 300;
        const scale = newWidth / this.state.labWidth;

        // Update all positions proportionally
        this.state.scientistX *= scale;
        this.state.bunsenX *= scale;
        this.state.beakers.forEach(beaker => {
            beaker.x *= scale;
        });
        this.state.labWidth = newWidth;
    },

    oncreate: function() {
        this.startAnimation();
        // Update to set new random position only when visibility changes
        this.catInterval = setInterval(() => {
            if (!this.state.catVisible) {
                this.state.catX = Math.random() * (window.innerWidth - 300);
            }
            this.state.catVisible = !this.state.catVisible;
            m.redraw();
        }, 5000);
    },


    onremove: function() {
        clearInterval(this.catInterval);
        cancelAnimationFrame(this.animationFrame);
        window.removeEventListener('resize', this.updateWidth);
    },

    chooseNewDestination: function() {
        const possibleDestinations = [
            ...this.state.beakers.map(b => b.x),
            this.state.heldBeaker ? this.state.bunsenX : null
        ].filter(x => x !== null && Math.abs(x - this.state.scientistX) > 100);

        if (possibleDestinations.length > 0) {
            const randomIndex = Math.floor(Math.random() * possibleDestinations.length);
            this.state.targetX = possibleDestinations[randomIndex];
            this.state.direction = this.state.targetX > this.state.scientistX ? 1 : -1;
        } else {
            this.state.targetX = Math.random() * this.state.labWidth;
            this.state.direction = this.state.targetX > this.state.scientistX ? 1 : -1;
        }
    },

    startAnimation: function() {
        this.chooseNewDestination();
        const animate = () => {
            const now = Date.now();
            if (this.state.currentAction === 'walk') {
                this.state.walkPhase = (this.state.walkPhase + 0.15) % (2 * Math.PI);
                this.state.scientistX += 2 * this.state.direction;

                // Check if we reached target or boundaries
                if (Math.abs(this.state.scientistX - this.state.targetX) < 5 ||
                    this.state.scientistX > this.state.labWidth || this.state.scientistX < 0) {
                    if (now - this.state.lastInteractionTime > 2000) {
                        this.chooseNewDestination();
                    }
                }

                this.checkInteractions();
            }
            m.redraw();
            this.animationFrame = requestAnimationFrame(animate);
        };
        animate();
    },

    checkInteractions: function() {
        const now = Date.now();
        if (now - this.state.lastInteractionTime < 2000) return;

        const nearestBeaker = this.state.beakers.find(b =>
            Math.abs(this.state.scientistX - b.x) < 15 &&
            (this.state.heldBeaker ? !b.filled : b.filled)
        );

        if (nearestBeaker) {
            this.state.currentAction = 'interact';
            this.state.lastInteractionTime = now;
            setTimeout(() => {
                this.handleBeakerInteraction(nearestBeaker);
                this.chooseNewDestination();
            }, 500);
            return;
        }

        if (Math.abs(this.state.scientistX - this.state.bunsenX) < 15 &&
            this.state.heldBeaker &&
            this.state.targetX === this.state.bunsenX) {
            this.state.currentAction = 'heat';
            this.state.lastInteractionTime = now;
            setTimeout(() => {
                this.state.heldBeaker.color = this.darkenColor(this.state.heldBeaker.color);
                this.state.currentAction = 'walk';
                this.chooseNewDestination();
            }, 2000);
        }
    },

    handleBeakerInteraction: function(beaker) {
        if (!this.state.heldBeaker && beaker.filled) {
            this.state.heldBeaker = { ...beaker };
            beaker.filled = false;
            this.state.heatedBeaker = false;
        } else if (this.state.heldBeaker && !beaker.filled) {
            beaker.filled = true;
            beaker.color = this.state.heldBeaker.color;
            this.state.heldBeaker = null;
            this.state.heatedBeaker = false;
        }
        setTimeout(() => {
            this.state.currentAction = 'walk';
        }, 500);
    },

    darkenColor: function(color) {
        const factor = 0.8;
        const r = Math.floor(parseInt(color.slice(1,3), 16) * factor).toString(16).padStart(2, '0');
        const g = Math.floor(parseInt(color.slice(3,5), 16) * factor).toString(16).padStart(2, '0');
        const b = Math.floor(parseInt(color.slice(5,7), 16) * factor).toString(16).padStart(2, '0');
        return `#${r}${g}${b}`;
    },

    mixColors: function(color1, color2) {
        const mix = (a, b) => Math.floor((parseInt(a, 16) + parseInt(b, 16)) / 2);
        const r = mix(color1.slice(1,3), color2.slice(1,3));
        const g = mix(color1.slice(3,5), color2.slice(3,5));
        const b = mix(color1.slice(5,7), color2.slice(5,7));
        return `#${r.toString(16)}${g.toString(16)}${b.toString(16)}`;
    },

    view: function() {
        return m("div.scientist-lab", {
            style: { width: "100%", height: "100%" }
        }, [
            m("svg", {
                xmlns: "http://www.w3.org/2000/svg",
                viewBox: `0 0 ${this.state.labWidth} 60`,
                style: { width: "100%", height: "100%" }
            }, [
                // Beakers
                ...this.state.beakers.map(this.renderBeaker.bind(this)),

                // Cat
                this.state.catVisible && this.renderCat(),

                // Bunsen burner
                this.renderBunsenBurner(),

                // Scientist
                this.renderScientist(),

                // Held beaker
                this.state.heldBeaker && this.renderHeldBeaker()
            ]),
            m("style", this.getAnimationStyles())
        ]);
    },

    renderScientist: function() {
        const { scientistX, direction, currentAction, walkPhase } = this.state;
        return m("g.scientist", {
            transform: `translate(${scientistX}, 35) scale(${direction}, 1)`
        }, [
            // Head
            m("circle", { cx: 0, cy: 0, r: 5, fill: "#fff" }),
            // Body
            m("line", { x1: 0, y1: 5, x2: 0, y2: 15, stroke: "#fff", "stroke-width": "2" }),
            // Arms
            m("path", {
                d: currentAction === 'interact' || currentAction === 'heat' ?
                    "M0,8 L-8,12 L-5,8 M0,8 L8,12 L5,8" :
                    `M0,8 L${-5 * Math.sin(walkPhase)},12 M0,8 L${5 * Math.sin(walkPhase + Math.PI)},12`,
                stroke: "#fff",
                "stroke-width": "2",
                class: currentAction === 'walk' ? 'walking-arms' : ''
            }),
            // Legs
            m("path", {
                d: currentAction === 'walk' ?
                    `M0,15 L${-5 * Math.sin(walkPhase)},20 M0,15 L${5 * Math.sin(walkPhase + Math.PI)},20` :
                    "M-3,15 L-3,20 M3,15 L3,20",
                stroke: "#fff",
                "stroke-width": "2",
                class: currentAction === 'walk' ? 'walking-legs' : ''
            })
        ]);
    },

    renderBeaker: function(beaker) {
        return m("g.beaker", { transform: `translate(${beaker.x}, 30)` }, [
            m("path", {
                d: "M-8,0 L-6,20 L6,20 L8,0",
                fill: "none",
                stroke: "#fff",
                "stroke-width": "1"
            }),
            beaker.filled && m("rect", {
                x: -5, y: 5,
                width: 10, height: 14,
                fill: beaker.color,
                opacity: 0.8
            })
        ]);
    },


    renderBunsenBurner: function() {
        const isHeating = this.state.currentAction === 'heat';
        return m("g.bunsen", { transform: `translate(${this.state.bunsenX}, 30)` }, [
            // Base
            m("rect", {
                x: -6, y: 0,
                width: 12, height: 20,
                fill: "#666"
            }),
            // Outer flame glow
            m("path.flame-glow", {
                d: isHeating
                    ? "M-5,-4 Q0,-18 5,-4 Q0,-8 -5,-4"
                    : "M-3,-2 Q0,-12 3,-2 Q0,-6 -3,-2",
                fill: "rgba(255, 150, 50, 0.2)",
                class: "animate-flame-glow"
            }),
            // Main flame
            m("path.flame-main", {
                d: isHeating
                    ? "M-4,-4 Q0,-16 4,-4 Q0,-8 -4,-4"
                    : "M-2,-2 Q0,-10 2,-2 Q0,-5 -2,-2",
                fill: "#ff6600",
                class: "animate-flame-main"
            }),
            // Inner flame
            m("path.flame-inner", {
                d: isHeating
                    ? "M-2,-4 Q0,-12 2,-4 Q0,-7 -2,-4"
                    : "M-1,-2 Q0,-8 1,-2 Q0,-4 -1,-2",
                fill: "#fff",
                class: "animate-flame-inner"
            }),
            // Blue base
            m("path.flame-base", {
                d: isHeating
                    ? "M-3,-4 Q0,-6 3,-4 Q0,-3 -3,-4"
                    : "M-1.5,-2 Q0,-3 1.5,-2 Q0,-1.5 -1.5,-2",
                fill: "#4499ff",
                class: "animate-flame-base"
            })
        ]);
    },

    renderHeldBeaker: function() {
        const { scientistX, direction } = this.state;
        return m("g", { transform: `translate(${scientistX + (direction * 8)}, 25)` }, [
            m("path", {
                d: "M-4,0 L-3,10 L3,10 L4,0",
                fill: this.state.heldBeaker.color,
                stroke: "#fff",
                "stroke-width": "1"
            })
        ]);
    },

    renderCat: function() {
        return m("g.cat", {
            transform: `translate(${this.state.catX}, 42)`
        }, [
            // Round body
            m("circle", {
                cx: 0, cy: 0,
                r: 8,
                fill: "#333333"
            }),
            // Pointy ears
            m("path", {
                d: "M-3,-4 L-4,-8 L-2,-4 M3,-4 L4,-8 L2,-4",
                fill: "#333333"
            }),
            // Face
            m("circle", { cx: -2, cy: -1, r: 0.7, fill: "#ffffff" }),
            m("circle", { cx: 2, cy: -1, r: 0.7, fill: "#ffffff" }),
            // Tail (wagging)
            m("path", {
                d: "M5,2 Q9,2 11,-2",
                stroke: "#333333",
                "stroke-width": "2",
                fill: "none",
                class: "animate-tail"
            })
        ]);
    },


    getAnimationStyles: function() {
        return `
            
            @keyframes flame-flicker {
                0%, 100% { transform: scaleY(1) translateY(0); }
                25% { transform: scaleY(1.1) translateY(-1px); }
                50% { transform: scaleY(0.9) translateY(0.5px); }
                75% { transform: scaleY(1.05) translateY(-0.5px); }
            }
            
            @keyframes flame-sway {
                0%, 100% { transform: translateX(0) rotate(0deg); }
                25% { transform: translateX(0.5px) rotate(1deg); }
                75% { transform: translateX(-0.5px) rotate(-1deg); }
            }
            
            .flame-glow {
                opacity: 0.7;
                animation: flame-flicker 1.5s infinite ease-in-out;
            }
            
            .flame-main {
                opacity: 0.9;
                animation: flame-flicker 1.2s infinite ease-in-out;
            }
            
            .flame-inner {
                opacity: 0.8;
                animation: flame-flicker 0.9s infinite ease-in-out;
            }
            
            .flame-base {
                opacity: 0.8;
                animation: flame-sway 1.8s infinite ease-in-out;
            }
        `;
    }

};

module.exports = ScientistLogo;